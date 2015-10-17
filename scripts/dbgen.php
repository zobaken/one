<?php
/**
 * Generate database classes
 * First argument is database config
 */


require_once __DIR__ . '/../inc/init.php';

$dbConfig = empty($argv[1]) ? 'db' : $argv[1];
$dbgen = new \Core\DbGenerator($dbConfig);
$dbgen->run();