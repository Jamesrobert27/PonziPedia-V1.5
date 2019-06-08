<?php if (!Auth::userCan('message_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header">
	<?php _e('admin.messages') ?>
	<a href="?page=message-new" class="btn btn-default btn-sm"><?php _e('admin.new_message') ?></a>
</h3>

<link href="<?php echo asset_url('css/vendor/dataTables.bootstrap.css') ?>" rel="stylesheet">
<script src="<?php echo asset_url('js/vendor/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo asset_url('js/vendor/dataTables.bootstrap.js') ?>"></script>
<script>
	$(document).ready(function() {
		EasyLogin.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
		EasyLogin.admin.messagesDT();
	});
</script>

<form action="" method="POST" id="messages_form">
	<table class="table table-striped table-bordered table-hover table-dt" id="messages">
		<thead>
			<tr>
				<th><input type="checkbox" class="select-all" value="1"></th>
				<th><?php _e('admin.message') ?></th>
				<th><?php _e('admin.from_to') ?></th>
				<th><?php _e('admin.date') ?></th>
				<th><?php _e('admin.action') ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</form>

<!-- Delete conversation Modal -->
<div class="modal fade" id="deleteConversationModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="deleteConversation" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('admin.confirm_action') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>
					<input type="hidden" name="user_id">
	          		<p><?php _e('admin.confirm_delete_conversation', array('user' => '<b class="user"></b>')) ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('admin.no'); ?></button>
					<button type="submit" class="btn btn-danger"><?php _e('admin.yes') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- Delete conversations Modal -->
<div class="modal fade" id="deleteConversationsModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="deleteConversations" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('admin.confirm_action') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>
					<input type="hidden" name="conversations">
	          		<p><?php _e('admin.confirm_delete_conversations', array('conversations' => '<b class="conversations"></b>')) ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('admin.no') ?></button>
					<button type="submit" class="btn btn-danger"><?php _e('admin.yes') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>


<?php echo View::make('admin.footer')->render() ?>