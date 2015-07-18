--TEST--
Test for Services_Mailman unsubscribe member
--FILE--
<?php

//settings
$testURL = 'http://example.co.uk/mailman/admin';
$testList = 'test_example.co.uk';
$testPW = 'password';

//get html
$html_success = file_get_contents(__DIR__ . '/findmember-james.html');
$len_success = strlen($html_success);
$html_fail = file_get_contents(__DIR__ . '/findmember-fail.html');
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
	var_dump($mailman->member('james'));
} catch (Services_Mailman_Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

// fail
try {
	var_dump($mailman->member('fail'));
} catch (Services_Mailman_Exception $e) {
	echo 'Caught exception: ',  $e->getMessage(), "\n";
}

?>
--EXPECT--
array(2) {
  [0]=>
  array(11) {
    ["address"]=>
    string(25) "james.smith@example.co.uk"
    ["realname"]=>
    string(0) ""
    ["mod"]=>
    string(3) "off"
    ["hide"]=>
    string(3) "off"
    ["nomail"]=>
    string(3) "off"
    ["ack"]=>
    string(3) "off"
    ["notmetoo"]=>
    string(3) "off"
    ["nodupes"]=>
    string(2) "on"
    ["digest"]=>
    string(3) "off"
    ["plain"]=>
    string(2) "on"
    ["language"]=>
    string(2) "en"
  }
  [1]=>
  array(11) {
    ["address"]=>
    string(25) "james.jones@example.co.uk"
    ["realname"]=>
    string(0) ""
    ["mod"]=>
    string(3) "off"
    ["hide"]=>
    string(3) "off"
    ["nomail"]=>
    string(3) "off"
    ["ack"]=>
    string(3) "off"
    ["notmetoo"]=>
    string(3) "off"
    ["nodupes"]=>
    string(3) "off"
    ["digest"]=>
    string(2) "on"
    ["plain"]=>
    string(3) "off"
    ["language"]=>
    string(2) "en"
  }
}
Caught exception: No match