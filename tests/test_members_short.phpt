--TEST--
Test for Services_Mailman members short list
--FILE--
<?php

//settings
$testURL = 'http://example.co.uk/mailman/admin';
$testList = 'test_example.co.uk';
$testPW = 'password';

//get html
$html=file_get_contents('members-short.html');
$length=strlen($html);

//set mailman
require_once 'Services/Mailman.php';
$mailman = new Services_Mailman($testURL,$testList,$testPW);

//set mock
require_once 'HTTP/Request2/Adapter/Mock.php';
$mock = new HTTP_Request2_Adapter_Mock();
$response=    "HTTP/1.1 200 OK\r\n" .
    "Content-Length: $length\r\n" .
    "Connection: close\r\n" .
    "\r\n" .
    $html;
$mock->addResponse($response);

//set mock adapter
$mailman->request->setAdapter($mock);
$members=$mailman->members();
var_dump($members);

?>
--EXPECT--

array(2) {
  [0]=>
  array(1) {
    [0]=>
    string(16) "test@example.com"
  }
  [1]=>
  array(1) {
    [0]=>
    string(0) ""
  }
}
