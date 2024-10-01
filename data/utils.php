<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/db.php";

const DATA_OBJECT = -1;
const DATA_NULL = 0;
const DATA_BOOL = 1;
const DATA_NUMBER = 2;
const DATA_STRING = 3;

const MAX_VALUE_SIZE = 256;
const FILE_ROW_SIZE = 256 + 64;
const HASH_ROW_SIZE = 32;

const PAGE_SIZE_DEFAULT = 20;
const BLOCK_SIZE = 10000;

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
    $path = explode("/", getDomain() . "/$path");
    $path = array_filter($path);
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
                $data_id = dataCreateRow($data_parent_id, $key, DATA_OBJECT);
            } else {
                return null;
            }
        }
        $GLOBALS[data_cache][$data_parent_id][$key] = $data_id;
    }
    return $data_id;
}

function dataSet($path, $value)
{
    if (is_array($path))
        $path = implode("/", $path);
    $path_array = explode("/", $path);

/*    if ($path_array[0] != getDomain()) error("script cannot set in $path_array[0] domain");*/

    $data_id = dataNew($path_array, true);
    if ($data_id == null) return false;
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
    $data[data_path] = $path;
    $GLOBALS[new_history][] = $data;
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

function dataKeys(array $path, $count, $page = 1)
{
    $offset = ($page - 1) * $count;
    $data_id = dataNew($path);
    if ($data_id == null) return [];
    return selectList("select data_key from `data`"
        . " where data_parent_id = $data_id "
        . " and data_type <> " . DATA_NULL
        . " limit $offset, $count");
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

function dataInc(array $path, $inc_val = 1)
{
    $value = dataGet($path);
    $value = ($value ?: 0) + $inc_val;
    dataSet($path, $value);
    return $value;
}

function dataDec(array $path, $dec_val = 1)
{
    return dataInc($path, -$dec_val);
}

function dataSearch($path, $search_text, $page = 1, $size = PAGE_SIZE_DEFAULT)
{
    $offset = ($page - 1) * $size;
    $data_id = dataNew($path);
    if ($data_id == null) return [];
    return selectList("select data_key from `data` where data_parent_id = $data_id and data_key like '%$search_text%'"
        . " limit $offset, $size");
}

function commitData()
{
    foreach ($GLOBALS[new_data] as $data_id => $data) {
        insertRow(data, $data);
    }
    foreach ($GLOBALS[update_data] as $data_id => $data) {
        updateWhere(data, $data, [data_id => $data_id]);
    }

    foreach ($GLOBALS[new_history] as $data) {
        $id = insertRowAndGetId(history, $data);
        /*if ($id % BLOCK_SIZE == 0) {
            while ($id > 0) {
                $id -= BLOCK_SIZE;
                $block_bank = select("select * from history where `id` >= $id limit 0," . BLOCK_SIZE);
                $dirpath = $_SERVER["DOCUMENT_ROOT"] . "/../item_bank";
                $filepath = "$dirpath/$id.json";
                if (!file_exists($filepath)) {
                    mkdir($dirpath);
                    file_put_contents($filepath, json_encode($block_bank));
                } else {
                    break;
                }
            }
        }*/
    }
    broadcast(data, $GLOBALS[new_history]);
}

function dataObject(array $path, $limit, &$count = 0)
{
    if (!dataExist($path)) return null;
    $keys = dataKeys($path, $limit - $count);
    $result = [];
    foreach ($keys as $key) {
        if ($count >= $limit) error("limit exceeded");

        $fullPath = array_merge($path, [$key]);
        $data = dataInfo($fullPath);

        if ($data[data_type] == DATA_OBJECT) {
            $result[$key] = dataObject($fullPath, $limit, $count);
        } else {
            switch ($data[data_type]) {
                case DATA_BOOL:
                    $result[$key] = boolval($data[data_value]);
                    break;
                case DATA_NUMBER:
                    $result[$key] = doubleval($data[data_value]);
                    break;
                case DATA_STRING:
                    $result[$key] = $data[data_value];
                    break;
                case DATA_NULL:
                    $result[$key] = null;
                    break;
            }
        }
        $count++;
    }
    return $result;
}


function broadcast($channel, $data)
{
    if (WEB_SOCKETS_ENABLED) {
        http_post(":8002/test", [
            channel => $channel,
            data => $data,
        ]);
    }
}