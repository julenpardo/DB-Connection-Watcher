<?php

$srcRoot = 'src/';
$buildRoot = 'build';

$phar = new Phar($buildRoot . '/dbcw.phar');
$phar->setDefaultStub('index.php');
$phar->buildFromDirectory($srcRoot);
