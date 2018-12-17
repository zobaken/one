<?php

namespace Core;

/**
 * Template interface
 */
interface TemplateInterface {

    /**
     * Run template
     *
     * @param string $path
     * @param array $parameters
     * @return string
     */
    function run(string $path, array $parameters) : string;
}