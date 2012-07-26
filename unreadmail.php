<?php
$debug = null;
function _read($socket)
{
    $data .= fgets($socket, 1024);
	if ($debug) print "Server <- ".$data."<br>";
	return $data;
}

function _write($socket,$data)
{
	if ($debug) print "Client -> ".$data."<br>";
	fputs($socket, $data."\r\n");
}

function _xor($string, $string2)
{
	$result = '';
	$size   = strlen($string);

	for ($i=0; $i<$size; $i++) {
		$result .= chr(ord($string[$i]) ^ ord($string2[$i]));
	}

	return $result;
}

function unreademails() {
	$username = 'info@carpe-viam.org';
	$password = 'super-sicheres-&-streng-geheimes-&%§$$!$%&-passwort';
	$ipad = '';
	$opad = '';

	if ($debug) echo "Open...<br>";
	if(!$socket = fsockopen("ssl://imap.worldserver.net", "993", $errno, $errstr, 3))
		return false;
	if ($debug) echo "Verbindung hergestellt...<br>";
	
	_read($socket);
	
	_write($socket,"a AUTHENTICATE CRAM-MD5");
	
	// generate reply
	$line = trim(_read($socket));
	if ($line[0] == '+') {
		$challenge = substr($line, 2);
	}
	
	// initialize ipad, opad
	for ($i=0; $i<64; $i++) {
		$ipad .= chr(0x36);
		$opad .= chr(0x5C);
	}

	// pad $password so it's 64 bytes
	$padLen = 64 - strlen($password);
	for ($i=0; $i<$padLen; $i++) {
		$password .= chr(0);
	}

	// generate hash
	$hash  = md5(_xor($password, $opad) . pack("H*",md5(_xor($password, $ipad) . base64_decode($challenge))));
	$reply = base64_encode($username . ' ' . $hash);
	
	_write($socket,$reply);
	
	_read($socket);
		
	/*
	_write($socket,"a GETQUOTAROOT INBOX\r\n");
	$data = trim(_read($socket));
	while ($data[0] != "a") {
		$result .= $data."<br>";
		$data = trim(_read($socket));
	}
	*/
	
	

	
	_write($socket,'a STATUS INBOX (unseen)');
	while ($data[0] != "a") {
		if (preg_match('/(?P<zahl>\d+)/', $data,$unseen));
			$return = $unseen["zahl"];
		$result .= $data."<br>";
		$data = trim(_read($socket));
	}
	
	
	
	
	_write($socket,"a LOGOUT\r\n");
	_read($socket);
	return $return;
}
echo unreademails();
?>