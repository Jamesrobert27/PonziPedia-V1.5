window.EasyLogin = {
	// Translate the given message.
	trans: function (message) {
        return (EasyLogin.options.lang[message] || message.toString());
    },

	// Show alert message box
	alert: function (message, type, form) { 
		// type: -1 - error | 0 - warning | 1 - success
		var alert = form.find('.alert:not(.ip-alert)');
		
		if (message === null) return alert.length ? alert.hide() : 0;

		if (typeof(message) == 'object') {
			var messages = message;
			
			message = '<ul>';
			$.each(messages, function (i, val) {
				message += '<li>'+(typeof(val) == 'object' ? val[0] : val)+'</li>';
			});
			message += '</ul>';
		}

		if (alert.length) {
			alert.html('');
			alert.append('<span class="close">&times;</span>');
			alert.on('click', '.close', function (e) { $(e.delegateTarget).hide() });
		} else {
			alert = $('<div class="alert alert-dismissible" role="alert"></div>');
			alert.append('<span class="close" data-dismiss="alert">&times;</span>')
			form.prepend(alert);
		}

		alert.removeClass(type === 1 ? 'alert-danger alert-warning' : type === 0 ? 'alert-danger alert-success' : 'alert-warning alert-danger');
		alert.addClass('alert-' + (type === 1 ? 'success' : type === 0 ? 'warning' : 'danger'));
        alert.append(message).show();
	},

	// Log to console the given message.
	log: function (message) {
		if (EasyLogin.options.debug) console.log('Server response:', message);
	},

	//  Ajax Form
	ajaxForm: function (e) {
		e.preventDefault();

		var form = $(e.currentTarget),
			data = form.serialize(),
			action = form.attr('action'),
			inputs = form.find('input, select, textarea'),
			btn = form.find('[type="submit"]');

		if (!action) return;

		inputs.prop('disabled', true);

		btn.attr('data-loading-text', EasyLogin.trans('loading'));
		
		btn.button('loading'); EasyLogin.alert(null, null, form);

		$.ajax({
			url: EasyLogin.options.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: data+'&action='+action,
		})
		.done(function (response) {
			if (response.success) {
				if (EasyLogin.ajaxFormCb[action]) 
					EasyLogin.ajaxFormCb[action](response.message, form);
			} else { 
				EasyLogin.alert(response.message || EasyLogin.trans('error'), -1, form);
				EasyLogin.log(response);
			}
		})
		// If the ajax fails show a generic error to the user and log the actual error.
		.fail(function (jqXHR) {
			EasyLogin.alert(EasyLogin.trans('error'), -1, form);
			EasyLogin.log((jqXHR.responseJSON && jqXHR.responseJSON.error) ? 
							jqXHR.responseJSON.error : jqXHR.responseText);
		})
		.always(function () {
			inputs.prop('disabled', false);

			form.find('.focus-me').focus();

			btn.button('reset');

			try { grecaptcha.reset(EasyLogin.reWidgetId) } catch(e) {}
		});
	},

   	// Generate display from first and last name.
    generateDisplayName: function () {
        var displayName = $('#display_name'),
            firstName = $('#usermeta-first_name'),
            lastName = $('#usermeta-last_name');

        $.each([firstName, lastName], function (i, el) {
            $(el).on('change', function () {
                var val = $(this).val(), exists = false;
                
                if ($.trim(val) == '') return;
                
                displayName.find('option').each(function (index, el) {
                    if ($(el).text() == val) { exists = true; return; }
                });
                
                if (!exists) {
                    displayName.append('<option>'+val+'</option>');

                    if ($.trim(firstName.val()) != '' && $.trim(lastName.val()) != '') {
                        displayName.append('<option>'+firstName.val() +' '+ lastName.val()+'</option>')
                                    .append('<option>'+lastName.val() +' '+ firstName.val()+'</option>');
                    }
                }
            });
        });
    },

    logout: function() {
    	$.post(EasyLogin.options.ajaxUrl, {action: 'logout'}, function() {
    		window.location.reload();
    	});
    },

    ajaxFormCb: {},
    admin: {},
	reWidgetId: null
};

jQuery(function ($) {
	
	// Add header to ajax for the CSRF check
	$.ajaxSetup({
        headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')}
    });
	
	$('.ajax-form').on('submit', EasyLogin.ajaxForm);

	// Close the the current opend modal when another one is triggered to be opend.
	$('.modal').on('show.bs.modal', function () {
		var self = this;
		$('.modal').each(function (){
			if (this != self) $(this).modal('hide');
		});
	})
	// Also reset the fields when it does close.
	.on('hidden.bs.modal', function () {
		$(this).find('.alert').hide();

		if ($(this).attr('id') == 'settingsModal') return;

		$(this).find('input[type="text"], input[type="password"], textarea').val('');
		$(this).find('input[type="checkbox"]').prop('checked', false);
	})
	// Prevent modals from closing when clicking outside
	.attr('data-backdrop', 'static');

	// Bootstrap Tooltip
	if ($.fn.tooltip) $('[data-toggle="tooltip"]').tooltip();
});