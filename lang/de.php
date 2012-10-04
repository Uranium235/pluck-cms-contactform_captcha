<?php
/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * It provides German language for the module.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.0 (October 2012)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

//Name of the language.
$language = 'German';

//----------------
//Translation data.
$lang['contactform_captcha']['module_name'] = 'Kontaktformular mit CAPTCHA';
$lang['contactform_captcha']['module_intro'] = 'Dieses Modul zeigt ein HTML5 Kontaktformular, welches mit einem CAPTCHA geschützt ist.';
$lang['contactform_captcha']['module_help'] = 'Tip: fügen Sie die data/modules/contactform_captcha/style_example.css in die CSS-Datei Ihres Farb-Schema\'s ein.';
$lang['contactform_captcha']['subject'] = 'Betreff:';
$lang['contactform_captcha']['captcha'] = 'Sicherheitscode:';
$lang['contactform_captcha']['update'] = 'Neues CAPTCHA-Bild anfordern';
$lang['contactform_captcha']['play'] = 'CAPTCHA Audio abspielen oder herunteraden';
$lang['contactform_captcha']['name_placeholder'] = 'Ihr Name';
$lang['contactform_captcha']['email_placeholder'] = 'Ihre E-Mail-Adresse';
$lang['contactform_captcha']['subject_placeholder'] = 'Betreff der Nachricht';
$lang['contactform_captcha']['message_placeholder'] = 'Ihre Nachricht';
$lang['contactform_captcha']['captcha_placeholder'] = 'code';
$lang['contactform_captcha']['form_error'] = 'Leider habe Sie das Formular nicht richtig ausgefüllt!';
$lang['contactform_captcha']['name_error'] = 'Bitte geben Sie Ihren Namen an';
$lang['contactform_captcha']['email_error'] = 'Bitte geben Sie Ihre E-Mail-Adresse an';
$lang['contactform_captcha']['email_invalid'] = 'Die E-Mail-Adresse ist ungültig';
$lang['contactform_captcha']['subject_error'] = 'Bitte geben Sie einen Betreff an';
$lang['contactform_captcha']['message_error'] = 'Bitte geben Sie eine Nachricht ein';
$lang['contactform_captcha']['captcha_error'] = 'Bitte geben Sie den Sicherheitscode ein';
$lang['contactform_captcha']['captcha_invalid'] = 'Der Sicherheitscode ist ungültig';
$lang['contactform_captcha']['field_invalid'] = 'Ungültige Felddaten für: ';
$lang['contactform_captcha']['send_error'] = 'Ihre Nachricht wurde nicht versendet. Es trat ein Fehler auf.';
$lang['contactform_captcha']['send_success'] = 'Vielen Dank für Ihre Nachricht. Ihre Nachricht wurde versendet.';
$lang['contactform_captcha']['email_title'] = 'Kontaktformular';
$lang['contactform_captcha']['cfg_email_checkhost'] = 'Hostnamen der E-Mail-Adresse des Absenders verifizieren';
$lang['contactform_captcha']['cfg_captcha_enable'] = 'CAPTCHA aktivieren';
$lang['contactform_captcha']['cfg_captcha_audio'] = 'Audio CAPTCHA aktivieren';
$lang['contactform_captcha']['cfg_captcha_sensitive'] = 'Groß- / Kleinschreibung beachten';
$lang['contactform_captcha']['cfg_captcha_codelen'] = 'Code-Länge';
$lang['contactform_captcha']['cfg_captcha_maxretries'] = 'Verifizierungsversuche';
$lang['contactform_captcha']['cfg_captcha_charset'] = 'Code-Zeichensatz';
$lang['contactform_captcha']['cfg_captcha_font'] = 'Schriftart';
$lang['contactform_captcha']['cfg_captcha_height'] = 'Bildhöhe';
$lang['contactform_captcha']['cfg_captcha_contrast'] = 'Bildkontrast [0, 100]';
$lang['contactform_captcha']['cfg_captcha_sharpness'] = 'Bildschärfe [0, 100]';
$lang['contactform_captcha']['cfg_captcha_audiotheme'] = 'Audio-Schema';
$lang['contactform_captcha']['error_nogd'] = 'GD Erweiterung nicht verfügbar';
$lang['contactform_captcha']['error_nofreetype'] = 'Keine GD FreeType Unterstützung';
$lang['contactform_captcha']['error_nopng'] = 'Keine GD PNG Unterstützung';
$lang['contactform_captcha']['error_nombstring'] = 'mbstring Erweiterung nicht verfügbar';
$lang['contactform_captcha']['error_nowavltr'] = 'Es fehlen Audio-Dateien für Buchstaben im aktuellen Audio-Schema';
$lang['contactform_captcha']['error_nowavcap'] = 'Es fehlt eine Audio-Datei für Großschreibung (capital.wav) im aktuellen Audio-Schema';
$lang['contactform_captcha']['error_nowavbg'] = 'Es gibt keine Audio-Hintergrund-Dateien';
$lang['contactform_captcha']['error_aok'] = 'Keine Probleme gefunden';
?>