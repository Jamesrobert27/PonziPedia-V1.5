<div class="modal fade" id="signupModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="signup" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('main.signup') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>
					
					<?php if (Config::get('auth.require_username')): ?>
						<div class="form-group">
					        <label for="signup-username"><?php _e('main.username') ?></label>
					        <input type="text" name="username" id="signup-username" class="form-control">
					    </div>
					<?php endif ?>

				    <div class="form-group">
				        <label for="signup-email"><?php _e('main.email') ?></label>
				        <input type="text" name="email" id="signup-email" class="form-control">
				    </div>

				    <div class="form-group">
				        <label for="signup-pass1"><?php _e('main.password') ?></label>
				        <input type="password" name="pass1" id="signup-pass1" class="form-control" autocomplete="off" value="">
				    </div>

				    <!--
				    <div class="form-group">
				        <label for="signup-pass2"><?php _e('main.password_confirmation') ?></label>
				        <input type="password" name="pass2" id="signup-pass2" class="form-control" autocomplete="off">
				    </div>
				    -->

				    <?php echo UserFields::build('signup') ?>

					<div class="form-group recaptcha"></div>

					<?php if (count(Config::get('auth.providers'))): ?>
			            <span class="help-block"><?php _e('main.login_with2') ?></span>
			            <div class="social-connect clearfix">
			            	<?php foreach (Config::get('auth.providers', array()) as $key => $provider) : ?>
								<a href="<?php echo App::url("oauth.php?provider={$key}") ?>" class="connect <?php echo $key ?>" title="<?php _e("main.connect_with_{$key}") ?>"><?php echo $provider ?></a>
							<?php endforeach ?>
			            </div>
			        <?php endif ?>

				</div>
				<div class="modal-footer">
					<div class="pull-left">
						<button type="submit" class="btn btn-primary"><?php _e('main.signup') ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="signupSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php _e('main.check_email') ?></h4>
			</div>
			<div class="modal-body"><?php _e('main.activation_check_email') ?></div>
		</div>
	</div>
</div>