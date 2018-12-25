<?php

namespace Core;

/**
 * Basic controller
 * @package Core
 */
class Controller {

    /**
     * @var array Controller defined routes
     */
    protected static $routes = [];

    /**
     * @var string Controller view (template) path
     */
    protected $view;

    /**
     * Run the controller
     * @param string|null $methodBase
     * @param array $parameters
     * @throws \Exception
     */
    function serve(string $methodBase = null, array $parameters = []) {
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
            throw new MethodNotImplementedException();
        }
    }

    /**
     * Render from controller properties
     * @param array $properties
     */
    function render(array $properties = []) {
        if ($this->view) {
            $template = Template::getAdapter();
            echo $template->run($this->view, $properties);
        } else {
            header('Content-Type: application/json');
            echo json_encode($properties);
        }
    }

    /**
     * Get request parameters
     * @param string $name
     * @param mixed $default
     * @return null
     */
    protected function param(string $name, $default = null) {
        if (isset($_REQUEST[$name])) return $_REQUEST[$name];
        return $default;
    }

    /**
     * Get public properties
     * @return array
     * @throws \ReflectionException
     */
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

    /**
     * Stub
     */
    function get() {
        throw new PageNotFoundException();
    }

    /**
     * Stub
     */
    function post() {
        throw new PageNotFoundException();
    }

    /**
     * Stub
     */
    function put() {
        throw new PageNotFoundException();
    }

    /**
     * Stub
     */
    function delete() {
        throw new PageNotFoundException();
    }

    /**
     * Init controller
     */
    protected function init() {}
}
