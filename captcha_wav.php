<?php
/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * It generates the wav audio file and CAPTCHA code if necessary.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.0 (October 2012)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

require_once(rtrim(dirname(__FILE__), '/\\') . '/WavFile.php');


function mt_rand_oi($a = 0, $b = 1) {
	//$max = min(0x7FFFFFFF, mt_getrandmax());
	$max = 0x7FFFFFFF;

	$x = mt_rand(0, $max - 1);

	return $a + ($b - $a) * ($x / $max);
}

function audioError(Exception $e) {
	//throw $e;
	global $audio_path;
	header('Content-Type: audio/x-wav', true, 200);
	header('Content-Disposition: attachment;filename="error.wav"', true);
	header('Content-Length: ' . filesize($audio_path . 'error.wav'), true);
	readfile($audio_path . 'error.wav');
	exit(1);
}


$captcha_sensitive = 'false';
$captcha_codelen = 3;
$captcha_maxretries = 3;
$captcha_charset = 'ACDEFGHJKLMNPRSTUVWXY345679';
$captcha_audiotheme = 'nato';
include(rtrim(dirname(__FILE__), '/\\') . '/../../settings/contactform_captcha.settings.php');
$captcha_sensitive = (bool)($captcha_sensitive === 'true');
$captcha_codelen = (int)$captcha_codelen;
$captcha_maxretries = (int)$captcha_maxretries;
$captcha_charset = (string)$captcha_charset;
$captcha_audiotheme = (string)$captcha_audiotheme;

$audio_path = rtrim(dirname(__FILE__), '/\\') . '/audio/';
session_cache_limiter('');
session_start();

if (!isset($_SESSION['captcha_code'])) {
	$captcha_code = '';
	$captcha_charset_len = strlen($captcha_charset);
	for ($i = 0; $i < $captcha_codelen; $i++) {
		$captcha_code .= $captcha_charset[(int)mt_rand_oi(0, $captcha_charset_len)];
	}
	$_SESSION['captcha_code'] = $captcha_code;
	$_SESSION['captcha_sensitive'] = $captcha_sensitive;
	$_SESSION['captcha_retries'] = $captcha_maxretries;
} else {
	$captcha_code = (string)@$_SESSION['captcha_code'];
	$captcha_sensitive = (bool)@$_SESSION['captcha_sensitive'];
}

if (strlen($captcha_code) <= 0) audioError(new Exception('No CODE!'));


if ($captcha_sensitive) {
	try {
		$wav_capital = new WavFile($audio_path . $captcha_audiotheme . '/' . 'capital.wav');
	} catch (Exception $e) {
		audioError($e);
	}
}

$wav = new WavFile(1, 8000, 8);
for ($i = 0; $i < strlen($captcha_code); $i++) {
	$wav->insertSilence(mt_rand_oi(0.25, 1));

	try {
		$wav_letter = new WavFile($audio_path . $captcha_audiotheme . '/' . strtolower($captcha_code[$i]) . '.wav');
	} catch (Exception $e) {
		audioError($e);
	}

	if ($captcha_sensitive && $captcha_code[$i] !== strtolower($captcha_code[$i])) {
		$wav->appendWav($wav_capital);
		$wav->insertSilence(0.1);
	}
	$wav->appendWav($wav_letter);
}
$wav->insertSilence(mt_rand_oi(0.25, 1));


$audio_backgrounds = array();
if (is_dir($audio_path . 'backgrounds/')) {
	$d = opendir($audio_path . 'backgrounds/');
	while (($f = readdir($d)) !== false) {
		if (is_file($audio_path . 'backgrounds/' . $f) && substr($f, -4) === '.wav') $audio_backgrounds[] = $f;
	}
	closedir($d);
}

if (!empty($audio_backgrounds)) {
	try {
		$wav_bg = new WavFile($audio_path . 'backgrounds/' . $audio_backgrounds[(int)mt_rand_oi(0, count($audio_backgrounds))], false);
	} catch (Exception $e) {
		audioError($e);
	}

	$randOffset = 0;
	try {
		if ($wav_bg->getNumBlocks() > 2 * $wav->getNumBlocks()) {
			$randBlock = (int)mt_rand_oi(0, $wav_bg->getNumBlocks() - $wav->getNumBlocks() + 1);
			$wav_bg->readWavData($randBlock * $wav_bg->getBlockAlign(), $wav->getNumBlocks() * $wav_bg->getBlockAlign());
		} else {
			$wav_bg->readWavData();
			$randOffset = (int)mt_rand_oi(0, $wav_bg->getNumBlocks());
	    }
	} catch (Exception $e) {
		audioError($e);
	}

	$mixOpts = array(
		'wav'  => $wav_bg,
		'loop' => true,
		'blockOffset' => $randOffset);

	$filters = array();
	$filters[WavFile::FILTER_MIX] = $mixOpts;
	$filters[WavFile::FILTER_NORMALIZE] = 0.6;
	//$filters[WavFile::FILTER_DEGRADE] = mt_rand_oi(0.9, 0.95);
} else {
	$filters = array();
	$filters[WavFile::FILTER_DEGRADE] = mt_rand_oi(0.9, 0.95);
}

try {
	$wav->filter($filters);
} catch (Exception $e) {
	audioError($e);
}

$wav->insertSilence(-0.1);
$wav->insertSilence(0.1);


header('Date: '.gmdate('r'), true);
header('Expires: '.gmdate('r', time() - 86400), true);
header('Last-Modified: '.gmdate('r'), true);
header('Cache-Control: no-cache,no-store,max-age=0,must-revalidate,post-check=0,pre-check=0', true);
header('Pragma: no-cache', true);
header('Vary: *', true);
header('Content-Type: audio/x-wav', true, 200);
header('Content-Disposition: attachment;filename="captcha-' . urlencode(uniqid()) . '.wav"', true);
header('Content-Length: ' . $wav->getActualSize(), true);

echo $wav;
?>