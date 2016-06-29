<?php

$srcRoot = 'src/';
$buildRoot = 'build';


$phar = new Phar($buildRoot . '/dbcw.phar');
$phar->startBuffering();

$defaultStub = $phar->createDefaultStub('index.php');
$stub = "#!/usr/bin/env php \n" . $defaultStub;

$phar->setStub($stub);
$phar->buildFromDirectory($srcRoot);

$phar->stopBuffering();
