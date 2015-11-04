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
array(13) {
  [0]=>
  array(6) {
    [0]=>
    string(19) "cpanelpm_cpanel.net"
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
    string(21) "edge-users_cpanel.net"
    [1]=>
    string(10) "Edge-Users"
    [2]=>
    string(28) "Edge-Users (invitation only)"
    ["path"]=>
    string(21) "edge-users_cpanel.net"
    ["name"]=>
    string(10) "Edge-Users"
    ["desc"]=>
    string(28) "Edge-Users (invitation only)"
  }
  [2]=>
  array(6) {
    [0]=>
    string(19) "giveaway_cpanel.net"
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
  [3]=>
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
  [4]=>
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
  [5]=>
  array(6) {
    [0]=>
    string(17) "python_cpanel.net"
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
  [6]=>
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
  [7]=>
  array(6) {
    [0]=>
    string(18) "uidtest_cpanel.net"
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
  [8]=>
  array(6) {
    [0]=>
    string(21) "unleashvip_cpanel.net"
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
  [9]=>
  array(6) {
    [0]=>
    string(18) "vending_cpanel.net"
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
  [10]=>
  array(6) {
    [0]=>
    string(18) "webinar_cpanel.net"
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
  [11]=>
  array(6) {
    [0]=>
    string(23) "weekendlunch_cpanel.net"
    [1]=>
    string(12) "Weekendlunch"
    [2]=>
    string(13) "Weekend Lunch"
    ["path"]=>
    string(23) "weekendlunch_cpanel.net"
    ["name"]=>
    string(12) "Weekendlunch"
    ["desc"]=>
    string(13) "Weekend Lunch"
  }
  [12]=>
  array(6) {
    [0]=>
    string(17) "xmlapi_cpanel.net"
    [1]=>
    string(6) "xmlapi"
    [2]=>
    string(8) "cPXMLAPI"
    ["path"]=>
    string(17) "xmlapi_cpanel.net"
    ["name"]=>
    string(6) "xmlapi"
    ["desc"]=>
    string(8) "cPXMLAPI"
  }
}