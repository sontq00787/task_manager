<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Push Message Test</title>
<!-- <link rel="stylesheet" href="../css/style.css"> -->


</head>
<body>


<form method="post">
Select Device: <!-- Device token: <input type="text" name="deviceToken" value="6bfd5665ec071590426084a30f694b1ff26e6e4f0780845564ab74960cca25a5" size="150" /> ( default is iPhone 4S VN ) -->
<select name="deviceToken">
<option value="76ff29444c7719834bccc5f1f1830bc88996be8695836ac44449a538b3aa185d">IPhone 5 VN</option>
<option value="608344b8870d5899089864d1448a2d88208f235b35098f9e20fd218ee715dd98">IPhone 4S VN</option>
</select>
<br />
Message Content: <textarea rows="20" cols="20" name="content"></textarea>
<br />
Badge: <input type="text" name="badge" />
<br />
<input type="submit" value="Send"> 
</form>

<?php 

if (isset ( $_POST ['deviceToken'] )) {
	$content = $_POST ['content'];
	$badgec = (int)$_POST ['badge'];
	$deviceToken = $_POST ['deviceToken'];
	sendMessage($deviceToken, $content, $badgec);
	// echo '<script type="text/javascript">', 'alert("' . $_POST ['answer'] . '");', '</script>';
}

function  sendMessage($ideviceToken,$message,$count_badge){
	
// Put your device token here (without spaces):
//$deviceToken = '7d27ecb656c13c915f4d829f5fd577cd1ada85fac7b14e8658c25822bd726e4f';
$deviceToken = $ideviceToken;

// Put your private key's passphrase here:
$passphrase = 'sysvn';

// Put your alert message here:
$message = $message;


$badge = $count_badge;

////////////////////////////////////////////////////////////////////////////////

$ctx = stream_context_create();
stream_context_set_option($ctx, 'ssl', 'local_cert', 'ck.pem');
stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);

// Open a connection to the APNS server
$fp = stream_socket_client(
		'ssl://gateway.sandbox.push.apple.com:2195', $err,
		$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);

if (!$fp)
	exit("Failed to connect: $err $errstr" . PHP_EOL);

echo 'Connected to APNS' . PHP_EOL;

// Create the payload body
$body['aps'] = array(
		'alert' => $message,
		'sound' => 'default',
		'badge' => $count_badge
);

// Encode the payload as JSON
$payload = json_encode($body);

// Build the binary notification
$msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;

// Send it to the server
$result = fwrite($fp, $msg, strlen($msg));

if (!$result)
	echo 'Message not delivered' . PHP_EOL;
else
	echo '<br />Message successfully delivered' . PHP_EOL;

// Close the connection to the server
fclose($fp);

}
?>
</body>
</html>