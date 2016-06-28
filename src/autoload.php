<?php

define('DBCW_PATH', dirname(__FILE__));

function dbConnectionWatcherAutoload($namespace) {
    $path = explode('\\', $namespace);
    array_shift($path);
    $class = array_pop($path);

    $path = implode('/', $path);
    $path = strtolower($path);

    $fullpath = DBCW_PATH . '/' . $path . '/' . $class . '.php';

    if (file_exists($fullpath)) {
        require($fullpath);
    }
}

spl_autoload_register('dbConnectionWatcherAutoload');
