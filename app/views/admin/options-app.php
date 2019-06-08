<?php if (!Auth::userCan('manage_settings')) page_restricted();

$colorSchemes = array('dark', 'light', 'blue', 'coffee', 'ectoplasm', 'midnight');

if (isset($_POST['submit']) && csrf_filter()) {

	$data = array(
    	'debug' => $_POST['debug'],
    	'url' => $_POST['url'],
    	'color_scheme' => $_POST['color_scheme'],
    	'locale' => $_POST['locale'],
    	'timezone' => $_POST['timezone'],
    	'csrf' => $_POST['csrf'],
    );

	$rules = array(
    	'debug' => 'in:0,1',
    	'url' => 'url',
    	'color_scheme' => 'in:'.implode(',', $colorSchemes),
    	'locale' => 'in:'.implode(',', Config::get('app.locales', array())),
    	'timezone' => 'timezone',
    	'csrf' => 'in:0,1',
    );

    $validator = Validator::make($data, $rules);

    $validator->passes();
    $errors = $validator->errors();

    if (!$errors->has('debug')) {
    	Config::set('app.debug', $_POST['debug']);
    }

    if (!$errors->has('url')) {
    	Config::set('app.url', $_POST['url']);
    }

    Config::set('app.name', escape($_POST['name']));

	if (!$errors->has('color_scheme')) {
    	Config::set('app.color_scheme', $_POST['color_scheme']);
    }

    if (!$errors->has('locale')) {
    	Config::set('app.locale', $_POST['locale']);
    }

	$locales = array();
	foreach (explode(',', escape($_POST['locales'])) as $key => $value) {
		$locale = explode(':', $value);
		if (isset($locale[0], $locale[1])) {
			$v1 = trim(str_replace(' ', '', $locale[0]));
			$v2 = trim($locale[1]);
			if (!empty($v1) && !empty($v2)) $locales[$v1] = $v2;
		}
	}
	if (empty($locales)) $locales['en'] = 'English';

	Config::set('app.locales', $locales);

	if (!$errors->has('timezone')) {
    	Config::set('app.timezone', $_POST['timezone']);
    }

    if (!$errors->has('csrf')) {
    	Config::set('app.csrf', $_POST['csrf']);
    }

	redirect_to('?page=options-app', array('updated' => true));
}

?>
<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.general_settings') ?></h3>

<div class="row">
	<div class="col-md-6">
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

			<div class="form-group">
				<label for="debug"><?php _e('options.app.debug') ?></label>
				<select name="debug" id="debug" class="form-control">
	        		<option value="1" <?php echo Config::get('app.debug') == '1' ? 'selected':'' ?>><?php _e('admin.yes') ?></option>
	        		<option value="0" <?php echo Config::get('app.debug') == '0' ? 'selected':'' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block"><?php _e('options.app.debug_help') ?></p>
			</div>
			
			<div class="form-group">
		        <label for="url"><?php _e('options.app.url') ?></label>
		        <input type="text" name="url" id="url" value="<?php echo Config::get('app.url') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.app.url_help') ?></p>
		    </div>

		    <div class="form-group">
		        <label for="name"><?php _e('options.app.name') ?></label>
		        <input type="text" name="name" id="name" value="<?php echo Config::get('app.name') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.app.name_help') ?></p>
		    </div>
			
			<div class="form-group">
			    <label for="color_scheme"><?php _e('options.app.color_scheme') ?></label>
				<select name="color_scheme" id="color_scheme" class="form-control">
					<?php foreach ($colorSchemes as $color) {
						echo '<option value="'.$color.'"'.(Config::get('app.color_scheme')==$color?' selected':'').'>'.ucfirst($color).'</option>';
					} ?>
				</select>
				<p class="help-block"><?php _e('options.app.color_scheme_help') ?></p>
			</div>

			<div class="form-group">
				<label for="locale"><?php _e('options.app.locale') ?></label>
				<select name="locale" id="locale" class="form-control">
	        		<?php foreach (Config::get('app.locales', array()) as $key => $value) {
	        			echo '<option value="'.$key.'"'.(Config::get('app.locale') == $key?' selected':'').'>'.$value.'</option>';
	        		} ?>
				</select>
				<p class="help-block"><?php _e('options.app.locale_help') ?></p>
			</div>

			<div class="form-group">
				<label for="locales"><?php _e('options.app.locales') ?></label>
				<textarea name="locales" id="locales" class="form-control" spellcheck="false"><?php 
					foreach (Config::get('app.locales', array()) as $key => $value) {
						echo "$key : $value , ";
					} 
				?></textarea>
				<p class="help-block"><?php _e('options.app.locales_help') ?></p>
			</div>

			 <div class="form-group">
		        <label for="timezone"><?php _e('options.app.timezone') ?></label>
		        
		        <select name="timezone" id="timezone" class="form-control">
	        		<?php foreach (DateTimeZone::listIdentifiers() as $timezone) {
	        			echo '<option value="'.$timezone.'"'.(Config::get('app.timezone') == $timezone?' selected':'').'>'.$timezone.'</option>';
	        		} ?>
				</select>

		        <p class="help-block"><?php _e('options.app.timezone_help') ?></p>
		    </div>

		    <div class="form-group">
				<label for="csrf"><?php _e('options.app.csrf') ?></label>
				<select name="csrf" id="csrf" class="form-control">
	        		<option value="1" <?php echo Config::get('app.csrf') == '1' ? 'selected':'' ?>><?php _e('admin.yes') ?></option>
	        		<option value="0" <?php echo Config::get('app.csrf') == '0' ? 'selected':'' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block"><?php _e('options.app.csrf_help') ?></p>
			</div>
		
			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.save_changes') ?></button>
			</div>
		</form>
	</div>
</div>
<?php echo View::make('admin.footer')->render() ?>