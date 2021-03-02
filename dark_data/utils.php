<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";


define("DATA_MAP", 0);
define("DATA_ARRAY", 1);
define("DATA_STRING", 2);
define("DATA_NUMBER", 3);
define("DATA_BOOL", 4);
define("DATA_NULL", 5);

function data_id($path_keys, $password, $create = false)
{
    $keys[] = explode('/', dirname($_SERVER['PHP_SELF']))[1];

    if (is_string($path_keys))
        $path_keys = explode(".", $path_keys);

    foreach ($path_keys as $path_key) {
        if (strpos($path_key, ".") === false)
            $keys[] = $path_key;
        else
            foreach (explode(".", $path_key) as $key)
                $keys[] = $key;
    }

    $data_id = null;
    foreach ($keys as $index => $key) {
        $push = strpos($key, "[]");
        $key = substr($key, 0, $push !== false ? $push : strlen($key));
        $data_parent_id = $data_id;

        $data = selectRowWhere("data", array(
            "data_parent_id" => $data_id,
            "data_key" => $key,
        ));

        if ($data["data_password"] != null && $data["data_password"] != $password)
            return null;

        $data_id = $data["data_id"];

        if ($data_id == null) {
            if ($create == true) {
                $data_id = insertRowAndGetId("data", array(
                    "data_parent_id" => $data_parent_id,
                    "data_key" => $key,
                    "data_password" => $index == sizeof($keys) - 1  && $push === false ? $password : null,
                    "data_type" => DATA_MAP,
                ));
            } else {
                return null;
            }
        }

        if ($push !== false) {
            updateWhere("data", array(
                "data_type" => DATA_ARRAY,
            ), array(
                "data_id" => $data_parent_id,
            ));
            $children_count = scalarWhere("data", "count(*)", array(
                "data_parent_id" => $data_id,
            ));
            $data_id = insertRowAndGetId("data", array(
                "data_parent_id" => $data_id,
                "data_key" => $children_count,
                "data_password" => $index == sizeof($keys) - 1 ? $password : null,
                "data_type" => DATA_MAP,
            ));
        }
    }
    return $data_id;
}


function data_get_value($data, $level = 0)
{
    // TODO  level
    if (is_numeric($data))
        $data = selectRowWhere("data", array("data_id" => $data));
    if ($data["data_type"] == DATA_BOOL) {
        $result = boolval($data["data_value"]);
    } else if ($data["data_type"] == DATA_NUMBER) {
        $result = doubleval($data["data_value"]);
    } else if ($data["data_type"] == DATA_STRING) {
        $result = $data["data_value"];
    } else if ($data["data_type"] == DATA_ARRAY) {
        $result = array();
        if ($level != 0) {
            $children = select("select * from data where data_parent_id = " . $data["data_id"] . " order by data_key + 0");
            foreach ($children as $child)
                $result[] = data_get_value($child, $level - 1);
        }
    } else if ($data["data_type"] == DATA_MAP) {
        $result = array();
        if ($level != 0) {
            $children = select("select * from data where data_parent_id = " . $data["data_id"] . " order by data_key + 0");
            foreach ($children as $child)
                $result[$child["data_key"]] = data_get_value($child, $level - 1);
        }
    } else {
        $result = null;
    }
    return $result;
}

function is_assoc(array $arr)
{
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function data_set_value($data_id, &$result)
{
    if (is_numeric($result) && !is_string($result)) {
        return updateWhere("data", array("data_type" => DATA_NUMBER, "data_value" => $result), array("data_id" => $data_id));
    } else if (is_bool($result)) {
        return updateWhere("data", array("data_type" => DATA_BOOL, "data_value" => $result), array("data_id" => $data_id));
    } else if (is_null($result)) {
        return updateWhere("data", array("data_type" => DATA_NULL, "data_value" => $result), array("data_id" => $data_id));
    } else if (is_string($result)) {
        return updateWhere("data", array("data_type" => DATA_STRING, "data_value" => $result), array("data_id" => $data_id));
    } else if (is_array($result)) {
        if (is_assoc($result)) {
            updateWhere("data", array("data_type" => DATA_MAP, "data_value" => null), array("data_id" => $data_id));
        } else {
            updateWhere("data", array("data_type" => DATA_ARRAY, "data_value" => null), array("data_id" => $data_id));
        }
        $success = true;
        foreach ($result as $key => $value) {
            $child_data_id = insertRowAndGetId("data", array(
                "data_parent_id" => $data_id,
                "data_key" => $key,
                "data_type" => DATA_NULL,
            ));
            $success = data_set_value($child_data_id, $value);
            if (!$success) break;
        }
        return $success ? $data_id : false;
    }
}

function data_get($key, $password, $level = 0)
{
    $data_id = data_id($key, $password);
    if ($data_id != null)
        return data_get_value($data_id, $level);
    return null;
}

function data_delete_children($data_id)
{
    // TODO doesnt work
    $children = selectListWhere("data", "data_id", array(
        "data_parent_id" => $data_id
    ));

    foreach ($children as $child_data_id) {
        data_delete_children($child_data_id);
        query("delete from data where data_id = $child_data_id");
    }
}

function data_put($keys, $password, $value)
{
    $data_id = data_id($keys, $password, true);
    data_delete_children($data_id);
    return data_set_value($data_id, $value);
}

function dataSelect($table, $index, $password)
{
    $data_id = data_id([$table, $index], $password);
    return data_get_value($data_id);
}

function dataPut($table, $index, $password, $value)
{
    return data_put([$table, $index], $password, $value);
}

function dataPush($table, $index, $password, $value)
{
    return data_put([$table, $index . "[]"], $password, $value);
}
