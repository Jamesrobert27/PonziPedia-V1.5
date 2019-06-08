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



//System settings
$settings = DB::table('settings')->where('id', 1)->first();

//Recomitment percentage calculation
$SettingPercentage = $settings->reccomitment;

//Total days for user to get help
$GetHelpDays = $settings->getHelpDay;



//Total days for user to Provide Help help
$ProvideHelpDays = $settings->ProvideHelpday;


//Total time for user to get help Extra
$TimeToMarge = $settings->timeMargin;


//System setting to decide Automatic margin and manual margin
$MarginSystemSet = $settings->margintype;



//Total time for user to get help Extra
$UserProfitPercentage = $settings->profit;



?>