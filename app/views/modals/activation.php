<div class="modal fade" id="activationModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="activation" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('main.send_activation') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>

					<div class="form-group">
		                <label for="activation-email"><?php _e('main.enter_email') ?></label>
		                <input type="text" name="email" id="activation-email" class="form-control">
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

<div class="modal fade" id="activationSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
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

<div class="modal fade" id="activateModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="activate" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('main.activate_account') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>
					<input type="hidden" name="reminder">
				</div>
				<div class="modal-footer">
					<a href="#" data-toggle="modal" data-target="#activationModal" class="v-middle"><?php _e('main.resend_activation') ?></a>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="activateSuccessModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title"><?php _e('main.activate_success') ?></h4>
			</div>
			<div class="modal-body">
				<p><?php _e('main.activate_success_msg') ?></p><br>
				<p><a href="#" data-toggle="modal" data-target="#loginModal" class="btn btn-primary"><?php _e('main.login') ?></a></p>
			</div>
		</div>
	</div>
</div>