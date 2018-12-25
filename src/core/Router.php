<?php

namespace Core;

/**
 * Class Router
 * @package Core
 */
class Router
{

    /**
     * @var string
     */
    protected $path;

    /**
     * @var array
     */
    protected $routes;

    /**
     * @return array
     */
    function findPartialRoute()
    {
        $match = null;
        $pathTest = $this->path;
        while ($pathTest) {
            $pathTestArray = explode('/', trim($pathTest, '/'));
            $pathTestSegments [] = array_pop($pathTestArray);
            $pathTest = implode('/', $pathTestArray);
            if (isset($this->routes[$pathTest])) {
                $route = $this->routes[$pathTest];
                $pathRight = substr($this->path, strlen($pathTest) + 1);
                return [$route, $pathRight];
            }
        }
    }

    /**
     * @return array
     */
    function findRoute()
    {
        $match = null;
        $pathRight = null;
        $route = null;
        $params = [];
        if (isset($this->routes[$this->path])) {
            $route = $this->routes[$this->path];
        } else {
            list($route, $params) = static::findPlaceholderRoute($this->path, $this->routes);
            if (!$route) {
                list($route, $pathRight) = $this->findPartialRoute();
            }
        }
        return [$route, $params, $pathRight];
    }

    /**
     * @return array
     */
    function findAutoRoute()
    {
        $pathRight = null;
        $pathArray = explode('/', $this->path);
        array_walk($pathArray, function (&$a) {
            $a = ucfirst($a);
        });
        $className = '\\Ctl\\' . implode('\\', $pathArray);
        $pathModified = $this->path;
        $pathSegments = [];
        while (!class_exists($className) && $pathModified) {
            $pathArray = explode('/', trim($pathModified, '/'));
            $pathSegments [] = array_pop($pathArray);
            $pathModified = implode('/', $pathArray);
            array_walk($pathArray, function (&$a) {
                $a = ucfirst($a);
            });
            $className = '\\Ctl\\' . implode('\\', $pathArray);
        }
        if (!class_exists($className)) {
            $className = null;
        } else {
            $pathRight = substr($this->path, strlen($pathModified) + 1);
        }
        return [$className, $pathRight];
    }

    /**
     * Application entry point
     * @throws \Exception
     */
    function dispatchHttp()
    {
        if (empty($_SERVER['PATH_INFO'])) {
            $this->path = '/';
        } else {
            $this->path = $_SERVER['PATH_INFO'];
            if (cfg()->root_url) {
                $this->path = substr($this->path, strlen(cfg()->root_url) - 1);
            }
            $this->path = trim($this->path, '/');
        }
        $this->routes = ConfigStore::load('routes')->getItems();
        list($route, $params, $pathRight) = $this->findRoute();
        if (!$route && cfg('auto_routes')) {
            list($className, $pathRight) = $this->findAutoRoute();
            if ($className) {
                list($methodBase, $params) = static::findPlaceholderRoute($pathRight, $className::$routes);
                if ($pathRight && !$methodBase) {
                    throw new PageNotFoundException();
                }
            }
        } else {
            list($className, $methodBase) = static::getRouteHandler($route);
        }
        if (class_exists($className)) {
            /** @var Controller $controller */
            $controller = new $className();
        } else {
            throw new PageNotFoundException();
        }
        $controller->serve($methodBase, $params);

    }

    /**
     * Find route by matching xxx/$ url syntax
     * @param string $path
     * @param array $routes
     * @return array
     */
    static function findPlaceholderRoute(string $path, array $routes): array
    {
        foreach ($routes as $match => $testRoute) {
            $regexp = '#^' . preg_replace('/\$/', '([^/]+)', $match) . '$#';
            if (preg_match($regexp, $path, $m)) {
                $params = array_slice($m, 1);
                return [$testRoute, $params];
            }
        }
        return [null, []];
    }

    /**
     * Get class and method from string like Class::method
     * @param $route
     * @return array
     */
    static function getRouteHandler($route)
    {
        $className = $route;
        $methodBase = null;
        if (preg_match('/(.+)::(.+)/', $route, $m)) {
            $className = $m[1];
            $methodBase = $m[2];
        }
        $className = str_replace('/', '\\', $className);
        if (!class_exists($className)) {
            $className = '\\Ctl\\' . $className;
        }
        return [$className, $methodBase];
    }

}