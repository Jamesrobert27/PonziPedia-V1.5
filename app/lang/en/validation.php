<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| as the size rules. Feel free to tweak each of these messages here.
	|
	*/

	"accepted"         => "The :attribute must be accepted.",
	"active_url"       => "The :attribute is not a valid URL.",
	"after"            => "The :attribute must be a date after :date.",
	"alpha"            => "The :attribute may only contain letters.",
	"alpha_dash"       => "The :attribute may only contain letters, numbers, and dashes.",
	"alpha_num"        => "The :attribute may only contain letters and numbers.",
	"array"            => "The :attribute must be an array.",
	"before"           => "The :attribute must be a date before :date.",
	"between"          => array(
		"numeric" => "The :attribute must be between :min and :max.",
		"file"    => "The :attribute must be between :min and :max kilobytes.",
		"string"  => "The :attribute must be between :min and :max characters.",
		"array"   => "The :attribute must have between :min and :max items.",
	),
	"boolean"          => "The :attribute field must be true or false",
	"confirmed"        => "The :attribute confirmation does not match.",
	"date"             => "The :attribute is not a valid date.",
	"date_format"      => "The :attribute does not match the format :format.",
	"different"        => "The :attribute and :other must be different.",
	"digits"           => "The :attribute must be :digits digits.",
	"digits_between"   => "The :attribute must be between :min and :max digits.",
	"email"            => "The :attribute format is invalid.",
	"exists"           => "The selected :attribute is invalid.",
	"image"            => "The :attribute must be an image.",
	"in"               => "The selected :attribute is invalid.",
	"integer"          => "The :attribute must be an integer.",
	"ip"               => "The :attribute must be a valid IP address.",
	"max"              => array(
		"numeric" => "The :attribute may not be greater than :max.",
		"file"    => "The :attribute may not be greater than :max kilobytes.",
		"string"  => "The :attribute may not be greater than :max characters.",
		"array"   => "The :attribute may not have more than :max items.",
	),
	"mimes"            => "The :attribute must be a file of type: :values.",
	"min"              => array(
		"numeric" => "The :attribute must be at least :min.",
		"file"    => "The :attribute must be at least :min kilobytes.",
		"string"  => "The :attribute must be at least :min characters.",
		"array"   => "The :attribute must have at least :min items.",
	),
	"not_in"           => "The selected :attribute is invalid.",
	"numeric"          => "The :attribute must be a number.",
	"regex"            => "The :attribute format is invalid.",
	"required"         => "The :attribute field is required.",
	"required_if"      => "The :attribute field is required when :other is :value.",
	"required_with"    => "The :attribute field is required when :values is present.",
	"required_without" => "The :attribute field is required when :values is not present.",
	"same"             => "The :attribute and :other must match.",
	"size"             => array(
		"numeric" => "The :attribute must be :size.",
		"file"    => "The :attribute must be :size kilobytes.",
		"string"  => "The :attribute must be :size characters.",
		"array"   => "The :attribute must contain :size items.",
	),
	"unique"           => "The :attribute has already been taken.",
	"url"              => "The :attribute format is invalid.",

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention "attribute.rule" to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(
		'new_password'     => array('required' => 'You must enter a new password in order to change it.'),
		'current_password' => array('required' => 'You must enter your current password in order to change it.'),
		'reminder_email'   => array('exists' => 'No account found with this email address.'),
		'reminder' => array(
			'required' => 'The recover link is invalid. Please generate a new one.', 
			'exists' => 'The recover link is invalid. Please generate a new one.', 
			'valid' => 'The recover link has expired. Please generate a new one.'
		),
		'activation_key' => array(
			'required' => 'The activation link is invalid. Please generate a new one.',
			'exists' => 'The activation link is invalid. Please generate a new one.'
		),
		'g-recaptcha-response' => array(
			'required' => 'Prove that you are not a robot.',
			'captcha'  => 'Prove that you are not a robot.',
		),
		'assignment' => array(
			'required' => 'The Assignment field is required.',
			'valid_assignment' => 'The Assignment field is invalid.'
		),
		'id' => array(
			'unique_field' => 'The :attribute has already been taken.'
		),
		'to' => array(
			'exists' => "The user you're trying to message does not exist.",
		),
		'to_user' => array(
			'exists' => 'The selected user was not found.',
		),
		'to_group' => array(
			'required' => 'The "To User" or "To Group" is required.',
		),
	),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of "email". This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(
		'name'       => 'Name',
		'first_name' => 'First Name',
		'last_name'  => 'Last Name',
		'username'   => 'Username',
		'email'      => 'E-mail',
		'url'        => 'Website',
		'password'   => 'Password',
		'role'       => 'Role',
		'status'     => 'Account Status',
		'reminder_email' => 'E-mail',
		'captcha' => 'Captcha',
		'to' => 'To',
		'subject' => 'Subject',
		'message' => 'Message',
		'type' => 'Type',
		'id' => 'ID',
		'order' => 'Display Order',
	),

);
