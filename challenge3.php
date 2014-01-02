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

//$dns = $client->dnsService();
//$domains = $dns->domainList(array( "name" => ""));
$dns = $client->dnsService();
$domainList = $dns->domainList();

//while($domain = $domains->Next()) {
//    printf("%s\n", $domain->Name());
//}


$count = 0;
$domains = array();
while ($domain = $domainList->Next()) {
    if ( ! isset($first)) {
        $first = $domain;
    } elseif ($domain->Name() == $first->Name()) {
        break;
    }
    printf("%s: %s\n", $count + 1, $domain->Name());
    $count++;
    $domains["${count}"] = $domain;
}

printf("Choose a domain [1-%s]: ", $count);
$handle = fopen ("php://stdin","r");
$selection = fgets($handle);
$selection = trim($selection, "\n");
while ( ! in_array($selection, range(1, $count))) {
    printf("Acceptable range [1-%s]: ", $count);
    $selection = fgets($handle);
    $selection = trim($selection, "\n");
}
$domain = $domains[$selection];

printf("Subdomain: <domain>.%s: ", $domain->Name());
$subdomain = fgets($handle);
$subdomain = trim($subdomain, "\n");
$subdomain = preg_replace('/' . $domain->Name() . '$/', '', $subdomain);

printf("IP address: ");
$ipaddress = fgets($handle);
$ipaddress = trim($ipaddress, "\n");

printf("TTL: ");
$ttl = fgets($handle);
if ( $ttl == "\n" ) {
    $ttl = 300;
} else {
    $ttl = trim($ttl, "\n");
}

printf("Comment (160 Characters): ");
$comment = fgets($handle);
$comment = trim($comment, "\n");

printf("Domain Name: %s\n", $domain->Name());
printf("SubDomain:   %s\n", $subdomain . "." . $domain->Name());
printf("Ipaddress:   %s\n", $ipaddress);
printf("TTL:         %s\n", $ttl);
printf("Comment:     %s\n", $comment);

$newsub = $domain->record();
$newsub->type = 'A';
$newsub->name = $subdomain . "." . $domain->Name();
$newsub->ttl = $ttl;
$newsub->comment = $comment;
$newsub->data = $ipaddress;
$newsub->create();

?>
