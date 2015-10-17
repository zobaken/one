<?php

namespace Ctl;

/**
 * Sample controller
 */
class Main extends \Core\Controller {

    function get() {
        $title = 'Sample page';
        $text = 'Hello world';
        require tpl('sample/main');
    }

}