<div class="modal fade" id="reminderModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="reminder" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('main.recover_pass') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>

					<div class="form-group">
		                <label for="reminder-email"><?php _e('main.enter_email') ?></label>
		                <input type="text" name="email" id="reminder-email" class="form-control">
		            </div>
					<div class="form-group recaptcha"></div>
				</div>
				<div class="modal-footer">
					<div class="pull-left">
						<button type="submit" class="btn btn-primary"><?php _e('main.continue') ?></button>
					</div>
					<a href="#" data-toggle="modal" data-target="#loginModal" class="v-middle"><?php _e('main.back_to_login') ?></a>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="reminderSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php _e('main.check_email') ?></h4>
			</div>
			<div class="modal-body"><?php _e('main.reminder_check_email') ?></div>
		</div>
	</div>
</div>

<div class="modal fade" id="resetModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="reset" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('main.reset_password') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>

					<div class="form-group">
		                <label for="reset-pass1"><?php _e('main.newpassword') ?></label>
		                <input type="password" name="pass1" id="reset-pass1" class="form-control">
		            </div>
		            
		            <div class="form-group">
		                <label for="reset-pass2"><?php _e('main.newpassword_confirmation') ?></label>
		                <input type="password" name="pass2" id="reset-pass2" class="form-control">
		            </div>
		            <input type="hidden" name="reminder">
				</div>
				<div class="modal-footer">
					<div class="pull-left">
						<button type="submit" class="btn btn-primary"><?php _e('main.change_pass') ?></button>
					</div>
					<a href="#" data-toggle="modal" data-target="#reminderModal" class="v-middle"><?php _e('main.new_reminder') ?></a>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="resetSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php _e('main.reset_success') ?></h4>
			</div>
			<div class="modal-body">
				<p><?php _e('main.reset_success_msg') ?></p><br>
				<p><a href="#" data-toggle="modal" data-target="#loginModal" class="btn btn-primary"><?php _e('main.login') ?></a></p>
			</div>
		</div>
	</div>
</div>