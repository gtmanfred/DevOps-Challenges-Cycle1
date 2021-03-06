<?php
require 'vendor/autoload.php';
use OpenCloud\Rackspace;

$ini_array = parse_ini_file(getenv("HOME") . "/.rackspace_cloud_credentials", true);

$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
    'username' => $ini_array['rackspace']['username'],
    'apiKey'   => $ini_array['rackspace']['api_key']
));

$client->authenticate();

try {
    $region = $ini_array['rackspace']['region'];
} catch (Exception $e) {
    $region = 'IAD';
}
?>
