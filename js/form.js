$(function() {
	// desabilitando o submit do form
	$('#content form').submit(function() { return false; });
	fnFocus();
});

function fnFocus() {
	var $fields = $('#content form div.field');
	$fields.click(function() {
		$(this).find('input, select, textarea').focus();		
	});
}

function fnDelete(formId, back) {
	
	if(confirm('Deseja realmente remover?')) {
		var $form = $('#' + formId);
		var $action = formId.replace("form-", "form/") + '.php';
		var $button = $('div.button input', $form);
		
		var $data = $form.serialize() + '&op=E';
		
		$button.attr('disabled', true);
		fnProcessing(false);
		fnMessage(null);
		
		$.post($action, $data, function(xml) {
			
			var $result = $('result', xml);
			var $type = parseInt($result.attr('value'));
			
			fnMessage($result.text(), $type, $form);
			if($type > 0) fnLink(back);
				
			$button.attr('disabled', false);
			fnProcessing(true);
		
		}, 'xml');
	}
	
}
function fnSubmit(formId, op, scroll, ajax) {

	var $form = $('#' + formId);
	if(ajax) {
		var $action = formId.replace("form-", "form/") + '.php';
		var $button = $('div.button input', $form);
		
		var $data = $form.serialize() + '&op=' + op;
		
		$button.attr('disabled', true);
		fnProcessing(false);
		$('div.field', $form).removeClass('field-error').find('span.error').remove();
		fnMessage(null);
		
		$.post($action, $data, function(xml) {
			
			var $valid = $('valid', xml);
			if($valid.length) {
		
				$('field', $valid).each(function() {
					var $field = $('#in-' + $(this).attr('name'));
					$field.addClass('field-error').find('label:not(.radio)').append(
						$('<span class="error"></span>').text($(this).text())
					);
				});
				
				fnMessage('Existem erros de preenchimento. Verifique os campos abaixo.', -1, $form);
				
			}
			else {
				
				var $result = $('result', xml);
				var $type = parseInt($result.attr('value'));
				
				if ($type > 0) {
					if(op == 'I') fnClearForm($form);
					if(typeof fnAfterSubmit == "function") fnAfterSubmit($form, xml, op);
				}
				fnMessage($result.text(), $type, $form);
				
			}
			
			scroll = isNaN(scroll) ? $(scroll).offset().top : parseInt(scroll, 10);
			if(scroll >= 0) $('html,body').animate({scrollTop:scroll}, 300);
			
			$button.attr('disabled', false);
			fnProcessing(true);
		
		}, 'xml');
	}
	else $form.unbind('submit').submit();
	
}

function fnClearForm(form) {
	var $f = $('div.field', form);
	$('input:text, input:password, textarea', $f).val('');
	$('input:checkbox, input:radio', $f).prop('checked', false);
	
	var $s = $('select', $f);
	if($s.length) {
		$s.each(function() { this.selectedIndex = 0; } );
		$s.trigger('change');
	}
}