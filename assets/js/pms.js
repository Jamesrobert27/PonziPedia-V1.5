// Private Messages
EasyLogin.addContact = function (userId) {
	$.post(EasyLogin.options.ajaxUrl, {action: 'addContact', id: userId});
	$('[data-contact-id="'+userId+'"]').fadeOut(200, function() { $(this).remove() });
};

EasyLogin.confirmContact = function (userId) {
	$.post(EasyLogin.options.ajaxUrl, {action: 'confirmContact', id: userId});
	$('[data-contact-id="'+userId+'"]').addClass('contact-confirmed');
};

EasyLogin.removeContact = function (userId) {
	$.post(EasyLogin.options.ajaxUrl, {action: 'removeContact', id: userId});

	$('[data-contact-id="'+userId+'"]').fadeOut(200, function() { $(this).remove() });
};

jQuery(function($) {
	var _log = function (xhr) { EasyLogin.log((xhr.responseJSON && xhr.responseJSON.error) 
											? xhr.responseJSON.error : xhr.responseText) };

	var text2link = function (text) {
		var re = /(https?:\/\/(([-\w\.]+)+(:\d+)?(\/([\w/_\.]*(\?\S+)?)?)?))/g;
		return text.replace(re, "<a href=\"$1\" target=\"_blank\">$1</a>");
	};

	var _realtime = EasyLogin.options.pms.realtime,
		_delay = parseInt(EasyLogin.options.pms.delay) * 1000;
	
	var nTimeout, cTimeout, pTimeout,
		modalBtn = $('.pm-open-modal'),
		conversationList = $('.pm-conversation-list'),
		conversationNew = $('.pm-conversation-new'),
		conversation = $('.pm-conversation'),
		notification = $('.pm-notification');

	// Check for notifications
	var checkNotifications = function () {
		clearTimeout(nTimeout);

		$.ajax({
			url: EasyLogin.options.ajaxUrl,
			dataType: 'json',
			data: {action: 'countUnreadMessages'},
		}).done(function(response) {
			if (response.success && response.message)
				notification.text(response.message).css('opacity', 1);
			else
				notification.css('opacity', 0).text('');
		}).fail(_log)
		.always(function() {
			if (_realtime) nTimeout = setTimeout(checkNotifications, _delay);
		});
	};

	// Load and render conversation list
	var loadCoversationList = function (loader) {
		clearTimeout(cTimeout);
		
		if (loader) conversationList.find('.ajax-loader').fadeIn();

		$.ajax({
			url: EasyLogin.options.ajaxUrl,
			dataType: 'json',
			data: {action: 'getConversations'},
		}).done(function(response) {
			conversationList.find('.list-group').html('');

			if (!response.success) return;

			if (!response.message.length)
				conversationList.find('.list-group').append(
					'<li class="no-messages">'+EasyLogin.trans('no_messages')+'</li>'
				);
			
			for (var i = 0; i < response.message.length; i++) {
				conversationList.find('.list-group').append(
					tmpl('conversationItemTemplate', response.message[i])
				);
			}

			conversationList.find('time.timeago').timeago();
		}).fail(_log)
		.always(function() {
			conversationList.find('.ajax-loader').fadeOut();
			
			if (_realtime) cTimeout = setTimeout(loadCoversationList, _delay);
		});
	};

	// Refresh conversations
	var refreshConversation = function(e) {
		loadCoversationList(true);
	};

	// Mark all messages as read
	var markAllAsRead = function (e) {
		e.preventDefault();

		if (!conversationList.find('.list-group-item.unread').length) return;

		$.post(EasyLogin.options.ajaxUrl, {action: 'markAllAsRead'}, function() {
			loadCoversationList(true);
			checkNotifications();
		}, 'json');
	};

	// Render message
	var renderMessage = function (message) {
		message.message = text2link(message.message);

		return tmpl('conversationMessageTemplate', message);
	};

	// Load conversation
	var loadCoversation = function (id, loader, scroll) {
		clearTimeout(pTimeout);

		if (loader) conversation.find('.ajax-loader').fadeIn();

		var timestamp = conversation.find('.pm').last().find('time').attr('datetime');

		$.ajax({
			url: EasyLogin.options.ajaxUrl,
			dataType: 'json',
			data: {action: 'getConversation', id: id, timestamp: timestamp},
		}).done(function(response) {
			if (!response.success) return;

			for (var i = 0; i < response.message.length; i++) {
				var message = $( renderMessage(response.message[i]) );
				conversation.find('.pm-list').append(message);
				message.fadeIn(200);
			}

			timeagoTooltipScroll(scroll);
		}).fail(_log)
		.always(function() {
			conversation.find('.ajax-loader').fadeOut();

			if (_realtime)
				pTimeout = setTimeout(function () { loadCoversation(id) }, _delay);
			else 
				checkNotifications();
		});
	};

	// Open conversation
	var openConversation = function(e) {
		var id =  $(e.currentTarget).attr('data-conversation-id');

		clearTimeout(cTimeout);

		loadCoversation(id, true, true);

		var textarea = conversation.find('.pm-textarea');
		textarea.val( conversationNew.find('.pm-textarea').val() );

		conversation.find('.pm-with').text( $(e.currentTarget).find('.pm-user-name').text() );
		conversation.find('input[name="to"]').val(id);
		conversation.find('.alert').remove();

		$(e.delegateTarget).addClass('hidden');
		$(conversationList).addClass('hidden');
		conversation.removeClass('hidden');

		EasyLogin.ajaxFormCb.sendMessage = function(message, form) {
			EasyLogin.alert(null, null, form);

			textarea.val('').focus();
			conversation.find('.counter').text(textarea.attr('maxlength'));
			
			message = $( renderMessage(message) );
			conversation.find('.pm-list').append(message);

			message.fadeIn(200);

			timeagoTooltipScroll(true);
		};
	};

	// Delete message
	var deleteMessage = function (e) {
		var el = $(e.currentTarget).closest('[data-message-id]');
		
		$.post(EasyLogin.options.ajaxUrl, {
				action: 'deleteMessage', 
				id: el.attr('data-message-id')
			}, function() {
				checkNotifications();
		}, 'json');
		
		el.fadeOut(200, function() { el.remove() });
	};

	// Delete all messages 
	var deleteMessages = function (e) {
		e.preventDefault();

		if (!conversationList.find('.list-group-item').length) return;

		$.post(EasyLogin.options.ajaxUrl, {action: 'deleteMessage'}, function() {
			checkNotifications();
		}, 'json');
		
		conversationList.find('.list-group-item').fadeOut(200, function() {  });
	};

	// Timeago + Tooltip + Scroll
	var timeagoTooltipScroll = function (scroll) {
		conversation.find('time.timeago').timeago();

		if ($.fn.tooltip) conversation.find('[data-toggle="tooltip"]').tooltip();

		if (scroll)
			conversation.find('.modal-body').animate({
				scrollTop: conversation.find('.modal-body')[0].scrollHeight
			}, 500);
	};

	// Go back to conversation list
	var retrunConversationList = function (e) {
		e.preventDefault();
		
		clearTimeout(pTimeout);

		conversation.find('.pm-list').html('');

		loadCoversationList(true);
		
		$(e.delegateTarget).addClass('hidden');
		conversationList.removeClass('hidden');
	};

	var newMessage = function (e) {
		clearTimeout(cTimeout);

		conversationList.addClass('hidden');
		conversationNew.removeClass('hidden');
	};

	var searchContact = function (e) {
		var list = conversationNew.find('.list-group');
		var value = $.trim( $(e.currentTarget).val() );

		if (value.length < 2) return list.html('');
		if (value == $(e.currentTarget).data('last-value')) return;
		
		$.get(EasyLogin.options.ajaxUrl, {action: 'searchContact', user: value}, function (response) {
			$(e.currentTarget).data('last-value', value);

			list.html('');

			if (!response.success) return;

			for (var i = 0; i < response.message.length; i++) {
				list.append( tmpl('contactSearchTemplate', response.message[i]) );
			}
		}, 'json');
	};

	var showAllContacts = function (e) {
		var list = conversationNew.find('.list-group');

		conversationNew.find('.pm-search-contact').val('');

		$.get(EasyLogin.options.ajaxUrl, {action: 'all_contacts'}, function (response) {
			list.html('');

			if (!response.success) return;

			for (var i = 0; i < response.message.length; i++) {
				list.append( tmpl('contactSearchTemplate', response.message[i]) );
			}

			if (typeof e === 'number') {
				list.find('[data-conversation-id="'+e+'"]').trigger('click');
			}
		}, 'json');
	};

	// Open messages modal
	EasyLogin.openPMS = function (userId) {
		conversation.find('.return').trigger('click');

		var modal = $('#pmModal');
		modal.modal('show');
		modal.on('hide.bs.modal', function(e) {
			clearTimeout(cTimeout);
			clearTimeout(pTimeout);
			conversationNew.addClass('hidden');
		});

		if (userId) {
			showAllContacts(userId);
		}
	};

	modalBtn.on('click', function(e) {
		e.preventDefault();
		EasyLogin.openPMS();
	});

	conversationList.on('click', '.list-group-item', openConversation);

	conversationList.find('.pm-refresh').on('click', refreshConversation);

	conversationList.find('.pm-mark-all').on('click', markAllAsRead);

	conversationList.find('.pm-delete-all').on('click', deleteMessages);

	conversationList.on('click', '.profile-url', function (e) { e.stopPropagation() });

	conversationList.find('.new-message').on('click', newMessage);

	conversation.on('click', '.return', retrunConversationList);

	conversation.on('click', '.pm-delete', deleteMessage);

	conversationNew.on('click', '.return', retrunConversationList);

	conversationNew.on('input', '.pm-search-contact', searchContact);

	conversationNew.on('click', '.pm-all-contacts', showAllContacts);

	conversationNew.on('click', '.list-group-item', function (e) {
		e.preventDefault();
		openConversation(e);
		$(e.delegateTarget).find('[name="search"]').val('');
		$(e.delegateTarget).find('.list-group').html('');
		$(e.delegateTarget).find('.pm-textarea').val('');
	});

	$('.pm-textarea').on('keyup', function () {
		$(this).parent().parent().find('.counter').text($(this).attr('maxlength') - this.value.length);
	}).on('keydown', function (e) {
    	if ($.trim(this.value).length && e.keyCode == 13) {
        	$(this.form).submit(); 
        	return false;
    	}
    });

	
	checkNotifications();
});