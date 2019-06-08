<?php if (!Auth::userCan('manage_settings')) page_restricted();
 
if (isset($_POST['submit']) && csrf_filter()) {

	Config::set('pms.realtime', (int) $_POST['realtime']);

	if (!empty($_POST['delay']) && is_numeric($_POST['delay'])) {
		Config::set('pms.delay', abs($_POST['delay']));
	}

	if (is_numeric($_POST['maxlength'])) {
		Config::set('pms.maxlength', abs($_POST['maxlength']));
	}

	if (is_numeric($_POST['limit'])) {
		Config::set('pms.limit', abs($_POST['limit']));
	}

	if (User::find($_POST['webmaster'])) {
		Config::set('pms.webmaster', $_POST['webmaster']);
	}

	redirect_to('?page=options-pms', array('updated' => true));
}
?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.pms_settings') ?></h3>

<div class="row">
	<div class="col-md-6">
		<?php if (Session::has('updated')): Session::deleteFlash(); ?>
			<div class="alert alert-success alert-dismissible">
				<?php _e('admin.changes_saved') ?>
				<span class="close" data-dismiss="alert">&times;</span>
			</div>
		<?php endif ?>

		<form action="" method="POST">
			<?php csrf_input() ?>
			
			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.save_changes') ?></button>
			</div>

			<div class="form-group">
				<label for="realtime"><?php _e('options.pms.realtime') ?></label>
				<select name="realtime" id="realtime" class="form-control">
	        		<option value="1" <?php echo Config::get('pms.realtime') == '1' ? 'selected':'' ?>><?php _e('admin.yes') ?></option>
	        		<option value="0" <?php echo Config::get('pms.realtime') == '0' ? 'selected':'' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block"><?php _e('options.pms.realtime_help') ?></p>
			</div>

			<div class="form-group">
		        <label for="delay"><?php _e('options.pms.delay') ?></label>
		        <input type="text" name="delay" id="delay" value="<?php echo Config::get('pms.delay') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.pms.delay_help') ?></p>
		    </div>

		    <div class="form-group">
		        <label for="maxlength"><?php _e('options.pms.maxlength') ?></label>
		        <input type="text" name="maxlength" id="maxlength" value="<?php echo Config::get('pms.maxlength') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.pms.maxlength_help') ?></p>
		    </div>

		    <div class="form-group">
		        <label for="limit"><?php _e('options.pms.limit') ?></label>
		        <input type="text" name="limit" id="limit" value="<?php echo Config::get('pms.limit') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.pms.limit_help') ?></p>
		    </div>

		    <div class="form-group">
		        <label for="webmaster"><?php _e('options.pms.webmaster') ?></label>
		        <input type="text" name="webmaster" id="webmaster" value="<?php echo Config::get('pms.webmaster') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.pms.webmaster_help') ?></p>
		    </div>

			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.save_changes') ?></button>
			</div>
		</form>
	</div>
</div>
<?php echo View::make('admin.footer')->render() ?>