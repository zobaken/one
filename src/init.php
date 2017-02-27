<?php

/**
 * Bootstrap
 */

if (!defined('ROOT')) {
    throw new Exception('ROOT constant should be defined');
}

if (PHP_SAPI !== 'cli') {
    session_start();
}

define('CORE', realpath(__DIR__));

require_once(CORE . '/inc/config.php');
$configLocal = ROOT . '/inc/config_local.php';
if (!file_exists($configLocal)) {
    throw new Exception($configLocal . ' not found');
}
require_once($configLocal);
require_once(CORE . '/inc/functions.php');
require_once(CORE . '/inc/dal.php');

spl_autoload_register('__autoload');

if (cfg()->debug) {
    error_reporting(E_ALL | E_STRICT);
} else {
    error_reporting(E_ALL - E_STRICT - E_WARNING);
}
