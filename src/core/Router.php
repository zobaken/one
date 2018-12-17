<?php

namespace Core;

/**
 * Application router
 */
class Router {

    static function route($path) {
        $routes = ConfigStore::load('routes');
        while (strlen($path) > 1 && $path[strlen($path) - 1] == '/') {
            $path = substr($path, 0, strlen($path) - 1);
        }
        $className = $routes->get($path);
        if ($className) {
            $className = str_replace('/', '\\', $className);
            if (!class_exists($className)) {
                $className = '\\Ctl\\' . $className;
            }
        } else  {
            if (cfg('auto_routes')) {
                $pathArray = explode('/', $path);
                array_walk($pathArray, function (&$a) {
                    $a = ucfirst($a);
                });
                $className = '\\Ctl' . implode('\\', $pathArray);
            }
        }
        if (class_exists($className)) {
            $controller = new $className($path);
        } else {
            throw new PageNotFoundException();
        }
        $controller->serve();
    }

    static function dispatchHttp() {
        if (empty($_SERVER['PATH_INFO'])) {
            $path = '/';
        } else {
            $path = $_SERVER['PATH_INFO'];
        }
        if (cfg()->root_url) {
            $path = substr($path, strlen(cfg()->root_url) - 1);
        }
        static::route($path);
    }
}