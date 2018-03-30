var fnAfterUpload = null;
var fnResult = null;
var elements = null;
var showMessage = true;

$(function() {
	elements = {
			form: $('#form-upload'),
			file: $('#form-upload .input'),
			button: $('#form-upload .button'),
			filename: $('#form-upload .filename'),
			background: $('#form-upload .background')
	};
	
	elements.file.change(function() { elements.filename.text(this.value.replace('C:\\fakepath\\', '')); });
	if(fnResult != null) {
		var p = fnResult;
		if(showMessage) message(p.msg, p.id, document.body);
		if(fnAfterUpload != null) fnAfterUpload(p);
	}
});

function message(message, id, obj) {
	$(obj).find('.message').remove();
	var cls = 'info';
	if(id == 0) cls = 'error'; 
	else if(id == -1) cls = 'alert';
	$(obj).prepend('<div class="message ' + cls + '">' + message + '</div>');
}

function setAutoSend() {
	elements.file.change(function() { elements.form.submit(); });
}

function getImage(id, name, prms, fn) {
	$.post(fnResult.urlSite + '/ajax/image.php', { 
			action : 'GET', id : id, name : name, prms : prms
		}, function(xml) {
			if(typeof fn == "function") fn($('image', xml).text());
		}, 'xml');
}

function getImageSize($url, $maxw, $maxh, fn) {
	$.post(fnResult.urlSite + '/ajax/image.php', { 
		action : 'SIZE', url : $url, maxw : $maxw, maxh : $maxh
		}, function(xml) {
			if(typeof fn == "function") fn($('width', xml).text(), $('height', xml).text());
		}, 'xml');
}

function setCaption(id, legend) {
	$.post(fnResult.urlSite + '/ajax/image.php', { 
		action:'SET_CAPTION', id:id, legend:legend
		}, function(xml) {
		}, 'xml');
}

function loadCSS(css) {
	if (document.createStyleSheet) document.createStyleSheet(css);
	else $("head").append($('<link rel="stylesheet" href="'+css+'" type="text/css" media="screen" />'));
}