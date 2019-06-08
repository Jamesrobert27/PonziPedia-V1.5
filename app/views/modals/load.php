<div class="_bs">
<?php 
if (Auth::check()) {

	if (@$_GET['p'] != 'profile') {
		echo View::make('modals.settings')->render();
	}

	echo View::make('modals.pms')->render();

} else {
	echo View::make('modals.login')->render();

	echo View::make('modals.signup')->render();

	echo View::make('modals.activation')->render();

	echo View::make('modals.reminder')->render();

	if (Config::get('auth.captcha') && !Auth::check()) {
		display_captcha(true);
		?>
		<script>EasyLogin.options.recaptchaSiteKey = '<?php echo Config::get('services.recaptcha.public_key') ?>';</script>
		<?php
	}
}
?>
</div>