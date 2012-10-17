<?php
/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * It provides basic functions for character set handling. Automatically sets some PHP options.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.0 (October 2012)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

function detect_charset_gpc() {
	$check = '';
	$charset_gpc = '';
	if (!empty($_POST['charset_check'])) {
		$check = bin2hex($_POST['charset_check']);
	} elseif (!empty($_GET['charset_check'])) {
		$check = bin2hex($_GET['charset_check']);
	} elseif (!empty($_COOKIE['charset_check'])) {
		$check = bin2hex($_COOKIE['charset_check']);
	/*} else {
		setcookie('charset_check', "äŠ", 0, '/', '', false, false);*/
	}

	switch ($check) {
		case 'c3a4c5a0':	// UTF-8 for "äŠ", "\xC3\xA4\xC5\xA0", "&auml;&Scaron;"
			$charset_gpc = 'UTF-8';
			break;
		case 'e48a':
			$charset_gpc = 'Windows-1252'; // or ISO-8859-1, which is a subset of Windows-1252
			break;
		case 'e4a6':
			$charset_gpc = 'ISO-8859-15';
			break;
		case 'e4a9':
			$charset_gpc = 'ISO-8859-2';
			break;
		case '00e40160':
			$charset_gpc = 'UTF-16BE';
			break;
		case 'e4006001':
			$charset_gpc = 'UTF-16LE';
			break;
		case '000000e400000160':
			$charset_gpc = 'UTF-32BE';
			break;
		case 'e400000060010000':
			$charset_gpc = 'UTF-32LE';
			break;
		default:
			//trigger_error('Get / Post / Cookie character set not supported. Expected UTF-8 encoding!', E_USER_WARNING);
			break;
	}

	if (!$charset_gpc && ini_get('mbstring.encoding_translation') && function_exists('mb_internal_encoding')) {
		$charset_gpc = strtoupper(mb_internal_encoding());
	}

	if (!$charset_gpc && isset($_SERVER['CONTENT_TYPE'])) {
		$matches = array();
		if (preg_match('/charset\\s*\\=\\s*([a-zA-Z0-9\\-]+)/i', $_SERVER['CONTENT_TYPE'], $matches)) $charset_gpc = strtoupper($matches[1]);
	}

	if (!defined('CHARSET_GPC')) define('CHARSET_GPC', $charset_gpc);

	return $charset_gpc;
}

function set_default_settings () {
	/*if (function_exists('set_magic_quotes_runtime')) @set_magic_quotes_runtime(false);
	ini_set('magic_quotes_runtime', 0);
	//ini_set('magic_quotes_sybase', 0); //skip to allow detection - magic_quotes_runtime disables this anyway
	if (function_exists('date_default_timezone_set')) date_default_timezone_set('UTC');
	ini_set('date.timezone', 'UTC');*/
	ini_set('default_mimetype', 'text/html');
	ini_set('default_charset', 'UTF-8');
	if (function_exists('mb_internal_encoding')) mb_internal_encoding('UTF-8');
	ini_set('mbstring.internal_encoding', 'UTF-8');
	if (function_exists('mb_regex_encoding')) mb_regex_encoding('UTF-8');
	ini_set('mbstring.regex_encoding', 'UTF-8');
	if (function_exists('mb_http_output')) mb_http_output('pass');
	ini_set('mbstring.http_output', 'pass');
	if (function_exists('iconv_set_encoding')) {
		iconv_set_encoding('input_encoding', 'UTF-8');
		iconv_set_encoding('internal_encoding', 'UTF-8');
		iconv_set_encoding('output_encoding', 'UTF-8');
	}
	ini_set('iconv.input_encoding', 'UTF-8');
	ini_set('iconv.internal_encoding', 'UTF-8');
	ini_set('iconv.output_encoding', 'UTF-8');
}

function check_encoding($string, $encoding = 'UTF-8') {
	return $string === mb_convert_encoding($string, $encoding, $encoding);
}

function is_ascii($string, $printableAsciiOnly = true) {
	if ($printableAsciiOnly) {
		return preg_match('/^ [\x09\x0A\x0D\x20-\x7E]* $/xs', $string);
	} else {
		return preg_match('/^ [\x00-\x7F]* $/xs', $string);
	}
}

function detect_nonascii($string) {
	return preg_match('/ [\x80-\xFF] /xs', $string);
}

function detect_windows1252($string) {
	return preg_match('/ [\x80\x82-\x8C\x8E\x91-\x9C\x9E\x9F] /xs', $string); // search for windows-1252 specific characters which are control only in some iso-8859
}

function is_windows1252($string) {
	return preg_match('/^ [\x09\x0A\x0D\x20-\x7E\x80\x82-\x8C\x8E\x91-\x9C\x9E\x9F-\xFF]* $/xs', $string);
}

function is_iso8859($string) {
	return preg_match('/^ [\x09\x0A\x0D\x20-\x7E\xA0-\xFF]* $/xs', $string);
}

function detect_utf8($string) {
	// Modified from http://w3.org/International/questions/qa-forms-utf-8.html
	return preg_match('/^ (?>[\x00-\x7F]*) (?>  # skip all 7-Bit ASCII characters (without backtracking) until non ASCII found and then either...
		  [\xC2-\xDF][\x80-\xBF]                # non-overlong 2-byte
		| \xE0[\xA0-\xBF][\x80-\xBF]            # excluding overlongs
		| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2}     # straight 3-byte
		| \xED[\x80-\x9F][\x80-\xBF]            # excluding surrogates
		| \xF0[\x90-\xBF][\x80-\xBF]{2}         # planes 1-3
		| [\xF1-\xF3][\x80-\xBF]{3}             # planes 4-15
		| \xF4[\x80-\x8F][\x80-\xBF]{2}         # plane 16
		| $                                     # ...or end of of string - plain ASCII is UTF-8 too!
	) /xs', $string);
}

function detect_encoding($string) {
	if (detect_utf8($string)) {
		return 'UTF-8';
	} else {
		if (detect_windows1252($string)) {
			return 'Windows-1252';
		} else {
			return 'ISO-8859-1';
		}
	}
}

function is_utf8($string, $printableAsciiOnly = false) {
	// Modified from http://w3.org/International/questions/qa-forms-utf-8.html
	if ($printableAsciiOnly) {
		$ascii = '[\x09\x0A\x0D\x20-\x7E]';
	} else {
		$ascii = '[\x00-\x7F]';
	}

	return preg_match('/^ (?>'.$ascii.'     # ASCII
		| [\xC2-\xDF][\x80-\xBF]            # non-overlong 2-byte
		| \xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
		| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
		| \xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
		| \xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
		| [\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
		| \xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
	)* $/xs', $string);
}

function encodeToUtf8($string) {
	return mb_convert_encoding($string, "UTF-8", detect_encoding($string));
}

function encodeToIso($string) {
	return mb_convert_encoding($string, "Windows-1252", detect_encoding($string));
}

function u8gpc($string, $inputcharset = '') {
	if (!$inputcharset && defined('CHARSET_GPC')) $inputcharset = CHARSET_GPC;

	if ($inputcharset !== 'UTF-8') {
		if ($inputcharset) {
			$string = mb_convert_encoding($string, 'UTF-8', $inputcharset);
		} else {
			$string = encodeToUtf8($string);
		}
	}

	if (!is_utf8($string, true)) {
		trigger_error('Invalid characters sent!', E_USER_ERROR);
		return false;
	}

	return $string;
}

function u8x($string) {
	//return str_replace("\r", '', htmlspecialchars($string, ENT_COMPAT, 'UTF-8', true));
	return strtr($string, array('&' => '&amp;', '"' => '&quot;', '<' => '&lt;', '>' => '&gt;', "\r" => ''));
}

function eu8x($string) {
	echo strtr($string, array('&' => '&amp;', '"' => '&quot;', '<' => '&lt;', '>' => '&gt;', "\r" => ''));
}


detect_charset_gpc();
set_default_settings();
?>