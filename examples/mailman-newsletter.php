<?php

//Settings
$notice='';
$_mmurl='http://example.co.uk/mailman/admin';
$_mmlist='newsletter_example.co.uk';
$_mmpw='password-cannot-have-spaces';
$_mmsub='Yey! Thanks for joining our newsletter.';
$_mmunsub='Sorry to see you go :(';
$_mmerror='There was some kind of error, check and try again.';

//Logic
	if ($_POST) {
		$_email=filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if ($_email) {
            require 'Services/Mailman.php';
            $mm=new Services_Mailman($_mmurl,$_mmlist,$_mmpw);
            $notice = $_mmsub;
            if ($_POST['sub'] == 1) {
                try {
                    $mm->subscribe($_email);
                } catch (Services_Mailman_Exception $e) {
                    $notice = $_mmerror;
                }
            }
            elseif ($_POST['sub'] == 0) {
                try {
                    $mm->unsubscribe($_email);
                } catch (Services_Mailman_Exception $e) {
                    $notice = $_mmerror;
                }
            }
        } else {
            $notice=$_mmerror;
        }
	}
	unset($_mmpw);

//Markup
?>
<h2>Newsletter</h2>
<?php if ($notice) {?><div class="notice"><?=$notice?></div><?php } ?>
<form method="post" id="newsletter_form" action="">
	<div>
		<label for="input_email">Email address:</label>
		<input type="text" name="email" id="input_email">
	</div>
	<div>
		<input type="radio" id="input_sub" name="sub" value="1" checked>
		<label for="input_sub">Subscribe</label>
		<br>
		<input type="radio" id="input_unsub" name="sub" value="0">
		<label for="input_unsub">Unsubscribe</label>
	</div>
	<input type="submit" value="Submit &raquo;" id="submit">
</form>