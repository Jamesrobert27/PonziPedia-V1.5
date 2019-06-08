<?php if (!Auth::userCan('manage_settings')) page_restricted();

if (isset($_POST['submit']) && csrf_filter()) {

	$restrictedWords = array();
	foreach (explode(',', escape($_POST['restricted_words'])) as $word) {
		$word = trim($word);
		if (!empty($word)) {
			$restrictedWords[] = $word;
		}
	}

	$blacklist = array();
	foreach (explode(',', escape($_POST['blacklist'])) as $userId) {
		if (!empty($userId) && is_numeric($userId)) {
			$blacklist[] = trim($userId);
		}
	}

	$blacklist = array();
	foreach (explode(',', escape($_POST['blacklist'])) as $userId) {
		if (!empty($userId) && is_numeric($userId)) {
			$blacklist[] = trim($userId);
		}
	}

	$whitelist = array();
	foreach (explode(',', escape($_POST['whitelist'])) as $userId) {
		if (!empty($userId) && is_numeric($userId)) {
			$whitelist[] = trim($userId);
		}
	}

	$maxLinks = null;
	if (!empty($_POST['max_links']) && is_numeric($_POST['max_links'])) {
		$maxLinks = $_POST['max_links'];
	}

	$maxPending = null;
	if (!empty($_POST['max_pending']) && is_numeric($_POST['max_pending'])) {
		$maxPending = $_POST['max_pending'];
	}

	$perPage = null;
	if (!empty($_POST['per_page']) && is_numeric($_POST['per_page'])) {
		$perPage = $_POST['per_page'];
	}

	$maxLength = null;
	if (!empty($_POST['maxlength']) && is_numeric($_POST['maxlength'])) {
		$maxLength = $_POST['maxlength'];
	}

	$timeBetween = null;
	if (!empty($_POST['time_between']) && is_numeric($_POST['time_between'])) {
		$timeBetween = $_POST['time_between'];
	}

	Config::set('comments.moderation', (int) $_POST['moderation']);

	Config::set('comments.use_smilies', (int) $_POST['use_smilies']);

	Config::set('comments.replies', (int) $_POST['replies']);

	Config::set('comments.restricted_words', $restrictedWords);

	Config::set('comments.blacklist', $blacklist);

	Config::set('comments.whitelist', $whitelist);

	Config::set('comments.max_links', $maxLinks);

	Config::set('comments.max_pending', $maxPending);

	Config::set('comments.per_page', $perPage);

	Config::set('comments.maxlength', $maxLength);

	Config::set('comments.time_between', $timeBetween);

	Config::set('comments.default_sort', (int) $_POST['default_sort']);

	redirect_to('?page=options-comments', array('updated' => true));
}

?>
<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.comments_settings') ?></h3>

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
				<label for="moderation"><?php _e('options.comments.moderation') ?></label>
				<select name="moderation" id="moderation" class="form-control">
	        		<option value="1" <?php echo  (bool) Config::get('comments.moderation') ? 'selected' : '' ?>><?php _e('admin.yes') ?></option>
	        		<option value="0" <?php echo !(bool) Config::get('comments.moderation') ? 'selected' : '' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block"><?php _e('options.comments.moderation_help') ?></p>
			</div>

			<div class="form-group">
				<label for="use_smilies"><?php _e('options.comments.use_smilies') ?></label>
				<select name="use_smilies" id="use_smilies" class="form-control">
	        		<option value="1" <?php echo  (bool) Config::get('comments.use_smilies') ? 'selected' : '' ?>><?php _e('admin.yes') ?></option>
	        		<option value="0" <?php echo !(bool) Config::get('comments.use_smilies') ? 'selected' : '' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block"><?php _e('options.comments.use_smilies_help') ?></p>
			</div>

			<div class="form-group">
				<label for="replies"><?php _e('options.comments.replies') ?></label>
				<select name="replies" id="replies" class="form-control">
	        		<option value="1" <?php echo  (bool) Config::get('comments.replies') ? 'selected' : '' ?>><?php _e('admin.yes') ?></option>
	        		<option value="0" <?php echo !(bool) Config::get('comments.replies') ? 'selected' : '' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block"><?php _e('options.comments.replies_help') ?></p>
			</div>

			<div class="form-group">
				<label for="restricted_words"><?php _e('options.comments.restricted_words') ?></label>
				<textarea name="restricted_words" id="restricted_words" class="form-control" spellcheck="false" rows="5"><?php 
					foreach (Config::get('comments.restricted_words', array()) as $word) {
						echo trim($word) . ', ';
					} 
				?></textarea>
				<p class="help-block"><?php _e('options.comments.restricted_words_help') ?></p>
			</div>

			<div class="form-group">
				<label for="blacklist"><?php _e('options.comments.blacklist') ?></label>
				<textarea name="blacklist" id="blacklist" class="form-control" spellcheck="false" rows="5"><?php 
					foreach (Config::get('comments.blacklist', array()) as $id) {
						echo trim($id) . ', ';
					} 
				?></textarea>
				<p class="help-block"><?php _e('options.comments.blacklist_help') ?></p>
			</div>

			<div class="form-group">
				<label for="whitelist"><?php _e('options.comments.whitelist') ?></label>
				<textarea name="whitelist" id="whitelist" class="form-control" spellcheck="false" rows="5"><?php 
					foreach (Config::get('comments.whitelist', array()) as $id) {
						echo trim($id) . ', ';
					} 
				?></textarea>
				<p class="help-block"><?php _e('options.comments.whitelist_help') ?></p>
			</div>

			<div class="form-group">
		        <label for="max_links"><?php _e('options.comments.max_links') ?></label>
		        <input type="text" name="max_links" id="max_links" value="<?php echo Config::get('comments.max_links') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.comments.max_links_help') ?></p>
		    </div>

		    <div class="form-group">
		        <label for="max_pending"><?php _e('options.comments.max_pending') ?></label>
		        <input type="text" name="max_pending" id="max_pending" value="<?php echo Config::get('comments.max_pending') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.comments.max_pending_help') ?></p>
		    </div>

		    <div class="form-group">
		        <label for="per_page"><?php _e('options.comments.per_page') ?></label>
		        <input type="text" name="per_page" id="per_page" value="<?php echo Config::get('comments.per_page') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.comments.per_page_help') ?></p>
		    </div>

		    <div class="form-group">
				<label for="default_sort"><?php _e('options.comments.default_sort') ?></label>
				<select name="default_sort" id="default_sort" class="form-control">
	        		<option value="1" <?php echo (int) Config::get('comments.default_sort') === 1 ? 'selected' : '' ?>><?php _e('admin.newest') ?></option>
	        		<option value="2" <?php echo (int) Config::get('comments.default_sort') === 2 ? 'selected' : '' ?>><?php _e('admin.oldest') ?></option>
	        		<option value="3" <?php echo (int) Config::get('comments.default_sort') === 3 ? 'selected' : '' ?>><?php _e('admin.best') ?></option>
				</select>
				<p class="help-block"><?php _e('options.comments.default_sort_help') ?></p>
			</div>

			<div class="form-group">
		        <label for="maxlength"><?php _e('options.comments.maxlength') ?></label>
		        <input type="text" name="maxlength" id="maxlength" value="<?php echo Config::get('comments.maxlength') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.comments.maxlength_help') ?></p>
		    </div>

		    <div class="form-group">
		        <label for="time_between"><?php _e('options.comments.time_between') ?></label>
		        <input type="text" name="time_between" id="time_between" value="<?php echo Config::get('comments.time_between') ?>" class="form-control">
		        <p class="help-block"><?php _e('options.comments.time_between_help') ?></p>
		    </div>

		    <div class="form-group">
				<label for="html"><?php _e('options.comments.html') ?></label><br>
		    	<?php _e('options.comments.html_help') ?>
		    </div>
			
			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.save_changes') ?></button>
			</div>
		</form>
	</div>
</div>

<?php echo View::make('admin.footer')->render() ?>