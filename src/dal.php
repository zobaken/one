<?php

/**
 * Get database query.
 * Expects configuration file at ROOT/config/database.json.
 * @param string $profile
 * @return \Dal\MysqlQuery
 * @throws \Dal\Exception
 */
function db($profile = 'default') {
    static $dbPool = [];
    if(empty($dbPool[$profile])) {
        $configuration = \Core\Config::load('database');
        if (!$configuration->$profile) {
            throw new \Dal\Exception('Configuration not found: ' . $profile);
        }
        $dbPool[$profile] = new \Dal\MysqlQuery($configuration->$profile);
    } else {
        $dbPool[$profile] = new \Dal\MysqlQuery($dbPool[$profile]);
    }
    return $dbPool[$profile];
}
