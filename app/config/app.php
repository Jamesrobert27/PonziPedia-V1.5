<?php
// +------------------------------------------------------------------------+
// | @author Olakunlevpn (Olakunlevpn)
// | @author_url 1: http://www.maylancer.cf
// | @author_url 2: https://codecanyon.net/user/gr0wthminds
// | @author_email: olakunlevpn@live.com   
// +------------------------------------------------------------------------+
// | PonziPedia - Peer 2 Peer 50% ROI Donation System
// | Copyright (c) 2018 PonziPedia. All rights reserved.
// +------------------------------------------------------------------------+



return array(

	/*
	|--------------------------------------------------------------------------
	| Debug Mode
	|--------------------------------------------------------------------------
	|
	| When enabled the actual PHP errors will be shown.
	| If disabled, a simple generic error message is shown.
	|
	*/

	'debug' => false,

	/*
	|--------------------------------------------------------------------------
	| Website URL
	|--------------------------------------------------------------------------
	|
	| This URL is used in emails, page redirects and assets.
	| You must set this to the root of the script.
	|
	*/

	'url' => 'https://localhost/poenzipedia',

	/*
	|--------------------------------------------------------------------------
	| Website Name
	|--------------------------------------------------------------------------
	|
	| The name is used in emails or page titles.
	|
	*/
	'name' => 'PonziPedia',
	
	/*
	|--------------------------------------------------------------------------
	| Website Name Abbreviation
	|--------------------------------------------------------------------------
	|
	| The name is used in emails or page titles abbreviation.
	|
	*/
	'short_name' => 'PPA',

	/*
	|--------------------------------------------------------------------------
	| Website Version
	|--------------------------------------------------------------------------
	|
	| The name is used in emails or page titles.
	|
	*/
	'version' => 1.5,
	


	/*
	|--------------------------------------------------------------------------
	| Website Email
	|--------------------------------------------------------------------------
	|
	| This is for notification and others
	|
	*/
	'webmail' => 'hello@ponzipedia.ml',

	/*
	|--------------------------------------------------------------------------
	| Website Phone
	|--------------------------------------------------------------------------
	|
	| This is for notification and others
	|
	*/
	'phone' => '+14803660401',

	/*
	|--------------------------------------------------------------------------
	| Website Telegram
	|--------------------------------------------------------------------------
	|
	| Set your website member telegram link 
	|
	*/
	'telegram' => 'https://t.me/joinchat/IRc9gQ-qHRWJXwmC',

	/*
	|--------------------------------------------------------------------------
	| Website Facebook
	|--------------------------------------------------------------------------
	|
	| Set your website member facebook link 
	|
	*/
	'facebook' => 'Facebook',

	/*
	|--------------------------------------------------------------------------
	| Website Yourtube
	|--------------------------------------------------------------------------
	|
	| Set your website youtube link 
	|
	*/
	'youtube' => 'UCy8PUfVzYwZ_pOykSRSPisA',


	/*
	|--------------------------------------------------------------------------
	| Website sms notification details 
	| We user the SMS API from https://www.multitexter.com/developers please read online on how to purchase sms units
	|--------------------------------------------------------------------------
	|
	| Set your website sms API login details
	|
	*/
	'sms-username' => 'dvlmob@gmail.com',
	'sms-passowrd' => 'yourpassword',
	/*
	|--------------------------------------------------------------------------
	| Website Color Scheme
	|--------------------------------------------------------------------------
	|
	| If you use the script with its design you can choose from multiple color
	| schemes.
	|
	| Supported: "dark", "light", "blue", "coffee", "ectoplasm", "midnight"
	|
	*/

	'color_scheme' => 'dark',

	/*
	|--------------------------------------------------------------------------
	| Default Locale & Locales Names
	|--------------------------------------------------------------------------
	|
	| The "locale" is the default locale that will be used by the translation. 
	| The "locales" are the available locales for translation.
	|
	*/

	'locale' => 'en',

	'locales' => array(
		'en' => 'English',
	),

	/*
	|--------------------------------------------------------------------------
	| Timezone
	|--------------------------------------------------------------------------
	|
	| The default timezone for your website.
	| http://www.php.net/manual/en/timezones.php
	|
	*/

	'timezone' => 'UTC',

	/*
	|--------------------------------------------------------------------------
	| Encryption Key
	|--------------------------------------------------------------------------
	|
	| The key should be set to a random 32 character string. 
	| This key is used when storing the remember token in the cookie.
	| Use extra/generate_key.php to generate a key.
	|
	*/

	'key' => '7kiHVY8kA4b6uvy9ZfWBUcfPdKoR6OzB',

	/*
	|--------------------------------------------------------------------------
	| CSRF Prevention
	|--------------------------------------------------------------------------
	|
	| Prevents the website from CSRF attacks.
	|
	*/

	'csrf' => true,

	/*
	|--------------------------------------------------------------------------
	| Service Providers
	|--------------------------------------------------------------------------
	*/

	'providers' => array(
		'Hazzard\Events\EventServiceProvider',
		'Hazzard\Cookie\CookieServiceProvider',
		'Hazzard\Session\SessionServiceProvider',
		'Hazzard\Hashing\HashServiceProvider',
		'Hazzard\Encryption\EncryptionServiceProvider',
		'Hazzard\Translation\TranslationServiceProvider',
		'Hazzard\View\ViewServiceProvider',
		'Hazzard\Mail\MailServiceProvider',
		'Hazzard\Database\DatabaseServiceProvider',
		'Hazzard\Validation\ValidationServiceProvider',
		'Hazzard\Auth\AuthServiceProvider',
		'Hazzard\Auth\OAuthServiceProvider',
		'Hazzard\Auth\RegisterServiceProvider',
		'Hazzard\Auth\PasswordReminderServiceProvider',
		'Hazzard\User\MetaServiceProvider',
		'Hazzard\User\FieldsServiceProvider',
		'Hazzard\Messages\MessageServiceProvider',
		'Hazzard\Comments\CommentsServiceProvider',
	),

	'manifest' => storage_path(),

	/*
	|--------------------------------------------------------------------------
	| Class Aliases
	|--------------------------------------------------------------------------
	*/

	'aliases' => array(
		'App'        => 'Hazzard\Support\Facades\App',
		'Config'     => 'Hazzard\Support\Facades\Config',
		'DB'         => 'Hazzard\Support\Facades\DB',
		'Model'      => 'Hazzard\Database\Model',
		'Validator'  => 'Hazzard\Support\Facades\Validator',
		'Cookie'     => 'Hazzard\Support\Facades\Cookie',
		'Session'    => 'Hazzard\Support\Facades\Session',
		'Hash'       => 'Hazzard\Support\Facades\Hash',
		'Crypt'      => 'Hazzard\Support\Facades\Crypt',
		'Event'      => 'Hazzard\Support\Facades\Event',
		'Lang'       => 'Hazzard\Support\Facades\Lang',
		'View'       => 'Hazzard\Support\Facades\View',
		'Mail'       => 'Hazzard\Support\Facades\Mail',
		'Auth'       => 'Hazzard\Support\Facades\Auth',
		'OAuth'      => 'Hazzard\Support\Facades\OAuth',
		'Register'   => 'Hazzard\Support\Facades\Register',
		'Password'   => 'Hazzard\Support\Facades\Password',
		'Usermeta'   => 'Hazzard\Support\Facades\Usermeta',
		'UserFields' => 'Hazzard\Support\Facades\UserFields',
		'Message'	 => 'Hazzard\Support\Facades\Message',
		'Contact'	 => 'Hazzard\Support\Facades\Contact',
		'Comments'   => 'Hazzard\Support\Facades\Comments'
	)
);