<?php

namespace Core;

/**
 * Class PageNotFoundException
 * @package Core
 */
class PageNotFoundException extends \RuntimeException {

    /**
     * PageNotFoundException constructor.
     */
    function __construct() {
        parent::__construct('Page not found', 404);
    }
}
