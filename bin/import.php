#!/usr/bin/env php
<?php

use Xhgui\Profiler\ImporterFactory;

require __DIR__ . '/../vendor/autoload.php';

$importer = ImporterFactory::create();

if ($argc <= 1) {
    throw new RuntimeException('Missing input filename');
}

$filename = $argv[1];
if (!is_readable($filename)) {
    throw new RuntimeException($filename . ' isn\'t readable');
}

$fp = fopen($filename, 'r');
if (!$fp) {
    throw new RuntimeException('Can\'t open ' . $filename);
}
$importer->import($fp);
fclose($fp);
