<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

define("DATA_UNSET", -1);
define("DATA_NULL", 0);
define("DATA_BOOL", 1);
define("DATA_NUMBER", 2);
define("DATA_STRING", 3);

define("MAX_VALUE_SIZE", 256);
define("FILE_ROW_SIZE", 256 + 64);
define("HASH_ROW_SIZE", 32);

define("PAGE_SIZE_DEFAULT", 20);
define("BLOCK_SIZE", 10000);

function dataCreateRow($data_parent_id, $data_key, $data_type)
{
    if ($GLOBALS[last_data_id] == null)
        $GLOBALS[last_data_id] = scalar("select max(data_id) from `data`") ?: 1;
    $data_id = ++$GLOBALS[last_data_id];
    $GLOBALS[new_data][$data_id] = [
        data_parent_id => $data_parent_id,
        data_key => $data_key,
        data_type => $data_type,
        data_time => time(),
    ];
    return $data_id;
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
        if ($GLOBALS[data_cache][$data_parent_id][$key] != null) {
            $data_id = $GLOBALS[data_cache][$data_parent_id][$key];
        } else {
            $data_id = scalarWhere(data, data_id, [
                data_parent_id => $data_parent_id,
                data_key => $key,
            ]);
        }
        if ($data_id == null) {
            if ($create == true) {
                $data_id = dataCreateRow($data_parent_id, $key, DATA_UNSET);
            } else {
                return null;
            }
        }
        $GLOBALS[data_cache][$data_parent_id][$key] = $data_id;
    }
    return $data_id;
}

function dataSet(array $path_array, $value, $addHistory = true)
{
    $data_id = dataNew($path_array, true);
    if ($data_id == null) return false;
    $path = implode("/", $path_array);
    $data = [
        data_value => $value,
        data_time => time(),
    ];
    if (is_numeric($value) && !is_string($value)) {
        $data[data_type] = DATA_NUMBER;
    } else if (is_bool($value)) {
        $data[data_type] = DATA_BOOL;
    } else if (is_null($value)) {
        $data[data_type] = DATA_NULL;
    } else if (is_string($value)) {
        if (strlen($value) <= MAX_VALUE_SIZE) {
            $data[data_type] = DATA_STRING;
        } else {
            error("value size is too big");
        }
    } else if (is_array($value)) {
        foreach ($value as $key => $subvalue)
            dataSet(array_merge($path_array, [$key]), $subvalue);
    }
    $GLOBALS[update_data][$data_id] = $data;
    if ($addHistory) {
        $data[data_path] = $path;
        $GLOBALS[new_history][] = $data;
    }
}

function dataNode($data_id)
{
    $data = $GLOBALS[update_data][$data_id];
    if ($data == null)
        $data = $GLOBALS[new_data][$data_id];
    if ($data == null)
        $data = $GLOBALS[get_data][$data_id];
    if ($data == null) {
        $data = selectRowWhere(data, [data_id => $data_id]);
        $GLOBALS[get_data][$data_id] = $data;
    }
    return $data;
}

function dataGet(array $path)
{
    $data_id = dataNew($path);
    if ($data_id == null)
        return null;
    if (is_numeric($data_id))
        $node = dataNode($data_id);
    if ($node[data_type] == DATA_BOOL) {
        $result = boolval($node[data_value]);
    } else if ($node[data_type] == DATA_NUMBER) {
        $result = doubleval($node[data_value]);
    } else if ($node[data_type] == DATA_STRING) {
        $result = $node[data_value];
    } else if ($node[data_type] == DATA_NULL) {
        $result = null;
    } else {
        $result = null;
    }
    return $result;
}

function dataExist($path)
{
    $data_id = dataNew($path);
    if ($data_id == null) return false;
    return true; //intval(scalarWhere(data, "count(*)", [data_parent_id => $data_id])) !== false;
}

function dataKeys(array $path, $page = 1, $size = PAGE_SIZE_DEFAULT)
{
    $offset = ($page - 1) * $size;
    $data_id = dataNew($path);
    if ($data_id == null) return [];
    return selectList("select data_key from `data` where data_parent_id = $data_id limit $offset, $size");
}

function dataCount(array $path)
{
    $data_id = dataNew($path);
    if ($data_id == null) return 0;
    return scalar("select count(*) from `data` where data_parent_id = $data_id");
}

function dataInfo(array $path)
{
    $data_id = dataNew($path);
    $info = selectRowWhere(data, [data_id => $data_id]);
    return $info;
}

function dataHistory(array $path_array, $page = 1, $size = PAGE_SIZE_DEFAULT)
{
    $offset = ($page - 1) * $size;
    $path = implode("/", $path_array);
    return selectList("select data_value from history where data_path = '$path' order by id desc limit $offset, $size");
}

function scriptPath()
{
    $path = $_SERVER["SCRIPT_NAME"];
    $path = str_replace("\\", "/", $path);
    if ($path[0] == "/")
        $path = substr($path, 1);
    return $path;
}

function getDomain()
{
    return explode("/", scriptPath())[0];
}

function dataInc(array $path, $inc_val, $addHistory = null)
{
    $value = dataGet($path);
    $value = ($value ?: 0) + $inc_val;
    dataSet($path, $value, $addHistory);
    return $value;
}

function dataDec(array $path, $dec_val, $addHistory = null)
{
    return dataInc($path, -$dec_val, $addHistory);
}

function dataSearch($path, $search_text, $page = 1, $size = PAGE_SIZE_DEFAULT)
{
    $offset = ($page - 1) * $size;
    $data_id = dataNew($path);
    if ($data_id == null) return [];
    return selectList("select data_key from `data` where data_parent_id = $data_id and data_key like '%$search_text%'"
        . " limit $offset, $size");
}

function dataCommit()
{
    foreach ($GLOBALS[new_data] as $data_id => $data) {
        insertRow(data, $data);
    }
    foreach ($GLOBALS[new_history] as $data) {
        $id = insertRowAndGetId(history, $data);
        if ($id % BLOCK_SIZE == 0) {
            while ($id > 0) {
                $id -= BLOCK_SIZE;
                $block = select("select * from history where `id` >= $id limit 0," . BLOCK_SIZE);
                $dirpath = $_SERVER["DOCUMENT_ROOT"] . "/../blocks";
                $filepath = "$dirpath/$id.json";
                if (!file_exists($filepath)) {
                    mkdir($dirpath);
                    file_put_contents($filepath, json_encode($block));
                } else {
                    break;
                }
            }
        }
    }
    foreach ($GLOBALS[update_data] as $data_id => $data) {
        updateWhere(data, $data, [data_id => $data_id]);
    }
}