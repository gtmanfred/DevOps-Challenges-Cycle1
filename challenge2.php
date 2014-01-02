<?php
require 'vendor/autoload.php';
use OpenCloud\Rackspace;

function check_all($service, $servers) {
    $returnVal = true;
    echo "\e[0K";
    foreach($servers as $server) {
        if ( $service->Server($server)->status == 'BUILD' ) {
            $returnVal = false;
        }
        printf("%s/%s: %s\n", $service->Server($server)->name, $service->Server($server)->status,
            $service->Server($server)->progress);
    }
    return $returnVal;
}

function expand_tilde($path)
{
    if ( strpos($path, '~') !== false) {
        $path = str_replace('~', getenv("HOME"), $path);
    }

    return $path;
}

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

echo "How many servers would you like to build? [1-3]: ";
$handle = fopen ("php://stdin","r");
$num = fgets($handle);
$range = range(1, 3);
while ( ! in_array($num, $range)): 
    echo "Acceptable range [1-3]: ";
    $handle = fopen ("php://stdin","r");
    $num = fgets($handle);
endwhile;

echo "Choose a naming scheme: ";
$handle = fopen ("php://stdin","r");
$name = fgets($handle);
$name = trim($name, "\n");
$servers = array();

if (file_exists( expand_tilde("~/.ssh/id_rsa.pub") ) ) {
    $key = file_get_contents(expand_tilde( "~/.ssh/id_rsa.pub" ));
    //$keyfile = expand_tilde("~/.ssh/id_rsa.pub");
} else {
    echo "Location of an ssh key: ";
    $handle = fopen ("php://stdin","r");
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
    $server->flavor = $compute->Flavor(2);
    $server->image = $compute->Image('f70ed7c7-b42e-4d77-83d8-40fa29825b85');
    #$server->keypair = $keypair;
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
    array_push($servers, $server->id);
}

while (! check_all($compute, $servers) ):
    sleep(1);
endwhile;

foreach($servers as $server) {
    printf("ip address: %s\n", $compute->Server($server)->ip(4));
}
?>
