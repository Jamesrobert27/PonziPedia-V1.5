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



// header('Access-Control-Allow-Origin: yourwebsite.com');
// header('Access-Control-Allow-Origin: www.yourwebsite.com');

require_once 'app/init.php';

use Hazzard\Support\MessageBag;

// CSRF check
if (Config::get('app.csrf') && function_exists('getallheaders')) {
	if (is_ajax_request()) {
		$headers = array_change_key_case(getallheaders());
		$token   = isset($headers['x-csrf-token']) ? $headers['x-csrf-token'] : '';
	} else {
		$token = isset($_GET['_token']) ? $_GET['_token'] : (isset($_POST['_token']) ? $_POST['_token'] : '');
	}

	if (Session::token() !== $token) {
		Session::regenerateToken();
		json_message('CSRF Token Mismatch. Reload the page.', false);
	}
}

// Login
function ajax_login()
{
	if (Auth::check()) exit;

	$email    = isset($_POST['email'])    ? $_POST['email']    : '';
	$password = isset($_POST['password']) ? $_POST['password'] : '';
	$remember = isset($_POST['remember']);
			
	Auth::login($email, $password, $remember);

	if (Auth::passes()) {
		json_message( Config::get('auth.login_redirect') );
	} else {
		json_message(Auth::errors()->toArray(), false);
	}
}

// Logout
function ajax_logout() 
{
	Auth::logout();
}

// Signup
function ajax_signup()
{
	if (Auth::check()) exit;

	$email    = isset($_POST['email']) ? $_POST['email'] : '';
	$password = isset($_POST['pass1']) ? $_POST['pass1'] : '';

	Register::signup($_POST);

	if (Register::passes()) {
		if (Config::get('auth.email_activation')) {
			Session::flash('signup_complete', true);
			json_message(true);
		} else {
			Auth::login($email, $password);
			json_message( array('redirect' => Config::get('auth.login_redirect')) );
		}
	} else {
		json_message(Register::errors()->toArray(), false);
	}
}

// Send activation reminder
function ajax_activation() 
{
	if (Auth::check()) exit;

	$email   = isset($_POST['email'])   ? $_POST['email']   : '';
	$captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

	Register::reminder($email, $captcha);
	
	if (Register::passes()) {
		Session::flash('activation_sent', true);
		json_message();
	} else {
		json_message(Register::errors()->toArray(), false);
	}
}

// Activate account
function ajax_activate()
{
	if (Auth::check()) exit;

	$reminder = isset($_POST['reminder']) ? $_POST['reminder'] : '';

	Register::activate($reminder);
	
	if (Register::passes()) {
		json_message();
	} else {
		json_message(Register::errors()->toArray(), false);
	}
}

// Send password reminder
function ajax_reminder()
{
	if (Auth::check()) exit;
	
	$email   = isset($_POST['email'])   ? $_POST['email']   : '';
	$captcha = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

	Password::reminder($email, $captcha);
	
	if (Password::passes()) {
		Session::flash('reminder_sent', true);
		json_message();
	} else {
		json_message(Password::errors()->toArray(), false);
	}
}

// Password reset
function ajax_reset()
{
	if (Auth::check()) exit;

	$pass1    = isset($_POST['pass1'])    ? $_POST['pass1']    : '';
	$pass2    = isset($_POST['pass2'])    ? $_POST['pass2']    : '';
	$reminder = isset($_POST['reminder']) ? $_POST['reminder'] : '';

	Password::reset($pass1, $pass2, $reminder);
	
	if (Password::passes()) {
		Session::flash('password_updated', true);
		json_message();
	} else {
		json_message(Password::errors()->toArray(), false);
	}
}

// Account settings - Account
function ajax_settings_account()
{
	if (Auth::guest()) exit;

	$email    = isset($_POST['email'])    ? $_POST['email']    : '';
	$username = isset($_POST['username']) ? $_POST['username'] : '';
	$locale   = isset($_POST['locale'])   ? $_POST['locale']   : '';

	$user = User::find(Auth::user()->id);
	
	$data  = array('email' => $email);
	$rules = array('email' => "required|email|max:100|unique:users,email,{$user->id}");

    if (Config::get('auth.require_username') && Config::get('auth.username_change')) {
    	$data['username']  = $username;
    	$rules['username'] = "required|min:3|max:50|alpha_dash|unique:users,username,{$user->id}";
    }

    $validator = Validator::make($data, $rules);

	if ($validator->passes()) {
		$user->email = $email;

		if (Config::get('auth.require_username') && Config::get('auth.username_change')) {
			$user->username = $username;
		}

		if ($user->save()) {
			if (array_key_exists($locale, Config::get('app.locales'))) {
				Usermeta::update($user->id, 'locale', $locale);
			}

			json_message();
		} else {
			json_message(with(new MessageBag(array('error' => trans('errors.dbsave'))))->toArray(), false);
		}
	}  else {
		json_message($validator->errors()->toArray(), false);
	}
}

// Account settings - Profile
function ajax_settings_profile()
{
	if (Auth::guest()) exit;

	$avatarType  = isset($_POST['avatar_type'])  ? $_POST['avatar_type']          : '';
	$displayName = isset($_POST['display_name']) ? escape($_POST['display_name']) : '';
	
	$types = implode(',', array_keys(Config::get('auth.providers', array())));

	$data  = array('avatar_type' => $avatarType);
	$rules = array('avatar_type' => "in:image,gravatar,$types");

	foreach (UserFields::all('user') as $key => $field) {
    	if (!empty($field['validation'])) {
    		$data[$key]  = isset($_POST[$key]) ? $_POST[$key] : '';
    		$rules[$key] = $field['validation'];
    	}
    }

    $validator = Validator::make($data, $rules);

	if ($validator->passes()) {
		$user = User::find(Auth::user()->id);

		if (!empty($displayName)) {
			$user->display_name = $displayName;
		}

		if ($user->save()) {
			$fields = array_merge(UserFields::all('user'), array('avatar_type' => ''));

			foreach ($fields as $key => $field) {
				$value = isset($_POST[$key]) ? escape($_POST[$key]) : '';
				$prev  = isset($user->usermeta[$key]) ? $user->usermeta[$key] : '';
				
				Usermeta::update($user->id, $key, $value, $prev);
			}

			json_message();
		} else {
			json_message(with(new MessageBag(array('error' => trans('errors.dbsave'))))->toArray(), false);
		}
	}  else {
		json_message($validator->errors()->toArray(), false);
	}
}

// Account settings - Password
function ajax_settings_password()
{
	if (Auth::guest()) exit;

	$pass1 = isset($_POST['pass1']) ? $_POST['pass1'] : '';
	$pass2 = isset($_POST['pass2']) ? $_POST['pass2'] : '';
	$pass3 = isset($_POST['pass3']) ? $_POST['pass3'] : '';

	$user = User::find(Auth::user()->id);

	$validator = Validator::make(
		array(
			'current_password' => $pass1,
			'new_password' => $pass2,
			'new_password_confirmation' => $pass3,
		), 
		array(
			'new_password' => 'required|between:4,30|confirmed',
			'current_password' => strlen($user->password) ? 'required' : ''
		)
	);

	if ($validator->passes()) {
		if (!strlen($user->password) || (strlen($user->password) && Hash::check($pass1, $user->password))) {
			$user->password = Hash::make($pass2);
			
			if ($user->save()) {
				json_message();
			} else {
				$errors = new MessageBag(array('error' => trans('errors.dbsave')));
			}
		} else {
			$errors = new MessageBag(array('error' => trans('errors.current_password')));
		}
	} else {
		$errors = $validator->errors();
	}

	json_message($errors->toArray(), false);
}

// Account settings - Messages
function ajax_settings_messages() 
{
	if (Auth::guest()) exit;

	if (isset($_POST['email_messages'])) {
		Usermeta::update(Auth::user()->id, 'email_messages', 1);
	} else {
		Usermeta::delete(Auth::user()->id, 'email_messages');
	}

	if (isset($_POST['email_comments'])) {
		Usermeta::update(Auth::user()->id, 'email_comments', 1);
	} else {
		Usermeta::delete(Auth::user()->id, 'email_comments');
	}

	json_message(true);
}

// Account settings - Avatar
function ajax_avatar()
{
	if (Auth::guest()) exit;

	$userId = Auth::user()->id;

	$options = array(
		'upload_dir' => app('path.base') . '/uploads/',
		'upload_url' => App::url('uploads/'),
		'max_file_size' => 5000000, // 5 mb
	    'max_width'  => 2000,
	    'max_height' => 2000,
	    'versions' => array(
	    	'' => array(
	    		'crop' => true,
	    		'max_width'  => 300,
	    		'max_height' => 300
	    	),
	    ),
		'upload_start' => function ($image) use ($userId) {
			$image->name = "~{$userId}.{$image->type}";
		},
		'crop_start' => function ($image) use ($userId) {
			$image->name = "{$userId}.{$image->type}";
		},
		'crop_complete' => function ($image) use ($userId) {
			Usermeta::update($userId, 'avatar_image', $image->name);
			Usermeta::update($userId, 'avatar_type', 'image');
		}
	);

	new Hazzard\Support\ImagePicker($options, trans('imgpicker'));
}

// Send Message
function ajax_send_message()
{
	if (Auth::guest()) exit;

	$to      = isset($_POST['to'])      ? (int) $_POST['to'] : null;
	$message = isset($_POST['message']) ? $_POST['message']  : null;

	if (!$to || !$message) exit;

	$limit     = Config::get('pms.limit');
	$maxlength = Config::get('pms.maxlength');
	$contact   = Contact::check(Auth::user()->id, $to);
	$webmaster = ($to == (int) Config::get('pms.webmaster'));
	
	
	if (!$contact && !$webmaster && !Auth::userCan('message_users')) {
		json_message(trans('errors.contact'), false);
	}

	if (Message::limitExceed($limit, App::make('session')) && !Auth::userCan('message_users')) {
		json_message(trans('errors.message_limit'), false);
	}

	$message = Message::send(Auth::user()->id, $to, $message, $maxlength);
	
	if (is_array($message)) {
		$email = Usermeta::get($to, 'email_messages', true);
		
		if (!empty($email)) {
			$user = User::find($to);
			
			if ($user) {
				Mail::send('emails.message', array('body' => $message['message']), function($message) use ($user) {
					$message->to($user->email);
					$message->subject(
						trans('emails.new_message_subject', array('user' => Auth::user()->display_name))
					);
				});
			}
		}

		json_message($message);
	} else {
		json_message(is_object($message) ? $message->toArray() : trans('errors.dbsave'), false);
	}
}

// Send message to the Webmaster
function ajax_webmaster_contact()
{
	if (Auth::guest()) exit;

	$message = isset($_POST['message']) ? $_POST['message'] : null;
	$userId  = Auth::user()->id;

	$limit     = Config::get('pms.limit');
	$webmaster = Config::get('pms.webmaster');
	$maxlength = Config::get('pms.maxlength');

	if (Message::limitExceed($limit, App::make('session'))) {
		json_message(trans('errors.message_limit'), false);
	}

	$message = Message::send($userId, $webmaster, $message, $maxlength);
	
	if (is_array($message)) {
		json_message($message);
	} else {
		json_message(is_object($message) ? $message->first() : trans('errors.dbsave'), false);
	}
}

// Delete Message(s) for the logged user.
function ajax_delete_message()
{
	if (Auth::guest()) exit;

	$id     = isset($_POST['id']) ? $_POST['id'] : 0;
	$userId = Auth::user()->id;
	
	if (Message::delete($userId, $id)) {
		json_message(true);
	} else {
		json_message(trans('errors.unexpected'), false);
	}
}
// Mark all messages as read for the logged user.
function ajax_mark_all_as_read() 
{
	if (Auth::guest()) exit;

	$userId = Auth::user()->id;

	json_message( Message::markAllAsRead($userId) );
}

// Add contact
function ajax_add_contact()
{
	if (Auth::guest()) exit;

	$userId = Auth::user()->id;
	$id     = isset($_POST['id']) ? $_POST['id'] : 0;
	
	json_message( Contact::add($userId, $id) );
}

// Remove contact
function ajax_remove_contact()
{
	if (Auth::guest()) exit;

	$userId = Auth::user()->id;
	$id     = isset($_POST['id']) ? $_POST['id'] : 0;

	json_message( Contact::remove($userId, $id) );
}

// Confirm contact
function ajax_confirm_contact()
{
	if (Auth::guest()) exit;

	$userId = Auth::user()->id;
	$id     = isset($_POST['id']) ? $_POST['id'] : 0;

	json_message( Contact::confirm($userId, $id) );
}

//  Delete user (admin)
function ajax_delete_user()
{
	if (!Auth::userCan('delete_users')) {
		json_message(trans('errors.permission'), false);
	}

	$id = isset($_POST['user_id']) ? (int) $_POST['user_id'] : 0;

	if (Auth::user()->id != $id) {
		User::where('id', $id)->limit(1)->delete();
		DB::table('userdetails')->where('userid', $id)->delete();
		$activationReceiver = DB::table('activationReceiver')->first();
		$activation = DB::table('activationFee')->where('receiver_id', $id)->first();
		if ($activation) {
			DB::table('activationFee')
        ->where('receiver_id', $id)
        ->update(array('receiver_id' => $activationReceiver->userid)); 
		}else{
			DB::table('activationFee')->where('sender_id', $id)->delete();
		}
		
		
		DB::table('referral')->where('sponsor', $id)->delete();
		DB::table('referral')->where('parent', $id)->delete();
		DB::table('referral')->where('userid', $id)->delete();
		DB::table('bank')->where('userid', $id)->delete();
		DB::table('testimony')->where('userid', $id)->delete();
		DB::table('testimoneytvotes')->where('userid', $id)->delete();
		
		Usermeta::newQuery()->where('user_id', $id)->delete();

		Message::newQuery()->where('to_user', $id)
							->orWhere('from_user', $id)
							->delete();
		
		Contact::deleteAll($id);

		Comments::deleteUserComments($id);
	}

	json_message();
}

// Delete users (admin)
function ajax_delete_users()
{
	if (!Auth::userCan('delete_users')) {
		json_message(trans('errors.permission'), false);
	}

	$users = isset($_POST['users']) ? $_POST['users'] : array();

	parse_str($users, $data);

	if (isset($data['users'])) {
		$users = array();
		
		foreach ((array) $data['users'] as $key => $id) {
			if (is_numeric($id) && (int) $id != Auth::user()->id) {
				$users[] = $id;
			}
		}

		if (count($users)) {
			$values = array_values($users);
			
			User::whereIn('id', $values)->limit(count($users))->delete();
			
			Usermeta::newQuery()->whereIn('user_id', $values)->delete();
			
			Message::newQuery()->whereIn('to_user', $values)
								->orWhereIn('from_user', $values)
								->delete();

			Contact::newQuery()->whereIn('user1', $values)
								->orWhereIn('user2', $values)
								->delete();

			Comments::deleteUserComments($values);
		}
	}

	json_message();
}

// Send Email (admin)
function ajax_send_email()
{
	if (!Auth::userCan('message_users')) {
		json_message(trans('errors.permission'), false);
	}

	$to      = isset($_POST['to'])      ? $_POST['to']      : '';
	$subject = isset($_POST['subject']) ? $_POST['subject'] : '';
	$message = isset($_POST['message']) ? $_POST['message'] : '';

	$validator = Validator::make(compact('to', 'subject', 'message'), 
		array(
			'to'      => 'required',
			'subject' => 'required',
			'message' => 'required',
		)
	);

	if ($validator->fails()) {
		json_message($validator->errors()->toArray(), false);
	}
	
	$to = explode(';', $to);		
	
	$emails = array();

	foreach ($to as $email) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$emails[] = $email;
		}
	}

	foreach ($emails as $email) {
		Mail::send('emails.email', array('body' => $message), function($message) use ($email, $subject) {
		    $message->to($email)->subject($subject);
		});
	}

	json_message();
}

// Delete conversation (admin)
function ajax_delete_conversation()
{
	if (!Auth::userCan('message_users')) {
		json_message(trans('errors.permission'), false);
	}

	$userId    = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
	$webmaster = Config::get('pms.webmaster');

	if (Message::deleteConversation($webmaster, $userId)) {
		json_message();
	} else {
		json_message(trans('errors.dbsave'), false);
	}
}

// Delete conversations (admin)
function ajax_delete_conversations()
{
	if (!Auth::userCan('message_users')) {
		json_message(trans('errors.permission'), false);
	}

	$webmaster     = Config::get('pms.webmaster');
	$conversations = isset($_POST['conversations']) ? $_POST['conversations'] : array();

	parse_str($conversations, $data);

	if (isset($data['messages'])) {
		foreach ((array) $data['messages'] as $userId) {
			Message::deleteConversation($webmaster, $userId);
		}
	}

	json_message();
}

// Get the number of unread messages for the logged user.
function ajax_count_unread_messages()
{
	if (Auth::guest()) exit;

	json_message( Message::countUnread(Auth::user()->id) );
}

// Get the conversations for the logged user.
function ajax_get_conversations() 
{
	if (Auth::guest()) exit;

	json_message( Message::getConversations(Auth::user()->id) );
}

// Get the conversation messages for the logged user.
function ajax_get_conversation()
{
	if (Auth::guest()) exit;

	$id        = isset($_GET['id'])		   ? $_GET['id']        : 0;
	$timestamp = isset($_GET['timestamp']) ? $_GET['timestamp'] : null;
	$userId    = Auth::user()->id;

	json_message( Message::getConversation($userId, $id, $timestamp) );
}

// Get the user contacts
function ajax_get_contacts()
{
	if (Auth::guest()) exit;

	json_message( Contact::all(Auth::user()->id) );
}

// Search Contact
function ajax_search_contact()
{
	if (Auth::guest()) exit;

	$user  = isset($_GET['user']) ? $_GET['user'] : '';
	$admin = isset($_GET['admin']); 
			
	if (strlen($user) < 2) exit;

	$usersTable    = User::getTable();
	$contactsTable = Contact::getTable();

	$query = User::select("{$usersTable}.id as id", 'username', 'display_name', 'email');
	
	if (!$admin || !Auth::userCan('message_users')) {
		$userId = Auth::user()->id;
		
		$query->join($contactsTable, function($join) use ($usersTable) {
				$join->on("{$usersTable}.id", '=', 'user1')
					 ->orOn("{$usersTable}.id", '=', 'user2');
			})
			->where(function($q) use ($userId) {
				$q->where('user1', $userId)
				  ->orWhere('user2', $userId);
			})
			->where("{$usersTable}.id", '!=', $userId)
			->where('accepted', 1);
	}

	$query->where('status', 1)
		->where(function($q) use ($user) {
			$q->where('username', 'like', "{$user}%");
			$q->orWhere('display_name', 'like', "{$user}%");
			$q->orWhere('email', 'like', "{$user}%");
		})
		->limit(5);

	$contacts = array();

	foreach ($query->get() as $user) {
		$contacts[] = array(
			'id' => $user->id,
			'name' => $user->display_name,
			'avatar' => $user->avatar,
			'username' => $user->username
		);
	}

	json_message($contacts);
}

function ajax_all_contacts()
{
	if (Auth::guest()) exit;

	$userId = Auth::user()->id;
	
	$usersTable    = User::getTable();
	$contactsTable = Contact::getTable();

	$query = User::select("{$usersTable}.id as id", 'username', 'display_name', 'email');
	$query->where('status', 1)
			->join($contactsTable, function($join) use ($usersTable) {
				$join->on("{$usersTable}.id", '=', 'user1')
					 ->orOn("{$usersTable}.id", '=', 'user2');
			})
			->where(function($q) use ($userId) {
				$q->where('user1', $userId)
			  	->orWhere('user2', $userId);
			})
			->where("{$usersTable}.id", '!=', $userId)
			->where('accepted', 1);

	$contacts = array();

	foreach ($query->get() as $user) {
		$contacts[] = array(
			'id' => $user->id,
			'name' => $user->display_name,
			'avatar' => $user->avatar,
			'username' => $user->username
		);
	}

	json_message($contacts);
}

// Get avatar preview
function ajax_avatar_preview()
{
	if (Auth::guest()) exit;

	$type  = isset($_GET['type']) ? $_GET['type'] : '';
	$meta  = Auth::user()->usermeta;
	$email = Auth::user()->email;

	json_message( User::generateAvatar($meta, $email, $type) );
}

// Get users for DataTables (admin)
function ajax_get_users()
{
	if (!Auth::userCan('list_users')) exit;

	$usersTable = User::getTable();
	$rolesTable = Role::getTable();

	$columns = array(
		array('db' => "{$usersTable}.id", 'dt' => 0, 'as' => 'id'),
		array('db' => 'username',         'dt' => 1),
		array('db' => 'email',            'dt' => 2),
		array('db' => 'display_name',     'dt' => 3),
		array('db' => 'joined',           'dt' => 4, 
			'formatter' => function($data, $row) {
				$date = new DateTime($data);
				return '<span title="'.$date->format('Y-m-d H:i:s').'">'.$date->format('M j, Y').'</span>';
			}
		),
		array('db' => 'status', 'dt' => 5),
		array('db' => "{$rolesTable}.name", 'dt' => 6, 'as' => 'role'),
	);

	$query = User::join($rolesTable, "{$usersTable}.role_id", '=', "{$rolesTable}.id", 'left');

	$dt = new Hazzard\Support\DataTables($_GET, $columns, $query);

	echo json_encode($dt->get($usersTable.'.id'));
}

// Get messages for DataTables (admin)
function ajax_get_messages()
{
	if (!Auth::userCan('message_users')) exit;

	$webmaster = Config::get('pms.webmaster');

	$columns = array(
		array('db' => 'id',        'dt' => 0),
		array('db' => 'message',   'dt' => 1),
		array('db' => 'from_user', 'dt' => 2),
		array('db' => 'date',      'dt' => 3,
			'formatter' => function($data, $row) {
				$date = new DateTime($data);
				return '<span title="'.$date->format('Y-m-d H:i:s').'">'.$date->format('M j, Y').'</span>';
			}
		),
		array('db' => 'read',    'dt' => 4),
		array('db' => 'to_user', 'dt' => 5)
	);

	$query = Message::newQuery()->orderBy('date', 'desc');

	$dt = new Hazzard\Support\DataTables($_GET, $columns, $query);

	$result = $dt->get(Message::getTable().'.id');

	$messages = array();

	foreach ($result['data'] as $message) {
		$id   = (int) $message[2] == (int) $webmaster ? $message[5] : $message[2];
		$user = User::find($id);

		if (!$user) continue;

		$messages[] = array(
			$message[0],
			mb_strlen($message[1]) > 70 ? mb_substr($message[1], 0, 70).'...' : $message[1],
			$user->display_name,
			$message[3],
			(bool) $message[4],
			$user->id,
			(int) $message[2] == (int) $webmaster
		);
	}

	$result['data'] = array_values($messages);

	echo json_encode($result);
}

// Get comments
function ajax_get_comments()
{
	$page   = isset($_GET['page'])   ? $_GET['page']   : '';
	$skip   = isset($_GET['skip'])   ? $_GET['skip']   : null;
	$sort   = isset($_GET['sort'])   ? $_GET['sort']   : null;
	$linked = isset($_GET['linked']) ? $_GET['linked'] : null;
	$parent = 0;
	$status = 1;
	$replies = true;
	$take   = Config::get('comments.per_page');

	$total   = Comment::where('page', $page)
						->where('status', $status)
						->count('id');

	$parents = Comment::where('page', $page)
						->where('status', $status)
						->where('parent', $parent)
						->count('id');

	$comments = Comments::getComments(compact('page', 'skip', 'sort', 'status', 'parent', 'linked', 'replies'));

	json_message(compact('comments', 'total', 'parents', 'take'));
}

// Post comment
function ajax_add_comment()
{
	if (Auth::guest()) exit;
	
	$content = isset($_POST['comment']) ? $_POST['comment'] : '';
	$parent  = isset($_POST['parent'])  ? $_POST['parent']  : 0;
	$page    = isset($_POST['page'])    ? $_POST['page']    : '';
	$page_url   = isset($_POST['page_url'])   ? $_POST['page_url']   : '';
	$page_title = isset($_POST['page_title']) ? $_POST['page_title'] : '';

	Comments::setMailer(app('mailer'))->setDispatcher(app('events'));

	$comment = Comments::addComment(compact('content', 'parent', 'page', 'page_url', 'page_title'));
	
	if (is_array($comment)) {
		json_message($comment);
	} else {
		json_message(is_object($comment) ? $comment->first() : trans('errors.dbsave'), false);
	}
}


// Edit comment
function ajax_update_comment()
{
	if (Auth::guest()) exit;

	$id 	 = isset($_POST['id'])      ? (int) $_POST['id'] : 0;
	$content = isset($_POST['comment']) ? $_POST['comment']  : '';

	Comments::setMailer(app('mailer'))->setDispatcher(app('events'));

	$comment = Comments::updateComment($id, compact('content'));

	if (is_array($comment)) {
		json_message($comment);
	} else {
		json_message(is_object($comment) ? $comment->first() : trans('errors.dbsave'), false);
	}
}

function ajax_vote_comment()
{
	if (Auth::guest()) exit;

	$comment_id = isset($_POST['id'])   ? (int) $_POST['id']   : 0;
	$type 		= isset($_POST['type']) ? (int) $_POST['type'] : 0;
	$user_id 	= Auth::user()->id;

	if (!$comment = Comment::find($comment_id)) {
		json_message(trans('comments.404'), false);
	}

	$vote = CommentVote::where('comment_id', $comment_id)->where('user_id', $user_id)->first();

	// Remove upvote / downvote
	if ($type == 1 || $type == 2) {
		if ($vote) {
			if ($type == 1) {
				$comment->upvotes = absint($comment->upvotes - 1);
			} else {
				$comment->downvotes = absint($comment->downvotes - 1);
			}

			$vote->delete();
		}
	} 
	// Upvote / Downvote
	elseif ($type == 3 || $type == 4) {
		if ($type == 3) {
			$comment->upvotes = $comment->upvotes + 1;
		} else {
			$comment->downvotes = $comment->downvotes + 1;
		}

		if ($vote) {
			if ($type == 3) {
				$comment->downvotes = absint($comment->downvotes - 1);
			} else {
				$comment->upvotes = absint($comment->upvotes - 1);
			}
			
			$vote->type = ($type == 3) ? 1 : 2;
			$vote->save();
		} else {
			$type = ($type == 3) ? 1 : 2;
			CommentVote::insert(compact('type', 'comment_id', 'user_id'));
		}
	} else {
		json_message('Invalid vote type.', false);
	}

	$comment->save();

	json_message(true);
}

// Trash comment
function ajax_trash_comment()
{
	if (Auth::guest()) exit;

	$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

	if (Comments::trashComment($id)) {
		json_message(true);
	}
}

// Bulk action for comments (admin)
function ajax_comments_bulk_action()
{
	if (!Auth::userCan('moderate')) exit;

	$action   = isset($_POST['bulk_action']) ? $_POST['bulk_action'] : '';
	$comments = isset($_POST['comments'])    ? $_POST['comments']    : array();

	Comments::commentAction($action, $comments);

	$pending = Comments::countPending();
	$trash   = Comments::countTrash();

	json_message(compact('pending', 'trash'));
}

// Comment reply admin
function ajax_comment_reply()
{
	$parent  = isset($_POST['id'])      ? absint($_POST['id']) : 0;
	$content = isset($_POST['content']) ? $_POST['content']    : '';

	if (!$comment = Comment::find($parent)) {
		json_message('Parent comment not found.', false);
	}

	$page       = $comment->page;
	$page_url   = $comment->page_url;
	$page_title = $comment->page_title;

	Comments::setMailer(app('mailer'))->setDispatcher(app('events'));

	$comment = Comments::addComment(compact('content', 'parent', 'page', 'page_url', 'page_title'));
	
	if (is_array($comment)) {
		json_message(true);
	} else {
		json_message(is_object($comment) ? $comment->first() : trans('errors.dbsave'), false);
	}
}

// Get comments (admin)
function ajax_get_comments_admin()
{
	if (!Auth::userCan('moderate')) exit;

	$columns = array(
		array('db' => 'comments.id',  'dt' => 0, 'as' => 'id'),
		array('db' => 'user_id',      'dt' => 1),
		array('db' => 'content',   'dt' => 2),
		array('db' => 'date',      'dt' => 3,
			'formatter' => function($data, $row) {
				$date = new DateTime($data);
				return '<span title="'.$date->format('Y-m-d H:i:s').'">'.$date->format('M j, Y').'</span>';
			}
		),
		array('db' => 'page_title',   'dt' => 4),
		array('db' => 'page_url',     'dt' => 5),
		array('db' => 'username',     'dt' => 6),
		array('db' => 'email' ,       'dt' => 7),
		array('db' => 'display_name', 'dt' => 8),
		array('db' => 'comments.status', 'dt' => 9, 'as' => 'status'),
		array('db' => 'page',  		  'dt' => 10),
	);

	$query = Comment::join('users', 'comments.user_id', '=', 'users.id');

	$status = trim(@$_GET['columns'][9]['search']['value']);
	if (empty($status)) {
		$query->where('comments.status', '!=', 2);
	}
 
	$dt = new Hazzard\Support\DataTables($_GET, $columns, $query);

	$result = $dt->get(User::getTable().'.id');

	$comments = array();
	$cache = array();

	foreach ($result['data'] as $comment) {
		$user = new User(array(
			'username' => $comment[6], 
			'email'    => $comment[7], 
			'display_name' => $comment[8]
		));
		
		$comment[2] = escape($comment[2]);
		$comment[6] = $user->display_name;

		if (!isset($cache[$comment[0]])) {
			$cache[$comment[0]] = Comment::where('page', $comment[10])->where('status', 1)->count('id');
		}

		$comment[10] = $cache[$comment[0]];

		$comments[] = $comment;
	}

	$result['data'] = array_values($comments);

	echo json_encode($result);
}


// Call ajax_[action] function
$action = isset($_GET['action']) ? $_GET['action'] : (isset($_POST['action']) ? $_POST['action'] : null);
$action = snake_case($action);

if (function_exists("ajax_{$action}")) {
	call_user_func("ajax_{$action}");
} else {
	json_message("Undefined ajax action [$action]", false);
}
