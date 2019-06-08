<?php if (!Auth::userCan('moderate')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.comments') ?></h3>

<link href="<?php echo asset_url('css/vendor/dataTables.bootstrap.css') ?>" rel="stylesheet">
<script src="<?php echo asset_url('js/vendor/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo asset_url('js/vendor/dataTables.bootstrap.js') ?>"></script>
<script>
	$(document).ready(function() {
		EasyLogin.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
		EasyLogin.admin.commentsDT();
	});
</script>


<form action="comments_bulk_action" method="POST" id="comments_form" class="ajax-form">
	<div style="display: none"><div class="alert"></div></div>

	<ul class="dt-filter status-filter">
		<li class="active"><a href="#" data-status=""><?php _e('admin.all') ?></a> |</li>

		<li><a href="#" data-status="1"><?php _e('admin.approved') ?></a> |</li>

		<li>
			<a href="#" data-status="0">
				<?php _e('admin.pending') ?>
				<span class="count">(<?php echo Comments::countPending(); ?>)</span>
			</a> |
		</li>

		<li>
			<a href="#" data-status="2">
				<?php _e('admin.trash') ?>
				<span class="count">(<?php echo Comments::countTrash(); ?>)</span>
			</a>
		</li>
	</ul>

	<table class="table table-striped table-bordered table-hover table-dt" id="comments">
		<thead>
			<tr>
				<th class="comment-cb"><input type="checkbox" class="select-all" value="1"></th>
				<th class="column-user"><?php _e('admin.user') ?></th>
				<th class="column-comment"><?php _e('admin.comment') ?></th>
				<th class="column-date"><?php _e('admin.date') ?></th>
				<th class="column-response"><?php _e('admin.in_response') ?></th>
				<th class="column-action"><?php _e('admin.action') ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</form>

<!-- Commen reply Modal -->
<div class="modal fade" id="commentReplyModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="comment_reply" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('admin.reply_to') ?> <b class="user"></b></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>
					<input type="hidden" name="id">
		          	
		          	<div class="form-group">
				        <label for="content"><?php _e('admin.comment') ?></label>
				        <textarea type="text" name="content" id="content" class="form-control" rows="5"></textarea>
				    </div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-primary"><?php _e('admin.reply') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php echo View::make('admin.footer')->render() ?>