<?php
/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * It generates the status page for the admin panel.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.2 (January 2015)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

defined('IN_PLUCK') OR exit('Access denied!');
defined('CFC_DIR') OR exit('Invalid request!');

function contactform_captcha_pages_admin() {
	global $lang;
	return array(array(
		'func'  => 'status',
		'title' => $lang['contactform_captcha']['module_name']
	));
}

function contactform_captcha_page_admin_status() {
	global $lang;
	$aok = true;
?>
	<p>
		<strong><?php eu8x($lang['contactform_captcha']['module_intro']); ?></strong>
	</p>
<?php
	if (!extension_loaded('gd') || !function_exists('gd_info')) {
		$aok = false;
		show_error($lang['contactform_captcha']['error_nogd'], 1);
	} else {
		$gd = gd_info();
		if (!$gd['FreeType Support']) {
			$aok = false;
			show_error($lang['contactform_captcha']['error_nofreetype'], 1);
		}
		if (!$gd['PNG Support']) {
			$aok = false;
			show_error($lang['contactform_captcha']['error_nopng'], 1);
		}
	}
	if (!extension_loaded('mbstring')) {
		$aok = false;
		show_error($lang['contactform_captcha']['error_nombstring'], 1);
	}

	$captcha_charset = (string)module_get_setting('contactform_captcha', 'captcha_charset');
	$captcha_sensitive = (bool)(module_get_setting('contactform_captcha', 'captcha_sensitive') === 'true');
	$captcha_audio = (bool)(module_get_setting('contactform_captcha', 'captcha_audio') === 'true');
	$captcha_audiotheme = (string)module_get_setting('contactform_captcha', 'captcha_audiotheme');

	$missing = false;
	for ($i = 0; $i < strlen($captcha_charset); $i++) {
		if (!is_file(CFC_DIR . 'audio/' . $captcha_audiotheme . '/' . strtolower($captcha_charset[$i]) . '.wav')) {
			$aok = false;
			$missing = true;
			break;
		}
	}
	if ($missing) {
		$aok = false;
		show_error($lang['contactform_captcha']['error_nowavltr'], $captcha_audio ? 1 : 2);
	}
	if (!is_file(CFC_DIR . 'audio/' . $captcha_audiotheme . '/' . 'capital.wav')) {
		$aok = false;
		show_error($lang['contactform_captcha']['error_nowavcap'], $captcha_audio ? 1 : 2);
	}

	$backgrounds = read_dir_contents(CFC_DIR . 'audio/backgrounds/', 'files');
	$missing = true;
	foreach ($backgrounds as $file) {
		if (substr($file, -4) === '.wav') {
			$missing = false;
			break;
		}
	}
	if ($missing) {
		$aok = false;
		show_error($lang['contactform_captcha']['error_nowavbg'], 2);
	}

	if ($aok) show_error($lang['contactform_captcha']['error_aok'], 3);
?>
	<p><?php eu8x($lang['contactform_captcha']['module_help']); ?></p>
	<p><a href="?action=modules">&lt;&lt;&lt; <?php eu8x($lang['general']['back']); ?></a> &emsp; <a href="?action=modulesettings">&gt;&gt;&gt; <?php eu8x($lang['modules_settings']['title']); ?></a></p>
<?php
}
?>