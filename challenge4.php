<?php

require "auth.php";
require "check.php";

function expand_tilde($path)
{
    if ( strpos($path, '~') !== false) {
        $path = str_replace('~', getenv("HOME"), $path);
    }

    return $path;
}

function walk($baseDir, $workingDir ) {
    $files = array();
    if( empty($workingDir) || $workingDir == '/' ) {
        $workingDir = '';
    } else {
        if( substr($workingDir, 0, 1) == '/' )
            $workingDir = substr($workingDir, 1);
        if( substr($workingDir, -1, 1) != '/' )
            $workingDir .= '/';
    }

    $dirStructure = scandir($baseDir . $workingDir);
    foreach( $dirStructure as $entry ) {
        if( $entry === '.' || $entry === '..' )
            continue;

        if( is_dir($baseDir . $workingDir . $entry) ) {
            $files = array_merge($files, walk($baseDir, $workingDir . $entry));
        } else {
            array_push($files, array("name" => $workingDir . $entry, "path" => $baseDir . $workingDir . $entry ));
        }
    }
    return $files;
}


$cloudfiles = $client->objectStoreService("cloudFiles");


printf("Container Name: ");
$handle = fopen ("php://stdin","r");
$name = trim(fgets($handle));
printf("Directory to upload: ");
$dir = expand_tilde(trim(fgets($handle)));
$dir .= ( substr($dir, -1, 1) != '/' ) ? '/' : '';
$container= $cloudfiles->createContainer($name);
$files = walk($dir, "/");

$container->uploadObjects($files);
$container->enableCDN();
$cdn = $container->getCdn();

printf("%s\n", $cdn->getCdnUri());

?>
