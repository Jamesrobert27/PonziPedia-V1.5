jQuery(function($) {
	function _log (xhr) { 
		EasyLogin.log((xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : xhr.responseText) 
	}

	function unescapeHtml (str) {
		return String(str).replace(/&amp;/g, '&').replace(/&quot;/g, '"').replace(/&#39;/g, '\'').replace(/&lt;/g, '<').replace(/&gt;/g, '>');
	}

	function newLine (str)
	{
		return str.replace(/\n/g, '<br>');
	}

	function commentLengthCounter (e) {
		$(e.delegateTarget).find('.counter').text($(this).attr('maxlength') - this.value.length);
		$(e.delegateTarget).find('.post-btn').prop('disabled', !this.value.length);
	}

	function Comments (container) {
		this.container = $(container);
		this.init();
	}

	Comments.prototype = {
		init: function () {
			this.sort = this.container.attr('data-sort')||1;
			this.skip = 0;
			this.count = 0;
			this.linked = 0;

			var hash = window.location.hash;

			if (hash.indexOf('#comment-') > -1) {
				this.linked = hash.substr(9, hash.length);
			}

			this.bindEvents();
			this.load();

			this.find('.comment-postbox textarea').autosize({append:''});
		},

		bindEvents: function () {
			var self = this;

			this.selectSort();

			// Login modal
			this.find('.login-modal').on('click', function(e) {
				e.preventDefault();
				
				var target = $(this).attr('data-target');

				if (window.parent.embedCommentsModalCallback) {
					window.parent.embedCommentsModalCallback(target);
				} else {
					$(target).modal('show');
				}
			});

			// Sort
			this.find('.comment-sort').on('click', 'a', function (e) {
				e.preventDefault();

				self.sort = $(this).attr('data-sort');

				self.selectSort();

				self.reload();
			});

			// Textarea
			this.find('.comment-postbox').on('focus', 'textarea', function (e) {
				$(e.delegateTarget).find('.comment-post').show();
			})
			.on('focusout', 'textarea', function (e) {
				if (!$.trim($(this).val()).length) {
					$(e.delegateTarget).find('.comment-post').hide();
				}
			})
			.on('keyup', 'textarea', commentLengthCounter);

			// Show more
			this.find('.show-more').on('click', function() {
				$(this).attr('data-loading-text', EasyLogin.trans('loading'));
		
				$(this).button('loading');

				self.linked = 0;

				self.load();
			});
		},

		selectSort: function () {
			var $sort = this.find('.comment-sort');

			$sort.find('.current').text( $sort.find('[data-sort="'+this.sort+'"]').text() );

			$sort.find('li').removeClass('selected');

			$sort.find('[data-sort="'+this.sort+'"]').parent().addClass('selected');
		},

		load: function () {
			var self = this,
				$loader = this.find('.loader'),
				$list = this.find('.comment-list').first(),
				$comment;


			$loader.show();

			$.ajax({
				url: EasyLogin.options.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'get_comments', 
					page: this.container.attr('data-page'),
					sort: this.sort,
					skip: this.skip,
					linked: this.linked,
				},
			})
			.done(function(response) {
				response = response.message;

				self.find('.comment-sort').show();

				self.render($list, response.comments);

				if (response.parents > response.take) {
					self.find('.show-more').removeClass('hidden');
				} 

				if (response.comments.length < response.take) {
					self.find('.show-more').addClass('hidden');
				}

				self.skip += response.comments.length;

				self.renderCount(response.total);

				if (self.linked) {
					var $linked = $list.find('[data-id="'+self.linked+'"]');

					if ($linked.length) {
						$linked.find('.linked').first().show();
						$('body').animate({scrollTop: $linked.offset().top - 70}, 300);
					}
				}
			})
			.fail(_log)
			.always(function() {
				$loader.hide();
				self.find('.show-more').button('reset');
			});
		},

		reload: function() {
			this.find('.comment-list').first().html('');
			this.skip = 0;
			this.linked = 0;
			this.load();
		},

		render: function (list, comments, parent, prepend) {
			var $comment;

			for (var i = 0; i < comments.length; i++) {
				comments[i].parent = parent;

				comments[i].content = newLine(comments[i].content);

				$comment = $(tmpl('commentTemplate', comments[i]));

				if (prepend)
					list.prepend($comment);
				else 
					list.append($comment);

				this.attachEvents($comment);

				if (comments[i].replies.length) {
					this.render($comment.find('.comment-list'), comments[i].replies, comments[i]);
				}
			}
		},

		attachEvents: function (comment) {
			comment.find('.comment-content')
				.on('click', '.comment-collapse', $.proxy(this.collapse, this))
				.on('click', '.comment-expand', $.proxy(this.expand, this))
				.on('click', '.comment-trash', $.proxy(this.trash, this))
				.on('click', '.cancel', $.proxy(this.cancel, this))
				.on('click', '.comment-reply', $.proxy(this.reply, this))
				.on('click', '.comment-edit', $.proxy(this.edit, this))
				.on('click', '.upvote, .downvote', $.proxy(this.vote, this))
				.on('click', '.comment-url', $.proxy(this.url, this))
				.on('keyup', 'textarea', commentLengthCounter)
				.on('submit', '.ajax-form', EasyLogin.ajaxForm)
				.find('.timeago').timeago();

			comment.find('textarea').autosize({append:''});
		},

		url: function (e) {
			e.preventDefault();

			var href = $(e.currentTarget).attr('href');
			var id;

			if (href.indexOf('#comment-') > -1) {
				id = href.substr(href.indexOf('#comment-')+9, href.length);
			}

			if (!id) return;

			var $comment = this.find('[data-id="'+id+'"]');
			if ($comment) {
				if (window.parent.embedCommentsCallback) {
					window.parent.embedCommentsCallback(id, $comment.offset().top);
				} else {
					$('body').animate({scrollTop: $comment.offset().top - 10}, 10);
				}
				window.location.hash = 'comment-'+id;
			}
		},

		collapse: function (e) {
			$(e.delegateTarget).parent().addClass('collapsed');
			$(e.currentTarget).hide();
			$(e.delegateTarget).find('.comment-expand').show();
		},

		expand: function (e) {
			$(e.delegateTarget).parent().removeClass('collapsed');
			$(e.currentTarget).hide();
			$(e.delegateTarget).find('.comment-collapse').show();
		},

		cancel: function (e) {
			$(e.delegateTarget).find('.comment-text').show();
			$(e.delegateTarget).find('.reply-box, .edit-box, .alert').hide();
			$(e.delegateTarget).find('textarea').val('').trigger('keyup');
		},

		reply: function (e) {
			$(e.delegateTarget).find('.cancel').trigger('click');
			$(e.delegateTarget).find('.reply-box').toggle();
		},

		edit: function (e) {
			e.preventDefault();
			
			$(e.delegateTarget).find('.cancel').trigger('click');
			$(e.delegateTarget).find('.comment-text').toggle();

			var value     = unescapeHtml($(e.delegateTarget).find('.comment--text').html())
				$textarea = $(e.delegateTarget).find('.edit-box').toggle().find('textarea');

			$textarea.val(value);
			$textarea.trigger('keyup').trigger('autosize.resize');
		},

		trash: function (e) {
			e.preventDefault();
			
			var id = $(e.currentTarget).attr('data-trash');
			$('.comments [data-id="'+id+'"]').remove();
			
			$.post(EasyLogin.options.ajaxUrl, {action: 'trash_comment', id: id});
		},

		vote: function (e) {
			e.preventDefault();
			
			var type,
			$el = $(e.currentTarget),
			$votes = $el.parent(),

			$upvote   = $votes.find('.upvote'),
			$downvote = $votes.find('.downvote'),
			
			$upvotes   = $votes.find('.upvotes'),
			$downvotes = $votes.find('.downvotes'),
			
			upvotes   = parseInt($upvotes.attr('data-votes')),
			downvotes = parseInt($downvotes.attr('data-votes')),

			setDownvotes = function (val) {
				$downvotes.attr('data-votes', val);
			},

			setUpvotes = function (val) {
				$upvotes.attr('data-votes', val);
			};

			// Click on upvote
			if ($el.hasClass('upvote')) {
				// Remove upvote
				if ($el.hasClass('voted')) {
					$el.removeClass('voted');
					setUpvotes(upvotes - 1);
					type = 1;
				}
				// Upvote
				else {
					type = 3;
					if ($downvote.hasClass('voted')) {
						$downvote.removeClass('voted');
						setDownvotes(downvotes - 1);
					}

					$el.addClass('voted');
					setUpvotes(upvotes + 1);
				}
			}
			// Click on downvote
			else {
				// Remove downvote
				if ($el.hasClass('voted')) {
					$el.removeClass('voted');
					setDownvotes(downvotes - 1);
					type = 2;
				} 
				// Downvote
				else {
					type = 4;
					if ($upvote.hasClass('voted')) {
						$upvote.removeClass('voted');
						setUpvotes(upvotes - 1);
					}

					$el.addClass('voted');
					setDownvotes(downvotes + 1);
				}
			}
			
			upvotes   = parseInt($upvotes.attr('data-votes'));
			downvotes = parseInt($downvotes.attr('data-votes'));

			  $upvotes.text(upvotes-downvotes > 0 ? upvotes-downvotes : '');
			$downvotes.text(downvotes-upvotes > 0 ? downvotes-upvotes : '');

			$.post(EasyLogin.options.ajaxUrl, {action: 'vote_comment', id: $votes.attr('data-comment'), type: type});
		},

		renderCount: function(count) {
			if (count) this.count = count;

			this.find('.comment-count').text(this.count + EasyLogin.trans(this.count == 1 ? 'comment' : 'comments'));
		},

		// Find element in the ".comments" container
		find: function(selector) {
			return this.container.find(selector);
		},
	};

	// Add comment ajax form callback
	EasyLogin.ajaxFormCb.addComment = function(comment, form) {
		var comments = form.closest('.comments').data('_comments'),
			parentId = form.find('input[name="parent"]').val();
		
		if (parentId) {
			var $parent = comments.find('[data-id="'+parentId+'"]').first();

			comments.render($parent.find('.comment-list').first(), [comment], {
				id: parentId, 
				user: {name: $parent.find('.comment-author').first().text()}
			});

			$parent.find('.reply-box .cancel').first().trigger('click');
		} else {
			comments.render(comments.find('.comment-list').first(), [comment], null, true);
			comments.find('.comment-postbox textarea').val('').trigger('focusout').trigger('keyup');
		}

		comments.count++;
		comments.renderCount();

		form.find('textarea').trigger('autosize.resize');
	};

	// Update comment ajax form callback
	EasyLogin.ajaxFormCb.updateComment = function(comment) {
		var $comment = $('.comments [data-id="'+comment.id+'"]').children('.comment-content');

		$comment.find('.comment-text').html(newLine(comment.content));
		$comment.find('.comment--text').html(unescapeHtml(comment._content));
		
		if (comment.status != 1) {
			$comment.find('.comment-on-hold').removeClass('hidden');
		}

		$comment.find('.cancel').trigger('click');
	};

	// Init comments
	$('.comments').each(function () {
		if (!$.data(this, '_comments')) {
			$.data(this, '_comments', new Comments(this));
		}
	});
});