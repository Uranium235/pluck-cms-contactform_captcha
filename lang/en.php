<?php
/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * It provides English language for the module.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.2 (January 2015)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

//----------------
//Translation data.
$lang['contactform_captcha']['module_name']  = 'Contact form with CAPTCHA';
$lang['contactform_captcha']['module_intro'] = 'This module shows a HTML5 contact form, which is protected by a CAPTCHA.';
$lang['contactform_captcha']['module_help'] = 'Tip: insert the data/modules/contactform_captcha/style_example.css into your theme\'s CSS file.';
$lang['contactform_captcha']['subject'] = 'Subject:';
$lang['contactform_captcha']['captcha']   = 'security code:';
$lang['contactform_captcha']['update'] = 'update CAPTCHA image';
$lang['contactform_captcha']['play'] = 'play or download CAPTCHA audio';
$lang['contactform_captcha']['name_placeholder'] = 'your name';
$lang['contactform_captcha']['email_placeholder'] = 'your e-mail address';
$lang['contactform_captcha']['subject_placeholder'] = 'message subject';
$lang['contactform_captcha']['message_placeholder'] = 'your message';
$lang['contactform_captcha']['captcha_placeholder'] = 'code';
$lang['contactform_captcha']['form_error'] = 'Unfortunately you have not filled in the form correctly!';
$lang['contactform_captcha']['name_error'] = 'please give your name';
$lang['contactform_captcha']['email_error'] = 'please give your e-mail address';
$lang['contactform_captcha']['email_invalid'] = 'the e-mail address is invalid';
$lang['contactform_captcha']['subject_error'] = 'please give a message subject';
$lang['contactform_captcha']['message_error'] = 'please enter a message';
$lang['contactform_captcha']['captcha_error'] = 'please enter the security code';
$lang['contactform_captcha']['captcha_invalid'] = 'the security code is invalid';
$lang['contactform_captcha']['field_invalid'] = 'Invalid field data for: ';
$lang['contactform_captcha']['send_error'] = 'Your message was not sent. An error occurred.';
$lang['contactform_captcha']['send_success'] = 'Thank you for your message. Your message has been sent successfully.';
$lang['contactform_captcha']['email_title'] = 'Contactform';
$lang['contactform_captcha']['cfg_logo_show'] = 'Show logo image';
$lang['contactform_captcha']['cfg_email_checkhost'] = 'Verify the host name of the sender\'s e-mail address';
$lang['contactform_captcha']['cfg_captcha_enable'] = 'Enable CAPTCHA';
$lang['contactform_captcha']['cfg_captcha_audio'] = 'Enable audio CAPTCHA';
$lang['contactform_captcha']['cfg_captcha_sensitive'] = 'Code is case sensitive';
$lang['contactform_captcha']['cfg_captcha_codelen'] = 'Code length';
$lang['contactform_captcha']['cfg_captcha_maxretries'] = 'Verification attempts';
$lang['contactform_captcha']['cfg_captcha_charset'] = 'Code character set';
$lang['contactform_captcha']['cfg_captcha_font'] = 'Font';
$lang['contactform_captcha']['cfg_captcha_height'] = 'Image height';
$lang['contactform_captcha']['cfg_captcha_contrast'] = 'Image contrast [0, 100]';
$lang['contactform_captcha']['cfg_captcha_sharpness'] = 'Image sharpness [0, 100]';
$lang['contactform_captcha']['cfg_captcha_audiotheme'] = 'Audio theme';
$lang['contactform_captcha']['error_nogd'] = 'GD extension not available';
$lang['contactform_captcha']['error_nofreetype'] = 'No GD FreeType support';
$lang['contactform_captcha']['error_nopng'] = 'No GD PNG support';
$lang['contactform_captcha']['error_nombstring'] = 'mbstring extension not available';
$lang['contactform_captcha']['error_nowavltr'] = 'There are character audio files missing in the current audio theme';
$lang['contactform_captcha']['error_nowavcap'] = 'There is no audio file for capital letters (capital.wav) in the current audio theme';
$lang['contactform_captcha']['error_nowavbg'] = 'There are no audio background files';
$lang['contactform_captcha']['error_aok'] = 'No problems found';
?>