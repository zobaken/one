<?php

namespace Core;

/**
 * Template factory
 * @package Core
 */
class Template {

    protected static $config;

    /**
     * Get template adapter defined in configuration
     * @return TemplateInterface
     */
    static function getAdapter() {
        if (!static::$config) {
            static::$config = new ConfigStore('template');
        }
        $adapterClass = static::$config->templateAdapter;
        if ($adapterClass) {
            return new $adapterClass();
        }
    }
}