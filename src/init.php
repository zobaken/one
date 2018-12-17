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

require_once(CORE . '/functions.php');
require_once(CORE . '/autoload.php');

//spl_autoload_register('core_autoload');

if (cfg('debug')) {
    error_reporting(E_ALL | E_STRICT);
} else {
    error_reporting(E_ALL - E_STRICT - E_WARNING);
}
