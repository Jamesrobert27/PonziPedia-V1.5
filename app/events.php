<?php 

/**
 * Fires after initialization.
 *	
 * @return void
 */
Event::listen('app.init', function() {
	app('auth')->check();

	// Detect user language from cookie, browser language,
	// country code or usermeta "locale".
	
	$locales  = app('config')->get('app.locales');
	$lifetime = 60*24*30*10;

	if (isset($_GET['lang'])) {
		if (array_key_exists($_GET['lang'], $locales)) {
			app('cookie')->set('easylogin_locale', $_GET['lang'], $lifetime);
		}
		
		redirect_to( isset($_GET['r']) ? $_GET['r'] : app()->url() );
	}

	if (app('auth')->check()) {
		$locale = app('auth')->user()->locale;
	}

	if (empty($locale)) {
		$locale = app('cookie')->get('easylogin_locale');
	}

	if (empty($locale) && function_exists('locale_accept_from_http') && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		$locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
		$locale = substr($locale, 0, 2);
	}

	if (array_key_exists($locale, $locales)) {
		app('cookie')->set('easylogin_locale', $locale, $lifetime);
	} else {
		$locale = 'en';
	}

	if (empty($locale)) {
		$locale = 'en';
	}

	app('translator')->setLocale($locale);
});


/**
 * Fires before user log in.
 *
 * @param  User  $user
 * @param  bool  $remember
 * @return void
 */
Event::listen('auth.login', function($user, $remember) {
	// Set last log in date.
	Usermeta::update($user->id, 'last_login', with(new DateTime)->format('Y-m-d H:i:s'));

	// Set last log in IP address.
	Usermeta::update($user->id, 'last_login_ip', $_SERVER['REMOTE_ADDR']);
});

/**
 * Fires before user logs out.
 *
 * @param  User  $user
 * @return void
 */
Event::listen('auth.logout', function($user) {

});

/**
 * Fires after user sign up.
 *
 * @param  User  $user	
 * @return void
 */
Event::listen('auth.signup', function($user) {

});

/**
 * Fires before adding a new comment.
 *
 * @param  Comment  $comment	
 * @return void
 */
Event::listen('comments.add', function($comment) {
	// Return false to cancel or MessageBag with error messages.
	
	// return false;
	// return new \Hazzard\Support\MessageBag(array('error' => 'Error message.');
});

/**
 * Whether the user can edit the given comment.
 *
 * @param  Comment  $comment	
 * @return void
 */
Event::listen('comments.canEdit', function($comment) {
	// return false;
});

/**
 * Whether the user can reply to the the given comment.
 *
 * @param  Comment  $comment	
 * @return void
 */
Event::listen('comments.canReply', function($comment) {
	// return false;
});