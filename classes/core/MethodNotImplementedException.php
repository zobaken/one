<?php

namespace Core;

/**
 * Class MethodNotImplementedException
 */
class MethodNotImplementedException extends \RuntimeException {

    function __construct() {
        parent::__construct('Method not implemented');
    }
}
