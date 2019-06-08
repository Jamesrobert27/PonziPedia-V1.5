<?php if (!Auth::userCan('manage_roles')) page_restricted();

if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
	redirect_to('?page=user-roles');
}

$role = Role::find($_GET['id']);

if (isset($_POST['submit']) && csrf_filter()) {
	$name = escape($_POST['name']);
	
	$permissions = array_map(function($permission) {
		return trim(str_replace(' ', '', $permission));
	}, explode(',', escape($_POST['permissions'])));

	$validator = Validator::make(
	    array('name' => $name),
	    array('name' => 'required|max:50|unique:roles,name,'. $role->id)
	);

	if ($validator->passes()) {
		$role->name = $name;
		$role->permissions = implode(',', $permissions);

		if ($role->save()) {
			redirect_to('?page=user-role-edit&id='.$role->id, array('role_updated' => true));
		} else {
			$errors = new Hazzard\Support\MessageBag(array('error' => trans('errors.dbsave')));
		}
	} else {
		$errors = $validator->errors();
	}
}
?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.edit_role') ?></h3>
<p><a href="?page=user-roles"><?php _e('admin.back_to_roles'); ?></a></p>

<div class="row">
	<div class="col-md-6">
		<?php if ($role): ?>
			
			<?php if (isset($errors)) {
				echo $errors->first(null, '<div class="alert alert-danger">:message <span class="close" data-dismiss="alert">&times;</span></div>');
			} ?>

			<?php if (Session::has('role_updated')): Session::deleteFlash(); ?>
				<div class="alert alert-success alert-dismissible">
					<span class="close" data-dismiss="alert">&times;</span>
					<?php _e('admin.role_updated') ?>
				</div>
			<?php endif ?>	
			
			<form action="?page=user-role-edit&id=<?php echo $role->id ?>" method="POST">
				<?php csrf_input() ?>
				
				<div class="form-group">
			        <label for="name"><?php _e('admin.role_name') ?></label>
			        <input type="text" name="name" id="name" value="<?php echo $role->name ?>" class="form-control">
			    </div>
			    
			    <div class="form-group">
			    	<label for="permissions"><?php _e('admin.role_perms') ?></label> <?php _e('admin.sep_comma') ?>
			    	<textarea name="permissions" id="permissions" class="form-control" rows="3"><?php echo $role->permissions ?></textarea>
			    </div>
			    
			    <div class="form-group">
			    	<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.update_role') ?></button>
			    </div>
			</form>

			<?php _e('admin.available_permissions') ?> <br>
			<span class="label label-default">dashboard</span>
			<span class="label label-default">add_users</span>
			<span class="label label-default">list_users</span>
			<span class="label label-default">edit_users</span>
			<span class="label label-default">delete_users</span>
			<span class="label label-default">message_users</span>
			<span class="label label-default">manage_roles</span>
			<span class="label label-default">manage_fields</span>
			<span class="label label-default">manage_settings</span>
			<span class="label label-default">moderate</span>
		<?php else: ?>
			<div class="alert alert-danger"><?php _e('errors.roleid') ?></div>
		<?php endif; ?>
	</div>
</div>

<?php echo View::make('admin.footer')->render() ?>