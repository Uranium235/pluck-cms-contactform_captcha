/**
 * Contact form with CAPTCHA
 *
 * This file is part of a module for pluck (http://www.pluck-cms.org/).
 * It provides the JavaScript for client side form validation.
 *
 * @copyright 2012 Paul Voegler
 * @author Paul Voegler (http://www.voegler.eu/)
 * @version 1.2 (January 2015)
 * @license GPL Version 3, 29 June 2007
 * See docs/COPYING for the complete license.
 */

function ajaxLoad(href, body, callback, callbackparam) {
	var request = null;

	if (typeof(XMLHttpRequest) == "undefined") return false;

	request = new XMLHttpRequest();
	request.onreadystatechange =
	function() {
		var result = null;
		var type = '';

		if (this.readyState == 4) {
			if (this.status == 200) {
				if (this.responseXML) {
					result = this.responseXML;
				} else if (this.responseText) {
					if (type = this.getResponseHeader('Content-Type')) {
						if (type.search(/^application\/json/i) >= 0) {
							if (JSON) {
								result = JSON.parse(this.responseText);
							} else if (!(/[^,:{}\[\]0-9.\-+Eaeflnr-u \n\r\t]/.test(this.responseText.replace(/"(\\.|[^"\\])*"/g, '')))) {
								result = eval('(' + this.responseText + ')');
							}
						} else if (type.search(/^text\//i) >= 0) {
							result = this.responseText;
						}
					}
				}
			}
			if (callback) {
				callback(this, result, callbackparam);
			}
		}
	};

	request.open('POST', href, true);
	request.setRequestHeader('Content-Type', 'application/json;charset=UTF-8');
	request.send(body);

	return true;
}

function captchaUpdate(self, idimg, idaudio, idaudiolnk) {
	var url = '';
	var p = 0;
	var uid = Math.random();
	var img = document.getElementById(idimg);
	var audio = document.getElementById(idaudio);
	var audiolnk = document.getElementById(idaudiolnk);

	if (!img && !audio && !audiolnk) return true;

	if (img) {
		url = img.src;
		p = url.indexOf('?');
		if (p >= 0) url = url.substr(0, p);
		url = url + '?uid=' + encodeURIComponent(uid);
		img.src = url;
	}

	if (audio) {
		url = audio.src;
		p = url.indexOf('?');
		if (p >= 0) url = url.substr(0, p);
		url = url + '?uid=' + encodeURIComponent(uid);
		audio.src = url;
	}

	if (audiolnk) {
		url = audiolnk.href;
		p = url.indexOf('?');
		if (p >= 0) url = url.substr(0, p);
		url = url + '?uid=' + encodeURIComponent(uid);
		audiolnk.href = url;
	}

	return false;
}

function captchaPlay(self, idaudio) {
	var audio = document.getElementById(idaudio);

	if (!audio) return true;

	if (!!audio.canPlayType && audio.canPlayType('audio/x-wav') != '') {
		//if (audio.load) audio.load();
		audio.play();
		return false;
	}

	return true;
}

function cfc_callback(request, response, param) {
	var element = null;
	var e = null;
	var s = '';
	var form = document.getElementById(param.idform);

	if (!form) return false;

	if (response && response.valid) {
		form.submit();
		return true;
	} else if (response) {
		if (element = form.elements['captcha']) {
			element.value = '';
			element.className = 'error invalid';
			if (e = document.getElementById(element.id + '_error_invalid')) {
				e.style.display = '';
				s = e.innerHTML;
			}
		}
		if (response.retries <= 0) {
			if (e = document.getElementById(param.idcaptchaupdate)) {
				e.onclick();
			}
		}
		if (param.popup) alert(s || 'Incorrect CAPTCHA!');
	} else {
		alert('Request error (' + request.status + ')!');
	}

	return false;
}

function cfc_fieldError(element, rexp) {
	var e = null;

	if (!element) return false;

	e = document.getElementById(element.id + '_error');
	if (element.value.search(rexp) < 0) {
		element.className = 'error';
		if (e) {
			e.style.display = '';
			return e.innerHTML || element.name || true;
		} else {
			return element.name || true;
		}
	} else {
		element.className = '';
		if (e) e.style.display = 'none';
		return false;
	}

	return true;
}

function cfc_formCheck(form, popup, captchaurl, idcaptchaupdate) {
	var f = null;
	var e = null;
	var s = '';
	var errors = new Array();
	var captcha_code = '';

	if (f = form.elements['name']) if (e = cfc_fieldError(f, /\S/)) errors.push(e);
	if (f = form.elements['email']) if (e = cfc_fieldError(f, /^[a-zA-Z0-9.!#$%&'*+\/=?\^_`{|}~\-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/)) errors.push(e);
	if (f = form.elements['subject']) if (e = cfc_fieldError(f, /\S/)) errors.push(e);
	if (f = form.elements['message']) if (e = cfc_fieldError(f, /\S/)) errors.push(e);

	if (f = form.elements['captcha']) {
		e = document.getElementById(f.id + '_error_invalid');
		if (e) e.style.display = 'none';
		if (e = cfc_fieldError(f, /^[a-zA-Z0-9]+$/)) {
			errors.push(e);
		} else {
			captcha_code = f.value;
		}
	}

	e = document.getElementById(form.id + '_error');
	if (errors.length > 0) {
		if (e) {
			 e.style.display = '';
			 s = e.innerHTML;
		}
		if (popup) {
			s = (s || 'Form validation error!') + '\n';
			for (var i = 0; i < errors.length; i++) {
				if (errors[i] !== true) s = s + '\n' + errors[i];
			}
			alert(s);
		}
		return false;
	}
	if (e) e.style.display = 'none';

	if (captcha_code !== '' && captchaurl) {
		if (ajaxLoad(
				captchaurl + '?code=' + encodeURIComponent(captcha_code) + '&uid=' + encodeURIComponent(Math.random()),
				null,
				cfc_callback, {
					"idform": form.id,
					"popup": popup,
					"idcaptchaupdate": idcaptchaupdate
				}
			)
		) {
			return false;
		}
	}

	return true;
}
