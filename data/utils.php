<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";


define("DATA_UNSET", -1);
define("DATA_MAP", 0);
define("DATA_ARRAY", 1);
define("DATA_STRING", 2);
define("DATA_NUMBER", 3);
define("DATA_BOOL", 4);
define("DATA_NULL", 5);
define("DATA_FILE", 6);

define("FILE_ROW_SIZE", 64);
define("HASH_ROW_SIZE", 32);

function dataCreateFile($data_parent_id, $data_key, $data_type)
{
    $GLOBALS["gas_bytes"] += FILE_ROW_SIZE;
    return insertRowAndGetId("data", array(
        "data_parent_id" => $data_parent_id,
        "data_key" => $data_key,
        "data_type" => $data_type,
    ));
}

function dataNew(array $path, $create = true)
{
    array_unshift($path, explode('/', dirname($_SERVER['PHP_SELF']))[1]);

    $data_id = null;

    foreach ($path as $index => $key) {
        if (!is_string($key))
            $key = "$key";
        $push = strpos($key, "[]");
        $key = substr($key, 0, $push !== false ? $push : strlen($key));
        $data_parent_id = $data_id;

        $data = selectRowWhere("data", array(
            "data_parent_id" => $data_parent_id,
            "data_key" => $key,
        ));

        $data_id = $data["data_id"];

        if ($data_id == null) {
            if ($create == true) {
                $data_id = dataCreateFile($data_parent_id, $key, DATA_MAP);
            } else {
                return null;
            }
        }

        if ($push !== false) {
            updateWhere("data", array(
                "data_type" => DATA_ARRAY,
            ), array(
                "data_id" => $data_id,
            ));
            $children_count = scalarWhere("data", "count(*)", array(
                "data_parent_id" => $data_id,
            ));
            $data_id = dataCreateFile($data_id, $children_count, DATA_MAP);
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
    } else if ($data["data_type"] == DATA_FILE) {
        $path = scalarWhere(hashes, path, [hash => $data["data_value"]]);
        $result = file_get_contents($_SERVER["DOCUMENT_ROOT"] . $path);
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
        if (strlen($result) > 64) {
            $hash = md5($result);
            $path = dataPath($data_id);
            $GLOBALS["gas_bytes"] += strlen($result) + HASH_ROW_SIZE;
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . $path, $result);
            insertRow("hashes", ["hash" => $hash, "path" => $path]);
            return updateWhere("data", array("data_type" => DATA_FILE, "data_value" => $hash), array("data_id" => $data_id));
        } else {
            return updateWhere("data", array("data_type" => DATA_STRING, "data_value" => $result), array("data_id" => $data_id));
        }
    } else if (is_array($result)) {
        if (is_assoc($result)) {
            updateWhere("data", array("data_type" => DATA_MAP, "data_value" => null), array("data_id" => $data_id));
        } else {
            updateWhere("data", array("data_type" => DATA_ARRAY, "data_value" => null), array("data_id" => $data_id));
        }
        $success = true;
        foreach ($result as $key => $value) {
            $child_data_id = dataCreateFile($data_id, $key, DATA_UNSET);

            $success = dataSetValue($child_data_id, $value);
            if (!$success) break;
        }
        return $success ? $data_id : false;
    }
    return false;
}

/*TODO offset -10 count 10*/
function dataGet(array $path, $asc = null, $offset = 0, $count = 10000, $level = -1)
{
    if ($offset < 0) {
        $all_count = dataCount($path);
        if ($count == 10000)
            $count = abs($offset);
        if (abs($offset) > $all_count)
            $offset = 0;
        else
            $offset = $all_count + $offset;
    }
    $data_id = dataNew($path, false);
    if ($data_id == null)
        return false;
    return data_get_value($data_id, $level, $asc, $offset, $count);
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

function dataSet(array $path, $value)
{
    $data_id = dataNew($path);
    if ($data_id == null) return false;
    return dataSetValue($data_id, $value);
}

function dataAdd(array $path, $value)
{
    $last = array_pop($path) . "[]";
    $path[] = $last;
    return dataSet($path, $value);
}

function dataCount(array $path)
{
    $data_id = dataNew($path, false);
    if ($data_id == null) return false;
    return intval(scalarWhere("data", "count(*)", array("data_parent_id" => $data_id)));
}

function dataExist(array $path)
{
    return dataCount($path) !== false;
}

function dataDel(array $path)
{
    $data_id = dataNew($path, false);
    if ($data_id == null) return false;
    dataDeleteChildren($data_id);
    return query("delete from data where data_id = $data_id");
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

function dataLike(array $path, $like, $asc = null, $offset = 0, $count = 10000)
{
    $data_id = dataNew($path, false);
    if ($data_id == null) return false;
    return selectList("select data_key from data where data_parent_id = $data_id and data_key like '$like' "
        . ($asc != null ? " order by data_key " . ($asc == true ? " ASC " : " DESC ") : "") . " limit $offset, $count");
}

function dataMapSet(array $path, $key, $value)
{
    dataSet(array_merge($path, ["keys", $key]), $value);
    dataSet(array_merge($path, ["vals", $value]), $key);
}

function dataMapDel(array $path, $key)
{
    $value = dataGet(array_merge($path, ["keys", $key]));
    dataDel(array_merge($path, ["keys", $key]));
    dataDel(array_merge($path, ["vals", $value]));
}

function dataPath($data_id)
{
    $node = selectRowWhere(data, [data_id => $data_id]);
    if ($node[data_parent_id] == null)
        return "";
    return dataPath($node[data_parent_id]) . "/" . $node[data_key];
}

function dataMeta(array $path)
{
    $data_id = dataNew($path, false);
    return selectRowWhere(data, [data_id => $data_id]);
}

function dataChildren(array $path)
{
    $data_id = dataNew($path, false);
    return selectListWhere(data, data_key, [data_parent_id => $data_id]);
}

function dataAppName()
{
    $path = $_SERVER["SCRIPT_NAME"];
    if (strpos($path, ".php") != 0)
        $path = explode(".php", $path)[0];
    $path = str_replace("\\", "/", $path);
    if ($path[0] == "/")
        $path = substr($path, 1);
    return $path;
}

function dataWalletInit(array $path, $address, $next_hash, $amount)
{
    dataWalletReg($path, $address, $next_hash);
    dataSet(array_merge($path, [$address, amount]), $amount);
}

function dataWalletReg(array $path, $address, $next_hash)
{
    if (dataExist(array_merge($path, [$address]))) error("address exist");
    return dataSet(array_merge($path, [$address, next_hash]), $next_hash);
}

function dataWalletDelegate(array $path, $address, $password, $owner)
{
    if (!dataExist(array_merge($path, [$address]))) error("address exist");
    if (dataGet(array_merge($path, [$address, next_hash])) != md5($password)) error("password is not right");
    return dataSet(array_merge($path, [$address, owner]), $owner);
}

function dataWalletBalance(array $path, $address)
{
    return dataGet(array_merge($path, [$address])) ?: 0.0;
}

function dataWalletSend(array $path, $fromAddress, $toAddress, $amount, $password = null, $next_hash = null)
{
    if ($amount == 0)
        return true;
    if (dataWalletBalance($path, $fromAddress) < $amount) error("balance is not enough");
    if ($password == null || $next_hash == null) {
        if (dataGet(array_merge($path, [$fromAddress, owner])) != dataAppName()) error("you are not owner of wallet");
    } else {
        if (dataGet(array_merge($path, [$fromAddress, next_hash])) != md5($password)) error("password is not right");
    }

    dataSet(array_merge($path, [$fromAddress, password]), $password);
    dataSet(array_merge($path, [$fromAddress, next_hash]), $next_hash);

    dataDec(array_merge($path, [$fromAddress, amount]), $amount);
    dataInc(array_merge($path, [$toAddress, amount]), $amount);

    /*return dataAdd([transactions], [
        from => $fromAddress,
        to => $toAddress,
        amount => $amount,
    ]);*/
    return true;
}


function commit($response, $gas_address = null)
{
    if ($gas_address != null) {
        if (!dataWalletSend([data, wallet], $gas_address, admin, $GLOBALS["gas_bytes"])) error("not");
    } else {
        if (!dataWalletSend(
            [data, wallet],
            get_required(gas_address),
            admin,
            $GLOBALS["gas_bytes"],
            get_required(gas_password),
            get_required(gas_address)
        )) error("not");
    }
    echo json_encode($response);
}