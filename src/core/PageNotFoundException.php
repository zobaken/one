<?php

namespace Core;

/**
 * Class MethodNotImplementedException
 */
class PageNotFoundException extends \RuntimeException {

    function __construct() {
        parent::__construct('Page not found', 404);
    }
}
