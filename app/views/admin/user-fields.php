<?php if (!Auth::userCan('manage_fields')) page_restricted();

if (isset($_POST['submit']) && csrf_filter()) {
	$id = escape($_POST['id']);

	Validator::extend('valid_assignment', function($attrs, $value) {
		foreach ($value as $val) {
			if (!in_array($val, array('user', 'signup', 'admin'))) {
				return false;
			}
		}
		return true;
	});

	Validator::extend('unique_field', function($attrs, $value) {
		return !Config::get("userfields.{$value}");
	});

	$validator = Validator::make(
	    array(
	    	'id' => $id,
	    	'type' => $_POST['type'],
	    	'assignment' => @$_POST['assignment']
	    ),
	    array(
	    	'id' => 'required|alpha_dash|unique_field',
	    	'type' => 'required|in:text,textarea,select,checkbox,radio',
	    	'assignment' => 'required|valid_assignment'
	    )
	);

	if ($validator->passes()) {

		$field = array(
			'type' => $_POST['type'],
			'assignment' => array_values($_POST['assignment'])
		);

		if (!empty($_POST['label'])) {
			$field['label'] = escape($_POST['label']);
		}

		Config::set("userfields.{$id}", $field);

		if (Option::where('group', 'userfields')->where('item', $id)->first()) {
			redirect_to("?page=user-field-edit&id={$id}");
		} else {
			redirect_to('?page=user-fields');
		}

	} else {
		$errors = $validator->errors();
	}
}

if (isset($_GET['delete']) && Config::get('userfields.'.$_GET['delete'])) {

	if (Option::where('group', 'userfields')
			->where('item', $_GET['delete'])
			->limit(1)
			->delete()) {

		redirect_to('?page=user-fields', array('deleted' => true));
	} else {
		redirect_to('?page=user-fields', array('delete_error' => true));
	}
}
?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.userfields') ?></h3>
<div class="row">
	<div class="col-md-6">
		<?php if (Session::has('deleted')): ?>
			<div class="alert alert-success alert-dismissible">
				<span class="close" data-dismiss="alert">&times;</span>
				<?php _e('admin.field_deleted') ?>
			</div>
		<?php endif ?>
		<?php if (Session::has('delete_error')): ?>
			<div class="alert alert-danger alert-dismissible">
				<span class="close" data-dismiss="alert">&times;</span>
				<?php _e('admin.field_delete_error') ?>
			</div>
		<?php endif ?>
		<?php Session::deleteFlash(); ?>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<h4><?php _e('admin.fields'); ?></h4>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th><?php _e('admin.field_id') ?></th>
					<th><?php _e('admin.field_type') ?></th>
					<th><?php _e('admin.field_assignment') ?></th>
					<th><?php _e('admin.action') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach (UserFields::all() as $key => $field): ?>
				<tr>
					<td><?php echo $key ?></td>
					<td><?php echo $field['type'] ?></td>
					<td class="word-break">
					<?php foreach ((array) $field['assignment'] as $assignment) {
						echo '<span class="label label-default">'.$assignment.'</span> ';
					} ?>
					</td>
					<td>
						<a href="?page=user-field-edit&id=<?php echo $key ?>" title="<?php _e('admin.edit_field') ?>">
							<span class="glyphicon glyphicon-edit"></span></a> 
						<a href="?page=user-fields&delete=<?php echo $key ?>" title="<?php _e('admin.delete_field') ?>">
							<span class="glyphicon glyphicon-trash"></span>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>

	<div class="col-md-6">
		<h4><?php _e('admin.add_field') ?></h4>
		
		<?php if (isset($errors)) {
			echo '<div class="alert alert-danger alert-dismissible"><span class="close" data-dismiss="alert">&times;</span><ul>';
			foreach ($errors->all('<li>:message</li>') as $error) {
			   echo $error;
			}
			echo '</ul></div>';
		} ?>

		<form action="" method="POST">
			<?php csrf_input() ?>
			
			<div class="form-group">
		        <label for="id"><?php _e('admin.field_id') ?></label> <em><?php _e('admin.required') ?></em>
		        <input type="text" name="id" id="id" value="<?php echo set_value('id') ?>" class="form-control">
		        <p class="help-block"><?php _e('admin.field_id_help') ?></p>
		    </div>

		    <div class="form-group">
		        <label for="label"><?php _e('admin.field_label') ?></label>
		        <input type="text" name="label" id="label" value="<?php echo set_value('label') ?>" class="form-control">
		        <p class="help-block"><?php _e('admin.field_label_help') ?></p>
		    </div>

		    <div class="form-group">
		        <label for="type"><?php _e('admin.field_type') ?></label> <em><?php _e('admin.required') ?></em>
		        <select name="type" id="type" class="form-control">
		        	<option value="text" <?php echo set_select('type', 'text') ?>>text</option>
		        	<option value="textarea" <?php echo set_select('type', 'textarea') ?>>textarea</option>
		        	<option value="select" <?php echo set_select('type', 'select') ?>>select</option>
		        	<option value="checkbox" <?php echo set_select('type', 'checkbox') ?>>checkbox</option>
		        	<option value="radio" <?php echo set_select('type', 'radio') ?>>radio</option>
		       	</select>
		    </div>

		    <div class="form-group">
		    	<label for="assignment"><?php _e('admin.field_assignment') ?></label> <em><?php _e('admin.required') ?></em>
		    	<select multiple name="assignment[]" id="assignment" class="form-control" style="height: 70px;">
	        		<option value="user">user</option>
	        		<option value="signup">signup</option>
	        		<option value="admin">admin</option>
				</select>
				<p class="help-block"><?php _e('admin.field_assignment_help') ?></p>
		    </div>

		    <div class="form-group">
		    	<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.add_field') ?></button>
		    </div>
		</form>
	</div>
</div>

<?php echo View::make('admin.footer')->render() ?>