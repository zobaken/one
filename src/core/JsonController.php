<?php

namespace Core;

/**
 * Json enabled controller
 */
class JsonController extends Controller {

    /**
     * @var string|null Error message
     */
    public $error = null;

    /**
     * Send error response
     * @param string $message
     * @param int $code HTTP response code
     */
    function error($message, $code = null) {
        $this->error = $message;
        if ($code) {
            $string = ErrorController::getHttpResponseString($code);
            if ($string) {
                header("Status: {$code} {$string}");
            }
            http_response_code($code);
        }
    }

    /**
     * Fire exception
     * @param \Exception $e
     */
    function exception(\Exception $e) {
        $this->error($e->getMessage(), $e->getCode() ? $e->getCode() : 500);
    }

    /**
     * @param string|null $methodBase
     * @param array $params
     * @throws \ReflectionException
     */
    function serve(string $methodBase = null, array $params = []) {
        try {
            parent::serve($methodBase, $params);
        } catch (\Exception $e) {
            $this->exception($e);
            $this->render($this->getProperties());
        }
    }

}