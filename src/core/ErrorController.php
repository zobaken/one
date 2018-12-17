<?php

namespace Core;

use Core\Controller;

class ErrorController extends Controller {

    public $code;
    public $message;

    function __construct($message, $code = 0, $view = null) {
        parent::__construct();
        $this->code = $code;
        $this->message = $message;
        $this->view = $view;
    }

    function render(array $parameters = []) {
        $statusCodes = require CORE . '/resources/http_status_codes.php';

        if ($this->code) {
            $statusMessage = 'Internal error';
            if (isset($statusCodes[$this->code])) {
                $statusMessage = $statusCodes[$this->code];
            }
            if (empty($parameters['message'])) {
                $parameters['message'] = $statusMessage;
            }
            header("Status: {$this->code} {$statusMessage}");
            http_response_code($this->code);
        }
        parent::render($parameters);
    }

    function serve() {
        static::init();
        $result = $this->getPublicProperties();
        $this->render($result);
    }

}