<?php

namespace Core;

/**
 * Application router
 */
class Router {

    static function route($path) {
        $routes = ConfigStore::load('routes');
        if ($path != '/') {
            $path = trim($path, '/');
        }
        $className = null;
        $pathModified = $path;
        $pathSegments = [];
        $className = $routes->get($pathModified);
        while (!$className && $pathModified) {
            $pathArray = explode('/', trim($pathModified, '/'));
            $pathSegments []= array_pop($pathArray);
            $pathModified = implode('/', $pathArray);
            $className = $routes->get($pathModified);
        }
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
                $className = '\\Ctl\\' . implode('\\', $pathArray);
                $pathModified = $path;
                $pathSegments = [];
                while (!class_exists($className) && $pathModified) {
                    $pathArray = explode('/', trim($pathModified, '/'));
                    $pathSegments []= array_pop($pathArray);
                    $pathModified = implode('/', $pathArray);
                    array_walk($pathArray, function (&$a) {
                        $a = ucfirst($a);
                    });
                    $className = '\\Ctl\\' . implode('\\', $pathArray);
                }
            }
        }
        if (class_exists($className)) {
            $controller = new $className($pathModified, implode('/', array_reverse($pathSegments)));
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