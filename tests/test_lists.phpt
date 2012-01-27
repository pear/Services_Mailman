--TEST--
Test for Services_Mailman lists
--FILE--
<?php

// settings
$testURL = 'http://mail.cpanel.net/mailman/admin';

require_once 'Services/Mailman.php';

$mailman = new Services_Mailman($testURL);
$lists=$mailman->lists();
print_r($lists);

?>
--EXPECT--

Array
(
    [0] => Array
        (
            [0] => admin/cpanelpm_cpanel.net
            [1] => cPanelpm
            [2] => [no description available]
            [path] => cpanelpm_cpanel.net
            [name] => cPanelpm
            [desc] => [no description available]
        )
 
    [1] => Array
        (
            [0] => admin/giveaway_cpanel.net
            [1] => Giveaway
            [2] => [no description available]
            [path] => giveaway_cpanel.net
            [name] => Giveaway
            [desc] => [no description available]
        )
 
    [2] => Array
        (
            [0] => admin/unleashvip_cpanel.net
            [1] => Unleashvip
            [2] => [no description available]
            [path] => unleashvip_cpanel.net
            [name] => Unleashvip
            [desc] => [no description available]
        )
 
    [3] => Array
        (
            [0] => admin/vending_cpanel.net
            [1] => Vending
            [2] => [no description available]
            [path] => vending_cpanel.net
            [name] => Vending
            [desc] => [no description available]
        )
 
    [4] => Array
        (
            [0] => admin/webinar_cpanel.net
            [1] => Webinar
            [2] => [no description available]
            [path] => webinar_cpanel.net
            [name] => Webinar
            [desc] => [no description available]
        )
 
)
 