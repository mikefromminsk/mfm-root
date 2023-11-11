<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

define("DATA_UNSET", -1);
define("DATA_NULL", 0);
define("DATA_BOOL", 1);
define("DATA_NUMBER", 2);
define("DATA_STRING", 3);
define("DATA_FILE", 4);

define("FILE_ROW_SIZE", 64);
define("HASH_ROW_SIZE", 32);

function dataCreateRow($data_parent_id, $data_key, $data_type)
{
    $GLOBALS["gas_bytes"] += FILE_ROW_SIZE;
    return insertRowAndGetId("data", array(
        "data_parent_id" => $data_parent_id,
        "data_key" => $data_key,
        "data_type" => $data_type,
    ));
}

function dataNew($path, $create = false)
{
    if (is_array($path))
        $path = implode("/", $path);
    $path = explode("/", $path);
    $data_id = null;
    foreach ($path as $key) {
        if (!is_string($key))
            $key = "$key";
        $data_parent_id = $data_id;
        $data = selectRowWhere("data", array(
            "data_parent_id" => $data_parent_id,
            "data_key" => $key,
        ));
        $data_id = $data["data_id"];
        if ($data_id == null) {
            if ($create == true) {
                $data_id = dataCreateRow($data_parent_id, $key, DATA_UNSET);
            } else {
                return null;
            }
        }
    }
    return $data_id;
}

function dataSet(array $path_array, $value)
{
    $data_id = dataNew($path_array, true);
    if ($data_id == null) return false;
    dataDeleteChildren($data_id);
    $path = dataPath($data_id);
    $data = [data_value => $value];
    if (is_numeric($value) && !is_string($value)) {
        $data[data_type] = DATA_NUMBER;
    } else if (is_bool($value)) {
        $data[data_type] = DATA_BOOL;
    } else if (is_null($value)) {
        $data[data_type] = DATA_NULL;
    } else if (is_string($value)) {
        if (strlen($value) <= 64) {
            $data[data_type] = DATA_STRING;
        } else {
            $data[data_type] = DATA_FILE;
            $data[data_value] = md5($value);
            $GLOBALS[gas_bytes] += strlen($value) + HASH_ROW_SIZE;
            file_put_contents($_SERVER[DOCUMENT_ROOT] . $path, $value);
            insertRow(hashes, [hash => $data[data_value], path => $path]);
        }
    } else if (is_array($value)) {
        foreach ($value as $key => $subvalue)
            dataSet(array_merge($path_array, [$key]), $subvalue);
    }
    updateWhere(data, $data, [data_id => $data_id]);
    $data[data_path] = $path;
    insertRow(history, $data);
}


function dataGet(array $path)
{
    $data_id = dataNew($path);
    if ($data_id == null)
        return null;
    if (is_numeric($data_id))
        $data_id = selectRowWhere("data", array("data_id" => $data_id));
    if ($data_id["data_type"] == DATA_BOOL) {
        $result = boolval($data_id["data_value"]);
    } else if ($data_id["data_type"] == DATA_NUMBER) {
        $result = doubleval($data_id["data_value"]);
    } else if ($data_id["data_type"] == DATA_STRING) {
        $result = $data_id["data_value"];
    } else if ($data_id["data_type"] == DATA_FILE) {
        $path = scalarWhere(hashes, path, [hash => $data_id["data_value"]]);
        $result = file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path);
    }
    return $result;
}

function dataDelete(array $path)
{
    $data_id = dataNew($path, false);
    if ($data_id == null) return false;
    dataDeleteChildren($data_id);
    return query("delete from data where data_id = $data_id");
}

function dataDeleteChildren($data_id)
{
    $children = selectListWhere(data, data_id, [
        data_parent_id => $data_id
    ]);

    foreach ($children as $child_data_id) {
        dataDeleteChildren($child_data_id);
        query("delete from data where data_id = $child_data_id");
    }
}

function dataExist($path)
{
    $data_id = dataNew($path);
    if ($data_id == null) return false;
    return intval(scalarWhere("data", "count(*)", array("data_parent_id" => $data_id))) !== false;
}

function dataLike(array $path, $like, $asc = null, $offset = 0, $count = 10000)
{
    $data_id = dataNew($path, false);
    if ($data_id == null) return false;
    return selectList("select data_key from data where data_parent_id = $data_id and data_key like '$like' "
        . ($asc != null ? " order by data_key " . ($asc == true ? " ASC " : " DESC ") : "") . " limit $offset, $count");
}

function dataPath($data_id)
{
    $node = selectRowWhere(data, [data_id => $data_id]);
    if ($node[data_parent_id] == null)
        return "";
    return dataPath($node[data_parent_id]) . "/" . $node[data_key];
}

function dataKeys(array $path)
{
    $data_id = dataNew($path);
    return selectListWhere(data, data_key, [data_parent_id => $data_id]);
}

function dataCount(array $path)
{
    $data_id = dataNew($path);
    return select("select count(*) from `data` where data_parent_id = $data_id");
}

function dataHistory(array $path, $page = 1, $size = 20) {
    return selectList("select * from history where data_path = '$path'"
        ." offset " . (($page - 1) * $size) . " limit $size");
}

function scriptPath()
{
    $path = $_SERVER["SCRIPT_NAME"];
    if (strpos($path, ".php") != 0)
        $path = explode(".php", $path)[0];
    $path = str_replace("\\", "/", $path);
    if ($path[0] == "/")
        $path = substr($path, 1);
    return $path;
}

function getDomain()
{
    return explode("/", scriptPath())[0];
}

function dataInc(array $path, $inc_val)
{
    $value = dataGet($path);
    $value = ($value ?: 0) + $inc_val;
    dataSet($path, $value);
    return $value;
}

function dataDec(array $path, $dec_val)
{
    return dataInc($path, -$dec_val);
}

function dataSearch($path, $search_text)
{
    if ($path == "") {
        return selectList("select data_key from `data` where data_parent_id is null and data_key like '$search_text%'");
    } else {
        $data_id = dataNew($path);
        if ($data_id == null) error("path '$path' not exist");
        return selectList("select data_key from `data` where data_parent_id = $data_id and data_key like '$search_text%'");
    }
}