<?php
require_once 'app/init.php';

if (Auth::check() || (empty($_GET['reminder']) && !isset($_GET['activated']))) {
	redirect_to(App::url());
}

if (isset($_GET['reminder'])) {
	
	Register::activate($_GET['reminder']);
	
	if (Register::passes()) {
		redirect_to('activate.php?activated=1');
	}
}
?>

<?php echo View::make('header')->render() ?>

<div class="row">
	<div class="col-md-6">
		<?php if (isset($_GET['activated'])): ?>
			<h3 class="page-header"><?php _e('main.activate_success') ?></h3>
			<p><?php _e('main.activate_success_msg') ?></p><br>
			<p><a href="login.php" class="btn btn-primary"><?php _e('main.login') ?></a></p>
		<?php else: ?>
			<h3 class="page-header"><?php _e('main.activate_account') ?></h3>
			<?php if (Register::fails()) {
				echo Register::errors()->first(null, '<div class="alert alert-danger">:message</div>');
			} ?>
			<p><a href="activation.php" class="v-middle"><?php _e('main.resend_activation') ?></a></p>
		<?php endif ?>
	</div>
</div>

<?php echo View::make('footer')->render() ?>