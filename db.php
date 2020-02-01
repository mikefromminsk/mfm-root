<?php
error_reporting(1);

header("Content-type: " . strpos($_SERVER["HTTP_ACCEPT"], "text/html") != -1 ? "text/html" : "application/json" . "; charset=utf-8");

include_once "properties.php";

if (!isset($db_host) || !isset($db_user) || !isset($db_pass) || !isset($db_name))
    die("create properties.php file with next body:\n"
        . "<?php\n"
        . "\$db_host=\"xxxx\";\n"
        . "\$db_user=\"xxxx\";\n"
        . "\$db_pass=\"xxxx\";\n"
        . "\$db_name=\"xxxx\";\n");

$mysql_conn = isset($GLOBALS["conn"]) ? $GLOBALS["conn"] : null;
if ($mysql_conn == null)
    $mysql_conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($mysql_conn->connect_error)
    die("Connection failed: " . $mysql_conn->connect_error . " check properties.php file");

$mysql_conn->set_charset("utf8");
$GLOBALS["conn"] = $mysql_conn;

if ($_SERVER['REQUEST_METHOD'] == 'POST' || $_SERVER['REQUEST_METHOD'] == 'PUT') {
    $inputJSON = file_get_contents('php://input');
    $inputParams = json_decode($inputJSON, true);
    foreach ($inputParams as $key => $value)
        $_POST[$key] = $value;
}

function query($sql, $show_query = false)
{
    if ($show_query)
        echo $sql;
    $success = $GLOBALS["conn"]->query($sql);
    if (!$success)
        db_error(ERROR_MYSQL, mysqli_error($GLOBALS["conn"]));
    return $success;
}

function select($sql, $show_query = false)
{
    $result = query($sql, $show_query);
    if ($result->num_rows > 0) {
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }
    return null;
}

function selectList($sql, $show_query = false)
{
    $result = query($sql, $show_query);
    if ($result->num_rows > 0) {
        $rows = array();
        while ($row = $result->fetch_assoc()) {
            $rows[] = array_shift($row);
        }
        return $rows;
    }
    return null;
}

function selectMap($sql, $show_query = false)
{
    $result = select($sql, $show_query);
    if ($result != null)
        return $result[0];
    return null;
}

function scalar($sql, $show_query = false)
{
    $rows = select($sql, $show_query);
    if (count($rows) > 0)
        return array_shift($rows[0]);
    else
        return null;
}

function table_exist($table_name)
{
    return scalar("show tables like '$table_name'") != null;
}

define("ERROR_PARAMS_IS_NO_SET", 0);
define("ERROR_DUPLICATE_REQUEST", 1);
define("USER_ERROR", 2);
define("ERROR_MYSQL", 3);
define("ERROR_TOKEN_IS_BAD", 4);
function db_error($error_code, $error_message = "")
{
    $result["error"] = $error_code;
    $result["message"] = $error_message;
    $result["stack"] = generateCallTrace();
    echo json_encode_readable($result);
    exit;
}

function uencode($param_value)
{
    return mysqli_real_escape_string($GLOBALS["conn"], $param_value);
}

function get($param_name, $def_value = null)
{
    // TODO add test on sql
    $param_value = null;
    if (isset($_GET[$param_name]))
        $param_value = $_GET[$param_name];
    if ($param_value === null && isset($_POST[$param_name]))
        $param_value = $_POST[$param_name];
    if ($param_value === null && isset($_SESSION[$param_name]))
        $param_value = $_SESSION[$param_name];
    if ($param_value === null && isset($_COOKIE[$param_name]))
        $param_value = $_COOKIE[$param_name];
    if (strpos($param_value, '\'') == true)
        return null;
    if ($param_value === null)
        return $def_value;

    return $param_value;
}

function get_string($param_name, $def_value = null)
{
    return (get($param_name, $def_value) !== null ? "" . (get($param_name, $def_value)) : null);
}

function get_int($param_name, $def_value = null)
{
    return (get($param_name, $def_value) !== null ? doubleval(get($param_name, $def_value)) : null);
}

function get_int_array($param_name)
{
    $arr = get($param_name);
    return $arr != null ? explode(",", $arr) : null;
}

function get_double($param_name, $def_value = null)
{
    return (get($param_name, $def_value) !== null ? doubleval(get($param_name, $def_value)) : null);
}

function get_required($param_name)
{
    $param_value = get($param_name);
    if ($param_value === null)
        db_error(ERROR_PARAMS_IS_NO_SET, $param_name . " is empty");
    return $param_value;
}

function get_int_required($param_name)
{
    return doubleval(get_required($param_name));
}

function get_double_required($param_name)
{
    return doubleval(get_required($param_name));
}

function get_string_required($param_name)
{
    return get_required($param_name);
}

function insert($sql, $show_query = null)
{
    $success_or_error = query($sql, $show_query);
    /*$insert_autoincrement_id = mysqli_insert_id($GLOBALS["conn"]);
    if ($insert_autoincrement_id === 0)
        return $success_or_error;
    return $insert_autoincrement_id;*/
    return $success_or_error;
}

function insertList($table_name, $params, $show_query = false)
{
    $params = array_filter($params, function ($value) {
        return $value !== null;
    });
    foreach ($params as $param_name => $param_value)
        if (is_string($param_value) && $param_value != "unix_timestamp()")
            $params[$param_name] = "'" . uencode($param_value) . "'";
    $insert_query = "insert into $table_name (" . implode(",", array_keys($params)) . ") values (" . implode(",", array_values($params)) . ")";
    return insert($insert_query, $show_query);
}

function update($sql, $show_query = null)
{
    return query($sql, $show_query);
}

function updateList($table_name, $params, $primary_key, $primary_value, $show_query = false)
{
    $params = array_filter($params, function ($value) {
        return $value !== null;
    });
    $update_params = "";
    foreach ($params as $param_name => $param_value)
        if (is_string($param_value) && $param_value != "unix_timestamp()" && $param_value != "$param_name + 1")
            $update_params .= "$param_name = '$param_value', ";
        else
            $update_params .= "$param_name = $param_value, ";
    $update_params = rtrim($update_params, ", ");
    $update_query = "update $table_name set $update_params ";
    if (is_array($primary_value))
        $update_query .= " where $primary_key  in ('" . implode("','", uencode($primary_value)) . "')";
    else
        $update_query .= " where $primary_key = " . (is_string($primary_value) ? "'" . uencode($primary_value) . "'" : $primary_value);
    if ($update_params != "") {
        return update($update_query, $show_query);
    } else
        return null;
}

function object_properties_to_number(&$object)
{
    if (is_object($object) || is_array($object))
        foreach ($object as &$property)
            object_properties_to_number($property);
    if (is_string($object) && is_numeric($object))
        $object = doubleval($object);
}

function json_encode_readable(&$result)
{
    //object_properties_to_number($result);
    $json = json_encode($result, JSON_UNESCAPED_UNICODE);
    //$json = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/', '$1:', $json);
    $tc = 0;        //tab count
    $r = '';        //result
    $q = false;     //quotes
    $t = "\t";      //tab
    $nl = "\n";     //new line
    for ($i = 0; $i < strlen($json); $i++) {
        $c = $json[$i];
        if ($c == '"' && $json[$i - 1] != '\\') $q = !$q;
        if ($q) {
            $r .= $c;
            continue;
        }
        switch ($c) {
            case '{':
            case '[':
                $r .= $c . $nl . str_repeat($t, ++$tc);
                break;
            case '}':
            case ']':
                $r .= $nl . str_repeat($t, --$tc) . $c;
                break;
            case ',':
                $r .= $c;
                if ($json[$i + 1] != '{' && $json[$i + 1] != '[') $r .= $nl . str_repeat($t, $tc);
                break;
            case ':':
                $r .= $c . ' ';
                break;
            default:
                $r .= $c;
        }
    }
    return $r;
}

function getExceptionTraceAsString($exception)
{
    $rtn = "";
    $count = 0;
    foreach ($exception->getTrace() as $frame) {
        $args = "";
        if (isset($frame['args'])) {
            $args = array();
            foreach ($frame['args'] as $arg) {
                if (is_string($arg)) {
                    $args[] = "'" . $arg . "'";
                } elseif (is_array($arg)) {
                    $args[] = "Array";
                } elseif (is_null($arg)) {
                    $args[] = 'NULL';
                } elseif (is_bool($arg)) {
                    $args[] = ($arg) ? "true" : "false";
                } elseif (is_object($arg)) {
                    $args[] = get_class($arg);
                } elseif (is_resource($arg)) {
                    $args[] = get_resource_type($arg);
                } else {
                    $args[] = $arg;
                }
            }
            $args = join(", ", $args);
        }
        $rtn .= sprintf("#%s %s(%s): %s(%s)\n",
            $count,
            $frame['file'],
            $frame['line'],
            $frame['function'],
            $args);
        $count++;
    }
    return $rtn;
}

function generateCallTrace()
{
    $e = new Exception();
    $trace = explode("\n", getExceptionTraceAsString($e));
    array_shift($trace); //generateCallTrace
    array_shift($trace); //db_error
    array_pop($trace); // empty line
    $result = array();
    for ($i = 0; $i < count($trace); $i++)
        $result[] = $trace[$i];
    return $result;
}

function random_id()
{
    //max mysqk bigint = 20 chars
    //max js int = 16 chars
    //max php double without E = 12 chars
    $random_long = mt_rand(1, 9);
    for ($i = 0; $i < 11; $i++)
        $random_long .= mt_rand(0, 9);
    return doubleval($random_long);
}

function random_key($table_name, $key_name)
{
    do {
        $random_key_id = random_id();
        $key_exist = scalar("select count(*) from $table_name where $key_name = $random_key_id");
    } while ($key_exist != 0);
    return $random_key_id;
}

function http_json_put($url, $fields)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function utf8ize($mixed)
{
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value)
            $mixed[$key] = utf8ize($value);
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'ISO-8859-1');
    }
    return $mixed;
}

function http_json_post($url, $data, $uencode = false)
{
    if ($uencode)
        $data = utf8ize($data);
    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
    );
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function http_json_get($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function redirect($url, $params = array(), $params_in_url = true)
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($params_in_url == true) {
            $url_params = "";
            foreach ($params as $key => $value)
                $url_params .= "&" . urlencode($key) . "=" . urlencode($value);
            if (strpos($url, "?") === false && $url_params != "")
                $url_params[0] = "?";
            $url .= $url_params;
        }
        $redirect_script = '<html><body><form id="redirect" action="' . $url . '" method="post">';
        if ($params_in_url == false)
            foreach ($params as $key => $value)
                $redirect_script .= '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities(json_encode($value)) . '">';
        $redirect_script .= '</form><script>document.getElementById("redirect").submit();</script></body></html>';
        die($redirect_script);
    }
}

function array_extend(array $a, array $b)
{
    foreach ($b as $k => $v)
        $a[$k] = is_array($v) && isset($a[$k]) ?
            array_extend(is_array($a[$k]) ?
                $a[$k] : array(), $v) :
            $v;
    return $a;
}

function getFiles($dir, &$results = array())
{
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (is_file($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getFiles($path, $results);
        }
    }
    return $results;
}