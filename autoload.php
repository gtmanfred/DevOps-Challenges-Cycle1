<?php
//require 'vendor/autoload.php';
// Define the path to the library
$libraryPath = getcwd();

require_once 'classloader/SplClassLoader.php';

// Register the root OpenCloud namespace
$classLoader = new SplClassLoader('OpenCloud', $libraryPath . '/php-opencloud/lib');
$classLoader->register();
$classLoader = new SplClassLoader('Guzzle', $libraryPath . '/guzzle/src');
$classLoader->register();
$classLoader = new SplClassLoader('Symfony', $libraryPath . '/symfony/src');
$classLoader->register();

?>
