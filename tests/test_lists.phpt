--TEST--
Test for Services_Mailman lists
--FILE--
<?php

// settings
$testURL = 'http://mail.cpanel.net/mailman/admin';

//get html
$html=file_get_contents('mail.cpanel.net.html');
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

array(7) {
  [0]=>
  array(6) {
    [0]=>
    string(25) "admin/cpanelpm_cpanel.net"
    [1]=>
    string(8) "cPanelpm"
    [2]=>
    string(26) "[no description available]"
    ["path"]=>
    string(19) "cpanelpm_cpanel.net"
    ["name"]=>
    string(8) "cPanelpm"
    ["desc"]=>
    string(26) "[no description available]"
  }
  [1]=>
  array(6) {
    [0]=>
    string(25) "admin/giveaway_cpanel.net"
    [1]=>
    string(8) "Giveaway"
    [2]=>
    string(26) "[no description available]"
    ["path"]=>
    string(19) "giveaway_cpanel.net"
    ["name"]=>
    string(8) "Giveaway"
    ["desc"]=>
    string(26) "[no description available]"
  }
  [2]=>
  array(6) {
    [0]=>
    string(23) "admin/python_cpanel.net"
    [1]=>
    string(6) "Python"
    [2]=>
    string(26) "[no description available]"
    ["path"]=>
    string(17) "python_cpanel.net"
    ["name"]=>
    string(6) "Python"
    ["desc"]=>
    string(26) "[no description available]"
  }
  [3]=>
  array(6) {
    [0]=>
    string(24) "admin/uidtest_cpanel.net"
    [1]=>
    string(7) "Uidtest"
    [2]=>
    string(26) "[no description available]"
    ["path"]=>
    string(18) "uidtest_cpanel.net"
    ["name"]=>
    string(7) "Uidtest"
    ["desc"]=>
    string(26) "[no description available]"
  }
  [4]=>
  array(6) {
    [0]=>
    string(27) "admin/unleashvip_cpanel.net"
    [1]=>
    string(10) "Unleashvip"
    [2]=>
    string(26) "[no description available]"
    ["path"]=>
    string(21) "unleashvip_cpanel.net"
    ["name"]=>
    string(10) "Unleashvip"
    ["desc"]=>
    string(26) "[no description available]"
  }
  [5]=>
  array(6) {
    [0]=>
    string(24) "admin/vending_cpanel.net"
    [1]=>
    string(7) "Vending"
    [2]=>
    string(26) "[no description available]"
    ["path"]=>
    string(18) "vending_cpanel.net"
    ["name"]=>
    string(7) "Vending"
    ["desc"]=>
    string(26) "[no description available]"
  }
  [6]=>
  array(6) {
    [0]=>
    string(24) "admin/webinar_cpanel.net"
    [1]=>
    string(7) "Webinar"
    [2]=>
    string(26) "[no description available]"
    ["path"]=>
    string(18) "webinar_cpanel.net"
    ["name"]=>
    string(7) "Webinar"
    ["desc"]=>
    string(26) "[no description available]"
  }
}
