--TEST--
Test for Services_Mailman lists
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

$lists = $mailman->lists();
var_dump($lists);

?>
--EXPECT--
array(5) {
  [0]=>
  array(6) {
    [0]=>
    string(21) "edge-users_cpanel.net"
    [1]=>
    string(10) "Edge-Users"
    [2]=>
    string(10) "Edge-Users"
    ["path"]=>
    string(21) "edge-users_cpanel.net"
    ["name"]=>
    string(10) "Edge-Users"
    ["desc"]=>
    string(10) "Edge-Users"
  }
  [1]=>
  array(6) {
    [0]=>
    string(31) "integration-announce_cpanel.net"
    [1]=>
    string(20) "Integration-announce"
    [2]=>
    string(32) "cPanel Integration Announcements"
    ["path"]=>
    string(31) "integration-announce_cpanel.net"
    ["name"]=>
    string(20) "Integration-announce"
    ["desc"]=>
    string(32) "cPanel Integration Announcements"
  }
  [2]=>
  array(6) {
    [0]=>
    string(15) "news_cpanel.net"
    [1]=>
    string(4) "News"
    [2]=>
    string(11) "cPanel News"
    ["path"]=>
    string(15) "news_cpanel.net"
    ["name"]=>
    string(4) "News"
    ["desc"]=>
    string(11) "cPanel News"
  }
  [3]=>
  array(6) {
    [0]=>
    string(18) "newtech_cpanel.net"
    [1]=>
    string(7) "Newtech"
    [2]=>
    string(61) "Discussion of User Interface new technology preview releases."
    ["path"]=>
    string(18) "newtech_cpanel.net"
    ["name"]=>
    string(7) "Newtech"
    ["desc"]=>
    string(61) "Discussion of User Interface new technology preview releases."
  }
  [4]=>
  array(6) {
    [0]=>
    string(19) "releases_cpanel.net"
    [1]=>
    string(8) "Releases"
    [2]=>
    string(26) "cPanel Release Information"
    ["path"]=>
    string(19) "releases_cpanel.net"
    ["name"]=>
    string(8) "Releases"
    ["desc"]=>
    string(26) "cPanel Release Information"
  }
}
