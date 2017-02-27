<?php

/**
 * Get DB Query
 * @param string $config
 * @return \Dal\MysqlQuery
 */
function db($config = 'db') {
    static $dbPool = [];
    if(empty($dbPool[$config])) {
        $cfg = cfg()->$config;
        $dbPool[$config] = new \Core\DalMysqlQuery($cfg);
    } else {
        $dbPool[$config] = new \Core\DalMysqlQuery($dbPool[$config]);
    }
    return $dbPool[$config];
}
