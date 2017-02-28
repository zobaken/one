<?php

namespace Core;

/**
 * Configuration wrapper
 */
class Config {

    static $pool = [];

    protected $configuration;

    function __construct($profile = 'app') {
        $fileName = ROOT . '/config/' . $profile . '.json';
        $fileName = str_replace('/', DIRECTORY_SEPARATOR, $fileName);
        if (file_exists($fileName)) {
            $configurationJson = file_get_contents($fileName);
            $this->configuration = json_decode($configurationJson);
        } else {
            $this->configuration = new \stdClass();
        }
    }

    function __get($name) {
        return $this->get($name);
    }

    function __set($name, $value) {
        return $this->set($name, $value);
    }

    function get($id) {
//        echo $id, "\n";
        if (isset($this->configuration->{$id})) {
//            print_r($this->configuration->{$id});
            return $this->configuration->{$id};
        }
        return null;
    }

    function set($id, $value) {
        return $this->configuration->{$id} = $value;
    }

    static function load($profile) {
        if (empty(static::$pool[$profile])) {
            static::$pool[$profile] = new self($profile);
        }
        return static::$pool[$profile];
    }

}