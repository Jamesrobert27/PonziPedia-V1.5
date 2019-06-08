<?php $user = Auth::user(); ?>

<link href="<?php echo asset_url('css/vendor/imgpicker.css') ?>" rel="stylesheet">

<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="settingsAccount" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('main.settings') ?></h4>
				</div>
				<div class="modal-body">
					<ul class="nav nav-pills" role="tablist">
						<li class="active"><a href="#settingsAccount" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-cog"></span> <?php echo _e('main.account') ?></a></li>
						<li><a href="#settingsProfile" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-user"></span> <?php echo _e('main.profile') ?></a></li>
						<li><a href="#settingsPassword" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-lock"></span> <?php echo _e('main.password') ?></a></li>
						<li><a href="#settingsMessages" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-envelope"></span> <?php echo _e('main.messages') ?></a></li>
						<li><a href="#connectTab" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-link"></span> <?php echo _e('main.connect') ?></a></li>
					</ul>

					<div class="alert" style="margin-top: 10px;"></div>

					<div class="tab-content" style="margin-top: 10px;">
						<div class="tab-pane active" id="settingsAccount">
							<?php if (Config::get('auth.require_username') && Config::get('auth.username_change')): ?>
								<div class="form-group">
							        <label for="settings-username"><?php _e('main.username') ?> <em><?php _e('main.required') ?></em></label>
							        <input type="text" name="username" id="settings-username" value="<?php echo $user->username ?>" class="form-control">
							    </div>
							<?php endif ?>

							<div class="form-group">
						        <label for="settings-email"><?php _e('main.email') ?> <em><?php _e('main.required') ?></em></label>
						        <input type="text" name="email" id="settings-email" value="<?php echo $user->email ?>" class="form-control">
						    </div>

							<div class="form-group">
						        <label for="settings-locale"><?php _e('main.language') ?></label>
						        <select name="locale" id="settings-locale" class="form-control">
						        <?php $locales = Config::get('app.locales'); ?>
					        	<?php foreach ($locales as $key => $lang) : ?>
									<option value="<?php echo $key ?>" <?php echo $user->locale == $key ? 'selected' : '' ?>><?php echo $lang ?></option>
								<?php endforeach ?>
								</select>
						    </div>
						</div>

						<div class="tab-pane" id="settingsProfile">
							<div class="avatar-container form-group">
								<label><?php _e('main.avatar') ?></label>

								<div class="clearfix">
									<div class="pull-left">
										<a href="<?php echo $user->avatar ?>" target="_blank"><img src="<?php echo $user->avatar ?>" class="avatar-image img-thumbnail"></a>
									</div>
									<div class="pull-left" style="margin-left: 10px;">
										<?php $avatarType = isset($user->usermeta['avatar_type']) ? $user->usermeta['avatar_type'] : null; ?>
										<select name="avatar_type" class="form-control">
											<option value="" <?php echo $avatarType == '' ? 'selected' : '' ?>><?php _e('main.default') ?></option>
											<option value="image" <?php echo $avatarType == 'image' ? 'selected' : '' ?>><?php _e('main.uploaded') ?></option>
											<option value="gravatar" <?php echo $avatarType == 'gravatar' ? 'selected' : '' ?>>Gravatar</option>

											<?php foreach (Config::get('auth.providers', array()) as $key => $provider) {
												if (!empty($user->usermeta["{$key}_id"])) {
													echo '<option value="'.$key.'" '.($avatarType == $key ? 'selected' : '').'>'.$provider.'</option>';
												}
											} ?>
										</select>
										<div class="btn btn-info btn-sm ip-upload"><?php _e('main.upload') ?> <input type="file" name="file" class="ip-file"></div>
										<button type="button" class="btn btn-info btn-sm ip-webcam"><?php _e('main.webcam') ?></button>
									</div>
								</div>

								<div class="alert ip-alert"></div>
								<div class="ip-info"><?php _e('main.crop_info') ?></div>
								<div class="ip-preview"></div>
								<div class="ip-rotate">
									<button type="button" class="btn btn-default ip-rotate-ccw" title="Rotate counter-clockwise"><span class="icon-ccw"></span></button>
									<button type="button" class="btn btn-default ip-rotate-cw" title="Rotate clockwise"><span class="icon-cw"></span></button>
								</div>
								<div class="ip-progress">
									<div class="text"><?php _e('main.uploading') ?></div>
									<div class="progress progress-striped active"><div class="progress-bar"></div></div>
								</div>
								<div class="ip-actions">
									<button type="button" class="btn btn-sm btn-success ip-save"><?php _e('main.save_image') ?></button>
									<button type="button" class="btn btn-sm btn-primary ip-capture"><?php _e('main.capture') ?></button>
									<button type="button" class="btn btn-sm btn-default ip-cancel"><?php _e('main.cancel') ?></button>
								</div>
							</div>

							<?php if (UserFields::has('first_name') && UserFields::has('last_name')): ?>
								<div class="form-group">
							        <label for="display_name"><?php _e('main.display_name') ?></label>

									<select name="display_name" id="display_name" class="form-control">
							        	<?php if (Config::get('auth.require_username')): ?>
											<option <?php echo $user->display_name == $user->username ? 'selected' : '' ?>><?php echo $user->username ?></option>
										<?php endif ?>

										<?php if (!empty($user->first_name)): ?>
							        		<option <?php echo $user->display_name == $user->first_name ? 'selected' : '' ?>><?php echo $user->first_name ?></option>
							        	<?php endif ?>

							        	<?php if (!empty( $user->last_name)): ?>
							        		<option <?php echo $user->display_name == $user->last_name ? 'selected' : '' ?>><?php echo $user->last_name ?></option>
							        	<?php endif ?>

							        	<?php if (!empty($user->first_name) && !empty($user->last_name)): ?>
							        		<option <?php echo $user->display_name == "$user->first_name $user->last_name" ? 'selected' : '' ?>><?php echo "$user->first_name $user->last_name" ?></option>
							        		<option <?php echo $user->display_name == "$user->last_name $user->first_name" ? 'selected' : '' ?>><?php echo "$user->last_name $user->first_name" ?></option>
							        	<?php endif ?>
							        </select>
							    </div>
							<?php endif ?>

						    <?php echo UserFields::setData((array) $user->usermeta)->build('user') ?>
						</div>

						<div class="tab-pane" id="settingsPassword">
							<div class="form-group">
						        <label for="settings-pass1"><?php _e('main.current_password') ?></label>
						        <input type="password" name="pass1" id="settings-pass1" class="form-control" autocomplete="off" value="">
						    </div>
							<div class="form-group">
						        <label for="settings-pass2"><?php _e('main.newpassword') ?></label>
						        <input type="password" name="pass2" id="settings-pass2" class="form-control" autocomplete="off" value="">
						    </div>
						    <div class="form-group">
						        <label for="settings-pass3"><?php _e('main.newpassword_confirmation') ?></label>
						        <input type="password" name="pass3" id="settings-pass3" class="form-control" autocomplete="off" value="">
							</div>
						</div>

						<div class="tab-pane" id="settingsMessages">
							<h4><?php _e('main.settings') ?></h4>
							<div class="checkbox">
								<label>
									<input type="checkbox" value="1" name="email_messages" <?php echo empty(Auth::user()->usermeta['email_messages'])?'':'checked'; ?>><?php echo _e('main.email_messages') ?>
								</label>
							</div>
							<div class="checkbox">
								<label>
									<input type="checkbox" value="1" name="email_comments" <?php echo empty(Auth::user()->usermeta['email_comments'])?'':'checked'; ?>><?php echo _e('main.email_comments') ?>
								</label>
							</div>
							<hr>
							<h4><?php _e('main.contacts') ?></h4>
							<ul class="list-group contact-list"></ul>

							<script type="text/html" id="contactItemTemplate">
								<li class="list-group-item <% if (accepted) { %>contact-confirmed<% } %>" data-contact-id="<%= id %>">
									<a href="<?php echo App::url('profile.php?u=') ?><%= id %>" target="_blank">
										<img src="<%= avatar %>" class="contact-avatar"><%= name %></a>
									<span class="label label-danger"><?php _e('main.contact_request') ?></span>
									<div class="pull-right">
										<span class="confirmed"><a href="javascript:EasyLogin.confirmContact(<%= id %>)"><?php _e('main.confirm_contact') ?></a> |</span>
										<a href="javascript:EasyLogin.removeContact(<%= id %>)"><?php _e('main.remove') ?></a>
									</div>
								</li>
							</script>
						</div>

						<div class="tab-pane" id="connectTab">
							<div class="row">
								<div class="col-md-8 social-icons">
									<?php foreach (Config::get('auth.providers', array()) as $key => $provider) {
										?>
										<ul class="list-group">
											<li class="list-group-item clearfix">
												<span class="icon-<?php echo $key ?>"></span> <?php echo $provider ?>
												<?php if (empty(Auth::user()->usermeta["{$key}_id"])): ?>
													<a href="<?php echo App::url("oauth.php?provider={$key}") ?>" class="btn btn-info btn-sm pull-right"><?php _e('main.connect') ?></a>
												<?php else: ?>
													<a href="<?php echo App::url("oauth.php?provider={$key}&disconnect=1") ?>" class="btn btn-danger btn-sm pull-right"><?php _e('main.disconnect') ?></a>
												<?php endif ?>
											</li>
										</ul>
										<?php
									} ?>
								</div>
							</div>
							<p>
								<span class="label label-warning"><?php _e('main.warning') ?></span>
								<?php _e('main.connect_warning', array('password' => '<a href="#settingsPassword" role="tab" data-toggle="tab">'.trans('main.password').'</a>')) ?>
							</p>
						</div>
					</div>

				</div>
				<div class="modal-footer">
					<div class="pull-left">
						<button type="submit" class="btn btn-primary"><?php _e('main.save_changes') ?></button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="<?php echo asset_url('js/vendor/jquery.Jcrop.min.js') ?>"></script>
<script src="<?php echo asset_url('js/vendor/jquery.imgpicker.js') ?>"></script>
<script>
	$(function() {
		$('.avatar-container').imgPicker({
			url: '<?php echo App::url("ajax.php?action=avatar") ?>',
			messages: <?php echo json_encode(trans('imgpicker.js')) ?>,
			aspectRatio: 1,
			cropSuccess: function(img) {
				$('.avatar-image').attr('src', img.url + '?'+new Date().getTime());
				this.container.find('select').val('image');
			}
		});
		EasyLogin.generateDisplayName();
	});
</script>
