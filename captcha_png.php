<?php
/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * It generates the CAPTCHA code and creates the image.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.2 (January 2015)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

function mt_rand_oi($a = 0, $b = 1) {
	//$max = min(0x7FFFFFFF, mt_getrandmax());
	$max = 0x7FFFFFFF;

	$x = mt_rand(0, $max - 1);

	return $a + ($b - $a) * ($x / $max);
}

function mt_rand_nd($m = 0, $s = 1, $a = null, $b = null, $n = 10) {
	//$max = min(0x7FFFFFFF, mt_getrandmax());
	$max = 0x7FFFFFFF;
	if ($b <= $a) return $m;

	$x = $m;
	$n = 10;
	do {
		do {
			$u1 = 2 * (mt_rand(0, $max) / $max) - 1;
			$u2 = 2 * (mt_rand(0, $max) / $max) - 1;
			$q = $u1 * $u1 + $u2 * $u2;
		} while ($q == 0 || $q > 1);
		$p = sqrt(-2 * log($q) / $q);
		$x1 = $m + $s * ($u1 * $p);
		if ((is_null($a) || $x1 >= $a) && (is_null($b) || $x1 <= $b)) {
			$x = $x1;
			break;
		} else {
			$x2 = $m + $s * ($u2 * $p);
			if ((is_null($a) || $x2 >= $a) && (is_null($b) || $x2 <= $b)) {
				$x = $x2;
				break;
			}
		}

		$n--;
	} while ($n > 0);

	return $x;
}

function circlepoly_generate($n = 3, $d = 0.4) {
	if ($n < 3) return false;

	$circle = 2 * M_PI;
	$rotate = mt_rand_oi(0, $circle);
	$segments = array($rotate);
	$angle = 0;
	for ($i = 1; $i < $n; $i++) {
		$mean = ($circle - $angle) / ($n - $i + 1);
		$x = mt_rand_nd($mean, $d * $mean, max($mean / 2, $circle - $angle - (M_PI * ($n - $i))), min(1.5 * $mean, M_PI, $circle - $angle));
		$angle += $x;
		$segments[$i] = $angle + $rotate;
		$segments[$i] = $segments[$i] >= $circle ? $segments[$i] - $circle : $segments[$i];
	}
	$segments[$i] = $segments[0];

	return $segments;
}


$captcha_sensitive = 'false';
$captcha_codelen = 3;
$captcha_maxretries = 3;
$captcha_charset = 'ACDEFGHJKLMNPRSTUVWXY345679';
$captcha_font = 'Action Man Bold.ttf';
$captcha_height = 64;
$captcha_contrast = 40;
$captcha_sharpness = 70;
include(rtrim(dirname(__FILE__), '/\\') . '/../../settings/contactform_captcha.settings.php');
$captcha_sensitive = (bool)($captcha_sensitive === 'true');
$captcha_codelen = (int)$captcha_codelen;
$captcha_maxretries = (int)$captcha_maxretries;
$captcha_charset = (string)$captcha_charset;
$captcha_font = (string)$captcha_font;
$captcha_height = (int)$captcha_height;
$captcha_contrast = (int)$captcha_contrast;
$captcha_sharpness = (int)$captcha_sharpness;


session_cache_limiter('');
session_start();

$captcha_code = '';
$captcha_charset_len = strlen($captcha_charset);
for ($i = 0; $i < $captcha_codelen; $i++) {
	$captcha_code .= $captcha_charset[(int)mt_rand_oi(0, $captcha_charset_len)];
}
$_SESSION['captcha_code'] = $captcha_code;
$_SESSION['captcha_sensitive'] = $captcha_sensitive;
$_SESSION['captcha_retries'] = $captcha_maxretries;


$ttf = rtrim(dirname(__FILE__), '/\\') . '/fonts/' . $captcha_font;
$ttf_size = ceil($captcha_height * 0.6);

$txt_maxxsize = imageftbbox($ttf_size, 0, $ttf, 'M');
$img_width = ceil(ceil(abs($txt_maxxsize[4] - $txt_maxxsize[0]) * $captcha_codelen * 1.15 / $captcha_height * 2) / 2 * $captcha_height);
$img_space = ceil(abs($txt_maxxsize[4] - $txt_maxxsize[0]) * 0.15);


//$start = microtime(true);

$img = imagecreate($img_width, $captcha_height);
$palette = array();
for ($i = 0; $i < 255; $i++) {
	$palette[] = imagecolorallocate($img, $i, $i, $i);
}
$palette[] = imagecolorallocate($img, 255, 0, 0);
$txt_color = $palette[255];

$txt_xsize = 0;
$char_box = array();
for ($i = 0; $i < $captcha_codelen; $i++) {
	$char_box[$i] = imageftbbox($ttf_size, 0, $ttf, $captcha_code[$i]);
	$txt_xsize += abs($char_box[$i][4] - $char_box[$i][0]);
	if ($i < $captcha_codelen - 1) $txt_xsize += $img_space;
}

$x = (int)(($img_width - $txt_xsize) / 2);
$y = (int)($captcha_height / 2);
for ($i = 0; $i < $captcha_codelen; $i++) {
	imagettftext($img, $ttf_size, mt_rand_oi(-8, 8), $x - $char_box[$i][0], $y + ceil(($char_box[$i][1] - $char_box[$i][5]) / 2), $txt_color, $ttf, $captcha_code[$i]);
	$x += abs($char_box[$i][4] - $char_box[$i][0]) + $img_space;
}


$poly = circlepoly_generate((int)mt_rand_oi(3, 5), 0.4);
$cx = (int)mt_rand_oi($img_width * 0.2, $img_width * 0.8);
$cy = (int)mt_rand_oi($captcha_height * 0.1, $captcha_height * 0.9);
$r = (int)mt_rand_oi($captcha_height * 0.7, $captcha_height);

$img2 = imagecreate($img_width, $captcha_height);
$palette2 = array();
$palette2[] = imagecolorallocate($img2, 0, 0, 0);
$palette2[] = imagecolorallocate($img2, 255, 255, 255);
$points = array();
for ($i = 0; $i < count($poly) - 1; $i++) {
	$points[] = $cx + $r * sin($poly[$i]);
	$points[] = $cy + $r * cos($poly[$i]);
}
imagefilledpolygon($img2, $points, count($poly) - 1, $palette2[1]);


$m1 = mt_rand_oi(96, 161);
$s1 = (100 - $captcha_sharpness) / 100 * 127;
$m2 = min(254, max(0, $m1 >= 127 ? $m1 - $captcha_contrast / 100 * 127 : $m1 + $captcha_contrast / 100 * 127));
$s2 = abs($m2 - $m1);
$m2 = min(254, max(0, (int)mt_rand_nd($m2, $s2 / 4, $m2 - $s2 / 4, $m2 + $s2 / 4)));
$s2 = $s1;
for ($x = 0; $x < $img_width; $x++) {
	for ($y = 0; $y < $captcha_height; $y++) {
		if ((imagecolorat($img, $x, $y) === $txt_color) XOR (imagecolorat($img2, $x, $y) === $palette2[1])) {
			$i = (int)(mt_rand_nd($m2, $s1, 0, 254));
		} else {
			$i = (int)(mt_rand_nd($m1, $s2, 0, 254));
		}
		imagesetpixel($img, $x, $y, $palette[$i]);
	}
}
imagedestroy($img2);

//imagestring($img, 5, 0, -3, $captcha_code, $txt_color);
//imagestring($img, 5, 0, -3, sprintf('%0.0F', (microtime(true) - $start) * 1000), $txt_color);


header('Date: '.gmdate('r'), true);
header('Expires: '.gmdate('r', time() - 86400), true);
header('Last-Modified: '.gmdate('r'), true);
header('Cache-Control: no-cache,no-store,max-age=0,must-revalidate,post-check=0,pre-check=0', true);
header('Pragma: no-cache', true);
header('Vary: *', true);
header('Content-Type: image/png', true, 200);

imagepng($img);
?>