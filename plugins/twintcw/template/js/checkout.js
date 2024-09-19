/**
  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 */

(function ($) {
	
	var disableConfirmationButton = function() {
		$('.twintcw-confirmation-buttons input').each(function () {
			$(this).prop('disabled', 'disabled');
		});
	}
	
	var enableConfirmationButton = function() {
		$('.twintcw-confirmation-buttons input').each(function () {
			$(this).prop('disabled', ''); // removeProp(..) is not always reliable
			$(this).removeProp('disabled');
		});
	}
	
	var attachEventHandlers = function() {
		if (typeof twintcw_ajax_submit_callback != 'undefined') {
			$('.twintcw-confirmation-buttons input').each(function () {
				$(this).click(function(e) {
					disableConfirmationButton();
					TwintCwHandleAjaxSubmit();
				});
			});
		}
	};
	
	var getFieldsDataArray = function () {
		var fields = {};
		
		var data = $('#twintcw-confirmation-ajax-authorization-form').serializeArray();
		$(data).each(function(index, value) {
			fields[value.name] = value.value;
		});
		
		return fields;
	};
	
	var TwintCwHandleAjaxSubmit = function() {
		
		if (typeof twintcw_ajax_submit_callback != 'undefined') {
			if(typeof cwValidateFields != 'undefined') {
				cwValidateFields(function(valid){
					twintcw_ajax_submit_callback(getFieldsDataArray());
				}, function(errors, valid){
					alert(errors[Object.keys(errors)[0]]);
					enableConfirmationButton();
				});
				return false;
			}
			twintcw_ajax_submit_callback(getFieldsDataArray());
			return false;
			
		}
		else {
			alert("No JavaScript callback function defined.");
			enableConfirmation();
		}
	};
		
	$( document ).ready(function() {
		attachEventHandlers();
		
		$('#twintcw_alias').change(function() {
			$('#twintcw-checkout-form-pane').css({
				opacity: 0.5,
			});
			
			$.ajax({
				type: 		'POST',
				url: 		'twintcw.php?controller=process&action=payment',
				data: 		'twintcw_alias=' + $('#twintcw_alias').val(),
				success: 	function( response ) {
					
					var newPane = $("#twintcw-checkout-form-pane", $(response));
					if (newPane.length > 0) {
						var newContent = newPane.html();
						$('#twintcw-checkout-form-pane').html(newContent);
						attachEventHandlers();
					}
					
					$('#twintcw-checkout-form-pane').animate({
						opacity : 1,
						duration: 100, 
					});
				},
			});
		});
		
	});
	
}(jQuery));