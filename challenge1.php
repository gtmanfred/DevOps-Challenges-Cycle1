<?php

require_once 'autoload.php';
use OpenCloud\Rackspace;

$ini_array = parse_ini_file(getenv("HOME") . "/.rackspace_cloud_credentials", true);

$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => $ini_array['rackspace']['username'],
    'apiKey'   => $ini_array['rackspace']['apikey']
));

$client->authenticate();

try {
    $region = $ini_array['rackspace']['region'];
} catch (Exception $e) {
    $region = 'IAD';
}

$compute = $client->computeService('cloudServersOpenStack', $region );

$server = $compute->Server();
$server->name = 'Challenge 1 Server';
$server->flavor = $compute->Flavor(2);
$server->image = $compute->Image('f70ed7c7-b42e-4d77-83d8-40fa29825b85');

$server->Create();

$serv = $compute->Server($server->id);
while ( ! $serv->ip(4) ):
    sleep(2);
    $serv = $compute->Server($server->id);
endwhile;

printf("accessIPv4: %s\n", $serv->ip(4));
printf("Root Password: %s\n", $server->adminPass);

?>
