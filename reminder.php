<?php
require_once 'app/init.php';

if (Auth::check()) redirect_to(App::url());
?>			

<?php echo View::make('header')->render() ?>

<div class="row">
	<div class="col-md-6">
		<?php if (Session::has('reminder_sent')): Session::deleteFlash(); ?>
			<h3 class="page-header"><?php _e('main.check_email') ?></h3>
			<?php _e('main.reminder_check_email') ?>
		<?php else: ?>
			<h3 class="page-header"><?php echo _e('main.recover_pass') ?></h3>
			
			<form action="reminder" class="ajax-form">				
				<div class="form-group">
			        <label for="reminder-email"><?php _e('main.enter_email') ?></label>
			        <input type="text" name="email" id="reminder-email" class="form-control">
			    </div>
				
				<?php if (Config::get('auth.captcha')): ?>
					<div class="form-group recaptcha">
						<?php display_captcha_tag(); ?>
					</div>
				<?php endif ?>

			    <div class="form-group">
			    	<button type="submit" name="submit" class="btn btn-primary"><?php _e('main.continue') ?></button>
			    </div>
			</form>
		<?php endif ?>
	</div>
</div>

<?php echo View::make('footer')->render() ?>
