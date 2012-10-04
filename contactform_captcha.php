<?php
/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * It is the main module file.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.0 (October 2012)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

defined('IN_PLUCK') OR exit('Access denied!');

define('CFC_DIR', str_replace('\\', '/', rtrim(dirname(__FILE__), '/\\') . '/'));
define('CFC_URL', str_replace('\\', '/', substr(rtrim(dirname($_SERVER['SCRIPT_FILENAME']), '/\\'), strlen(rtrim($_SERVER['DOCUMENT_ROOT'], '/\\')))) . '/data/modules/contactform_captcha/');

require_once(CFC_DIR . 'basic.php');


function contactform_captcha_info() {
	global $lang;

	return array(
		'name'          => $lang['contactform_captcha']['module_name'],
		'intro'         => $lang['contactform_captcha']['module_intro'],
		'version'       => '1.0',
		'author'        => 'Paul Voegler',
		'website'       => 'http://www.voegler.eu/',
		'icon'          => 'images/icon.png',
		'compatibility' => '4.7'
	);
}

function contactform_captcha_settings_default() {
	return array(
		'email_checkhost' => 'false',
		'captcha_enabled' => 'true',
		'captcha_audio' => 'true',
		'captcha_sensitive' => 'false',
		'captcha_codelen' => '3',
		'captcha_maxretries' => '3',
		'captcha_charset' => 'ACDEFGHJKLMNPRSTUVWXY345679',
		'captcha_font' => 'Action Man Bold.ttf',
		'captcha_height' => '64',
		'captcha_contrast' => '40',
		'captcha_sharpness' => '70',
		'captcha_audiotheme' => 'nato'
	);
}

function contactform_captcha_admin_module_settings_beforepost() {
	global $lang;

	$email_checkhost = (bool)(module_get_setting('contactform_captcha', 'email_checkhost') === 'true');
	$captcha_enabled = (bool)(module_get_setting('contactform_captcha', 'captcha_enabled') === 'true');
	$captcha_audio = (bool)(module_get_setting('contactform_captcha', 'captcha_audio') === 'true');
	$captcha_sensitive = (bool)(module_get_setting('contactform_captcha', 'captcha_sensitive') === 'true');
	$captcha_codelen = (int)module_get_setting('contactform_captcha', 'captcha_codelen');
	$captcha_maxretries = (int)module_get_setting('contactform_captcha', 'captcha_maxretries');
	$captcha_charset = (string)module_get_setting('contactform_captcha', 'captcha_charset');
	$captcha_font = (string)module_get_setting('contactform_captcha', 'captcha_font');
	$captcha_height = (int)module_get_setting('contactform_captcha', 'captcha_height');
	$captcha_contrast = (int)module_get_setting('contactform_captcha', 'captcha_contrast');
	$captcha_sharpness = (int)module_get_setting('contactform_captcha', 'captcha_sharpness');
	$captcha_audiotheme = (string)module_get_setting('contactform_captcha', 'captcha_audiotheme');

	$fonts = read_dir_contents(CFC_DIR . 'fonts/', 'files');
	$audiothemes = read_dir_contents(CFC_DIR . 'audio/', 'dirs');
?>
<span class="kop2"><?php eu8x($lang['contactform_captcha']['module_name']); ?></span>
<table>
	<tr>
		<td><input type="checkbox" name="cfc_email_checkhost" id="cfc_email_checkhost" value="true"<?php if ($email_checkhost) echo ' checked="checked"'; ?> /></td>
		<td>&emsp; <label for="cfc_email_checkhost"><?php eu8x($lang['contactform_captcha']['cfg_email_checkhost']); ?></label></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="cfc_captcha_enabled" id="cfc_captcha_enabled" value="true"<?php if ($captcha_enabled) echo ' checked="checked"'; ?> /></td>
		<td>&emsp; <label for="cfc_captcha_enabled"><?php eu8x($lang['contactform_captcha']['cfg_captcha_enable']); ?></label></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="cfc_captcha_audio" id="cfc_captcha_audio" value="true"<?php if ($captcha_audio) echo ' checked="checked"'; ?> /></td>
		<td>&emsp; <label for="cfc_captcha_audio"><?php eu8x($lang['contactform_captcha']['cfg_captcha_audio']); ?></label></td>
	</tr>
	<tr>
		<td><input type="checkbox" name="cfc_captcha_sensitive" id="cfc_captcha_sensitive" value="true"<?php if ($captcha_sensitive) echo ' checked="checked"'; ?> /></td>
		<td>&emsp; <label for="cfc_captcha_sensitive"><?php eu8x($lang['contactform_captcha']['cfg_captcha_sensitive']); ?></label></td>
	</tr>
	<tr>
		<td><input type="text" name="cfc_captcha_codelen" id="cfc_captcha_codelen" maxlength="2" size="3" value="<?php eu8x($captcha_codelen); ?>" /></td>
		<td>&emsp; <label for="cfc_captcha_codelen"><?php eu8x($lang['contactform_captcha']['cfg_captcha_codelen']); ?></label></td>
	</tr>
	<tr>
		<td><input type="text" name="cfc_captcha_maxretries" id="cfc_captcha_maxretries" maxlength="3" size="3" value="<?php eu8x($captcha_maxretries); ?>" /></td>
		<td>&emsp; <label for="cfc_captcha_maxretries"><?php eu8x($lang['contactform_captcha']['cfg_captcha_maxretries']); ?></label></td>
	</tr>
	<tr>
		<td><input type="text" name="cfc_captcha_charset" id="cfc_captcha_charset" maxlength="64" size="32" value="<?php eu8x($captcha_charset); ?>" /></td>
		<td>&emsp; <label for="cfc_captcha_charset"><?php eu8x($lang['contactform_captcha']['cfg_captcha_charset']); ?></label></td>
	</tr>
	<tr>
		<td><input type="text" name="cfc_captcha_height" id="cfc_captcha_height" maxlength="3" size="3" value="<?php eu8x($captcha_height); ?>" /></td>
		<td>&emsp; <label for="cfc_captcha_height"><?php eu8x($lang['contactform_captcha']['cfg_captcha_height']); ?></label></td>
	</tr>
	<tr>
		<td><input type="text" name="cfc_captcha_contrast" id="cfc_captcha_contrast" maxlength="3" size="3" value="<?php eu8x($captcha_contrast); ?>" /></td>
		<td>&emsp; <label for="cfc_captcha_contrast"><?php eu8x($lang['contactform_captcha']['cfg_captcha_contrast']); ?></label></td>
	</tr>
	<tr>
		<td><input type="text" name="cfc_captcha_sharpness" id="cfc_captcha_sharpness" maxlength="3" size="3" value="<?php eu8x($captcha_sharpness); ?>" /></td>
		<td>&emsp; <label for="cfc_captcha_sharpness"><?php eu8x($lang['contactform_captcha']['cfg_captcha_sharpness']); ?></label></td>
	</tr>
	<tr>
		<td><select name="cfc_captcha_font">
<?php
	foreach ($fonts as $font) {
		echo "\t\t\t<option value=\"" . u8x($font) . "\"" . ($font === $captcha_font ? ' selected="selected"' : '') . ">" . u8x($font) . "</option>\n";
	}
?>
		</select></td>
		<td>&emsp; <label for="cfc_captcha_font"><?php eu8x($lang['contactform_captcha']['cfg_captcha_font']); ?></label></td>
	</tr>
	<tr>
		<td><select name="cfc_captcha_audiotheme">
<?php
	foreach ($audiothemes as $theme) {
		if ($theme !== 'backgrounds') echo "\t\t\t<option value=\"" . u8x($theme) . "\"" . ($theme === $captcha_audiotheme ? ' selected="selected"' : '') . ">" . u8x($theme) . "</option>\n";
	}
?>
		</select></td>
		<td>&emsp; <label for="cfc_captcha_audiotheme"><?php eu8x($lang['contactform_captcha']['cfg_captcha_audiotheme']); ?></label></td>
	</tr>
</table><br />
<?php
}

function contactform_captcha_admin_module_settings_afterpost() {
	$settings = array(
		'email_checkhost' => (string)(isset($_POST['cfc_email_checkhost']) ? 'true' : 'false'),
		'captcha_enabled' => (string)(isset($_POST['cfc_captcha_enabled']) ? 'true' : 'false'),
		'captcha_audio' => (string)(isset($_POST['cfc_captcha_audio']) ? 'true' : 'false'),
		'captcha_sensitive' => (string)(isset($_POST['cfc_captcha_sensitive']) ? 'true' : 'false'),
		'captcha_codelen' => (string)min(99, max(1, (int)trim(u8gpc($_POST['cfc_captcha_codelen'])))),
		'captcha_maxretries' => (string)min(999, max(1, (int)trim(u8gpc($_POST['cfc_captcha_maxretries'])))),
		'captcha_charset' => (string)substr(trim(u8gpc($_POST['cfc_captcha_charset'])), 0, 255),
		'captcha_font' => (string)substr(u8gpc(@$_POST['cfc_captcha_font']), 0, 255),
		'captcha_height' => (string)min(999, max(10, (int)trim(u8gpc($_POST['cfc_captcha_height'])))),
		'captcha_contrast' => (string)min(100, max(0, (int)trim(u8gpc($_POST['cfc_captcha_contrast'])))),
		'captcha_sharpness' => (string)min(100, max(0, (int)trim(u8gpc($_POST['cfc_captcha_sharpness'])))),
		'captcha_audiotheme' => (string)substr(u8gpc(@$_POST['cfc_captcha_audiotheme']), 0, 255)
	);
	module_save_settings('contactform_captcha', $settings);
}

function message_flow($message, $width = 78) {
	$message = preg_split('/ \x0D\x0A | [\x0A\x0D] /xs', $message);

	foreach ($message as &$value) {
		$value = preg_replace('/^ \x3E | \x20 | From\x20 /xs', ' $1', $value); // space stuffing
		$value = wordwrap(rtrim($value), $width, " \r\n", false);
	}
	unset($value);

	return implode("\r\n", $message);
}

function encode_mimeheader_word($word, $wordlength = 75) {
	$out = '';
	$curr = '';

	$maxchars = (int)(($wordlength - 12) / 4) * 3;
	while (strlen($word) > 0) {
		$i = $maxchars;
		while ($i >= 0 && ($curr = substr($word, 0, $i--)) !== mb_convert_encoding($curr, 'UTF-8', 'UTF-8'));
		if ($curr === '') {
			return false;
		} else {
			$word = substr($word, $i + 1);
			$out .= '=?UTF-8?B?' . base64_encode($curr) . "?=\r\n ";
		}
	}

	return substr($out, 0, -3);
}

function encode_mimeheader($text, $linelength = 78) {
	$callback = create_function('$matches', 'return encode_mimeheader_word($matches[0], ' . min(75, $linelength - 1) . ');');

	$out = preg_replace_callback('/ [^ \r\n]*? [\x80-\xFF] [^ \r\n]* /xs', $callback, $text);

	return wordwrap($out, $linelength, "\r\n ", false);
}
?>