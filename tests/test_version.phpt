--TEST--
Test for Services_Mailman version
--FILE--
<?php

// settings
$testURL = 'http://mail.cpanel.net/mailman/admin';

//get html
$html=file_get_contents(dirname(__FILE__) . '/mail.cpanel.net.html');
$length=strlen($html);

//set mailman
require_once 'Services/Mailman.php';
$mailman = new Services_Mailman($testURL,'none');

//set mock
require_once 'HTTP/Request2/Adapter/Mock.php';
$mock = new HTTP_Request2_Adapter_Mock();
$response=    "HTTP/1.1 200 OK\r\n" .
    "Content-Length: $length\r\n" .
    "Connection: close\r\n" .
    "\r\n" .
    $html;
$mock->addResponse($response);

echo $mailman->version();

?>
--EXPECT--
2.1.20