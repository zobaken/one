<?php

namespace Core;

/**
 * Configuration wrapper
 */
class Config {

    function __get($name) {
        return static::get($name);
    }

    function __set($name, $value) {
        return static::set($name, $value);
    }

    static function get($id) {
        global $cfg;
        if (isset($cfg->{$id})) {
            return $cfg->{$id};
        }
        return null;
    }

    static function set($id, $value) {
        global $cfg;
        return $cfg->{$id} = $value;
    }

}