<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";


define("DATA_MAP", 0);
define("DATA_ARRAY", 1);
define("DATA_STRING", 2);
define("DATA_NUMBER", 3);
define("DATA_BOOL", 4);
define("DATA_NULL", 5);

function dataCreate(array $path, $password, $create = true)
{
    array_unshift($path, explode('/', dirname($_SERVER['PHP_SELF']))[1]);

    $password_checked = false;
    $data_id = null;

    foreach ($path as $index => $key) {

        $push = strpos($key, "[]");
        $key = substr($key, 0, $push !== false ? $push : strlen($key));
        $data_parent_id = $data_id;

        $data = selectRowWhere("data", array(
            "data_parent_id" => $data_parent_id,
            "data_key" => $key,
        ));

        if ($data["data_password"] != null) {
            if ($data["data_password"] != $password)
                return null;
            $password_checked = true;
        }

        $data_id = $data["data_id"];

        if ($data_id == null) {
            if ($create == true) {
                $data_id = insertRowAndGetId("data", array(
                    "data_parent_id" => $data_parent_id,
                    "data_key" => $key,
                    "data_password" => $password_checked === false && $index == sizeof($path) - 1 && $push === false ? $password : null,
                    "data_type" => DATA_MAP,
                ));
            } else {
                return null;
            }
        }

        if ($push !== false) {

            /*if ($GLOBALS["test"] != null)
                die(json_encode($keys));*/

            updateWhere("data", array(
                "data_type" => DATA_ARRAY,
            ), array(
                "data_id" => $data_id,
            ));
            $children_count = scalarWhere("data", "count(*)", array(
                "data_parent_id" => $data_id,
            ));
            $data_id = insertRowAndGetId("data", array(
                "data_parent_id" => $data_id,
                "data_key" => $children_count,
                "data_password" => $password_checked === false && $index == sizeof($path) - 1 ? $password : null,
                "data_type" => DATA_MAP,
            ));
        }

    }
    return $data_id;
}


function data_get_value($data, $level = -1, $asc = null, $offset = 0, $count = 10000)
{
    if ($data == null)
        return null;
    if (is_numeric($data))
        $data = selectRowWhere("data", array("data_id" => $data));

    if ($data["data_type"] == DATA_BOOL) {
        $result = boolval($data["data_value"]);
    } else if ($data["data_type"] == DATA_NUMBER) {
        $result = doubleval($data["data_value"]);
    } else if ($data["data_type"] == DATA_STRING) {
        $result = $data["data_value"];
    } else {
        $result = array();
        if ($level != 0) {
            $children = select("select * from data where data_parent_id = " . $data["data_id"]
                . ($asc !== null ? " order by data_key " . ($asc ? "asc" : "desc") : "")
                . " limit $offset, $count");
            if ($data["data_type"] == DATA_ARRAY) {
                foreach ($children as $child)
                    $result[] = data_get_value($child, $level - 1);
            } else if ($data["data_type"] == DATA_MAP) {

                foreach ($children as $child)
                    $result[$child["data_key"]] = data_get_value($child, $level - 1);
            }

        }
    }
    return $result;
}

function is_assoc(array $arr)
{
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function dataSetValue($data_id, &$result)
{
    if ($data_id == null)
        return false;
    dataDeleteChildren($data_id);
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
            $success = dataSetValue($child_data_id, $value);
            if (!$success) break;
        }
        return $success ? $data_id : false;
    }
}

function dataDeleteChildren($data_id)
{
    // TODO doesnt work
    $children = selectListWhere("data", "data_id", array(
        "data_parent_id" => $data_id
    ));

    foreach ($children as $child_data_id) {
        dataDeleteChildren($child_data_id);
        query("delete from data where data_id = $child_data_id");
    }
}

function dataGet(array $path, $password, $asc = null, $offset = 0, $count = 10000, $level = -1)
{
    $data_id = dataCreate($path, $password, false);
    return data_get_value($data_id, $level, $asc, $offset, $count);
}

function dataInc(array $path, $password, $inc_val)
{
    $last_val = dataGet($path, $password);
    dataSet($path, $password, $last_val + $inc_val);
}

function dataDec(array $path, $password, $dec_val)
{
    $last_val = dataGet($path, $password);
    dataSet($path, $password, $last_val - $dec_val);
}

function dataSet(array $path, $password, $value)
{
    $data_id = dataCreate($path, $password);
    return dataSetValue($data_id, $value);
}

function dataAdd(array $path, $password, $value)
{
    $last = array_pop($path) . "[]";
    $path[] = $last;
    return dataSet($path, $password, $value);
}

function dataCount(array $path, $password)
{
    $data_id = dataCreate($path, $password, false);
    return scalarWhere("data", "count(*)", array("data_parent_id" => $data_id));
}