<?php

namespace Core;

/**
 * Json response
 */
class JsonResponse {

    /**
     * @var string Error message or null
     */
    var $error = null;

    /**
     * @var boolean Success flag
     */
    var $success = true;

    /**
     * Send response
     */
    function send($exit = true) {
        header('Content-Type: application/json');
        echo json_encode($this);
        if ($exit) exit();
    }

    /**
     * Send error response
     * @param string $message
     * @param int $code HTTP response code
     */
    function error($message, $code = null) {
        if ($code) {
            http_response_code($code);
        }
        $this->success = false;
        $this->error = $message;
        $this->send();
    }

    /**
     * Fire exception
     * @throws \Exception
     * @param \Exception $e
     */
    function exception(\Exception $e) {
        $this->error($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
    }

}
