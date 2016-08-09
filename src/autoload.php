<?php

/**
 * File for the autoloading, included in the program entry point (index.php). Finds each class basing in its namespace.
 *
 * @author Julen Pardo
 */

// The PSR standard does not allow to define constants where "flow" exists, so, it must be a variable.
$dbcwPath = dirname(__FILE__);

spl_autoload_register(function ($namespace) {
    global $dbcwPath;

    $path = explode('\\', $namespace);
    array_shift($path);
    $class = array_pop($path);

    $path = implode('/', $path);
    $path = strtolower($path);

    if ($path === '') {
        $fullpath = $dbcwPath . '/' . $class . '.php';
    } else {
        $fullpath = $dbcwPath . '/' . $path . '/' . $class . '.php';
    }

    if (file_exists($fullpath)) {
        require($fullpath);
    }
});
