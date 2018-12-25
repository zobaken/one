<?php

namespace Core;
/**
 * Class MethodNotImplementedException
 * @package Core
 */
class MethodNotImplementedException extends \RuntimeException {

    /**
     * MethodNotImplementedException constructor.
     */
    function __construct() {
        parent::__construct('Method not implemented', 404);
    }
}
