<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Real Time Private messages
	|--------------------------------------------------------------------------
	|
	| Whether the message should appear in the inbox in real time.
	| Enabling this may slow down your website.
	|
	*/

	'realtime' => true,

	/*
	|--------------------------------------------------------------------------
	| Request Delay
	|--------------------------------------------------------------------------
	|
	| The delay in secods between the requests for checking for new messages.
	|
	*/

	'delay' => 10,

	/*
	|--------------------------------------------------------------------------
	| Message Maxlength
	|--------------------------------------------------------------------------
	|
	| The message max length allowed.
	|
	*/

	'maxlength' => 500,
   

   
	/*
	|--------------------------------------------------------------------------
	| Messages Limit
	|--------------------------------------------------------------------------
	|
	| The number of allowd messages an user can send per hour.
	|
	*/
	
	'limit' => 100,

	/*
	|--------------------------------------------------------------------------
	| Webmaster User ID
	|--------------------------------------------------------------------------
	|
	| The User ID of the Webmaster. 
	| Used when user want to contact the site administrator.
	|
	*/
	
	'webmaster' => 1,
);