<?php

namespace Core;

/**
 * Json enabled controller
 */
class JsonController extends Controller {

    /** @var  JsonResponse */
    var $response;

    function __construct($path) {
        parent::__construct($path);
        $this->response = new JsonResponse();
    }

}