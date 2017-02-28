<?php

/**
 * Autoload class
 * @param string $className
 */
function core_autoload($className) {

    if (substr_count($className, '\\')) {
        $path = explode('\\', $className);
        $classLastName = $path[count($path) - 1];
        $topDir = $path[0];
        unset($path[count($path) - 1]);
        unset($path[0]);
        $classPath = ROOT . "/{$topDir}/" . implode('/', $path) . "/{$classLastName}.php";
    } else {
        $classPath = ROOT . "/classes/{$className}.php";
    }

    if(file_exists($classPath)){
        include_once($classPath);
    }
}
