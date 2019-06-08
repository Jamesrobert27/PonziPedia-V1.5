<?php if (!Auth::userCan('manage_roles')) page_restricted();

if (isset($_POST['submit']) && csrf_filter()) {
	$name = $_POST['name'];
	
	$permissions = array_map(function($permission) {
		return trim(str_replace(' ', '', $permission));
	}, explode(',', escape($_POST['permissions'])));

	$validator = Validator::make(
	    array('name' => $name),
	    array('name' => 'required|max:50|unique:roles')
	);

	if ($validator->passes()) {
		
		$role = new Role(array(
			'name' => escape($name), 
			'permissions' => implode(',', $permissions)
		));

		if ($role->save()) {
			redirect_to('?page=user-roles', array('role_added' => true));
		} else {
			$errors = new Hazzard\Support\MessageBag(array('error' => trans('errors.dbsave')));
		}
	} else {
		$errors = $validator->errors();
	}
}

$roles = Role::orderBy('id', 'desc')->get();
?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.user_roles') ?></h3>
<div class="row">
	<div class="col-md-6">
		<?php if (Session::has('role_added')): Session::deleteFlash(); ?>
			<div class="alert alert-success alert-dismissible">
				<span class="close" data-dismiss="alert">&times;</span>
				<?php _e('admin.role_added') ?>
			</div>
		<?php endif ?>

		<?php if (Session::has('role_deleted')): Session::deleteFlash(); ?>
			<div class="alert alert-success alert-dismissible">
				<span class="close" data-dismiss="alert">&times;</span>
				<?php _e('admin.role_deleted') ?>
			</div>
		<?php endif ?>
	</div>
</div>

<div class="row">
	<div class="col-md-6">
		<h4><?php _e('admin.roles'); ?></h4>
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th><?php _e('admin.role_name') ?></th>
					<th><?php _e('admin.role_perms') ?></th>
					<th><?php _e('admin.action') ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($roles as $role): ?>
				<tr>
					<td><?php echo $role->name ?></td>
					<td class="word-break">
					<?php foreach (explode(',', $role->permissions) as $perm) {
						echo '<span class="label label-default">'.$perm.'</span> ';
					} ?>
					</td>
					<td>
						<a href="?page=user-role-edit&id=<?php echo $role->id ?>" title="<?php _e('admin.edit_role') ?>">
							<span class="glyphicon glyphicon-edit"></span></a> 
						<a href="?page=user-role-delete&id=<?php echo $role->id ?>" title="<?php _e('admin.delete_role') ?>">
							<span class="glyphicon glyphicon-trash"></span>
						</a>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		
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
	</div>

	<div class="col-md-6">
		<h4><?php _e('admin.add_role') ?></h4>
		
		<?php if (isset($errors)) {
			echo $errors->first(null, '<div class="alert alert-danger alert-dismissible"><span class="close" data-dismiss="alert">&times;</span> :message</div>');
		} ?>

		<form action="?page=user-roles" method="POST">
			<?php csrf_input() ?>
			
			<div class="form-group">
		        <label for="name"><?php _e('admin.role_name') ?></label>
		        <input type="text" name="name" id="name" value="<?php echo set_value('name') ?>" class="form-control">
		    </div>

		    <div class="form-group">
		    	<label for="permissions"><?php _e('admin.role_perms') ?></label> <?php _e('admin.sep_comma') ?>
		    	<textarea name="permissions" id="permissions" class="form-control" rows="3"><?php echo set_value('permissions') ?></textarea>
		    </div>
		    
		    <div class="form-group">
		    	<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.add_role') ?></button>
		    </div>
		</form>
	</div>
</div>

<?php echo View::make('admin.footer')->render() ?>