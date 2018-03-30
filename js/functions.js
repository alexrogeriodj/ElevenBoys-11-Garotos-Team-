var fnAfterSubmit;

function fnTableOver($obj) {
	$('table.list tr:odd', $obj).not('.head').addClass('odd').hover(
			function() { $(this).addClass('over'); },
			function() { $(this).removeClass('over'); }
		);
	$('table.list tr:even', $obj).not('.head').addClass('even').hover(
			function() { $(this).addClass('over'); },
			function() { $(this).removeClass('over'); }
		);
}

function fnProcessing(attr) { 
	if(attr) $('#processing').remove();
	else $(document.body).append('<div id="processing">Processando...</div>');	
}

function fnLink(url) {
	document.location.href = url;
}

function fnBrowser() {
	if($.browser.msie && $.browser.version <= 6) {
		alert('Esse site não é completamente compatível com a versão de seu navegador. Por favor atualize para uma versão mais recente.');
	}
}