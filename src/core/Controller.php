<?php

namespace Core;

/**
 * Basic controller
 */
class Controller {

    protected static $routes = [];
    protected $path;
    protected $view;

    function __construct($path = null) {
        $this->path = $path;
    }

    function getMethodBase() {
        if (!$this->path) return '';
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
            $result = call_user_func([$this, $method]);
            if (is_null($result)) {
                $result = $this->getPublicProperties();
            }
            $this->render($result);
        } else {
            throw new PageNotFoundException();
        }
    }

    function render(array $parameters = []) {
        if ($this->view) {
            $template = \Core\Template::getAdapter();
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

    function getPublicProperties() {
        $reflection = new \ReflectionClass($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        $static = $reflection->getProperties(\ReflectionProperty::IS_STATIC);
        $properties = array_diff($properties, $static);
        $result = [];
        foreach ($properties as $p) {
            $result [$p->name] = $this->{$p->name};
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
