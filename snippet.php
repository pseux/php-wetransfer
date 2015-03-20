
$url = 'https://www.wetransfer.com/downloads/XXXXXXXX/YYY';
$target = 'download.zip';

// --

$parts = explode('/', $url);
$parts = array_slice($parts, -2);

$first_url = vsprintf('https://www.wetransfer.com/api/v1/transfers/%s/download?recipient_id=&security_hash=%s&password=&ie=false', $parts);

$response = file_get_contents($first_url);
$response = json_decode($response, true);

if ($second_url = arp($response, 'direct_link')); 
	else die('Invalid URL (or WeTransfer changed things)');
	
// --

file_put_contents($target, file_get_contents($second_url));

