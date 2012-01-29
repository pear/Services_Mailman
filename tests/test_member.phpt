--TEST--
Test for Services_Mailman unsubscribe member
--FILE--
<?php

//settings
$testURL = 'http://example.co.uk/mailman/admin';
$testList = 'test_example.co.uk';
$testPW = 'password';

//get html
$html_success = file_get_contents('findmember-james.html');
$len_success = strlen($html_success);
$html_fail = file_get_contents('findmember-fail.html');
$len_fail = strlen($html_fail);

//set mailman
require_once 'Services/Mailman.php';
$mailman = new Services_Mailman($testURL,$testList,$testPW);

//set mock
require_once 'HTTP/Request2/Adapter/Mock.php';
$mock = new HTTP_Request2_Adapter_Mock();
$response = "HTTP/1.1 200 OK\r\n" .
    "Content-Length: %s\r\n" .
    "Connection: close\r\n" .
    "\r\n%s";
$mock->addResponse(sprintf($response,$len_success,$html_success));
$mock->addResponse(sprintf($response,$len_fail,$html_fail));

//set mock adapter
$mailman->request->setAdapter($mock);

// success
try {
	$mailman->member('james');
} catch (Services_Mailman_Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

// fail
try {
	$mailman->member('fail');
} catch (Services_Mailman_Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>
--EXPECT--

Caught exception: No match.