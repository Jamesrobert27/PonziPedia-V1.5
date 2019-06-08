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
 
$demoSet =0;

if ($demoSet ==1) {

  if (Auth::user()->username != "admin") {
   echo '<div class="container"> <div class="alert alert-info alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Welcome Demo!</strong> This is demo view and you cant edit or save settings.
            </div>
            </div>';
   if (isset($_POST['submit']) || isset($_POST['margeNow']) || isset($_POST['Deletesubmit']) || isset($_POST['SetMargin']) || isset($_POST['UnsetMargin']) || isset($_POST['SingleMail'])  || isset($_POST['MassMail']) || isset($_POST['ConfirmPayment']) || isset($_POST['SetReceiver']) || isset($_POST['DisengageNow']) || isset($_POST['SetMargin']) || isset($_POST['UnsetMargin']) || isset($_POST['MessageSubmit'])){
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> You are on demo view and you cant make changes, please purchase PonziPedia to have full right to make changes.
            </div>';
            exit;
   }
  }
  
}

//Admin get total request margin from users
function GetMarginReq()
{
  $settings = DB::table('settings')->where('id', 1)->first();
	$users = DB::table('requestMaching')->where('status', 'pending')->get();
	foreach ($users as $row) {
    $then = $row->timeReq; 
$now = time();
 
//convert $then into a timestamp.
$thenTimestamp = strtotime($then);
 
//Get the difference in seconds.
$difference = $now - $thenTimestamp;
 if ($difference >=0) {
   $TimeX = "Ready To Marge";
 }elseif ($difference <=0) {
  
  $TimeX = $difference;
 }

 else{
   $TimeX = $difference;
 }


	$users = DB::table('users')->where('id', $row->userid)->first();
	$user = DB::table('userdetails')->where('userid', $row->userid)->first();
		echo '<tr>
				<th><input type="checkbox" class="select-all" value="1"></th>
				<td>'.$users->username.'</td>
				<td>'.$user->accountname.'</td>
				<td>'.$settings->currency.''.$row->amount.'</td>
				<td>'.$settings->currency.''.$row->balance.'</td> 
        
				<td>'.$row->status.'</td>
        <td>'.$TimeX.'</td>
				<td><form method="POST" action="">
           <input type="hidden" name="id" value="'.$row->id.'">
           <input type="submit" name="delete" value="Delete" class="btn btn-danger btn-sm ">
           </form></td>
				<td><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row->id.'"><button class="btn btn-primary">Marge</button></a></td>
			</tr>

			 <div id="collapse'.$row->id.'" class="panel-collapse collapse">
      <div class="panel-body">
      <h1>Create Margin Manual ('.$users->username.')</h1>
      <form role="form" method="POST" action="" class="col-md-4">
 <div class="form-group"> 
  <input type="hidden" name="package_id" value="'.$row->package_id.'" >
    <label for="nameoftheorganisation">Name of Receiver</label>
    <input type="text" class="form-control" name="amount" value="'.$row->balance.'" placeholder="Amount To Marge">
  </div>
   <div class="form-group">
    <label for="name">Receiver Username</label>
     ';
    $sql = DB::table('requestHelp')->where('status', 'pending')->where('userid','!=', $row->userid)->get();
    echo ' <select id="receivername" name="receivername" required="required" class="form-control"><option value="NULL" selected="selected">Select Receiver</option>';
    foreach ($sql as $row) {
      $usersss = DB::table('users')->where('id', $row->userid)->first();
      echo '<option value="'.$usersss->username.'">'.$usersss->username.' | '.$settings->currency.''.$row->balance.'</option>
     ';
    }
   echo '</select>
  </div>
   <div class="form-group">
    <label for="name">Sender Username</label>
    <input type="text" class="form-control"  value="'.$users->username.'" disabled>
    <input type="hidden" name="sendername" value="'.$users->username.'" >
  </div>
 
  <input type="submit" name="margeNow" class="btn btn-default" value="Create Margin" />
</form>
        
        
      </div>
    </div>
  </div>';
	}
}



function StartMargin($Amount,$username,$receivername,$sendername,$package_id)
{

  $settings = DB::table('settings')->where('id', 1)->first();
	$users = DB::table('users')->where('username', $username)->first();
	$user = DB::table('users')->where('username', $receivername)->first();
	$pack = DB::table('packages')->where('id', $package_id)->first();
	$startDate = time();
    $timeNow = date('Y-m-d H:i:s', strtotime('+'.$settings->timeMargin.'day', $startDate));
	$sql = DB::table('marching')->insert(
               array('receiver_id' => $user->id,
               	     'sender_id' => $users->id,
               	     'amount' => $Amount,
               	     'package_id' => $pack->id,
               	     'payment_status' => 'pending',
               	     'expiringTime' => $timeNow, 
                     'active' => 1)
     ); 

	if ($sql) {
    //SMS Notification settings allow or disabled 
    if ($settings->smsallow ==1) {
      $UMessage = DB::table('users')->where('id', $users->id)->first();
      $SenderP = DB::table('userdetails')->where('userid', $users->id)->first();
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

     DB::table('notification')->insert(
    array('userid' =>        $user->id,
         'type' =>        "receive",
          'details' =>    "You have been marged to receive ".$Amount, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd"));

        DB::table('notification')->insert(
    array('userid' =>        $users->id,
         'type' =>        "receive",
          'details' =>    "You have been marged to payout ".$Amount, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd"));
     
		$userssend = DB::table('requestMaching')->where('userid', $users->id)->where('status', 'pending')->first();
		$Upamount = $userssend->balance - $Amount;

		$sqls = DB::table('requestMaching')
        ->where('userid', $users->id)
        ->update(array('balance' => $Upamount));
    

  
  if ($sql) {

      //SMS Notification settings allow or disabled 
      if ($settings->smsallow ==1) {
        
     $ReciverMessage = DB::table('users')->where('id', $user->id)->first();
      $ReciverP = DB::table('userdetails')->where('userid', $user->id)->first();
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
          $usersen = DB::table('requestMaching')->where('userid', $users->id)->where('status', 'pending')->first();
          $pamount = $usersen->balance;
		if ($pamount ==0) {
			DB::table('requestMaching')
        ->where('userid', $users->id)
        ->update(array('status' => 'active'));

		}
  }
		$usersreceive = DB::table('requestHelp')->where('userid', $user->id)->where('status', 'pending')->first();
    $ReceiverAmount = $usersreceive->balance - $Amount;
    $sqlss = DB::table('requestHelp')
        ->where('userid', $user->id)
        ->update(array('balance' => $ReceiverAmount));
   
   if ($sqlss) {
          $userRe = DB::table('requestHelp')->where('userid', $user->id)->where('status', 'pending')->first();
          $Reamount = $userRe->balance;
    if ($Reamount ==0) {
      DB::table('requestHelp')
        ->where('userid', $user->id)
        ->update(array('status' => 'active'));

    }
}


		
		echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully create margin to username '.$receivername.' to receive  '.$settings->currency.' '.$Amount.'
            </div>';
	}
	else{
		echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> Change a few things up and try submitting again.
            </div>';
	}
}


//Admin get total help margin from users
function GetMarginHelp()
{
   $settings = DB::table('settings')->where('id', 1)->first();
	$users = DB::table('requestHelp')->where('status', 'pending')->get();
	foreach ($users as $row) {
	$users = DB::table('users')->where('id', $row->userid)->first();
	$user = DB::table('userdetails')->where('userid', $row->userid)->first();
		echo '<tr>
				<th><input type="checkbox" class="select-all" value="1"></th>
				<td>'.$users->username.'</td>
				<td>'.$user->accountname.'</td>
				<td>'.$settings->currency.''.$row->amount.'</td>
				<td>'.$settings->currency.''.$row->profit.'</td>
				<td>'.$settings->currency.''.$row->balance.'</td>
				<td>'.$row->timeReq.'</td>
				<td>'.$row->status.'</td>
				<td> <form method="POST" action="">
           <input type="hidden" name="id" value="'.$row->id.'">
           <input type="submit" name="delete" value="Delete" class="btn btn-danger btn-sm ">
           </form></td>
				<td><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row->id.'"><button class="btn btn-primary">Marge</button></a></td>
			</tr>

			 <div id="collapse'.$row->id.'" class="panel-collapse collapse">
      <div class="panel-body">
      <h1>Create Margin Manual</h1>
      <form role="form" method="POST" action="" class="col-md-4">
 <div class="form-group">
  <input type="hidden" name="package_id" value="'.$row->package_id.'" >
    <label for="nameoftheorganisation">Name of Receiver</label>
    <input type="text" class="form-control" name="amount" value="'.$row->balance.'" placeholder="Amount To Marge">
    <span style="color: #f40000;"><strong>! Important</strong> Make sure sender amount to payout is equal or less than the amount requesting to payout to avoid balance overdraft</span>
  </div>

   <div class="form-group">
    <label for="name">Receiver Username</label>
    <input type="text" class="form-control"  value="'.$users->username.'" disabled>
    <input type="hidden" name="receivername" value="'.$users->username.'" >
  </div>
  
    <div class="form-group">
    <label for="name">Sender Username</label>
    ';
    $sql = DB::table('requestMaching')->where('status', 'pending')->where('userid','!=', $row->userid)->get();
    echo ' <select id="sendername" name="sendername" required="required" class="form-control"><option value="NULL" selected="selected">Select Waiting Member</option>';
    foreach ($sql as $row) {
    	$userss = DB::table('users')->where('id', $row->userid)->first();

    	echo '<option value="'.$userss->username.'">'.$userss->username.' | '.$settings->currency.''.$row->balance.'</option>
      ';
    }
   echo '</select>

  </div>
 
  <input type="submit" name="margeNow" class="btn btn-default" value="Create Margin" />
</form>
        
        
      </div>
    </div>
  </div>';
	}
}


function StartMarginReq($Amount,$username,$receivername,$sendername,$package_id,$senderBalance)
{
  $settings = DB::table('settings')->where('id', 1)->first();
	$users = DB::table('users')->where('username', $username)->first();
	$user = DB::table('users')->where('username', $receivername)->first();
	$pack = DB::table('packages')->where('id', $package_id)->first();
	$startDate = time();
    $timeNow = date('Y-m-d H:i:s', strtotime('+'.$settings->timeMargin.'day', $startDate));
	$sql = DB::table('marching')->insert(
               array('receiver_id' => $user->id,
               	     'sender_id' => $users->id,
               	     'amount' => $Amount,
               	     'package_id' => $pack->id,
               	     'payment_status' => 'pending',
               	     'expiringTime' => $timeNow, 
                     'active' => 1)
     );

	$RegBalance = DB::table('requestHelp')->where('userid', $user->id)->where('status', 'pending')->first();
	if ($RegBalance) {
		$balanceUp = $RegBalance->balance - $Amount;
		$StatsUp = DB::table('requestHelp')
        ->where('userid', $user->id)
        ->update(array('balance' => $balanceUp));

        if ($RegBalance->balance =='0' || $RegBalance->balance <0) {
        	DB::table('requestHelp')
        ->where('userid', $user->id)
        ->update(array('status' => 'active'));
        }
	}

    DB::table('notification')->insert(
    array('userid' =>        $user->id,
         'type' =>        "receive",
          'details' =>    "You have been marged to receive ".$Amount, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd"));

        DB::table('notification')->insert(
    array('userid' =>        $users->id,
         'type' =>        "receive",
          'details' =>    "You have been marged to payout ".$Amount, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd"));
  //SMS Notification settings allow or disabled
    if ($settings->smsallow ==1) {
     $UMessage = DB::table('users')->where('id', $users->id)->first();
      $SenderP = DB::table('userdetails')->where('userid', $users->id)->first();
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
	if ($sql) {
    
    //SMS Notification settings allow or disabled 
    if ($settings->smsallow ==1) {
     $ReciverMessage = DB::table('users')->where('id', $user->id)->first();
      $ReciverP = DB::table('userdetails')->where('userid', $user->id)->first();
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

		$userssend = DB::table('requestMaching')->where('userid', $users->id)->where('status', 'pending')->first();
		$Upamount = $userssend->balance - $Amount;

		$sqls = DB::table('requestMaching')
        ->where('userid', $users->id)
        ->update(array('balance' => $Upamount));

		if ($sqls) {
          $usersen = DB::table('requestMaching')->where('userid', $users->id)->where('status', 'pending')->first();
          $pamount = $usersen->balance;
		if ($pamount ==0) {
			DB::table('requestMaching')
        ->where('userid', $users->id)
        ->update(array('status' => 'active')); 

		}
			
		}
		
		echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully create margin to username '.$receivername.' to receive  '.$settings->currency.' '.$Amount.'
            </div>';
	}
	else{
		echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> Change a few things up and try submitting again.
            </div>';
	}
}

function SettMargin($id)
{
 $sql = DB::table('settings')
        ->where('id', 1)
        ->update(array('margintype' => 1));
  if ($sql) {
      echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully set system to auto margin.
            </div>';
  }
    else{
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> i cant complete your request, try submitting again.
            </div>';
  }
}


function UnsettMargin($id)
{
 $sql = DB::table('settings')
        ->where('id', 1)
        ->update(array('margintype' => 2));
  if ($sql) {
      echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully set system to manual margin.
            </div>';
  }
    else{
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> i cant complete your request, try submitting again.
            </div>';
  }
}



function GetUserTickets(){
  $sql = DB::table('tickets')->where('status', 1)->where('moderator', 'new')->orderBy('id', 'desc')->get();
  foreach ($sql as $row) {
    $query = DB::table('ticketsreply')->where('userid', $row->userid)->count();
    echo ' <li class="list-group-item">
    <span class="badge">'.$query.'</span>';
    if ($row->moderator =="new"){
    echo '<span class="badge" style="background-color: #f40000 !important;">New</span>';
  }
  echo '
   <a href="admin.php?page=ticketview&reply='.$row->id.'">'.$row->subject.'</a>
  </li>';
  }

   $sqls = DB::table('tickets')->where('status', 1)->where('moderator', 'replied')->orderBy('id', 'desc')->get();
  foreach ($sqls as $row) {
    $query = DB::table('ticketsreply')->where('userid', $row->userid)->count();
    echo ' <li class="list-group-item">
    <span class="badge">'.$query.'</span>
   <a href="admin.php?page=ticketview&reply='.$row->id.'">'.$row->subject.'</a>
  </li>';
  }

}


function GetUserMessages(){
 $sql= DB::table('messagess')->groupBy('from_user')
                ->having('to_user', '=', 1)->orderBy('id', 'desc')
                ->get();
  foreach ($sql as $row) {
    $sqls= DB::table('messagess')->where('from_user', $row->from_user)->where('read', 1)->count();
    $user = DB::table('users')->where('id', $row->from_user)->first();
    echo '
  <tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$user->username.'</td>
        <td>'.substr($row->message, 0, 50).''; if ($sqls >=1){echo '<span class="label label-danger">New</span> ';}else{} echo '</td>
        <td> <form method="POST" action=""><input name="id" value="'.$row->id.'" type="hidden"> <input type="submit" value="Delete" name="deleteUser" class="btn btn-danger"> </form><span class="glyphicon glyphicon-trash" style="color: #fff;"></a></td>
        <td><a href="?page=messagesreply&u='.$row->from_user.'" class="btn btn-primary">Read</a></td>
      </tr>';
  }
}



function TicketReplyView($userid){
     $user = DB::table('tickets')->where('id', $userid)->first();
     $row = DB::table('users')->where('id', $user->userid)->first();

  echo '
  <li class="list-group-item">
                        <div class="row">
                            <div class="col-xs-2 col-md-1">
                                <img src="http://placehold.it/80" class="img-circle img-responsive" alt="" /></div>
                            <div class="col-xs-10 col-md-11">
                                <div>
                                    <a href="#">
                                        '.$user->subject.'</a>
                                    <div class="mic-info">
                                        By: <a href="#">'.$row->username.'</a> on '.$user->date.'
                                    </div>
                                </div>
                                <div class="comment-text">
                                   '.$user->description.'
                                </div>
                               
                        
    
      
                              
                            </div>
                        </div>
                    </li>'
                ;
}


function ReplyTickets($message,$userid)
{
   $user = DB::table('tickets')->where('id', $userid)->first();
   $row = DB::table('users')->where('id', $user->userid)->first();
  $sql =DB::table('ticketsreply')->insert(
    array('ticketid' => $userid, 
         'admin' => 'Support', 
         'userid' => $row->id, 
         'reply' => $message,
         'replied' => '0', 
         'status' => 1)
);

   if ($sql) {

    $ticreply = DB::table('tickets')->where('id', $userid)->first();
  $replied = $user->replied + 1;
   DB::table('tickets')
        ->where('id', $userid)
        ->update(array('replied' => $replied));
  
   DB::table('ticketsreply')
        ->where('userid', $userid)
        ->update(array('replied' => 'old'));
  
    DB::table('tickets')
        ->where('id', $user->id)
        ->update(array('moderator' => 'replied'));

        DB::table('notification')->insert(
    array('userid' =>        $row->id,
         'type' =>        "support",
          'details' =>    "Your ticket has been replied by support", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-ticket")
);

      echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully reply to this ticket.
            </div>';
  }
    else{
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> i cant complete your request, try submitting again.
            </div>';
  }
}

function TicketReplyViewReply($userid){
     $user = DB::table('ticketsreply')->where('ticketid', $userid)->get();
     foreach ($user as $row) {
      $rows = DB::table('users')->where('id', $row->userid)->first();

       echo '
            <li class="list-group-item">
                        <div class="row">
                            <div class="col-xs-2 col-md-1">
                                <img src="http://placehold.it/80" class="img-circle img-responsive" alt="" /></div>
                            <div class="col-xs-10 col-md-11">
                                <div>
                                    <a href="#"> </a>
                                        Reply: <a href="#">'.$rows->username.'</a> on '.$row->date.'
                                  
                                </div>
                                <div class="comment-text">
                                   '.$row->reply.'
                                </div>
                               
                        
    
      
                              
                            </div>
                        </div>
                    </li>'
                ;
     }

 
}



function messageUView($userid){
        $sql = DB::table('messagess')->where('from_user', $userid)->orWhere('to_user', $userid)->where('deleted', 0)->get();
        foreach ($sql as $row) {

          if ($row->from_user ==1) {
           $classs = "right";
           $classsR = "pull-right";
           $classsL = "pull-left";
           
           $userSender = "Support";

           $MesName = ' <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>'.$row->date.'</small>
                                    <strong class="'.$classsL.' primary-font">'.$userSender.'</strong>';
          }
          elseif($row->from_user == $userid){
             $classs = "left";
             $classsR = "pull-left";
             $classsL = "pull-right";

             $user = DB::table('users')->where('id', $userid)->first();
             $userSender = $user->username;

              $MesName = ' <strong class="primary-font">'.$userSender.'</strong> <small class="'.$classsL.' text-muted">
                           <span class="glyphicon glyphicon-time"></span>'.$row->date.'</small>';

          }
         echo '    <li class="'.$classs.' clearfix"><span class="chat-img '.$classsR.'">
                            <img src="http://placehold.it/50/55C1E7/fff&text=U" alt="User Avatar" class="img-circle" />
                        </span>
                            <div class="chat-body clearfix">
                                <div class="header">
                                   '.$MesName.'
                                </div>
                                <p>
                                   '.$row->message.'
                                </p>
                            </div>
                        </li>';
        }
}

 

function ReplyMessageU($userid,$message)
{
  $sql = DB::table('messagess')->insert(
             array('from_user' => 1, 
                   'to_user' => $userid,
                   'message' => $message, 
                   'read' => 0, 
                   'reply' => 1, 
                   'deleted' => 0)
       );

  if ($sql) {
     DB::table('messagess')
        ->where('from_user', $userid)
        ->update(array('read' => 0));
  
      echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully reply to this message
            </div>';
  }
    else{
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> i cant complete your request, try submitting again.
            </div>';
 
  }
}

function SeupportActiveCount() 
{
  $ticket = DB::table('tickets')->where('replied', 1)->count();
  $ticketreply = DB::table('ticketsreply')->where('replied', 'new')->count();
  $cases = DB::table('courtcase')->where('status', '!=', 0)->count();

  $total = $ticket+ $ticketreply +$cases;
  echo $total;
}


function SeupportTicketCount()
{
  $ticket = DB::table('tickets')->where('replied', 1)->count();
  $ticketreply = DB::table('ticketsreply')->where('replied', 'new')->count();

  $total = $ticket + $ticketreply;
  echo $total;
}


function SeupportMessageCount()
{
  $message = DB::table('messagess')->where('read', 1)->count();

  $total = $message ;
  echo $total;
}

function SeupportCourtCaseCount()
{
  $cases = DB::table('courtcase')->where('status', '!=', 0)->count();

  $total = $cases ;
  echo $total;
}


function DeleteMessageU($id){

    $delete = DB::table('messagess')->where('id', $id)->first();
   $sql = DB::table('messagess')->where('from_user', $delete->from_user)->orWhere('to_user', $delete->from_user)->delete();
   if ($sql) {
         echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully delete user message
            </div>';
  }
    else{
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> i cant complete your request, try submitting again.
            </div>';
   }
}



function SettingMain($id,$margintype,$profit,$getHelpDay,$ProvideHelpday,$reccomitment,$referralProfit,$guiderProfit,$GuiderMin,$days,$activationFeeSwitch,$activationAmount,$activationdays,$site,$currency,$regmode,$invitecode,$sms){


   $sql = DB::table('settings')
        ->where('id', $id)
        ->update(array('margintype' => $margintype,
                         'profit' => $profit,
                          'days' => $days,
                         'getHelpDay' => $getHelpDay,
                         'ProvideHelpday' => $ProvideHelpday,
                         'activationFee' => $activationFeeSwitch,
                         'referralProfit' => $referralProfit,
                         'guiderProfit' => $guiderProfit,
                         'GuiderMin' => $GuiderMin,
                         'activationPrice' => $activationAmount,
                         'activationFeeExp' => $activationdays,
                         'currency' => $currency,
                         'site_status' => $site,
                         'reccomitment' => $reccomitment,
                           'registration' => $regmode,
                           'invitecode' => $invitecode,
                            'smsallow' => $sms));


         if ($sql) {
         echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully updated website setting
            </div>';
  }
    else{
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> You didnt make any Change, make change and try submitting again.
            </div>';
   }
}
 


function packagesView()
{
   $settings = DB::table('settings')->where('id', 1)->first();
  $packages =DB::table('packages')->orderBy('id', 'DESC')->get();

  foreach ($packages as $row) {
    $settings = DB::table('settings')->where('id', 1)->first();
    echo '  <div class="col-md-6 text-center">
                    <div class="plan-cell">
                        <ul class="list-group green">
                            <li class="list-group-item plan-name mayPackages" style="font-size: 25px;background-color: #222;color: #fff;">'.$row->packname.'</li>
                            <li class="list-group-item plan-price catamaran">'.$settings->currency.''.$row->price.'</li>
                            <li class="list-group-item catamaran"><i class="fa fa-circle-o" style="font-size: 25px;"></i> Auto Matching</li>
                            <li class="list-group-item catamaran"><i class="fa fa-arrow-up" style="font-size: 25px;"></i> '.$settings->profit.'% Return on Investment </li>
                            <li class="list-group-item catamaran"><i class="ion-arrow-up-c"></i> '.$settings->currency.' '.$row->profit.' Return on Investment</li>';
                             if ($row->codes !="") {
                               echo ' <li class="list-group-item catamaran"><i class="fa fa-lock" style="font-size: 25px;color:#f40000;"></i>Unlock Key: '.$row->codes.' </li>';}
                            echo '<li class="list-group-item catamaran" style="padding:0">
                            
                            <button class="btn btn-danger btn-block" style="border-radius: 0px !important;" data-toggle="modal" data-target="#packDelete'.$row->id.'">Delete<button/></li>
                         </li>
                        </ul>
                    </div>
                </div>



<div class="modal fade" id="packDelete'.$row->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
      </div>
      <div class="modal-body">
      <h3>User with this package will get error, Only delete if its new package</h3>
       <form method="POST" action="">
                            <input type="hidden" name="pack_id" value='.$row->id.'">
                            <input type="submit" name="Deletesubmit" class="btn btn-danger btn-block" style="border-radius: 0px !important;" value="Delete" /></li>
                          </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

';
  }

}

function CreatePackages($price,$name,$codes)
{

  $settings = DB::table('settings')->where('id', 1)->first(); 

    //User Recommitment Percentage
      $percentage = $settings->profit;
      $totalWidth = $price;
      $new_amount = ($percentage / 100) * $totalWidth;

       $profit = $price + $new_amount;
  $sql = DB::table('packages')->insert(
               array('packname' => $name,
                     'price' => $price,
                     'profit' => $profit,
                     'days' =>   $settings->days,
                     'codes' =>   $codes,
                     'status' => 1)
);

     if ($sql) {
         echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully create package
            </div>';
  }
    else{
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> You didnt make any Change, make change and try submitting again.
            </div>';
   }
}


function DeletePackages($id){

    $pack = DB::table('packages')->where('id', $id)->first();
    $sql = DB::table('packages')->where('id', $id)->delete();
    
     if ($sql) {
         echo '<div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Well done!</strong> You successfully delete package '.$pack->packname.'
            </div>';
  }
    else{
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> You didnt make any Change, make change and try submitting again.
            </div>';
   }
}

   

function SetmemberMargin($username,$packages){
$settings = DB::table('settings')->where('id', 1)->first();
$usernameGet = DB::table('users')->where('username', $username)->first();
$user_package =  DB::table('packages')->where('id', $packages)->first();
 $startDate = time();
$timeX        =   date('Y-m-d H:i:s', strtotime('+'.$settings->getHelpDay.' day', $startDate));
 $sql =  DB::table('requestHelp')->insert(
                  array('userid' => $usernameGet->id,
                         'package_id' => $packages,
                         'pack_name' => $user_package->packname,
                         'amount' => $user_package->price,
                         'profit' => $user_package->profit,
                         'timeReq' => $timeX,
                         'balance' => $user_package->profit,
                         'status' => 'pending')
);
 

if ($sql) {
  DB::table('notification')->insert(
    array('userid' =>        $usernameGet->id,
         'type' =>        "credit",
          'details' =>    "You have been credited with ".$settings->currency."".$user_package->profit.", you will be marge soon", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd")
);


  echo'<div class="alert alert-success" role="alert">
  You have successfully request help and you will Get-Help soon
</div>';
 }
 else{
  echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
}
}



//Admin get total activation fees margin from users
function GetactivationReq() 
{
  $settings = DB::table('settings')->where('id', 1)->first();
  $users = DB::table('activationFee')->orderBy('id', 'desc')->get();
  foreach ($users as $row) {
  $users = DB::table('users')->where('id', $row->sender_id)->first();
  $receiver = DB::table('users')->where('id', $row->receiver_id)->first();
  $user = DB::table('userdetails')->where('userid', $row->sender_id)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$users->username.'</td>
        <td>'.$user->accountname.'</td>
         <td>'.$receiver->username.'</td>
        <td>'.$settings->currency.''.$row->amount.'</td>
        <td>'.$row->payment_status.'</td>';
          if ($row->ProofPic =="") {
           echo '<td><a href="#"><img src="" alt="No POF Uploaded" style="width: 100px;"></a></td>';
          }
          else{ echo '<td><a href="'.App::url().'/images/'.$row->ProofPic.'" target="_BLANK"><img src="'.App::url().'/images/'.$row->ProofPic.'" alt="No POF Uploaded" style="width: 100px;"></a></td>';}
        
         if ($row->payment_status =="confirm") {
           echo '<td> <a class="btn btn-danger btn-block" style="border-radius: 0px !important;">Payment Confirmed<a/></td>';
          }
          else{ echo '<td> <a class="btn btn-success btn-block" style="border-radius: 0px !important;" data-toggle="modal" data-target="#ActionConfirmed'.$row->id.'">Confirm Payment<a/></td>';}

       echo ' 
      </tr>

<div class="modal fade" id="ActionConfirmed'.$row->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="myModalLabel">Choose Action</h4>
      </div>
      <div class="modal-body">
          <form method="POST" action="">
                            <input type="hidden" name="id" value='.$row->id.'">
                            <input type="submit" name="ConfirmPayment" class="btn btn-success btn-block" style="border-radius: 0px !important;" value="Confirm Payment" /></li>
                          </form>
      </div>
   
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
';
  }
}



//Admin get total activation fees margin from users
function GetactivationPending() 
{
  $settings = DB::table('settings')->where('id', 1)->first();
  $users = DB::table('activationFee')->where('payment_status', 'pending')->orderBy('id', 'desc')->get();
  foreach ($users as $row) {
  $users = DB::table('users')->where('id', $row->sender_id)->first();
  if ($users->status != "2") {
    $receiver = DB::table('users')->where('id', $row->receiver_id)->first();
  $user = DB::table('userdetails')->where('userid', $row->sender_id)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$users->username.'</td>
        <td>'.$user->accountname.'</td>
         <td>'.$receiver->username.'</td>
        <td>'.$settings->currency.''.$row->amount.'</td>
        <td>'.$row->payment_status.'</td>';
          if ($row->ProofPic =="") {
           echo '<td><a href="#"><img src="" alt="No POF Uploaded" style="width: 100px;"></a></td>';
          }
          else{ echo '<td><a href="'.App::url().'/images/'.$row->ProofPic.'" target="_BLANK"><img src="'.App::url().'/images/'.$row->ProofPic.'" alt="No POF Uploaded" style="width: 100px;"></a></td>';}
        
         if ($row->payment_status =="confirm") {
           echo '<td> <a class="btn btn-danger btn-block" style="border-radius: 0px !important;">Payment Confirmed<a/></td>';
          }
          else{ echo '<td> <a class="btn btn-success btn-block" style="border-radius: 0px !important;" data-toggle="modal" data-target="#ActionConfirmed'.$row->id.'">Confirm Payment<a/></td>';}

       echo ' 
      </tr>

<div class="modal fade" id="ActionConfirmed'.$row->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="myModalLabel">Choose Action</h4>
      </div>
      <div class="modal-body">
          <form method="POST" action="">
                            <input type="hidden" name="id" value='.$row->id.'">
                            <input type="submit" name="ConfirmPayment" class="btn btn-success btn-block" style="border-radius: 0px !important;" value="Confirm Payment" /></li>
                          </form>
      </div>
   
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
';
  }
 
  }
}


//Admin get total activation fees margin from users
function GetactivationWaiting() 
{
  $settings = DB::table('settings')->where('id', 1)->first();
  $users = DB::table('activationFee')->where('payment_status', 'waiting')->orderBy('id', 'desc')->get();
  foreach ($users as $row) {
  $users = DB::table('users')->where('id', $row->sender_id)->first();
   if ($users->status != "2") {
  $receiver = DB::table('users')->where('id', $row->receiver_id)->first();
  $user = DB::table('userdetails')->where('userid', $row->sender_id)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$users->username.'</td>
        <td>'.$user->accountname.'</td>
         <td>'.$receiver->username.'</td>
        <td>'.$settings->currency.''.$row->amount.'</td>
        <td>'.$row->payment_status.'</td>';
          if ($row->ProofPic =="") {
           echo '<td><a href="#"><img src="" alt="No POF Uploaded" style="width: 100px;"></a></td>';
          }
          else{ echo '<td><a href="'.App::url().'/images/'.$row->ProofPic.'" target="_BLANK"><img src="'.App::url().'/images/'.$row->ProofPic.'" alt="No POF Uploaded" style="width: 100px;"></a></td>';}
        
         if ($row->payment_status =="confirm") {
           echo '<td> <a class="btn btn-danger btn-block" style="border-radius: 0px !important;">Payment Confirmed<a/></td>';
          }
          else{ echo '<td> <a class="btn btn-success btn-block" style="border-radius: 0px !important;" data-toggle="modal" data-target="#ActionConfirmed'.$row->id.'">Confirm Payment<a/></td>';}

       echo ' 
      </tr>

<div class="modal fade" id="ActionConfirmed'.$row->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="myModalLabel">Choose Action</h4>
      </div>
      <div class="modal-body">
          <form method="POST" action="">
                            <input type="hidden" name="id" value='.$row->id.'">
                            <input type="submit" name="ConfirmPayment" class="btn btn-success btn-block" style="border-radius: 0px !important;" value="Confirm Payment" /></li>
                          </form>
      </div>
   
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
';
  }
}
}


//Admin get total activation fees margin from users
function GetactivationReqPaid() 
{
  $settings = DB::table('settings')->where('id', 1)->first();
  $users = DB::table('activationFee')->where('payment_status', 'confirm')->orderBy('id', 'desc')->get();
  foreach ($users as $row) {
  $users = DB::table('users')->where('id', $row->sender_id)->first();
  $receiver = DB::table('users')->where('id', $row->receiver_id)->first();
  $user = DB::table('userdetails')->where('userid', $row->sender_id)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$users->username.'</td>
        <td>'.$user->accountname.'</td>
         <td>'.$receiver->username.'</td>
        <td>'.$settings->currency.''.$row->amount.'</td>
        <td>'.$row->payment_status.'</td>';
          if ($row->ProofPic =="") {
           echo '<td><a href="#"><img src="" alt="No POF Uploaded" style="width: 100px;"></a></td>';
          }
          else{ echo '<td><a href="'.App::url().'/images/'.$row->ProofPic.'" target="_BLANK"><img src="'.App::url().'/images/'.$row->ProofPic.'" alt="No POF Uploaded" style="width: 100px;"></a></td>';}
        
         if ($row->payment_status =="confirm") {
           echo '<td> <a class="btn btn-success btn-block" style="border-radius: 0px !important;">Payment Confirmed<a/></td>';
          }
          else{ echo '<td> <a class="btn btn-danger btn-block" style="border-radius: 0px !important;" data-toggle="modal" data-target="#ActionConfirmed'.$row->id.'">Confirm Payment<a/></td>';}

       echo ' 
      </tr>

<div class="modal fade" id="ActionConfirmed'.$row->id.'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 class="modal-title" id="myModalLabel">Choose Action</h4>
      </div>
      <div class="modal-body">
          <form method="POST" action="">
                            <input type="hidden" name="id" value='.$row->id.'">
                            <input type="submit" name="ConfirmPayment" class="btn btn-success btn-block" style="border-radius: 0px !important;" value="Confirm Payment" /></li>
                          </form>
      </div>
   
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
';
  }
}


function ConfirmActivation($id){
   $ActivationDetails = DB::table('activationFee')->where('id', $id)->first();

  $sql = DB::table('activationFee')
        ->where('id', $id)
        ->update(array('payment_status' => 'confirm'));
        if ($sql) {
      $BanalnceGet = DB::table('bank')->where('userid', $ActivationDetails->sender_id)->first();
      $userActivation = DB::table('activationFee')->where('id', $id)->first();
      $BanalnceUp = $BanalnceGet->balance + $ActivationDetails->amount;



           echo'<div class="alert alert-success" role="alert">
  You have successfully confirm your upline.
</div>';
     $user = DB::table('users')->where('id', $ActivationDetails->sender_id)->first();
     $subject = 'Your payment is confirmed' .Config::get('app.name');
     
      $message = "<h3>Your activation fees payment is confirmed</h1><br><br>
                      <p>Your activation fees  payment has been received and confirmed, you can now choose plan to provide donation to others and to be entitle to receive payment.";
      include 'emails.php';
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

      $headers .= 'From: '.Config::get('app.webmail')."\r\n".
       'Reply-To: '.Config::get('app.webmail')."\r\n" .
        'X-Mailer: PHP/' . phpversion();
        $mail = mail($user->email, $subject, $EmaiMessage, $headers);

        if ($mail) {
          $Querys =DB::table('notification')->insert(
    array('userid' =>        $ActivationDetails->sender_id,
         'type' =>        "confirm",
          'details' =>    "Your activation fees payment is confirmed", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd")
);

         
        }
      }
        else{
            echo'<div class="alert alert-danger" role="alert">
  We cannot process your request now please try again.
</div>';
        }
}




//Set activation fees receiver details and view
function activationReceiver()
{
  $row = DB::table('activationReceiver')->where('id', 1)->first();

  $users = DB::table('users')->where('id', $row->userid)->first();
  $user = DB::table('userdetails')->where('userid', $row->userid)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$users->username.'</td>
        <td>'.$user->accountname.'</td>
        <td>'.$user->accountnumber.'</td>
        <td>'.$user->bankname.'</td> 
        <td>'.$row->deleted.'</td>
        <td><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row->id.'"><button class="btn btn-primary">Edit</button></a></td>
      </tr>

       <div id="collapse'.$row->id.'" class="panel-collapse collapse">
      <div class="panel-body">
      <h1>Activation Receiver user</h1>
      <form role="form" method="POST" action="" class="col-md-4">

  <input type="hidden" name="accountid" value="'.$row->id.'" >

   <div class="form-group">
    <label for="name">Receiver Username</label>
    <input type="text" class="form-control"  name="receivername">
  </div>
 
  <input type="submit" name="SetReceiver" class="btn btn-default" value="Save Settings" />
</form>
        
        
      </div>
    </div>
  </div>';

}


function ConfirmActivationReceiver($id,$username)
{
   $users = DB::table('users')->where('username', $username)->first();

  $sql = DB::table('activationReceiver')
        ->where('id', $id)
        ->update(array('userid' => $users->id));
        if ($sql) {


          $Querys =DB::table('notification')->insert(
    array('userid' =>       $users->id,
         'type' =>        "congrat",
          'details' =>    "You have been set to receive all activation fees! Cheers", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd")
);

           echo'<div class="alert alert-success" role="alert">
  You have successfully set new activation fees receiver
</div>';
     
      }
        else{
            echo'<div class="alert alert-danger" role="alert">
  We cannot process your request now please try again.
</div>';
        }
}



//Admin get All total request margin from users
function GetAllMarginReq()
{
  $users = DB::table('marching')->where('id', '>', 1)->get();
  foreach ($users as $row) {
 $settings = DB::table('settings')->where('id', 1)->first();
    $pack = DB::table('packages')->where('id', $row->package_id)->first();
  $userSender = DB::table('userdetails')->where('userid', $row->sender_id)->first();
  $userReceiver = DB::table('userdetails')->where('userid', $row->receiver_id)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$userSender->accountname.'</td>
        <td>'.$userReceiver->accountname.'</td>
        <td>'.$settings->currency.''.$row->amount.'</td>
        <td>'.$pack->packname.'</td>
        <td>'.$row->payment_status.'</td>
        <td><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row->id.'"><button class="btn btn-primary">Disengage</button></a></td>
      </tr>

       <div id="collapse'.$row->id.'" class="panel-collapse collapse">
      <div class="panel-body">
      <h1>Disengage Margin</h1>
      <h2>Disengage margin of '.$userReceiver->accountname.' set to receive from '.$userSender->accountname.'  amount of '.$settings->currency.' '.$row->amount.' now, please note this margin will be deleted only without returning user balances
      <form role="form" method="POST" action="" class="col-md-4">
  <input type="hidden" name="id" value="'.$row->id.'" >
  <input type="submit" name="DisengageNow" class="btn btn-danger" value="Disengage" />
</form>
        
        
      </div>
    </div>
  </div>';
  }
}

//Admin get All total request margin from users
function GetAllMarginPeningReq()
{
  $users = DB::table('marching')->where('payment_status', 'pending')->where('id', '>', 1)->get();
  foreach ($users as $row) {
 $settings = DB::table('settings')->where('id', 1)->first();
    $pack = DB::table('packages')->where('id', $row->package_id)->first();
  $userSender = DB::table('userdetails')->where('userid', $row->sender_id)->first();
  $userReceiver = DB::table('userdetails')->where('userid', $row->receiver_id)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$userSender->accountname.'</td>
        <td>'.$userReceiver->accountname.'</td>
        <td>'.$settings->currency.''.$row->amount.'</td>
        <td>'.$pack->packname.'</td>
        <td>'.$row->payment_status.'</td>
        <td><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row->id.'"><button class="btn btn-primary">Disengage</button></a></td>
      </tr>

       <div id="collapse'.$row->id.'" class="panel-collapse collapse">
      <div class="panel-body">
      <h1>Disengage Margin</h1>
      <h2>Disengage margin of '.$userReceiver->accountname.' set to receive from '.$userSender->accountname.'  amount of '.$settings->currency.' '.$row->amount.' now, please note this margin will be deleted only without returning user balances
      <form role="form" method="POST" action="" class="col-md-4">
  <input type="hidden" name="id" value="'.$row->id.'" >
  <input type="submit" name="DisengageNow" class="btn btn-danger" value="Disengage" />
</form>
        
        
      </div>
    </div>
  </div>';
  }
}



//Admin get All total request margin from users
function GetAllMarginWaitingReq()
{
  $users = DB::table('marching')->where('payment_status', 'waiting')->where('id', '>', 1)->get();
  foreach ($users as $row) {
 $settings = DB::table('settings')->where('id', 1)->first();
    $pack = DB::table('packages')->where('id', $row->package_id)->first();
  $userSender = DB::table('userdetails')->where('userid', $row->sender_id)->first();
  $userReceiver = DB::table('userdetails')->where('userid', $row->receiver_id)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$userSender->accountname.'</td>
        <td>'.$userReceiver->accountname.'</td>
        <td>'.$settings->currency.''.$row->amount.'</td>
        <td>'.$pack->packname.'</td>
        <td>'.$row->payment_status.'</td>
        <td><a href="'.App::url().'/images/'.$row->ProofPic.'" target="_BLANK"><img src="'.App::url().'/images/'.$row->ProofPic.'" alt="No POF Uploaded" style="width: 100px;"></a></td>
        <td><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row->id.'"><button class="btn btn-primary">Disengage</button></a></td>
      </tr>

       <div id="collapse'.$row->id.'" class="panel-collapse collapse">
      <div class="panel-body">
      <h1>Disengage Margin</h1>
      <h2>Disengage margin of '.$userReceiver->accountname.' set to receive from '.$userSender->accountname.'  amount of '.$settings->currency.' '.$row->amount.' now, please note this margin will be deleted only without returning user balances
      <form role="form" method="POST" action="" class="col-md-4">
  <input type="hidden" name="id" value="'.$row->id.'" >
  <input type="submit" name="DisengageNow" class="btn btn-danger" value="Disengage" />
</form>
        
        
      </div>
    </div>
  </div>';
  }
}




//Admin get All total request margin from users
function GetAllMarginConfirmReq()
{
  $users = DB::table('marching')->where('payment_status', 'confirm')->where('id', '>', 1)->get();
  foreach ($users as $row) {
 $settings = DB::table('settings')->where('id', 1)->first();
    $pack = DB::table('packages')->where('id', $row->package_id)->first();
  $userSender = DB::table('userdetails')->where('userid', $row->sender_id)->first();
  $userReceiver = DB::table('userdetails')->where('userid', $row->receiver_id)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$userSender->accountname.'</td>
        <td>'.$userReceiver->accountname.'</td>
        <td>'.$settings->currency.''.$row->amount.'</td>
        <td>'.$pack->packname.'</td>
        <td>'.$row->payment_status.'</td>
        <td><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row->id.'"><button class="btn btn-primary">Disengage</button></a></td>
      </tr>

       <div id="collapse'.$row->id.'" class="panel-collapse collapse">
      <div class="panel-body">
      <h1>Disengage Margin</h1>
      <h2>Disengage margin of '.$userReceiver->accountname.' set to receive from '.$userSender->accountname.'  amount of '.$settings->currency.' '.$row->amount.' now, please note this margin will be deleted only without returning user balances
      <form role="form" method="POST" action="" class="col-md-4">
  <input type="hidden" name="id" value="'.$row->id.'" >
  <input type="submit" name="DisengageNow" class="btn btn-danger" value="Disengage" />
</form>
        
        
      </div>
    </div>
  </div>';
  }
}



//Admin get All total request margin from users
function GetAllMarginBlockedReq()
{
  $users = DB::table('marching')->where('payment_status', 'confirm')->where('id', '>', 1)->get();
  foreach ($users as $row) {
 $settings = DB::table('settings')->where('id', 1)->first();
    $pack = DB::table('packages')->where('id', $row->package_id)->first();
  $userSender = DB::table('userdetails')->where('userid', $row->sender_id)->first();
  $userReceiver = DB::table('userdetails')->where('userid', $row->receiver_id)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$userSender->accountname.'</td>
        <td>'.$userReceiver->accountname.'</td>
        <td>'.$settings->currency.''.$row->amount.'</td>
        <td>'.$pack->packname.'</td>
        <td>'.$row->payment_status.'</td>
        <td><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row->id.'"><button class="btn btn-primary">Disengage</button></a></td>
      </tr>

       <div id="collapse'.$row->id.'" class="panel-collapse collapse">
      <div class="panel-body">
      <h1>Disengage Margin</h1>
      <h2>Disengage margin of '.$userReceiver->accountname.' set to receive from '.$userSender->accountname.'  amount of '.$settings->currency.' '.$row->amount.' now, please note this margin will be deleted only without returning user balances
      <form role="form" method="POST" action="" class="col-md-4">
  <input type="hidden" name="id" value="'.$row->id.'" >
  <input type="submit" name="DisengageNow" class="btn btn-danger" value="Disengage" />
</form>
        
        
      </div>
    </div>
  </div>';
  }
}



function DisengageMargingNow($id)
{
 $sql = DB::table('marching')->where('id', $id)->delete();

 if ($sql) {

  echo'<div class="alert alert-success" role="alert">
  You have successfully Disengage margin
</div>';
     
      }
        else{
   echo'<div class="alert alert-danger" role="alert">
  We cannot process your request now please try again.
</div>';
}
}




//Admin get All total request margin from users
function SubscriberView()
{
  $users = DB::table('subscriber')->get();
  foreach ($users as $row) {
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$row->email.'</td>
        <td>'.$row->date.'</td>
       <td> <form method="POST" action="">
           <input type="hidden" name="id" value="'.$row->id.'">
           <input type="submit" name="delete" value="Delete" class="btn btn-danger btn-sm ">
           </form></td>
        ';
      
        echo '
        <td><a data-toggle="collapse" data-parent="#accordion" href="#collapse'.$row->id.'"><button class="btn btn-primary">Send Mass Mail</button></a></td>
      </tr>
       <div id="collapse'.$row->id.'" class="panel-collapse collapse">
      <div class="panel-body">
      <h1>Send Mail to '.$row->email.'</h1>
      <h2>Send Mass Mail to all your subscribers</h2>

         <div class="panel-body"> 
         <form role="form" method="POST" action="">
          <fieldset>
           <div class="form-group">
    <input type="text" name="usermail" class="form-control" value="'.$row->email.'">
     </div>
           <div class="form-group">
    <input type="text" name="subject" class="form-control" placeholder="Your subject here">
     </div>
       <div class="form-group">
    <textarea class="form-control"  name="message" rows="3" placeholder="Write in your wall" autofocus=""></textarea>
     </div>
     <input  type="submit" class="[ btn btn-success ]" name="SingleMail" value="Send Mail To User">
     </fieldset>
     </form>
     </div>

      </div>
    </div>
  </div>'; 
  }
}


function MassMailNow($messagesss,$subjectss)
{
 $users = DB::table('subscriber')->where('deleted', 0)->get();
 foreach ($users as $row) {

  $email = $row->email;

 $subject = $subjectss.'From' .Config::get('app.name');
     
      $message = $messagesss;
      include 'emails.php';
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

      $headers .= 'From: '.Config::get('app.webmail')."\r\n".
       'Reply-To: '.Config::get('app.webmail')."\r\n" .
        'X-Mailer: PHP/' . phpversion();
        $mail = mail($email, $subject, $EmaiMessage, $headers);

        if ($mail) {
  echo'<div class="alert alert-success" role="alert">
  You have successfully Send mass mail to all your subscribers
</div>';
     
      }
        else{
   echo'<div class="alert alert-danger" role="alert">
  We cannot process your request now please try again.
</div>';
 }
 }
}


function SingleMailNow($messages,$subjectss,$email)
{

 $users = DB::table('subscriber')->where('email', $email)->first();

 $subject = $subjectss.'From' .Config::get('app.name');
     
      $message = $messages;
      include 'emails.php';
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

      $headers .= 'From: '.Config::get('app.webmail')."\r\n".
       'Reply-To: '.Config::get('app.webmail')."\r\n" .
        'X-Mailer: PHP/' . phpversion();
        $mail = mail($email, $subject, $EmaiMessage, $headers);

        if ($mail) {
  echo'<div class="alert alert-success" role="alert">
  You have successfully Send mail to your subscribers '.$email.'
</div>';
     
      }
        else{
   echo'<div class="alert alert-danger" role="alert">
  We cannot process your request now please try again.
</div>';
 }
}





function CourtCaseShow(){
   $settings = DB::table('settings')->where('id', 1)->first();
  $cases = DB::table('courtcase')->where('status', '!=', 0)->get();
  foreach ($cases as $row) {
  $users = DB::table('userdetails')->where('userid', $row->userid)->first();
  $user = DB::table('userdetails')->where('userid', $row->accused)->first();
  $UserAccused = DB::table('users')->where('id', $row->accused)->first();
  $UserPlain = DB::table('users')->where('id', $row->userid)->first();
  $margin = DB::table('marching')->where('id', $row->margin_id)->first();

  if ($row->status ==2) {
              $status = '<b style="color:yellow;">(OPEN)</b>';
            }elseif ($row->status ==1) {
              $status = '<b style="color:Green;">(Investigating)</b>';
            
              }elseif ($row->status ==0) {
              $status = '<b style="color:red;">(resolved)</b>';
            }else
            {
              $status = '<b style="color:yellow;">(OPEN)</b>';
            }
   echo '<div class="col-md-6">
<div class="list-group">
  <a href="#" class="list-group-item">Plaintiff <span class="badge">'.$users->accountname.'</span></a>
  <a href="#" class="list-group-item">Plaintiff username<span class="badge">'.$UserPlain->username.'</span></a>
  <a href="#" class="list-group-item">Plaintiff Phone<span class="badge">'.$users->phonenumber.'</span></a>
  <a href="#" class="list-group-item">Accused<span class="badge">'.$user->accountname.'</span></a>
  <a href="#" class="list-group-item">Accused Phone<span class="badge">'.$user->phonenumber.'</span></a>
  <a href="#" class="list-group-item">Accused username<span class="badge">'.$UserAccused->username.'</span></a>
  <a href="#" class="list-group-item">Amount<span class="badge">'.$settings->currency.' '.$margin->amount.'</span></a>
  <a href="#" class="list-group-item">Margin ID<span class="badge">'.$row->margin_id.'</span></a>
   <a href="#" class="list-group-item">case Type <span class="badge">'.$row->type.'</span></a>
   <a href="#" class="list-group-item">'.$row->details.'</a>
   <a href="#" class="list-group-item">Status <span class="badge">'.$status.'</span></a>
   <form method="POST" action="">
   <input type="hidden" name="id" value="'.$row->id.'">
   <select name="status" class="form-control">
              <option value="1">We are Investigating</option>
              <option value="0">Case Resolved</option>
        </select>
   <input class="list-group-item btn btn-danger" type="submit" btn-block btn-lg" name="submit" style="background-color: black;color: #fff;font-size: 20px;width: 100%;" value="Action">
   </form>
</div>
</div>';
  }
}



function ResolvedCourtCaseShow(){
   $settings = DB::table('settings')->where('id', 1)->first();
  $cases = DB::table('courtcase')->where('status', 0)->get();
  foreach ($cases as $row) {
  $users = DB::table('userdetails')->where('userid', $row->userid)->first();
  $user = DB::table('userdetails')->where('userid', $row->accused)->first();
  $UserAccused = DB::table('users')->where('id', $row->accused)->first();
  $UserPlain = DB::table('users')->where('id', $row->userid)->first();
  $margin = DB::table('marching')->where('id', $row->margin_id)->first();

  if ($row->status ==2) {
              $status = '<b style="color:yellow;">(OPEN)</b>';
            }elseif ($row->status ==1) {
              $status = '<b style="color:Green;">(Investigating)</b>';
            
              }elseif ($row->status ==0) {
              $status = '<b style="color:red;">(resolved)</b>';
            }else
            {
              $status = '<b style="color:yellow;">(OPEN)</b>';
            }
   echo '<div class="col-md-6">
<div class="list-group">
  <a href="#" class="list-group-item">Plaintiff <span class="badge">'.$users->accountname.'</span></a>
  <a href="#" class="list-group-item">Plaintiff username<span class="badge">'.$UserPlain->username.'</span></a>
  <a href="#" class="list-group-item">Plaintiff Phone<span class="badge">'.$users->phonenumber.'</span></a>
  <a href="#" class="list-group-item">Accused<span class="badge">'.$user->accountname.'</span></a>
  <a href="#" class="list-group-item">Accused Phone<span class="badge">'.$user->phonenumber.'</span></a>
  <a href="#" class="list-group-item">Accused username<span class="badge">'.$UserAccused->username.'</span></a>
  <a href="#" class="list-group-item">Amount<span class="badge">'.$settings->currency.' '.$margin->amount.'</span></a>
  <a href="#" class="list-group-item">Margin ID<span class="badge">'.$row->margin_id.'</span></a>
   <a href="#" class="list-group-item">case Type <span class="badge">'.$row->type.'</span></a>
   <a href="#" class="list-group-item">Status <span class="badge">'.$status.'</span></a>
</div>
</div>';
  }
}




function ChangeCaseStatus($id,$status){
   $Cases = DB::table('courtcase')->where('id', $id)->first();
   if ($status ==1) {
    $NewStats = "Investigating";
   }else if ($status ==0) {
    $NewStats = "Case Resolved";
   }
  $sql = DB::table('courtcase')
        ->where('id', $id)
        ->update(array('status' => $status));
  if ($sql) {
        DB::table('notification')->insert(
    array('userid' =>        $Cases->userid,
         'type' =>        "My Court case",
          'details' =>    "Your court case with id ".$id." status has been changed to ".$NewStats, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-gavel")
      );
    
     DB::table('notification')->insert(
    array('userid' =>        $Cases->accused,
         'type' =>        "My Court case",
          'details' =>    "Your court case with id ".$id." status has been changed to ".$NewStats, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-gavel")
);

  echo'<div class="alert alert-success" role="alert">
  You have successfully chase case status
</div>';
     
      }
        else{
   echo'<div class="alert alert-danger" role="alert">
  We cannot process your request now please try again.
</div>';
  }
}



function MembersTestimony()
{

  $testimony = DB::table('testimony')->where('status', 0)->get();
  foreach ($testimony as $row) {
  $users = DB::table('users')->where('id', $row->userid)->first();
  $user = DB::table('userdetails')->where('userid', $row->userid)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$user->accountname.'</td>
        <td>'.$row->Title.'</td>
        <td>'.$row->content.'</td>
        <td>'.$row->date.'</td>
        <td>'.$row->status.'</td>
        <td><a data-toggle="collapse" data-parent="#accordion" href="#deleted'.$row->id.'"><button  class="btn btn-danger">Delete</button></td>
        <td><form method="POST" action="">
        <input type="hidden" name="test_id" value='.$row->id.'">
           <input type="submit" name="confirmTestimony" class="btn btn-success" style="border-radius: 0px !important;"  value="Approve">
           </form></td>
      </tr>


<div class="modal fade" id="deleted'.$row->id.'" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <a href="admin.php?page=testimony" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</a>
        <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
      </div>
      <div class="modal-body">
      <h3>Are you sure you want to delete this testimony? </h3>
       <form method="POST" action="">
                            <input type="hidden" name="test_id" value='.$row->id.'">
                            <input type="submit" name="Deletesubmit" class="btn btn-danger btn-block" style="border-radius: 0px !important;" value="Yes Delete" /></li>
                          </form>
      </div>
      <div class="modal-footer">
        <a href="admin.php?page=testimony" type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
      </div>
    </div>
  </div>
</div>
';
  }
}

function MembersTestimonies()
{

  $testimony = DB::table('testimony')->where('status', 1)->get();
  foreach ($testimony as $row) {
  $users = DB::table('users')->where('id', $row->userid)->first();
  $user = DB::table('userdetails')->where('userid', $row->userid)->first();
  $likes = DB::table('testimoneytvotes')
        ->where('comment_id', '=', $row->id)
        ->orWhere(function($query) {
            $query->where('type', 'like');})->count();
  $dislike = DB::table('testimoneytvotes')
        ->where('comment_id', '=', $row->id)
        ->orWhere(function($query) {
            $query->where('type', 'dislike');})->count();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$user->accountname.'</td>
        <td>'.$row->Title.'</td>
        <td>'.$row->content.'</td>
        <td>'.$row->date.'</td>
        <td>'.$likes.'</td>
        <td>'.$dislike.'</td>
        <td>'.$row->status.'</td>
        <td><a data-toggle="collapse" data-parent="#accordion" href="#deleted'.$row->id.'"><button  class="btn btn-danger">Delete</button></td>
        <td><form method="POST" action="">
        <input type="hidden" name="test_id" value='.$row->id.'">
           <input type="submit" name="UnAprroveTestimony" class="btn btn-success" style="border-radius: 0px !important;"  value="Un-Approve">
           </form></td>
      </tr>


<div class="modal fade" id="deleted'.$row->id.'" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <a href="admin.php?page=testimony" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</a>
        <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
      </div>
      <div class="modal-body">
      <h3>Are you sure you want to delete this testimony? </h3>
       <form method="POST" action="">
                            <input type="hidden" name="test_id" value='.$row->id.'">
                            <input type="submit" name="Deletesubmit" class="btn btn-danger btn-block" style="border-radius: 0px !important;" value="Yes Delete" /></li>
                          </form>
      </div>
      <div class="modal-footer">
        <a href="admin.php?page=testimony" type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
      </div>
    </div>
  </div>
</div>
';
  }
}



function MembersTestimonyAll()
{

  $testimony = DB::table('testimony')->get();
  foreach ($testimony as $row) {
  $users = DB::table('users')->where('id', $row->userid)->first();
  $user = DB::table('userdetails')->where('userid', $row->userid)->first();
   $likes = DB::table('testimoneytvotes')
        ->where('comment_id', '=', $row->id)
        ->orWhere(function($query) {
            $query->where('type', 'like');})->count();
  $dislike = DB::table('testimoneytvotes')
        ->where('comment_id', '=', $row->id)
        ->orWhere(function($query) {
            $query->where('type', 'dislike');})->count();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$user->accountname.'</td>
        <td>'.$row->Title.'</td>
        <td>'.$row->content.'</td>
        <td>'.$row->date.'</td>
         <td>'.$likes.'</td>
        <td>'.$dislike.'</td>
        <td>'.$row->status.'</td>
        <td><a data-toggle="collapse" data-parent="#accordion" href="#deleted'.$row->id.'"><button  class="btn btn-danger">Delete</button></td>
       
      </tr>


<div class="modal fade" id="deleted'.$row->id.'" tabindex="1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <a href="admin.php?page=testimony" type="button" class="close" data-dismiss="modal" aria-hidden="true">×</a>
        <h4 class="modal-title" id="myModalLabel">Confirm Delete</h4>
      </div>
      <div class="modal-body">
      <h3>Are you sure you want to delete this testimony? </h3>
       <form method="POST" action="">
                            <input type="hidden" name="test_id" value='.$row->id.'">
                            <input type="submit" name="Deletesubmit" class="btn btn-danger btn-block" style="border-radius: 0px !important;" value="Yes Delete" /></li>
                          </form>
      </div>
      <div class="modal-footer">
        <a href="admin.php?page=testimony" type="button" class="btn btn-default" data-dismiss="modal">Cancel</a>
      </div>
    </div>
  </div>
</div>
';
  }
}




//Set activation fees receiver details and view
function AccountDetailsView()
{
  $details = DB::table('userdetails')->get();
  foreach ($details as $row) {
     $users = DB::table('users')->where('id', $row->userid)->first();
    echo '<tr>
        <th><input type="checkbox" class="select-all" value="1"></th>
        <td>'.$users->username.'</td>
        <td>'.$row->firstname.'</td>
        <td>'.$row->lastname.'</td>
        <td>'.$row->phonenumber.'</td> 
        <td>'.$row->bankname.'</td>
         <td>'.$row->accounttype.'</td>
          <td>'.$row->accountname.'</td>
           <td>'.$row->accountnumber.'</td>
            <td>'.$row->country.'</td>
       <td>'.$row->state.'</td>
      <td>'.$row->bitcoinwallet.'</td>
      <td>'.$row->refid.'</td>
      <td><a href="'.$row->avater.'" target="_BLANK"><img src="'.$row->avater.'" alt="No Avatar" style="width: 100px;"></a></td>
      </tr>

      ';

  }
   
}



function  AddBanlanceGetHelp($username,$balance){
  $users = DB::table('users')->where('username', $username)->first();
  $Count = DB::table('requestHelp')->where('userid', $users->id)->orderBy('id', 'DESC')->first();
  $balanceS = $Count->balance + $balance;
 $sql=  DB::table('requestHelp')
        ->where('id', $Count->id)
        ->update(array('balance' => $balanceS,
                       'status' => 'pending'));
  if ($sql) {
    echo'<div class="alert alert-success" role="alert">
  You have successfully add user balance of '.$balance.' to user '.$username.'
</div>';
  }
}



function  AddBanlance($username,$balance){
  $users = DB::table('users')->where('username', $username)->first();
  $Count = DB::table('bank')->where('userid', $users->id)->orderBy('id', 'DESC')->first();
  $balanceS = $Count->balance + $balance;
  $sql = DB::table('bank')
        ->where('id', $Count->id)
        ->update(array('balance' => $balanceS));
  if ($sql) {
    echo'<div class="alert alert-success" role="alert">
  You have successfully add user balance of '.$balance.' to user '.$username.'
</div>';
  }
}

  function TestimonyPeningCount(){
    $Count = DB::table('testimony')->where('status', 0)->count();
    echo $Count;
  }
   function TestimonyApprovedCount(){
    $Count = DB::table('testimony')->where('status', 1)->count();
    echo $Count;
  }
   function TestimonyAllCount(){
    $Count = DB::table('testimony')->count();
    echo $Count;
  }
   function getHelpCountCount(){
    $Count = DB::table('requestHelp')->where('status', 'pending')->count();
    echo $Count;
  }
   function ProvideHelpCount(){
    $Count = DB::table('requestMaching')->where('status', 'pending')->count();
    echo $Count;
  }
   function PendingMarginCount(){ 
    $Count = DB::table('marching')->where('payment_status', 'pending')->count();
    echo $Count;
  }
  function AllPackagesCount(){
    $Count = DB::table('packages')->count();
    echo $Count;
  }



   function PendingActivationCountCount(){
    $Count = DB::table('activationFee')->where('payment_status', 'pending')->count();
    echo $Count;
  }



   function ApprovedActivationCountCount(){
    $Count = DB::table('activationFee')->where('payment_status', 'confirm')->count();
    echo $Count;
  }


   function WaitingActivationCountCount(){
    $Count = DB::table('activationFee')->where('payment_status', 'waiting')->count();
    echo $Count;
  }

   function AllActivationCountCount(){
    $Count = DB::table('activationFee')->count();
    echo $Count;
  }



 function AllMarginCountCount(){
    $Count = DB::table('marching')->count();
    echo $Count;
  }

    function WaitingmarchinCountCount(){
    $Count = DB::table('marching')->where('payment_status', 'waiting')->count();
    echo $Count;
  }

   function pendingmarchinCountCount(){
    $Count = DB::table('marching')->where('payment_status', 'pending')->count();
    echo $Count;
  }


   function ConfirmmarchinCountCount(){
    $Count = DB::table('marching')->where('payment_status', 'confirm')->count();
    echo $Count;
  }


?>