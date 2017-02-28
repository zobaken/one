<?php

namespace Core;

/**
 * Application router
 */
class Router {

    static function route($path) {
        $className = null;
        while ($path[strlen($path) - 1] == '/') {
            if ($path == '/') {
                $className = '\Ctl\Main';
                break;
            }
            $path = substr($path, 0, strlen($path) - 1);
        }
        if (!$className) {
            $pathArray = explode('/', $path);
            array_walk($pathArray, function (&$a) {
                $a = ucfirst($a);
            });
            $className = '\\Ctl' . implode('\\', $pathArray);
        }
        if (!class_exists($className)) {
            header('Status: 404 Not Found');
            require tpl('service/404');
            return;
        }
        $controller = new $className($path);
        $controller->serve();
    }

    static function dispatchHttp() {
        $requestUrl = $_SERVER['REQUEST_URI'];
        $path_len = strlen($requestUrl) - strlen(cfg()->root_url) + 1;
        if (strlen($_SERVER['QUERY_STRING'])) $path_len -= strlen($_SERVER['QUERY_STRING']) + 1;
        $path = substr($requestUrl, strlen(cfg()->root_url) - 1, $path_len);
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