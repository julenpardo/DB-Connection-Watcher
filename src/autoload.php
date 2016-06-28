<?php

// The PSR standard does not allow to define constants where "flow" exists, so, it must be a variable.
$dbcwPath = dirname(__FILE__);

spl_autoload_register(function ($namespace) {
    global $dbcwPath;

    $path = explode('\\', $namespace);
    array_shift($path);
    $class = array_pop($path);

    $path = implode('/', $path);
    $path = strtolower($path);

    $fullpath = $dbcwPath . '/' . $path . '/' . $class . '.php';

    if (file_exists($fullpath)) {
        require($fullpath);
    }
});
