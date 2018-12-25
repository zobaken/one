<?php

namespace Core;

/**
 * Class Store
 * @package Core
 */
class Store {

    /**
     * @var array
     */
    protected $items = [];
    /**
     * @var Store|null
     */
    protected $defaults;

    /**
     * @param string $name
     * @return mixed|null
     */
    function __get(string $name) {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed
     */
    function __set(string $name, $value) {
        return $this->set($name, $value);
    }

    /**
     * @param string $id
     * @return mixed|null
     */
    function get(string $id) {
        if (empty($this->items[$id])) {
            if ($this->defaults) {
                return $this->defaults->get($id);
            }
            return null;
        }
        return $this->items[$id];
    }

    /**
     * @param string $id
     * @param mixed $value
     * @return mixed
     */
    function set(string $id, $value) {
        return $this->items[$id] = $value;
    }

    /**
     * @param Store $store
     */
    function setDefaults(Store $store) {
        $this->defaults = $store;
    }

    /**
     * @return array
     */
    function getItems() {
        return $this->items;
    }

}