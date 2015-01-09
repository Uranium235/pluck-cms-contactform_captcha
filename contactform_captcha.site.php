<?php
/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * Is generates the form and handles the request.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.0 (October 2012)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

defined('IN_PLUCK') OR exit('Access denied!');
defined('CFC_URL') OR exit('Invalid request!');


function contactform_captcha_site_theme(&$page_theme) {
	if (module_is_included_in_page('contactform_captcha', CURRENT_PAGE_SEONAME)) {
		header('Date: '.gmdate('r'), true);
		header('Expires: '.gmdate('r', time() - 86400), true);
		header('Last-Modified: '.gmdate('r'), true);
		header('Cache-Control: no-cache,no-store,max-age=0,must-revalidate,post-check=0,pre-check=0', true);
		header('Pragma: no-cache', true);
		header('Vary: *', true);
		header('Accept-Charset: UTF-8,UTF-16BE;q=0.8,UTF-16;q=0.75,UTF-32BE;q=0.7,UTF-32;q=0.65,Windows-1252;q=0.5,ISO-8859-1;q=0.3,ISO-8859-15;q=0.1,*;q=0', true);
	}
}

function contactform_captcha_theme_meta() {
	echo '<link href="' . CFC_URL . 'style_defaults.css" rel="stylesheet" type="text/css" />'."\n";
}

function contactform_captcha_theme_main() {
	global $lang;

	$name = '';
	$email = '';
	$subject = '';
	$message = '';
	$captcha = '';
	$logo_show = (bool)(module_get_setting('contactform_captcha', 'logo_show') === 'true');
	$email_checkhost = (bool)(module_get_setting('contactform_captcha', 'email_checkhost') === 'true');
	$captcha_enabled = (bool)(module_get_setting('contactform_captcha', 'captcha_enabled') === 'true');
	$captcha_audio = (bool)(module_get_setting('contactform_captcha', 'captcha_audio') === 'true');
	$captcha_height = (int)module_get_setting('contactform_captcha', 'captcha_height');
	$captcha_codelen = (int)module_get_setting('contactform_captcha', 'captcha_codelen');

	if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		// fetch fields
		$name = (string)substr(trim(u8gpc(@$_POST['name'])), 0, 255);
		$email = (string)substr(trim(u8gpc(@$_POST['email'])), 0, 254);
		$subject = (string)substr(trim(u8gpc(@$_POST['subject'])), 0, 255);
		$message = (string)substr(trim(u8gpc(@$_POST['message'])), 0, 10240);
		$captcha = (string)substr(trim(u8gpc(@$_POST['captcha'])), 0, 255);

		// validate fields
		$valid = true;
		if (!preg_match('/^[^\x00-\x1F\x7F]{1,255}$/', $name)) {
			$valid = false;
			echo '<p class="error">' . u8x($lang['contactform_captcha']['field_invalid']) . "name</p>\n";
		}
		if (!preg_match('/^[a-zA-Z0-9.!#$%&\'*+\/=?\^_`{|}~\-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/', $email) || strlen($email) > 254) {
			$valid = false;
			echo '<p class="error">' . u8x($lang['contactform_captcha']['field_invalid']) . "email</p>\n";
		} elseif ($email_checkhost) {
			$host = substr($email, strpos($email, '@') + 1);
			$mxhosts = array();
			if (!preg_match('/^(?:\d{1,3}\.){3}\d{1,3}$/', $host) && !(function_exists('getmxrr') && getmxrr($host, $mxhosts)) && $host === gethostbyname($host)) {
				$valid = false;
				echo '<p class="error">' . u8x($lang['contactform_captcha']['email_invalid']) . "</p>\n";
			}
		}
		if (!preg_match('/^[^\x00-\x1F\x7F]{1,255}$/', $subject)) {
			$valid = false;
			echo '<p class="error">' . u8x($lang['contactform_captcha']['field_invalid']) . "subject</p>\n";
		}
		if (!preg_match('/^[^\x00-\x08\x0B\x0C\x0E-\x1F\x7F]{1,10240}$/', $message)) {
			$valid = false;
			echo '<p class="error">' . u8x($lang['contactform_captcha']['field_invalid']) . "message</p>\n";
		}

		// validate CAPTCHA
		$captcha_valid = false;
		if ($captcha_enabled) {
			if (!session_id()) session_start();
			$captcha_code = (string)@$_SESSION['captcha_code'];
			$captcha_sensitive = (bool)@$_SESSION['captcha_sensitive'];
			unset($_SESSION['captcha_code']);
			unset($_SESSION['captcha_retries']);
			unset($_SESSION['captcha_sensitive']);
			if (strlen($captcha) > 0 && strlen($captcha_code) > 0) {
				if ($captcha_sensitive) {
					$captcha_valid = $captcha === $captcha_code;
				} else {
					$captcha_valid = strtolower($captcha) === strtolower($captcha_code);
				}
			}
			if (!$captcha_valid) {
				$valid = false;
				echo '<p class="error">' . u8x($lang['contactform_captcha']['captcha_invalid']) . "</p>\n";
			}
		}


		// send the message
		if ($valid) {
			$headers = 'From: ' . encode_mimeheader_word('"' . str_replace('"', "'", $name) . '"', 72) . ' <' . $email . '>' . "\r\n";
			$headers .= 'X-Originating-IP: [' . $_SERVER['REMOTE_ADDR'] . "]\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-type: text/plain;charset=UTF-8;format=flowed\r\n";
			$headers .= "Content-Transfer-Encoding: 8bit";

			$sendmail = '';
			if(!empty($_SERVER['SERVER_ADMIN'])) {
				$headers .= "\r\nSender: " . trim($_SERVER['SERVER_ADMIN']);
				$sendmail = '-f' . trim($_SERVER['SERVER_ADMIN']);
			}

			if (@mail(EMAIL, encode_mimeheader($lang['contactform_captcha']['email_title'] . ' (' . (string)$_SERVER['SERVER_NAME'] . '): ' . $subject, 69), message_flow($message, 78), $headers, $sendmail)) {
				echo '<p class="success">' . u8x($lang['contactform_captcha']['send_success']) . "</p>\n";
				$name = '';
				$email = '';
				$subject = '';
				$message = '';
			} else {
				echo '<p class="error">' . u8x($lang['contactform_captcha']['send_error']) . "</p>\n";
			}
		} else {
			echo '<p class="error">' . u8x($lang['contactform_captcha']['send_error']) . "</p>\n";
		}
	}

	// show the form
	$uid = urlencode(uniqid());
?>
<script type="text/javascript" src="<?php eu8x(CFC_URL); ?>contactform_captcha.js"></script>
<form id="contactform_captcha" method="post" accept-charset="UTF-8 UTF-16BE UTF-16 UTF-32BE UTF-32 Windows-1252 ISO-8859-1 ISO-8859-15" enctype="multipart/form-data" action="<?php eu8x(htmlspecialchars($_SERVER['REQUEST_URI'])); ?>" onsubmit="return cfc_formCheck(this, false, '<?php eu8x(CFC_URL); ?>captcha_json.php', 'cfc_captcha_update');">
	<h2 id="contactform_captcha_error" class="error" style="display: none;"><?php eu8x($lang['contactform_captcha']['form_error']); ?></h2>
	<fieldset>
		<input name="charset_check" type="hidden" value="&auml;&Scaron;" />
<?php if ($logo_show) { ?>
		<img id="cfc_logo" src="<?php eu8x(CFC_URL); ?>images/logo.png" width="160" height="104" alt="mail" />
<?php } ?>
		<ol>
			<li>
				<label for="cfc_name"><?php eu8x($lang['general']['name']); ?> <span id="cfc_name_error" class="error" style="display: none;"><?php eu8x($lang['contactform_captcha']['name_error']); ?></span></label>
				<input id="cfc_name" name="name" type="text" size="30" maxlength="255" tabindex="1" value="<?php eu8x($name); ?>" required="required" placeholder="<?php eu8x($lang['contactform_captcha']['name_placeholder']); ?>" />
			</li>
			<li>
				<label for="cfc_email"><?php eu8x($lang['general']['email']); ?> <span id="cfc_email_error" class="error" style="display: none;"><?php eu8x($lang['contactform_captcha']['email_error']); ?></span></label>
				<input id="cfc_email" name="email" type="email" size="30" maxlength="254" tabindex="2" value="<?php eu8x($email); ?>" required="required" placeholder="<?php eu8x($lang['contactform_captcha']['email_placeholder']); ?>" />
			</li>
			<li>
				<label for="cfc_subject"><?php eu8x($lang['contactform_captcha']['subject']); ?> <span id="cfc_subject_error" class="error" style="display: none;"><?php eu8x($lang['contactform_captcha']['subject_error']); ?></span></label>
				<input id="cfc_subject" name="subject" type="text" size="48" maxlength="255" tabindex="3" value="<?php eu8x($subject); ?>" required="required" placeholder="<?php eu8x($lang['contactform_captcha']['subject_placeholder']); ?>" />
			</li>
			<li>
				<label for="cfc_message"><?php eu8x($lang['general']['message']); ?> <span id="cfc_message_error" class="error" style="display: none;"><?php eu8x($lang['contactform_captcha']['message_error']); ?></span></label>
				<textarea id="cfc_message" name="message" maxlength="10240" cols="46" rows="8" wrap="soft" tabindex="4" required="required" placeholder="<?php eu8x($lang['contactform_captcha']['message_placeholder']); ?>" ><?php eu8x($message); ?></textarea>
			</li>
			<li>
<?php if ($captcha_enabled) { ?>
				<label for="cfc_captcha"><?php eu8x($lang['contactform_captcha']['captcha']); ?> <span id="cfc_captcha_error" class="error" style="display: none;"><?php eu8x($lang['contactform_captcha']['captcha_error']); ?></span><span id="cfc_captcha_error_invalid" class="error" style="display: none;"><?php eu8x($lang['contactform_captcha']['captcha_invalid']); ?></span></label>
				<img id="cfc_captcha_img" src="<?php eu8x(CFC_URL); ?>captcha_png.php?uid=<?php eu8x($uid); ?>" height="<?php eu8x($captcha_height); ?>" alt="CAPTCHA" />
				<input id="cfc_captcha" name="captcha" type="text" size="<?php eu8x($captcha_codelen); ?>" maxlength="<?php eu8x($captcha_codelen); ?>" tabindex="5" value="" required="required" placeholder="<?php eu8x($lang['contactform_captcha']['captcha_placeholder']); ?>" />
				<a id="cfc_captcha_update" href="#" title="<?php eu8x($lang['contactform_captcha']['update']); ?>" tabindex="7" onclick="this.blur(); return captchaUpdate(this, 'cfc_captcha_img', 'cfc_captcha_audio', 'cfc_captcha_play');"><img src="<?php eu8x(CFC_URL); ?>images/update.png" alt="update" width="64" height="32" /></a>
<?php if ($captcha_audio) { ?>
				<a id="cfc_captcha_play" href="<?php eu8x(CFC_URL); ?>captcha_wav.php?uid=<?php eu8x($uid); ?>" title="<?php eu8x($lang['contactform_captcha']['play']); ?>" tabindex="8" onclick="this.blur(); return captchaPlay(this, 'cfc_captcha_audio');"><img src="<?php eu8x(CFC_URL); ?>images/speaker.png" alt="speaker" width="160" height="32" /></a>
				<audio id="cfc_captcha_audio" src="<?php eu8x(CFC_URL); ?>captcha_wav.php?uid=<?php eu8x($uid); ?>" preload="none" onwaiting="document.getElementById('cfc_captcha_play').className='loading';" onplaying="document.getElementById('cfc_captcha_play').className='playing';" onended="document.getElementById('cfc_captcha_play').className='';" style="display: none;"></audio>
<?php }} ?>
				<input id="cfc_submit" type="submit" value="<?php eu8x($lang['general']['send']); ?>" tabindex="6" onclick="this.blur(); return true;" />
			</li>
		</ol>
	</fieldset>
</form>
<?php
}
?>