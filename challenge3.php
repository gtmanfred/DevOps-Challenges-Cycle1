<?php
require 'vendor/autoload.php';
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

//$dns = $client->dnsService();
//$domains = $dns->domainList(array( "name" => ""));
$domains = $client->dnsService()->domainList();

while($domain = $domains->Next()) {
    printf("%s\n", $domain->Name());
}

?>
