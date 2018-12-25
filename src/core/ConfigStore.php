<?php

namespace Core;

/**
 * Configuration wrapper
 * @package Core
 */
class ConfigStore extends Store {

    /**
     * @var array
     */
    static $pool = [];

    /**
     * ConfigStore constructor.
     * @param string @domain
     */
    function __construct(string $domain) {
        $fileName = ROOT . '/config/' . $domain . '.json';
        $fileName = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
        if (file_exists($fileName)) {
            $configurationJson = file_get_contents($fileName);
            $this->items = json_decode($configurationJson, JSON_OBJECT_AS_ARRAY);
        }
    }

    /**
     * @param string $key
     * @return Store|mixed|null
     */
    function get(string $key) {
        $result = parent::get($key);
        if (is_array($result)) {
            $store = new Store();
            $store->items = $result;
            return $store;
        }
        return $result;
    }

    /**
     * @param string $domain
     * @return mixed
     */
    static function load(string $domain) {
        if (empty(static::$pool[$domain])) {
            static::$pool[$domain] = new self($domain);
        }
        return static::$pool[$domain];
    }

}