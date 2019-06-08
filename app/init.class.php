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


function marginAPI()
{     
    $startDate = date('Y-m-d H:i:s');
    $settings = DB::table('settings')->where('id', 1)->first();
	    $GetAvalaibleReq = DB::table('requestMaching')->where('balance', '>',0)->where('timeReq', '<', $startDate)->where('status', 'pending')->count();
	    if ($GetAvalaibleReq >= 1) {

		$GetAvalaibleBalance = DB::table('requestMaching')->where('balance', '>',0)->where('timeReq', '<', $startDate)->where('status', 'pending')->orderBy('rand()')->limit(1)->get();
            
		  foreach ($GetAvalaibleBalance as $row) {
      
		  $GetPendingMarge = DB::table('requestHelp')->where('balance', '>',0)->where('timeReq', '<',$startDate)->where('status', 'pending')->count();

          if ($GetPendingMarge >= 1) {


          	$GetBalance = DB::table('requestHelp')->where('balance', '>',0)->where('timeReq', '<', $startDate)->where('status', 'pending')->orderBy('rand()')
                ->limit(1)->get();
           foreach ($GetBalance as $rows) {
            if ($row->balance <= $rows->balance) {
 
            $Amount     = $row->balance;
            $package_id = $row->package_id;
 
            $startDates = time();
            $timeNow = date('Y-m-d H:i:s', strtotime('+'.$settings->timeMargin.' day', $startDates));
	        $Margin =  DB::table('marching')->insert(
               array('receiver_id' => $rows->userid,
               	     'sender_id' => $row->userid,
               	     'amount' => $Amount,
               	     'package_id' => $package_id,
               	     'payment_status' => 'pending',
               	     'expiringTime' => $timeNow, 
                     'active' => 1)
           );

	        if ($Margin) {
       //SMS Notification settings allow or disabled 
      if ($settings->smsallow ==1) {
       $UMessage = DB::table('users')->where('id', $row->userid)->first();
      $SenderP = DB::table('userdetails')->where('userid', $row->userid)->first();
      $message = urlencode("Hi, ".$UMessage->username."  You have been marged to payout on ".Config::get('app.name').". check your dashboard to proceed. https://bit.ly/2FDnK5U");
      $sender= urlencode("NiFunds");
      $mobile = $SenderP->phonenumber;
      $url = 'http://www.MultiTexter.com/tools/geturl/Sms.php?username='.Config::get('app.sms-username').'&password='.Config::get('app.sms-passowrd').'&sender='.$sender.'&message='.$message .'&flash=0&recipients='. $mobile;
     $ch = curl_init();
     curl_setopt($ch,CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_HEADER, 0);
     $resp = curl_exec($ch);
     curl_close($ch);  
      }
	        	   $SenderBalance=  $row->balance - $Amount;
                $blanceQuery =  DB::table('requestMaching') 
                     ->where('id', $row->id)
                      ->update(array('balance' => $SenderBalance));
	        	
                if ($blanceQuery) {
       //SMS Notification settings allow or disabled 
      if ($settings->smsallow ==1) {
       $ReciverMessage = DB::table('users')->where('id', $rows->userid)->first();
      $ReciverP = DB::table('userdetails')->where('userid', $rows->userid)->first();
      $message = urlencode("Hi, ".$ReciverMessage->username."  You have been marged to receive on ".Config::get('app.name').". check your dashboard to confirm. https://bit.ly/2FDnK5U");
      $sender= urlencode("NiFunds");
      $mobile = $ReciverP->phonenumber;
     $url = 'http://www.MultiTexter.com/tools/geturl/Sms.php?username='.Config::get('app.sms-username').'&password='.Config::get('app.sms-passowrd').'&sender='.$sender.'&message='.$message .'&flash=0&recipients='. $mobile;
     $ch = curl_init();
     curl_setopt($ch,CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_HEADER, 0);
     $resp = curl_exec($ch);
     curl_close($ch);
      }
                if ($SenderBalance <=0) {
	        	  $query =  DB::table('requestMaching')
                     ->where('id', $row->id)
                      ->update(array('status' => 'active'));
                  if ($query) {

                  	
                  		$BalanceID =  $rows->balance - $Amount;
                  		  DB::table('requestHelp')
                     ->where('id', $rows->id)
                      ->update(array('balance' => $BalanceID));
                  



	        	     	
	        	     } 
                     
                  }
               }
              
	        }
             }
             


           }

          
              
 
          }
		}
	}
}


function SecondmarginAPI()
{     $startDate = date('Y-m-d H:i:s');
    $settings = DB::table('settings')->where('id', 1)->first();
	    $GetAvalaibleReq = DB::table('requestMaching')->where('balance', '>',0)->where('timeReq', '<', $startDate)->where('status', 'pending')->count();
	    if ($GetAvalaibleReq >= 1) {
		$GetAvalaibleBalance = DB::table('requestMaching')->where('balance', '>',0)->where('timeReq', '<', $startDate)->where('status', 'pending')->orderBy('rand()')
                ->limit(1)->get();
           
		  foreach ($GetAvalaibleBalance as $row) {
		  $GetPendingMarge = DB::table('requestHelp')->where('balance', '>',0)->where('timeReq', '<', $startDate)->where('status', 'pending')->count();

          if ($GetPendingMarge >= 1) {


          	$GetBalance = DB::table('requestHelp')->where('balance', '>',0)->where('timeReq', '<', $startDate)->where('status', 'pending')->orderBy('rand()')
                ->limit(1)->get();
           foreach ($GetBalance as $rows) {

           	
            if ($row->balance > $rows->balance) {

            $Amount     = $rows->balance;
            $package_id = $row->package_id;

            $startDates = time();
            $timeNow = date('Y-m-d H:i:s', strtotime('+'.$settings->timeMargin.' day', $startDates));
	        $Margin =  DB::table('marching')->insert(
               array('receiver_id' => $rows->userid,
               	     'sender_id' => $row->userid,
               	     'amount' => $Amount,
               	     'package_id' => $package_id,
               	     'payment_status' => 'pending',
               	     'expiringTime' => $timeNow, 
                     'active' => 1)
           );
 
	        if ($Margin) {
       //SMS Notification settings allow or disabled 
      if ($settings->smsallow ==1) {
      $UMessage = DB::table('users')->where('id', $row->userid)->first();
      $SenderP = DB::table('userdetails')->where('userid', $row->userid)->first();
      $message = urlencode("Hi, ".$UMessage->username."  You have been marged to payout on ".Config::get('app.name').". check your dashboard to proceed. https://bit.ly/2FDnK5U");
      $sender= urlencode("NiFunds");
      $mobile = $SenderP->phonenumber;
       $url = 'http://www.MultiTexter.com/tools/geturl/Sms.php?username='.Config::get('app.sms-username').'&password='.Config::get('app.sms-passowrd').'&sender='.$sender.'&message='.$message .'&flash=0&recipients='. $mobile;
     $ch = curl_init();
     curl_setopt($ch,CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_HEADER, 0);
     $resp = curl_exec($ch);
     curl_close($ch);
     }
	        	$SenderBalance=  $row->balance - $Amount;
                $sql =  DB::table('requestMaching')
                     ->where('userid', $row->userid)
                      ->update(array('balance' => $SenderBalance));
	        	
                if ($sql) {

       //SMS Notification settings allow or disabled 
      if ($settings->smsallow ==1) {
      $ReciverMessage = DB::table('users')->where('id', $rows->userid)->first();
      $ReciverP = DB::table('userdetails')->where('userid', $rows->userid)->first();
      $message = urlencode("Hi, ".$ReciverMessage->username."  You have been marged to receive on ".Config::get('app.name').". check your dashboard to confirm. https://bit.ly/2FDnK5U");
      $sender= urlencode("NiFunds");
      $mobile = $ReciverP->phonenumber;
       $url = 'http://www.MultiTexter.com/tools/geturl/Sms.php?username='.Config::get('app.sms-username').'&password='.Config::get('app.sms-passowrd').'&sender='.$sender.'&message='.$message .'&flash=0&recipients='. $mobile;
     $ch = curl_init();
     curl_setopt($ch,CURLOPT_URL, $url);
     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
     curl_setopt($ch, CURLOPT_HEADER, 0);
     $resp = curl_exec($ch);
     curl_close($ch);
     }
        	if ($rows->balance <= $Amount) {

                  		$BalanceID = $Amount - $rows->balance;
                  		  DB::table('requestHelp')
                     ->where('userid', $rows->userid)
                      ->update(array('balance' => $BalanceID));
                  	}
                    elseif ($rows->balance > $Amount) {
                      $BalanceID = $rows->balance - $Amount;
                        DB::table('requestHelp')
                     ->where('userid', $rows->userid)
                      ->update(array('balance' => $BalanceID));
                    }
                if ($row->balance <=0) {
	        	  $query =  DB::table('requestMaching')
                     ->where('userid', $row->userid)
                      ->update(array('status' => 'active'));
                  if ($query) {
                  	



	        	     	
	        	     } 
                     
                  }
               }
              
	        }
             }
             


           }

          
              

          }
		}
	}
}




function RequestHelpBalanceZero()
{
	$RequestHelpBalance = DB::table('requestHelp')->where('balance', '=',0)->where('status', 'pending')->orderBy('rand()')
                ->limit(5)->get();
    foreach ($RequestHelpBalance as $row) {
    	DB::table('requestHelp')
        ->where('userid', $row->userid)
        ->update(array('status' => 'active'));
    }
}




?>