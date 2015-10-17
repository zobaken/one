<?php

namespace Ctl;
/**
 * Json sample controller
 */
class Json extends \Core\JsonController {

    function get() {
        $this->response->send();
    }

}