<?php

namespace Core;

/**
 * Basic controller
 */
class Controller {

    var $path;

    function __construct($path) {
        $this->path = $path;
    }

    function getMethodBase() {
        foreach (static::$routes as $regexp => $base) {
            if (!$regexp) continue;
            if (preg_match("#$regexp#", $this->path)) {
                return $base;
            }
        }
        return '';
    }

    function serve() {
        static::init();
        $methodBase = $this->getMethodBase();
        $method = null;

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $method = "get$methodBase";
        } elseif($_SERVER['REQUEST_METHOD'] == 'POST') {
            $method = "post$methodBase";
        } elseif($_SERVER['REQUEST_METHOD'] == 'PUT') {
            $method = "put$methodBase";
        } elseif($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $method = "delete$methodBase";
        }
        if (method_exists($this, $method)) {
            call_user_func([$this, $method]);
        } else {
            throw new MethodNotImplementedException();
        }
    }

    function param($name, $default = null) {
        if (isset($_REQUEST[$name])) return $_REQUEST[$name];
        return $default;
    }

    function get() {
        throw new MethodNotImplementedException();
    }

    function post() {
        throw new MethodNotImplementedException();
    }

    function put() {
        throw new MethodNotImplementedException();
    }

    function delete() {
        throw new MethodNotImplementedException();
    }

    static $routes = [];

    static function init() {}

}
