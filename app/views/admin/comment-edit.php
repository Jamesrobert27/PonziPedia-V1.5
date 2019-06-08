<?php if (!Auth::userCan('moderate')) page_restricted();

$id = isset($_GET['id']) ? absint($_GET['id']) : 0;

$comment = Comment::find($id);

if (isset($_POST['submit']) && csrf_filter()) {
	$content = isset($_POST['content']) ? $_POST['content']        : '';
	$status  = isset($_POST['status'])  ? absint($_POST['status']) : 0;

	$comment->content = Comments::parse($content);
	$comment->status  = $status;
	$comment->updated = with(new DateTime)->format('Y-m-d H:i:s');

	$comment->save();

	redirect_to('?page=comments');
}
?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.edit_comment') ?></h3>
<p><a href="?page=comments"><?php _e('admin.back_to_comments') ?></a></p>

<div class="row">
	<div class="col-md-6">
		<?php if ($comment): ?>
			<form action="" method="POST">
				<?php csrf_input() ?>

				<div class="form-group">
	            	<label for="status"><?php _e('admin.comment_status') ?></label>
	            	<select name="status" id="status" class="form-control">
	            		<option value="0" <?php echo $comment->status === 0 ? 'selected' : ''; ?>><?php _e('admin.unapproved') ?></option>
	            		<option value="1" <?php echo $comment->status === 1 ? 'selected' : ''; ?>><?php _e('admin.approved') ?></option>
	            		<option value="2" <?php echo $comment->status === 2 ? 'selected' : ''; ?>><?php _e('admin.trash') ?></option>
					</select>
	            </div>

				<div class="form-group">
			        <label for="content"><?php _e('admin.comment') ?></label>
			        <textarea type="text" name="content" id="content" class="form-control" rows="5"><?php echo $comment->content; ?></textarea>
			    </div>

				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.update_comment') ?></button>
				</div>
			</form>
		<?php else: ?>
			<div class="alert alert-danger"><?php _e('errors.commentid') ?></div>
		<?php endif ?>
	</div>
</div>

<?php echo View::make('admin.footer')->render() ?>