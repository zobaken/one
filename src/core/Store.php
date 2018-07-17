<?php

namespace Core;

/**
 * Simple store
 */
class Store {

    protected $items = [];
    protected $defaults;


    function __get($name) {
        return $this->get($name);
    }

    function __set($name, $value) {
        return $this->set($name, $value);
    }

    function get($id) {
        if (empty($this->items[$id])) {
            if ($this->defaults) {
                return $this->defaults->get($id);
            }
            return null;
        }
        return $this->items[$id];
    }

    function set($id, $value) {
        return $this->items[$id] = $value;
    }

    function setDefaults(Store $store) {
        $this->defaults = $store;
    }

}