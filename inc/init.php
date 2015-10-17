<?php

/**
 * Bootstrap
 */

session_start();

define('ROOT', realpath(__DIR__ . '/../'));

define('MINUTE', 60);
define('HOUR', 60 * 60);
define('DAY', 60 * 60 * 24);

require_once(ROOT . '/inc/config.php');
$configLocal = ROOT . '/inc/config_local.php';
if (!file_exists($configLocal)) {
    throw new Exception($configLocal . ' not found');
}
require_once($configLocal);
require_once(ROOT . '/inc/functions.php');

spl_autoload_register('__autoload');

if (cfg()->debug) {
    error_reporting(E_ALL | E_STRICT);
} else {
    error_reporting(E_ALL - E_STRICT - E_WARNING);
}
