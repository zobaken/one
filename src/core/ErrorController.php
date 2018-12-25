<?php

namespace Core;

/**
 * Class ErrorController
 * @package Core
 */
class ErrorController extends Controller {

    /**
     * @var int|null
     */
    public $code;
    /**
     * @var string
     */
    public $message;

    /**
     * ErrorController constructor.
     * @param $message
     * @param null $code
     * @param null $view
     */
    function __construct($message, $code = null, $view = null) {
        $this->message = $message;
        $this->code = $code;
        $this->view = $view;
    }

    /**
     * @param array $properties
     */
    function render(array $properties = []) {
        if ($this->code) {
            $statusMessage = static::getHttpResponseString($this->code);
            if (!$statusMessage) {
                $statusMessage = 'Internal error';
            }
            if (empty($properties['message'])) {
                $properties['message'] = $statusMessage;
            }
            header("Status: {$this->code} {$statusMessage}");
            http_response_code($this->code);
        }
        parent::render($properties);
    }

    /**
     * @param string|null $methodBase
     * @param array $parameters
     * @throws \ReflectionException
     */
    function serve(string $methodBase = null, array $parameters = []) {
        $this->init();
        $result = $this->getProperties();
        $this->render($result);
    }

    /**
     * @param int $code
     * @return null
     */
    static function getHttpResponseString(int $code) {
        $statusCodes = require CORE . '/resources/http_status_codes.php';
        $result = null;
        if (isset($statusCodes[$code])) {
            $result = $statusCodes[$code];
        }
        return $result;
    }
}