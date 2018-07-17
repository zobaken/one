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
            $controller = new Ctl\HttpError($path, 404);
        }
        $controller->serve();
    }

    static function dispatchHttp() {
        if (empty($_SERVER['PATH_INFO'])) {
            throw new \Core\ResponseException('Path not found');
        }
        $path = $_SERVER['PATH_INFO'];
        if (cfg()->root_url) {
            $path = substr($path, strlen(cfg()->root_url) - 1);
        }
        try {
            static::route($path);
        } catch (\Exception $e) {
            if (cfg()->debug) {
                $error = $e->getMessage();
                $trace = $e->getTraceAsString();
                require tpl('debug/error');
            } else {
                require tpl('service/error');
            }
        }
    }
}