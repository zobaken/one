<?php

namespace Core\Ctl;

use Core\Controller;

class HttpError extends Controller {

    protected $code;
    protected $message;

    function __construct($path, $code) {
        parent::__construct($path);
        $this->code = $code;
    }

    function serve() {
        require CORE . '/resources/http_status_codes.php';

        $this->message = 'Unknown error';
        if (isset($http_status_codes[$this->code])) {
            $this->message = $http_status_codes[$this->code];
        }
        header("Status: {$this->code} {$this->message}");
        http_response_code($this->code);
        if (file_exists(tpl('service/error'))) {
            $error = $this->code . ': ' . $this->message;
            require tpl('service/error');
        } else {
            echo "{$this->code} {$this->message}";
        }
        return;
    }

}