<?php

function downloadWeTransfer($url, $target)
{
	$parts = explode('/', $url);
	$parts = array_slice($parts, -2);
	
	$virtual_link = vsprintf('https://www.wetransfer.com/api/v1/transfers/%s/download?recipient_id=&security_hash=%s&password=&ie=false', $parts);
	
	$response = file_get_contents($virtual_link);
	$response = json_decode($response, true);
	
	if ($direct_link = arp($response, 'direct_link')); 
		else throw new Exception('Invalid URL (or WeTransfer changed things)');
		
	if (file_put_contents($target, file_get_contents($direct_link)) === false)
		throw new Exception('Error saving file.');
}

// -- Example:  

downloadWeTransfer('https://www.wetransfer.com/downloads/XXXXXXXX/YYY', 'download.zip');

