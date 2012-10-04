<?php
/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * It provides an AJAX endpoint for code verification.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.0 (October 2012)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

require_once(rtrim(dirname(__FILE__), '/\\') . '/basic.php');


session_cache_limiter('');
session_start();

$captcha_check = (string)substr(trim(u8gpc(@$_REQUEST['code'])), 0, 255);
$captcha_code = (string)@$_SESSION['captcha_code'];
$captcha_retries = (int)@$_SESSION['captcha_retries'];
$captcha_sensitive = (bool)@$_SESSION['captcha_sensitive'];

$captcha_valid = false;
if (strlen($captcha_check) > 0 && strlen($captcha_code) > 0 && $captcha_retries > 0) {
	$captcha_retries--;
	$_SESSION['captcha_retries'] = $captcha_retries;
	if ($captcha_sensitive) {
		$captcha_valid = $captcha_check === $captcha_code;
	} else {
		$captcha_valid = strtolower($captcha_check) === strtolower($captcha_code);
	}
} elseif (strlen($captcha_check) > 0) {
	unset($_SESSION['captcha_code']);
	unset($_SESSION['captcha_retries']);
	unset($_SESSION['captcha_sensitive']);
}

header('Date: '.gmdate('r'), true);
header('Expires: '.gmdate('r', time() - 86400), true);
header('Last-Modified: '.gmdate('r'), true);
header('Cache-Control: no-cache,no-store,max-age=0,must-revalidate,post-check=0,pre-check=0', true);
header('Pragma: no-cache', true);
header('Vary: *', true);
header('Content-Type: application/json;charset=UTF-8', true, 200);

echo
'{
	"version": "1.0",
	"encoding": "UTF-8",
	"valid": ' . json_encode($captcha_valid) . ',
	"code": ' . json_encode($captcha_check) . ',
	"retries": ' . json_encode($captcha_retries) . ',
	"sensitive": ' . json_encode($captcha_sensitive) . '
}';
?>