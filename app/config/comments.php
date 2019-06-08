<?php

return array(
	/*
	|--------------------------------------------------------------------------
	| Comment Moderation
	|--------------------------------------------------------------------------
	|
	| Whether the moderators have to approve all comments.
	|
	*/
	'moderation' => true,

	/*
	|--------------------------------------------------------------------------
	| Comment Smilies
	|--------------------------------------------------------------------------
	|
	| The smilies must be defined in smilies.php
	*/
	'use_smilies' => false,

	/*
	|--------------------------------------------------------------------------
	| Comment Replies
	|--------------------------------------------------------------------------
	|
	| Whether to allow comment replies.
	|
	*/
	'replies' => true,

	/*
	|--------------------------------------------------------------------------
	| Restricted Words
	|--------------------------------------------------------------------------
	|
	| Comments containing these words will require moderator approval before 
	| being published.
	|
	*/
	'restricted_words' => array(),

	// Blacklisted users will not be able the post comments
	
	/*
	|--------------------------------------------------------------------------
	| Blacklist User IDs
	|--------------------------------------------------------------------------
	|
	| The users from this list will not be able the post comments.
	|
	*/
	'blacklist' => array(),

	/*
	|--------------------------------------------------------------------------
	| Whitelisted User IDs
	|--------------------------------------------------------------------------
	|
	| The users from this list will bypass the "Restricted Words" filter.
	|
	*/
	'whitelist' => array(),

	/*
	|--------------------------------------------------------------------------
	| Maximum Links
	|--------------------------------------------------------------------------
	|
	| Comments containing more links will require moderator approval before 
	| being published. Set to null to disable.
	|
	*/
	'max_links' => null,

	/*
	|--------------------------------------------------------------------------
	| Maximum Pending Comments
	|--------------------------------------------------------------------------
	|
	| Block users that have more than 'max_pending' unapproved comments.
	| Set to null to disable.
	|
	*/
	'max_pending' => null,

	/*
	|--------------------------------------------------------------------------
	| Comments Per Page
	|--------------------------------------------------------------------------
	|
	| The number of comments that should be loaded on the page.
	| Set to null to load all comments.
	|
	*/
	'per_page' => 50,

	/*
	|--------------------------------------------------------------------------
	| Default Comment Sort
	|--------------------------------------------------------------------------
	|
	| 1 - Newest comments first.
	| 2 - Oldest comments first.
	|
	*/
	'default_sort' => 1,

	/*
	|--------------------------------------------------------------------------
	| Maximum Comment Length
	|--------------------------------------------------------------------------
	|
	| Set to null for unspecified length.
	|
	*/
	'maxlength' => 500,

	/*
	|--------------------------------------------------------------------------
	| Time Between Comments
	|--------------------------------------------------------------------------
	|
	| The number of seconds between comments to prevent comment flood.
	| Set to null to disable.
	|
	*/
	'time_between' => 5,

	/*
	|--------------------------------------------------------------------------
	| Comment Edit
	|--------------------------------------------------------------------------
	|
	| false   - Users can't edit comments.
	| true    - Users cant edit comments anytime.
	| numeric - Users can edit comments only for this period of time (seconds).
	|
	*/
	'edit' => 120,

	/*
	|--------------------------------------------------------------------------
	| KSES (HTML tags)
	|--------------------------------------------------------------------------
	|
	| true  - Allow certain HTML tags (+ entities, protocols and css).
	| false - Convert tags to HTML entities (plain text).
	|
	*/
	'kses' => false,
	
	/*
	|--------------------------------------------------------------------------
	| Allowed Tags
	|--------------------------------------------------------------------------
	|
	| Array of allowed html tags with attributes. 
	| Eg:	array(
	|			'a'   => array('href' => true),
	|  			'b'	  => true,
	|			'img' => array('src' => true),
	|		),
	|
	| Set to null to allow the default tags. 
	| See getDefaultAllowedTags() method in src/Hazzard/Support/Kses.php.
	|
	*/
	'allowed_tags' => null,

	/*
	|--------------------------------------------------------------------------
	| Allowed Entities
	|--------------------------------------------------------------------------
	|
	| Array of allowed html entities. 
	| Eg:	array('nbsp', 'raquo', 'bull'),
	|
	| Set to null to allow the default entities. 
	| See getDefaultAllowedEntities() method in src/Hazzard/Support/Kses.php.
	|
	*/
	'allowed_entities' => null,

	/*
	|--------------------------------------------------------------------------
	| Allowed Protocols
	|--------------------------------------------------------------------------
	|
	| Array of allowed protocols. 
	| Eg:	array('http', 'https', 'ftp', 'mailto'),
	|
	| Set to null to allow the default protocols. 
	| See getDefaultAllowedProtocols() method in src/Hazzard/Support/Kses.php.
	|
	*/
	'allowed_protocols' => null,

	/*
	|--------------------------------------------------------------------------
	| Allowed CSS Attributes
	|--------------------------------------------------------------------------
	|
	| Array of allowed css attributes. 
	| Eg:	array(text-align', 'margin', 'color'),
	|
	| Set to null to allow the default protocols. 
	| See getDefaultAllowedCss() method in src/Hazzard/Support/Kses.php.
	|
	*/
	'allowed_css' => null,
);