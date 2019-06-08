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


require_once 'app/init.php';
require_once 'app/class.admin.php';
if (isset($_GET['logout'])) {
	Auth::logout();
	redirect_to( App::url('admin.php') );
}

if (Auth::guest()) { 
	echo View::make('admin.login')->render();
	exit; 
}

if (!Auth::userCan('dashboard')) {
	echo View::make('admin.restricted')->render();
	exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

if (View::exists("admin.{$page}")) {
	echo View::make('admin.'.$page)->render();
} else {
	echo View::make('admin.404')->render();
}