<?php
/*
*@author Henrik Huckauf
*/

require './includes/DEF.php';

$vorname = '';
$nachname = '';
$alter = '';
$email = '';
$message = '';

$output = '';

if(isset($_POST['set']) && $_POST['set'] == '1')
{
	$vorname = $_POST['vorname'];
	$nachname = $_POST['nachname'];
	$alter = $_POST['alter'];
	$email = $_POST['email'];
	$message = $_POST['message'];

	$IP = $_SERVER['REMOTE_ADDR'];
	$BROWSER = $_SERVER['HTTP_USER_AGENT'];
	$USERLANGUAGE = $_SERVER['HTTP_ACCEPT_LANGUAGE'];
	$date = date("d.m.Y");
	$time = date("H:i");

	$error = false;
	if(empty($vorname))
	{
		$output .= "<red>" . $wlang['contact_err_preName'] . "</red><br>";
		$error = true;
	}	
	if(empty($nachname))
	{
		$output .= "<red>" . $wlang['contact_err_postName'] . "</red><br>";
		$error = true;
	}	
	if(!empty($alter) && !is_numeric($alter))
	{
		$output .= "<red>" . $wlang['contact_err_age'] . "</red><br>";
		$error = true;
	}	
	if(!empty($email))
	{
		$emailParts = explode("@", $email);
		$emailPartsCount = count($emailParts);
		$dotCount = count(explode(".", $emailParts[1]));
	
		if($emailPartsCount != 2 || $dotCount < 2)
		{
			$output .= "<red>" . $wlang['contact_err_email'] . "</red><br>";
			$error = true;
		}
	}
	if(empty($message))
	{
		$output .= "<red>" . $wlang['contact_err_message'] . "</red><br>";
		$error = true;
	}
	
	// session_start(); // ganz oben bevor header gesendet wird
	if(strtoupper($_POST['captcha_code']) != $_SESSION['captcha_spam'])
	{
		$output .= "<red>" . $wlang['contact_err_captcha'] . "</red><br>";
		$error = true;
	}

	
    if(!$error)
	{ 
		$infos = $date . ", " . $time . "
Name: " . $vorname . " " . $nachname . "
Alter: " . $alter . "
E-Mail: " . $email . "
IP: " . $IP . "
Browser: " . $BROWSER . "
Sprache: " . $USERLANGUAGE . "
Nachricht: " . $message . "

";
	
		$subject = "Nachicht an Team von " . $vorname . " " . $nachname;
		$mailMessage = $infos;
		$mailFrom = "From: TueGutes";
		
		/*$array = array(); // admin mails
		$count = count($array);
		for($i = 0; $i < $count; $i++)
			mail($array[$i], $subject, $mailMessage, $mailFrom);*/
		sendEmail("irgendeine-admin@mail.de", $subject, $mailMessage);

		$output .= "<green>" . $wlang['contact_suc_sent'] . "</green><br>";
		
		// Felder resetten
		$vorname = '';
		$nachname = '';
		$alter = '';
		$email = '';
		$message = '';
	}
}

require './includes/_top.php';
?>

<h2><?php echo $wlang['contact_head']; ?></h2>

<div id='contact'>
	<div><?php echo $output; ?><br></div>
	<form id='messageForm' method='post' class='block'>
		<input type='text' name='vorname' value='<?php echo $vorname; ?>' size='40' placeholder='<?php echo $wlang['contact_form_preName']; ?>' required />&nbsp;*<br>
		<br>
		<input type='text' name='nachname' value='<?php echo $nachname; ?>' size='40' placeholder='<?php echo $wlang['contact_form_postName']; ?>' required />&nbsp;*<br>
		<br>
		<input type='text' name='alter' value='<?php echo $alter; ?>' size='6' placeholder='<?php echo $wlang['contact_form_age']; ?>' /> <?php echo $wlang['contact_form_years']; ?><br>
		<br>
		<input type='email' name='email' value='<?php echo $email; ?>' size='40' placeholder='<?php echo $wlang['contact_form_email']; ?>' /><br>
		<br>
		<textarea cols='42' rows='10' name='message' size='20' placeholder='<?php echo $wlang['contact_form_message']; ?>' required><?php echo $message; ?></textarea>&nbsp;*<br>
		* = <?php echo $wlang['contact_form_mandatoryField']; ?><br>
		<div class='center'>
			<img id='captcha_image' class='block' src='./includes/captcha/captcha.php' alt='Captcha...' title='<?php echo $wlang['contact_captcha_title']; ?>' width='140' height='40' /><br>
			<br>
			<span id='captcha_reload'>&#8635;</span><br>
			<input type='text' name='captcha_code' size='10' placeholder='Code' required autocomplete='off' />&nbsp;*
			<br><br>
			<input type='hidden' name='set' value='1' />
			<input type='submit' value='<?php echo $wlang['contact_form_submit']; ?>'>
		</div>
	</form>
</div>

<script type='text/javascript'>
	$(document).ready(function()
	{
		$('#captcha_reload').click(function(e)
		{
			reloadCaptchaImage();
		});
	});
	function reloadCaptchaImage()
	{
		$('#captcha_image').attr('src', './includes/captcha/captcha.php?' + (new Date().getTime())); // + '? + (new Date().getTime())', da der Browser das Bild cached... Also muss sich die URL jedes Mal ändern, damit es für den Browser eine neue URL ist
	}
</script>


<?php
require './includes/_bottom.php';
?>