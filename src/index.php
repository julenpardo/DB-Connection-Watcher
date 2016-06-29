<?php

/**
 * Entry point to the tool. Just includes the file that registers the autoloader, instantiates the main class, and runs
 * its main method.
 *
 * @author Julen Pardo
 */

require_once('autoload.php');

$dbcw = new \DBConnectionWatcher\DBConnectionWatcher();
$dbcw->run();
