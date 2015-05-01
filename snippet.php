<?php

function downloadWeTransfer($url, $target)
{
	set_time_limit(0);
	preg_match('/https?:\/\/(www\.)?wetransfer\.com\/downloads\/(.+)/', $url, $matches);

	$url = $matches[2];
	$parts = explode('/', $url);

	switch (count($parts))
	{
		case 2: 	$virtual_link = vsprintf('https://www.wetransfer.com/api/v1/transfers/%s/download?recipient_id=&security_hash=%s&password=&ie=false',   $parts); break;
		case 3: 	$virtual_link = vsprintf('https://www.wetransfer.com/api/v1/transfers/%s/download?recipient_id=%s&security_hash=%s&password=&ie=false', $parts); break;
		default:
			throw new Exception('Invalid WeTransfer URL');
	}

	$response = file_get_contents($virtual_link);
	$response = json_decode($response, true);

	if (isset($response['direct_link']))
	{
		$result = download($response['direct_link'], $target, stream_context_create());

		if (!$result) throw new Exception('Error saving file.');
		return true;
	}
	
	if (isset($response['fields']))
	{
		$postdata = http_build_query($response['fields']);
		$opts = array('http' => array(
			'method'  => 'POST',
			'header'  => 'Content-type: application/x-www-form-urlencoded',
			'content' => $postdata
		));

		$context = stream_context_create($opts);
		$result = download($response['formdata']['action'], $target, $context);

		if (!$result) throw new Exception('Error saving file.');
		return true;
	}

	return false;
}

function download($file_source, $file_target, $context)
{
	$rh = fopen($file_source, 'rb', null, $context);
	$wh = fopen($file_target, 'w+b');

	if (!$rh || !$wh)
		return false;

	while (!feof($rh))
		if (fwrite($wh, fread($rh, 4096)) === FALSE)
			return false;

	fclose($rh);
	fclose($wh);

	return true;
}

// -- Example:  

downloadWeTransfer('https://www.wetransfer.com/downloads/XXXXXXXXXX/YYYYYYYYY', 'first.zip');
downloadWeTransfer('https://www.wetransfer.com/downloads/XXXXXXXXXX/YYYYYYYYY/ZZZZZZZZ', 'second.zip');
