--TEST--
Test for Services_Mailman members big list
--FILE--
<?php

//settings
$testURL = 'http://example.co.uk/mailman/admin';
$testList = 'test_example.co.uk';
$testPW = 'password';

//get html
$html=file_get_contents(dirname(__FILE__) . '/members-big.html');
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
foreach (range('a', 'z') as $letter) {
    $mock->addResponse(str_replace('a2000',$letter.'2000',$response));
}

//set mock adapter
$mailman->request->setAdapter($mock);
$members=$mailman->members();
var_dump($members);

?>
--EXPECT--

array(2) {
  [0]=>
  array(26) {
    [0]=>
    string(17) "a2000@example.com"
    [1]=>
    string(17) "b2000@example.com"
    [2]=>
    string(17) "c2000@example.com"
    [3]=>
    string(17) "d2000@example.com"
    [4]=>
    string(17) "e2000@example.com"
    [5]=>
    string(17) "f2000@example.com"
    [6]=>
    string(17) "g2000@example.com"
    [7]=>
    string(17) "h2000@example.com"
    [8]=>
    string(17) "i2000@example.com"
    [9]=>
    string(17) "j2000@example.com"
    [10]=>
    string(17) "k2000@example.com"
    [11]=>
    string(17) "l2000@example.com"
    [12]=>
    string(17) "m2000@example.com"
    [13]=>
    string(17) "n2000@example.com"
    [14]=>
    string(17) "o2000@example.com"
    [15]=>
    string(17) "p2000@example.com"
    [16]=>
    string(17) "q2000@example.com"
    [17]=>
    string(17) "r2000@example.com"
    [18]=>
    string(17) "s2000@example.com"
    [19]=>
    string(17) "t2000@example.com"
    [20]=>
    string(17) "u2000@example.com"
    [21]=>
    string(17) "v2000@example.com"
    [22]=>
    string(17) "w2000@example.com"
    [23]=>
    string(17) "x2000@example.com"
    [24]=>
    string(17) "y2000@example.com"
    [25]=>
    string(17) "z2000@example.com"
  }
  [1]=>
  array(26) {
    [0]=>
    string(0) ""
    [1]=>
    string(0) ""
    [2]=>
    string(0) ""
    [3]=>
    string(0) ""
    [4]=>
    string(0) ""
    [5]=>
    string(0) ""
    [6]=>
    string(0) ""
    [7]=>
    string(0) ""
    [8]=>
    string(0) ""
    [9]=>
    string(0) ""
    [10]=>
    string(0) ""
    [11]=>
    string(0) ""
    [12]=>
    string(0) ""
    [13]=>
    string(0) ""
    [14]=>
    string(0) ""
    [15]=>
    string(0) ""
    [16]=>
    string(0) ""
    [17]=>
    string(0) ""
    [18]=>
    string(0) ""
    [19]=>
    string(0) ""
    [20]=>
    string(0) ""
    [21]=>
    string(0) ""
    [22]=>
    string(0) ""
    [23]=>
    string(0) ""
    [24]=>
    string(0) ""
    [25]=>
    string(0) ""
  }
}
