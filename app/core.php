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

function CheckSiteRegistration(){
$settings = DB::table('settings')->where('id', 1)->first();
if ($settings->registration ==1) {
	if (!empty($_GET['invite'])) {
		if ($settings->invitecode == $_GET['invite']) {
			
		}else{
			redirect_to(App::url('account/closed.php'));
		}
	}else{
		redirect_to(App::url('account/closed.php'));
	}
}
}
?>