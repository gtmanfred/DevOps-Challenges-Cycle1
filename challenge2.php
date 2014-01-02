<?php

require "auth.php";
require "check.php";
use OpenCloud\Compute\Constants\ServerState;

function expand_tilde($path)
{
    if ( strpos($path, '~') !== false) {
        $path = str_replace('~', getenv("HOME"), $path);
    }

    return $path;
}

$compute = $client->computeService('cloudServersOpenStack', $region );

echo "How many servers would you like to build? [1-3]: ";
$handle = fopen ("php://stdin","r");
$num = fgets($handle);
$range = range(1, 3);
while ( ! in_array($num, $range)): 
    echo "Acceptable range [1-3]: ";
    $num = fgets($handle);
endwhile;

echo "Choose a naming scheme: ";
$name = fgets($handle);
$name = trim($name, "\n");
$servers = array();

if (file_exists( expand_tilde("~/.ssh/id_rsa.pub") ) ) {
    $key = file_get_contents(expand_tilde( "~/.ssh/id_rsa.pub" ));
    //$keyfile = expand_tilde("~/.ssh/id_rsa.pub");
} else {
    echo "Location of an ssh key: ";
    $keyfile = fgets($handle);
    $keyfile = trim($keyfile, "\n");
    $keyfile = expand_tilde($keyfile);
    $key = file_get_contents(expand_tilde($keyfile));
}

$keypair = $compute->Keypair(array( 'name' => 'daniel-challenges', 'publicKey' => $key));
//$keypair->setName("daniel-challenges");
//$keypair->upload(array(
//    'path' => $keyfile
//));
//exit();

foreach (range(1, $num) as $number ){
    $server = $compute->Server();
    $server->name = "${name}${number}";
    $server->flavor = $compute->Flavor('2');
    $server->image = $compute->Image('f70ed7c7-b42e-4d77-83d8-40fa29825b85');
    //$server->keypair = $keypair;
    $server->addFile('/root/.ssh/authorized_keys', $key);
    $server->Create();
//    $server = $compute->Server();
//    $server->create(array(
//        'name'     => $name . $number,
//        'image'    => $compute->Image('f70ed7c7-b42e-4d77-83d8-40fa29825b85'),
//        'flavor'   => $compute->Flavor(2),
//        'OS-DCF:diskConfig' => 'AUTO',
//        'keypair' => array(
//            'name'      => 'daniel-challenges',
//        )
//    ));
    array_push($servers, $server);
}

foreach($servers as $server) {
    checkaction($server, ServerState::ACTIVE);
    printf("\nName: %s\nip address: %s\n\n", $server->name, $server->ip(4));
}
?>
