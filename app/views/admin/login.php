<?php 
if (isset($_POST['email'], $_POST['password']) && csrf_filter()) {
	
	Auth::login($_POST['email'], $_POST['password'], isset($_POST['remember']));

	if (Auth::passes()) {
		redirect_to('admin.php');
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link href="<?php echo asset_url('img/favicon.png') ?>" rel="icon">
	
	<title><?php echo Config::get('app.name') ?> | Admin</title>
	
	<link href="<?php echo asset_url('css/vendor/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/bootstrap-custom.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/admin.css') ?>" rel="stylesheet">
	<!-- <link href="<?php echo asset_url('css/flat.css') ?>" rel="stylesheet"> -->
	
	<?php $color = Config::get('app.color_scheme'); ?>
	<link href="<?php echo asset_url("css/colors/{$color}.css") ?>" rel="stylesheet">
</head>
<body>
	<div class="container">
		<div class="login">
	        
	        <?php if (Auth::fails()) {
				echo Auth::errors()->first(null, '<div class="error">:message</div>');
			} ?>

	        <form action="" method="POST">
				<?php csrf_input(); ?>

				<div class="form-group">
	                <label for="email"><?php _e('admin.email_username') ?></label>
	                <input type="text" name="email" id="email" value="<?php echo set_value('email') ?>" class="form-control">
	            </div>

	            <div class="form-group">
	                <label for="password"><?php _e('admin.password') ?></label>
	                <input type="password" name="password" id="password" class="form-control">
	            </div>
	            
	            <div class="form-group clearfix">
	                <div class="checkbox pull-left">
		                <label><input type="checkbox" name="remember" value="1" <?php echo set_checkbox('remember', '1') ?>> <?php _e('admin.remember') ?></label>
		            </div>
	                <button class="btn btn-primary pull-right" type="submit" name="login"><?php _e('admin.login') ?></button>
	            </div>
	        </form>
        	<span class="pull-left"><a href="<?php echo App::url() ?>">&larr; <?php _e('admin.back_home') ?></a></span>
        </div>
    </div>
</body>
</html>