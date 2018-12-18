<?php

namespace Core;
use Dal\Exception;

/**
 * Basic controller
 */
class Controller {

    protected static $routes = [];
    protected $path;
    protected $segments;
    protected $view;

    function __construct($path = null, $segments = []) {
        $this->path = $path;
        $this->segments = $segments;
    }

    function getMethodBase() {
        if (!$this->segments) {
            return null;
        }
        foreach (static::$routes as $regexp => $base) {
            if (!$regexp) continue;
            if (preg_match("#$regexp#", $this->segments, $m)) {
                $params = array_slice($m, 1);
                $params = array_map(function($value) {
                    return trim($value, '/');
                }, $params);
                return [ ucfirst($base), $params ];
            }
        }
        return null;
    }

    function serve() {
        $methodBase = '';
        $parameters = [];
        if ($this->segments) {
            list($methodBase, $parameters) = $this->getMethodBase();
            if (!$methodBase) {
                throw new PageNotFoundException();
            }
        }
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
            $this->init();
            $result = call_user_func_array([$this, $method], $parameters);
            if (is_null($result)) {
                $result = $this->getProperties();
            }
            $this->render($result);
        } else {
            throw new PageNotFoundException();
        }
    }

    function render(array $parameters = []) {
        if ($this->view) {
            $template = Template::getAdapter();
            echo $template->run($this->view, $parameters);
        } else {
            header('Content-Type: application/json');
            echo json_encode($parameters);
        }
    }

    function param($name, $default = null) {
        if (isset($_REQUEST[$name])) return $_REQUEST[$name];
        return $default;
    }

    function getProperties() {
        $reflection = new \ReflectionClass($this);
        $properties = (array) $this;
        $result = [];
        foreach ($properties as $key => $value) {
            if (preg_match('/^[\w\d\_]+$/', $key)) {
                $result[$key] = $value;
            }
        }
        return $result;
    }

    function get() {
        throw new PageNotFoundException();
    }

    function post() {
        throw new PageNotFoundException();
    }

    function put() {
        throw new PageNotFoundException();
    }

    function delete() {
        throw new PageNotFoundException();
    }

    protected static function init() {}
}
