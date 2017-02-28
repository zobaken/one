<?php

/**
 * Basic functions
 */

/**
 * Get configuration option
 * Expects configuration file at ROOT/config/app.json.
 * @return stdClass
 */
function cfg($param = null) {
    static $config;
    if (!$config) $config = new \Core\Config();
    if ($param) {
        return $config->get($param);
    }
    return $config;
}

/**
 * Generate random base32 string
 * @param int $len Length (optional)
 * @return string
 */
function uid($len = 24) {
    $res = '';
    while(strlen($res) < $len) {
        $res .= base_convert(mt_rand(), 10, 32);
    }
    return substr($res, 0, $len);
}

/**
 * Generate random integer
 * @return int
 */
function uint() {
    return mt_rand() << 16 | time();
}

/**
 * Get template filename
 * @param string $name
 * @return string
 */
function tpl($name) {
    return ROOT . "/templates/$name.phtml";
}

/**
 * Get request value and trim
 * @param string $param
 * @param mixed $default
 * @return mixed
 */
function r($param, $default = null) {
    if (!empty($_REQUEST[$param]))
        return is_array($_REQUEST[$param]) ? $_REQUEST[$param] : trim($_REQUEST[$param]);
    return $default;
}

/**
 * Get request value and trim
 * @param string $param
 * @param mixed $default
 * @return mixed
 */
function post($param, $default = null) {
    static $rawPost;
    if (!empty($_POST[$param]))
        return is_array($_POST[$param]) ? $_POST[$param] : trim($_POST[$param]);
    if (!$rawPost) parse_str(file_get_contents('php://input'), $rawPost);
    if (!empty($rawPost[$param])) {
        return $rawPost[$param];
    }
    return $default;
}

/**
 * Escape single quotes (') in string
 * @param string $str
 * @return string
 */
function jse($str) {
    return str_replace("\n", '\'+"\n"+\'', str_replace("'", '\\\'', str_replace("\r", '', str_replace('\\', '\\\\', $str))));
}

/**
 * Shortcut for htmlspecialchars (too long)
 * @param string $str
 * @return string
 */
function hte($str) {
    return htmlspecialchars($str);
}

/**
 * Return time in database format
 * @param mixed $time Integer time or string date of nothing (for current time)
 * @return string
 */
function dbtime($time = false) {
    if (is_string($time)) {
        $time = strtotime($time);
    }
    return $time ? date('Y-m-d H:i:s', $time) : date('Y-m-d H:i:s');
}

/**
 * Return date in database format
 * @param mixed $time Integer time or string date of nothing (for current time)
 * @return string
 */
function dbdate($time = false) {
    if (is_string($time)) {
        $time = strtotime($time);
    }
    return $time ? date('Y-m-d', $time) : date('Y-m-d');
}

/**
 * @param string $key
 * @param bool $value
 * @param bool $timeout
 * @return mixed
 * @throws RuntimeException
 */
function mc($key, $value = false, $timeout = false) {
    static $memcache = null;

    if (!$memcache) {

        if(!cfg()->memcache_active){
            return false;
        }

        if (!class_exists('Memcache')) {
            throw new RuntimeException('No memcache - no fun');
        }

        $memcache = new Memcache();
        if (!$memcache->connect(cfg()->memcache_host, cfg()->memcache_port)) {
            return false;
        }
    }

    if ($value === false) {
        return $memcache->get($key);
    } elseif ($value === null) {
        $memcache->delete($key, 0);
    } else {
        if (!$memcache->set($key, $value, false, $timeout ? $timeout : cfg()->memcache_timeout)) {
            # throw new RuntimeException('Memcache set failed');
        }
    }
    return null;
}

/**
 * Get full url for relative url
 * @param string $url
 * @param array $params
 * @return mixed
 */
function url($url, $params = null) {
    $url = cfg()->root_url . $url;
    $url = str_replace('//', '/', $url);
    if ($params) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}

/**
 * Redirect to relative url
 * @param $url
 */
function redirect($url) {
    $url = get_path($url);
    header("location: $url");
    exit();
}

/**
 * Create path fore filename
 * @param string $dir
 * @param int $permissions
 */
function mkdir_r($dir, $permissions = 0755) {
    @mkdir($dir, $permissions, true);
}

/**
 * Delete directory recursively
 * @param $dir
 * @return bool
 */
function rmdir_r($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), ['.','..']);
    foreach ($files as $file) {
        (is_dir("$dir/$file")) ? rmdir_r("$dir/$file") : unlink("$dir/$file");
    }
    return rmdir($dir);
}

/**
 * Process uploaded file
 * @param string $field
 * @param string $dstFilename
 * @param bool $createPath
 * @throws \Core\ResponseException
 */
function upload_file($field, $dstFilename, $createPath = true) {
    if (!isset($_FILES[$field]) || @$_FILES[$field]['error'] != 0) {
        throw new \Core\ResponseException('Error uploading file (' . @$_FILES[$field]['error'] . ')');
    }

    if ($createPath) {
        create_path($dstFilename);
    }

    $name = $_FILES[$field]['name'];
    if (!@move_uploaded_file($_FILES[$field]['tmp_name'], $dstFilename)) {
        throw new \Core\ResponseException('Error moving uploaded file');
    }
    return $name;
}

/**
 * Remove untranslated symbols
 *
 * @param string $string
 * @return string
 */
function unaccent($string) {
    if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false) {
        $string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
    }
    return $string;
}

/**
 * Get text in A-Z a-z 0-9 characters range
 * @param string $str
 * @return string
 */
function azname($str) {
    $str = unaccent($str);
    $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $str = str_replace('&', 'and', $str);
    return preg_replace('#[^A-Za-z0-9\.\-]#', '_', $str);
}

/**
 * Change file name extension
 * @param string $filename
 * @param string $ext
 * @return string
 */
function ext_change($filename, $ext) {
    return preg_replace('/\.\w+$/', ".$ext", $filename, 1);
}

/**
 * pre print_r text and die
 * @param $text
 */
function pp($text) {
    echo '<pre>';
    print_r($text);
    exit();
}

/**
 * Get associative array of objects
 * @param array $array Array of objects
 * @param string $key Object property used for array key
 * @return array
 */
function associate($array, $key) {
    $result = [];
    foreach ($array as $obj) {
        if (!isset($obj->$key))
            continue;
        if ($obj->$key === null)
            continue;
        $result[$obj->$key] = $obj;
    }
    return $result;
}

/**
 * Get array of fields from array of objects
 * @param array $array
 * @param string $field
 * @return array
 */
function column($array, $field) {
    $res = array();
    foreach ($array as $r) {
        if (!empty($r->$field)) {
            $res []= $r->$field;
        }
    }
    return $res;
}

/**
 * Dump arguments and die
 * @param array ...$arguments
 */
function dd($arguments) {
    $arguments = func_get_args();
    if (PHP_SAPI != 'cli') {
        echo '<pre/>';
    }
    foreach ($arguments as $arg) {
        //echo json_encode($arg, JSON_PRETTY_PRINT);
        print_r($arg);
    }
    exit(1);
}