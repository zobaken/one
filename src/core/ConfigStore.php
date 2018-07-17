<?php

namespace Core;

/**
 * Configuration wrapper
 */
class ConfigStore extends Store {

    static $pool = [];

    function __construct($domain) {
        $fileName = ROOT . '/config/' . $domain . '.json';
        $fileName = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
        if (file_exists($fileName)) {
            $configurationJson = file_get_contents($fileName);
            $this->items = json_decode($configurationJson, JSON_OBJECT_AS_ARRAY);
        }
    }

    static function load($domain) {
        if (empty(static::$pool[$domain])) {
            static::$pool[$domain] = new self($domain);
        }
        return static::$pool[$domain];
    }

}