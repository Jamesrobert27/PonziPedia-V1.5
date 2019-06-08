<?php
require_once 'app/init.php';

if (Auth::check() || (empty($_GET['reminder']) && !Session::has('password_updated'))) {
	redirect_to(App::url());
}
?>

<?php echo View::make('header')->render() ?>

<div class="row">
	<div class="col-md-6">
		
		<?php if (Session::has('password_updated')): Session::deleteFlash(); ?>
			<h3 class="page-header"><?php _e('main.reset_success') ?></h3>
			<p><?php _e('main.reset_success_msg') ?></p><br>
			<p><a href="login.php" class="btn btn-primary"><?php _e('main.login') ?></a></p>
		<?php else: ?>
			<h3 class="page-header"><?php echo _e('main.recover_pass') ?></h3>
			
			<form action="reset" class="ajax-form clearfix">
				<div class="form-group">
	                <label for="reset-pass1"><?php _e('main.newpassword') ?></label>
	                <input type="password" name="pass1" id="reset-pass1" class="form-control">
	            </div>
	            
	            <div class="form-group">
	                <label for="reset-pass2"><?php _e('main.newpassword_confirmation') ?></label>
	                <input type="password" name="pass2" id="reset-pass2" class="form-control">
	            </div>
	            
	            <div class="form-group pull-left">
					<button type="submit" name="submit" class="btn btn-primary"><?php _e('main.change_pass') ?></button>
				</div>
				
				<div class="form-group pull-right">
					<a href="reminder.php" class="v-middle"><?php _e('main.new_reminder') ?></a>
				</div>
				<input type="hidden" name="reminder" value="<?php echo escape($_GET['reminder']) ?>">
			</form>
		<?php endif ?>
	</div>
</div>

<?php echo View::make('footer')->render() ?>