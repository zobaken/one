<?php
/**
 * Http entry point
 */

require_once __DIR__ . '/../inc/init.php';

Core\Router::dispatchHttp();
