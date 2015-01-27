<?php

defined('ROOT') || define('ROOT', realpath(__DIR__.'/../'));
defined('SRC') || define('SRC', ROOT.'/src');
defined('APP') || define('APP', 'Dumper');

defined('VERSION') || define('VERSION', '0.1');


$found_file = false;
$file_path  = ROOT.'/vendor/autoload.php';

if (file_exists($file_path)) {
    $loader = require_once($file_path);
    $loader->set('Dumper', SRC);
    $found_file = true;
}


if (! $found_file) {
    die('$ php composer.phar install を実行してください');
}

