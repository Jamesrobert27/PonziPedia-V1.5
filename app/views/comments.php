<?php 
$page 	   = isset($page)       ? $page       : get_current_url();
$pageUrl   = isset($page_url)   ? $page_url   : get_current_url();
$pageTitle = isset($page_title) ? $page_title : '';
$parentUrl = isset($_GET['_parent_url']) ? escape($_GET['_parent_url']) : '';
$maxlength    = Config::get('comments.maxlength');
$defaultSort  = Config::get('comments.default_sort');
$maxlengthTag = $maxlength ? "maxlength=\"{$maxlength}\"" : '';

if (!empty($parentUrl)) {
	$pageUrl = $parentUrl;
}
?>

<link rel="stylesheet" href="<?php echo asset_url('css/comments.css') ?>">
<script src="<?php echo asset_url('js/vendor/jquery.autosize.min.js') ?>"></script>
<script src="<?php echo asset_url('js/vendor/jquery.timeago.js') ?>"></script>
<script src="<?php echo asset_url('js/vendor/tmpl.js') ?>"></script>
<script src="<?php echo asset_url('js/comments.js') ?>"></script>

<div class="comments" data-page="<?php echo $page; ?>" data-sort="<?php echo $defaultSort; ?>">
	<div class="comment-count"></div>
	<div class="comment-box">
		<?php if (Auth::check()): ?>
			<form action="addComment" class="ajax-form">
				<input type="hidden" name="page" value="<?php echo $page; ?>">
				<input type="hidden" name="page_url" value="<?php echo $pageUrl; ?>">
				<input type="hidden" name="page_title" value="<?php echo $pageTitle; ?>">
				<div class="comment-avatar">
					<a href="#"><img src="<?php echo Auth::user()->avatar ?>"></a>
				</div>
				<div class="comment-postbox">
					<textarea name="comment" class="form-control" <?php echo $maxlengthTag ?> placeholder="<?php _e('comments.join_discussion') ?>"></textarea>
					<div class="pull-right comment-post">
						<?php if ($maxlength): ?>
							<span class="counter"><?php echo $maxlength; ?></span>
		            	<?php endif ?>
						<button type="submit" class="btn btn-primary post-btn" disabled><?php _e('comments.post') ?></button>
					</div>
				</div>
			</form>
		<?php else: ?>
			<p>
				<!-- <?php _e('comments.logged_in', array('attrs' => 'href="login.php" target="_parent"')) ?> -->
				<?php _e('comments.logged_in', array('attrs' => 'href="#" class="login-modal" data-target="#loginModal"')) ?>
			</p>
		<?php endif ?>
	</div>

	<div class="comment-sort dropdown">
		<button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown">
			<?php _e('comments.sort_by') ?> 
			<span class="current"></span> 
			<span class="caret"></span>
		</button>
		<ul class="dropdown-menu">
			<li><a href="javascript:void(0)" data-sort="1"><?php _e('comments.newest') ?> <span class="glyphicon glyphicon-ok"></span></a></li>
			<li><a href="javascript:void(0)" data-sort="2"><?php _e('comments.oldest') ?> <span class="glyphicon glyphicon-ok"></span></a></li>
			<li><a href="javascript:void(0)" data-sort="3"><?php _e('comments.best') ?> <span class="glyphicon glyphicon-ok"></span></a></li>
		</ul>
	</div>
	<ul class="comment-list"></ul>
	<div class="loader"></div>
	<button type="button" class="btn btn-default btn-sm btn-block show-more hidden"><?php _e('comments.show_more') ?></button>
</div>

<!-- Comment template -->
<script type="text/html" id="commentTemplate">
	<li class="comment clearfix" id="comment-<%= id %>" data-id="<%= id %>">
		<div class="comment-content">
			<div class="comment-menu dropdown">
				<span class="comment-collapse" title="<?php _e('comments.collapse') ?>">−</span>
				<span class="comment-expand" title="<?php _e('comments.expand') ?>">+</span>

				<% if (auth.moderate || auth.edit) { %>
				<span class="sep"></span>

				<div class="dropdown-toggle" data-toggle="dropdown">
					<span class="caret"></span>
				</div>
		
				<ul class="dropdown-menu">
					<% if (auth.edit) { %>
						<li><a href="javascript:void(0)" class="comment-edit"><?php _e('comments.edit') ?></a></li>
					<% } %>

					<% if (auth.moderate) { %>
						<li><a href="javascript:void(0)" class="comment-trash" data-trash="<%= id %>"><?php _e('comments.trash') ?></a></li>
					<% } %>
				</ul>
				<% } %>		
			</div> 
			
			<div class="comment-avatar">
				<a href="#"><img src="<%= user.avatar %>"></a>
			</div>

			<div class="comment-body">
				<div class="comment-header">
					<a href="#" class="comment-author"><%= user.name %></a> 
					
					<% if (parent) { %>
						<a href="<?php echo $parentUrl; ?>#comment-<%= parent.id %>" class="comment-parent-link comment-url" title="<?php _e('comments.in_reply') ?>">
							<span class="glyphicon glyphicon-share-alt"></span><%= parent.user.name %></a>
					<% } %>

					<span class="comment-bullet">&bull;</span>
					
					<a href="<?php echo $parentUrl; ?>#comment-<%= id %>" class="comment-time comment-url">
						<time class="timeago" datetime="<%= date %>" title="<%= date %>"></time>
					</a>

					<span class="linked"><?php _e('comments.linked') ?></span>
				</div>

				<div class="comment-on-hold <% if (status == 1) { %>hidden<% } %>">
					<span class="glyphicon glyphicon-exclamation-sign"></span>
					<?php _e('comments.on_hold') ?>
				</div>

				<div class="comment-text"><%= content %></div>

				<% if (auth.edit) { %>
					<div class="comment--text"><%= _content %></div>
					
					<form action="updateComment" class="ajax-form">
						<div class="edit-box">
							<input type="hidden" name="id" value="<%= id %>">
							<textarea name="comment" class="form-control" <?php echo $maxlengthTag ?>></textarea>
							
							<div class="pull-right comment-post">
								<?php if ($maxlength) { ?>
									<span class="counter"><?php echo $maxlength; ?></span>
								<?php } ?>

								<button type="button" class="btn btn-default btn-sm cancel"><?php _e('comments.cancel') ?></button>
								<button type="submit" class="btn btn-primary btn-sm post-btn" disabled><?php _e('comments.save') ?></button>
							</div>
						</div>
					</form>
				<% } %>

				<div class="comment-footer">
					
					<div class="comment-votes" data-comment="<%= id %>">
						<?php if (Auth::check()): ?>
							
							<span class="upvotes" data-votes="<%= upvotes %>"><%= (upvotes-downvotes > 0 ? upvotes-downvotes : '') %></span>
							<a href="javascript:void(0)" class="upvote <%= (auth.vote === 1 ? 'voted' : '') %>" title="<?php _e('comments.upvote') ?>"><span class="glyphicon glyphicon glyphicon-heart" style="font-size: 20px;"></span></a>
							<span class="sep"></span>
							<span class="downvotes" data-votes="<%= downvotes %>"><%= (downvotes-upvotes > 0 ? downvotes-upvotes : '') %></span>
							<a href="javascript:void(0)" class="downvote <%= (auth.vote === 2 ? 'voted' : '') %>" title="<?php _e('comments.downvote') ?>"><span class="glyphicon glyphicon-thumbs-down" style="font-size: 20px;"></span></a>

						<?php else: ?>
							<span class="upvotes" data-votes="<%= upvotes %>"><%= (upvotes-downvotes > 0 ? upvotes-downvotes : '') %></span>
							<a href="javascript:void(0)" title="<?php _e('comments.logged_vote') ?>"><span class="glyphicon glyphicon glyphicon-heart" style="font-size: 20px;"></span></a>
							<span class="sep"></span>
							<span class="downvotes" data-votes="<%= downvotes %>"><%= (downvotes-upvotes > 0 ? downvotes-upvotes : '') %></span>
							<a href="javascript:void(0)" title="<?php _e('comments.logged_vote') ?>"><span class="glyphicon glyphicon-thumbs-down" style="font-size: 20px;"></span></a>
						<?php endif ?>
					</div>
					<span class="comment-bullet">&bull;</span>
					

					<?php if (Auth::check()): ?>
					<% if (status == 1 && auth.reply) { %>
						<a href="javascript:void(0)" class="comment-reply"><?php _e('comments.reply') ?></a>

						<div class="reply-box">
							<div class="comment-avatar">
								<a href="#"><img src="<?php echo Auth::user()->avatar ?>"></a>
							</div>

							<form action="addComment" class="reply-body ajax-form">
								<input type="hidden" name="page" value="<?php echo $page ?>">
								<input type="hidden" name="page_url" value="<?php echo $pageUrl; ?>">
								<input type="hidden" name="page_title" value="<?php echo $pageTitle; ?>">
								<input type="hidden" name="parent" value="<%= id %>">
								<textarea name="comment" class="form-control" <?php echo $maxlengthTag ?>></textarea>
								<div class="pull-right comment-post">
									<?php if ($maxlength): ?>
										<span class="counter"><?php echo $maxlength; ?></span>
									<?php endif ?>
									<button type="button" class="btn btn-default btn-sm cancel"><?php _e('comments.cancel') ?></button>
									<button type="submit" class="btn btn-primary btn-sm post-btn" disabled><?php _e('comments.reply') ?></button>
								</div>
							</form>
						</div>
					<% } %>
					<?php endif ?>
				</div>
			</div>
		</div>

		<ul class="comment-list children"></ul>
	</li>
</script>
