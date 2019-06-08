<?php ob_end_clean(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title><?php echo Config::get('app.name') ?> | Admin</title>
	
	<link href="<?php echo asset_url('css/vendor/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/bootstrap-custom.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/admin.css') ?>" rel="stylesheet">
</head>
<body>
	<div class="container">
		<div class="restricted-error"><?php _e('admin.restricted') ?></div>
    </div>
</body>
</html>