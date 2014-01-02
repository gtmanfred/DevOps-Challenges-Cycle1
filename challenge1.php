<?php

require "auth.php";
require "check.php";
use OpenCloud\Compute\Constants\ServerState;
$compute = $client->computeService('cloudServersOpenStack', $region );

$server = $compute->Server();
$server->name = 'challenge1';
$server->flavor = $compute->Flavor(2);
$server->image = $compute->Image('f70ed7c7-b42e-4d77-83d8-40fa29825b85');
$server->Create();

checkaction($server, ServerState::ACTIVE);

printf("\naccessIPv4: %s\n", $server->ip(4));
printf("Root Password: %s\n", $server->adminPass);

?>
