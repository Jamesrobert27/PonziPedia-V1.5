<?php if (!Auth::userCan('add_users')) page_restricted(); 

if (isset($_POST['submit']) && csrf_filter()) {

	$data = array(
    	'email'    => $_POST['email'],
    	'password' => $_POST['pass1'],
    	'password_confirmation' => $_POST['pass2'],
    	'role'     => $_POST['role'],
    	'status'   => $_POST['status']
    );

	$rules = array(
    	'email'    => 'required|email|max:100|unique:users',
    	'password' => 'required|between:4,30|confirmed',
    	'role'     => 'required',
    	'status'   => 'required'
    );

    if (Config::get('auth.require_username')) {
    	$data['username'] = $_POST['username'];
    	$rules['username'] = 'required|min:3|max:50|alpha_dash|unique:users';
    }

	foreach (UserFields::all('admin') as $key => $field) {
    	if (!empty($field['validation'])) {
    		$data[$key] = @$_POST[$key];
    		$rules[$key] = $field['validation'];
    	}
    }
	
	$validator = Validator::make($data, $rules);

	if ($validator->passes()) {

		$user = new User;

		$firstName = escape(@$_POST['first_name']);
		$lastName = escape(@$_POST['last_name']);
		
		if (!empty($firstName) && !empty($lastName)) {
			$user->display_name = "{$firstName} {$lastName}";
		} elseif (!empty($_POST['username'])) {
			$user->display_name = $_POST['username'];
		}
		
		if (Config::get('auth.require_username')) {
			$user->username = $_POST['username'];
		}

		$user->email 	= $_POST['email'];
		$user->password = Hash::make($_POST['pass1']);
		$user->role_id 	= (int) $_POST['role'];
		$user->status   = (int) $_POST['status'];

		if ($user->save()) {
			foreach (UserFields::all('admin') as $key => $field) {
				Usermeta::add($user->id, $key, escape(@$_POST[$key]), true);
			}

			if (isset($_POST['send_pass'])) {
				$data = array(
					'password' => $_POST['pass1'], 
					'username' => @$_POST['username'], 
					'email'    => $_POST['email']
				);
				
				Mail::send('emails.new_user', $data, function($message) use($user) {
				    $message->to($user->email);
				    $message->subject(trans('emails.new_user_subject'));
				});
			}

			redirect_to('?page=users', array('user_added' => true, 'user_id' => $user->id));
		} else {
			$errors = new Hazzard\Support\MessageBag(array('error' => trans('errors.dbsave')));
		}
	} else {
		$errors = $validator->errors();
	}
}
?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.add_user') ?></h3>
<div class="row">
	<div class="col-md-6">

		<?php if (isset($errors)) {
			echo '<div class="alert alert-danger"><span class="close" data-dismiss="alert">&times;</span><ul>';
			foreach ($errors->all('<li>:message</li>') as $error) {
			   echo $error;
			}
			echo '</ul></div>';
		} ?>

		<form action="" method="POST">
			<?php csrf_input() ?>
			
			<?php if (Config::get('auth.require_username')): ?>
				<div class="form-group">
			        <label for="username"><?php _e('admin.username') ?> <em><?php _e('admin.required') ?></em></label>
			        <input type="text" name="username" id="username" value="<?php echo set_value('username') ?>" class="form-control">
			    </div>
			<?php endif ?>

		    <div class="form-group">
		        <label for="email"><?php _e('admin.email') ?> <em><?php _e('admin.required') ?></em></label>
		        <input type="text" name="email" id="email" value="<?php echo set_value('email') ?>" class="form-control">
		    </div>

		    <div class="form-group">
		        <label for="pass1"><?php _e('admin.password') ?> <em><?php _e('admin.required') ?></em></label>
		        <input type="password" name="pass1" id="pass1" class="form-control" autocomplete="off" value="">
		    </div>

		    <div class="form-group">
		        <label for="pass2"><?php _e('admin.password_confirmation') ?> <em><?php _e('admin.required') ?></em></label>
		        <input type="password" name="pass2" id="pass2" class="form-control" autocomplete="off">
		    </div>

		    <div class="form-group">
		    	<div class="checkbox">
                	<label><input type="checkbox" name="send_pass" value="1" checked> <?php _e('admin.send_password') ?></label>
                </div>
            </div>

            <div class="form-group">
            	<label for="role"><?php _e('admin.role') ?></label>
            	<select name="role" id="role" class="form-control">
            		<option value=""> </option>
					<?php foreach ((array) Role::all() as $role) {
						echo '<option value="'.$role->id.'"'.set_select('role', $role->id).'>'.$role->name.'</option>';
					} ?>
				</select>
            </div>

             <div class="form-group">
            	<label for="status"><?php _e('admin.account_status') ?></label>
            	<select name="status" id="status" class="form-control">
            		<option value="1" <?php echo set_select('status', '1') ?>><?php _e('admin.activated') ?></option>
            		<option value="0" <?php echo set_select('status', '0') ?>><?php _e('admin.unactivated') ?></option>
					<option value="2" <?php echo set_select('status', '2') ?>><?php _e('admin.suspended') ?></option>
				</select>
            </div>

            <?php echo UserFields::build('admin') ?>

            <br>
            <div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.add_user') ?></button>
			</div>
		</form>
	</div>
</div>

<?php echo View::make('admin.footer')->render() ?>