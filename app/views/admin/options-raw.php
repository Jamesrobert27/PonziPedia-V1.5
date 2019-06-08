<?php if (!Auth::userCan('manage_settings')) page_restricted();

if (isset($_POST['submit']) && csrf_filter()) {
	
	foreach (Option::all() as $option) {
		if (isset($_POST["{$option->group}_{$option->item}"]) && $_POST["{$option->group}_{$option->item}"] != $option->value) {
			Option::where('group', $option->group)
					->where('item', $option->item)
					->limit(1)
					->update(array('value' => $_POST["{$option->group}_{$option->item}"]));
		}
	}

	redirect_to('?page=options-raw', array('updated' => true));
}

if (isset($_POST['delete']) && csrf_filter()) {
	Option::find(key($_POST['delete']))->delete();

	redirect_to('?page=options-raw');
}
?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.raw_settings') ?></h3>

<div class="row">
	<div class="col-md-6">
		<?php if (Option::count('id')): ?>
			<?php if (Session::has('updated')): Session::deleteFlash(); ?>
				<div class="alert alert-success alert-dismissible">
					<span class="close" data-dismiss="alert">&times;</span>
					<?php _e('admin.changes_saved') ?>
				</div>
			<?php endif ?>

			<form action="" method="POST">
				<?php csrf_input() ?>
				
				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.save_changes') ?></button>
				</div>

				<?php foreach (Option::all() as $option): ?>
					<div class="form-group">
				        <label for="<?php echo "{$option->group}_{$option->item}" ?>"><?php echo "{$option->group}.{$option->item}" ?></label>
				        
				        <textarea name="<?php echo "{$option->group}_{$option->item}" ?>" id="<?php echo "{$option->group}_{$option->item}" ?>" class="form-control" rows="1" spellcheck="false"><?php 
				        echo $option->value;
				        ?></textarea>
				        
				        <button type="submit" name="delete[<?php echo $option->id ?>]" class="btn btn-danger btn-xs pull-right"><?php _e('admin.delete') ?></button>
				    </div>
				<?php endforeach ?>

				<div class="form-group">
					<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.save_changes') ?></button>
				</div>
			</form>
		<?php endif ?>
	</div>
</div>

<?php echo View::make('admin.footer')->render() ?>