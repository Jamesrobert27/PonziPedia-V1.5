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


ini_set('date.timezone', 'Africa/Lagos');
 $settings = DB::table('settings')->where('id', 1)->first();


//Website live or mentainace mode or comming soon.

/*if ($settings->site_status == 3) {
 redirect_to(App::url('coming-soon/index.php'));
}
*/
if ($settings->site_status ==0) {
  redirect_to(App::url('account/mentainace-mode.php'));
}


//Automatic margin 
if ($settings->margintype ==1) {
   
   marginAPI();
   SecondmarginAPI();
 
}
RequestHelpBalanceZero();

 //DB::table('userdetails')
   //     ->where('refid', '!=', "")
     //   ->update(array('refid' => ""));


//Generate unique referral id for each registered members automatically
function SetRefID()
{
 
// function to generate random ID number
function createID() {
    $id = mt_rand(10000000, 99999999);
    return $id;
}



// generates random ID number
$id = createID();

$EmptyRef = DB::table('userdetails')->where('refid', "")->get();

 foreach ($EmptyRef as $row) {

  $EmptyGet = DB::table('userdetails')->where('refid', $id)->first();
  while($EmptyGet) {
    // generates random ID number
    $id = createID();
    $EmptyGet = DB::table('userdetails')->where('refid', $id)->first();
} 
 
   DB::table('userdetails')
        ->where('id', $row->id)
        ->update(array('refid' => $id));
 }
}

//Run the unique referral id Generator
SetRefID();

$user_id = Auth::user()->id; 
$user = DB::table('users')->where('id', $user_id)->first();
if ($user->status =='2')
  {
  redirect_to(App::url('account/blocked.php'));
  }

function CheckAvaverPic($user_id)
{
  $CheckAvater = DB::table('userdetails')->where('userid', $user_id)->first();
  if ($CheckAvater) {
  if ($CheckAvater->avater =="") {
   if (!empty(Auth::user()->avatar)){
    DB::table('userdetails')
        ->where('userid', $user_id)
        ->update(array('avater' => Auth::user()->avatar));
}
}
}
  }
CheckAvaverPic($user_id);


function CheckAvaverPicUpdate($user_id)
{
  $CheckAvater = DB::table('userdetails')->where('userid', $user_id)->first();
  if ($CheckAvater) {
  if ($CheckAvater->avater != Auth::user()->avatar) {
   if (!empty(Auth::user()->avatar)){
    DB::table('userdetails')
        ->where('userid', $user_id)
        ->update(array('avater' => Auth::user()->avatar));
}
}
}
}
CheckAvaverPicUpdate($user_id);


//Guide Total User under same tree 
  //Pending Request Balance
  function GuiderTotalUser($user_id)
  {
   $guideCount = DB::table('referral')->where('parent', $user_id)->count();
    if ($guideCount) {
      echo $guideCount;
    }
    else{
      echo '0';
    }
    
  }
 //Guider Total amount earned 
 function BalanceEarnedGuiders($user_id){
  $settings = DB::table('settings')->where('id', 1)->first();
  $id = DB::table('referral')->where('parent', $user_id)->where('status', 0)->sum('amount');
   $percentagP= $settings->guiderProfit;
        $totalP = $id;
       $new_amountP = ($percentagP / 100) * $totalP;
  echo $new_amountP;
}



//Pending Request Balance
  function PendingRequestBalance($user_id)
  {
    $id = DB::table('requestMaching')->where('userid', $user_id)->where('status', 'pending')->sum('balance');
    if ($id) {
      echo $id;
    }
    else{
      echo '0';
    }
    
  }

  //Pending GetHelp Balance
  function requestHelpBalance($user_id)
  {
    $id = DB::table('requestHelp')->where('userid', $user_id)->where('status', 'pending')->sum('balance');
    if ($id) {
      echo $id;
    }
    else{
      echo '0';
    }
    
  }


   //Member Profit Balance
  function ProfitBalance($user_id)
  {
    $id = DB::table('requestMaching')->where('userid', $user_id)->where('status', 'pending')->sum('profit');
    if ($id) {
      echo $id;
    }
    else{
      echo '0';
    }
    
  }
function random($length = 16) {
	if (function_exists('openssl_random_pseudo_bytes')) {
		$bytes = openssl_random_pseudo_bytes($length * 2);
		if ($bytes === false) exit('Unable to generate random string.');
		return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
	}
	$pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
}
$RandomNumbers = random(52);



//User bank details fetching 
$user_bank = DB::table('bank')->where('userid', $user_id)->first();

//User Transaction fetching 


function transactionView($user_id)
{    
  $settings = DB::table('settings')->where('id', 1)->first();
	$user_transaction = DB::table('transactions')->where('userid', $user_id)->take(20)->orderBy('id', 'DESC')->get();
	if ($user_transaction >0) {
		foreach ($user_transaction as $row) {
			 echo '<tr>
                              <th scope="row">'.$row->id.'</th>
                              <td>'.date("Y-m-d",strtotime($row->dateNow)).'</td>
                              <td>'.$settings->currency.' '.$row->amount.'</td>
                              <td>'.$row->type.'</td>
                              <td>'.$row->description.'</td>
                              <td>'.$row->status.'</td>
                            </tr>';
		}
	}
		else {
			echo "<h1>You dont have any transaction recorded";
		}

}


//User Notification on the activities page
function notificationView($user_id)
{    
	$user_notification = DB::table('notification')->where('userid', $user_id)->where('status', '=', 'verify')->take(10)->orderBy('id', 'DESC')->get();
	if ($user_notification >0) {
		foreach ($user_notification as $row) {
			 echo '<li><a rel="nofollow" href="activity.php?read='.$row->id.'" class="dropdown-item"> 
                        <div class="notification">
                          <div class="notification-content"><i class="fa '.$row->faIcon.' bg-green"></i>'.substr($row->details, 0, 50).' </div>
                          <div class="notification-time"><small>'.date("Y-m-d",strtotime($row->date)).'</small></div>
                        </div></a></li>
               ';
		}
	}
		else {
			echo "<p>You dont have any notification</p>";
		}

}
//Notification count
function notificationCount($user_id)
{    
	$user_notification = DB::table('notification')->where('userid', $user_id)->where('status', '=', 'verify')->count();
	echo  $user_notification;
}


//User Notification page content view
function TimelineView($user_id)
{    
	$user_notification = DB::table('notification')->where('userid', $user_id)->take(20)->orderBy('id', 'DESC')->get();
	if ($user_notification >0) {
		foreach ($user_notification as $row) {
			
			 echo '<li>
                <i class="fa '.$row->faIcon.' bg-orange" style="color: #fff;"></i>

                <div class="timeline-item">
                  <span class="time"><i class="fa fa-clock-o" style="color: #f40000;"></i> '.date("Y-m-d H:i:s",strtotime($row->date)).'</span>

                  <h3 class="timeline-header">'.$row->details.'</h3>

                                  </div>
              </li>';
		}
	}
		else {
			echo "<h1>You dont have any notification</h1>";
		}

}
//Notification View and mark as read
function Notificationread($user_id, $not_id)
{
	$update =DB::table('notification')->where('userid', $user_id)->where('id', '=', $not_id)->update(array('status' => 'read'));
}

function packagesView()
{
$settings = DB::table('settings')->where('id', 1)->first();
	$packages =DB::table('packages')->orderBy('id', 'DESC')->get();

	foreach ($packages as $row) {
    $settings = DB::table('settings')->where('id', 1)->first();
		echo '  <div class="col-sm-4 col-md-4 text-center">
                    <div class="plan-cell">
                        <ul class="list-group green">
                            <li class="list-group-item plan-name mayPackages">'.$row->packname.'</li>
                            <li class="list-group-item plan-price catamaran">'.$settings->currency.''.$row->price.'</li>
                            <li class="list-group-item catamaran"><i class="fa fa-circle-o" style="font-size: 25px;"></i> Auto Matching</li>
                            <li class="list-group-item catamaran"><i class="fa fa-arrow-up" style="font-size: 25px;"></i> '.$settings->profit.'% Return on Investment </li>
                            <li class="list-group-item catamaran"><i class="ion-arrow-up-c"></i> '.$settings->currency.' '.$row->profit.' Return on Investment</li>';

                             if ($row->codes !="") {
                               echo ' <li class="list-group-item catamaran"><i class="fa fa-lock" style="font-size: 25px;color:#f40000;"></i> <abbr title="You will need activation code to unlock this package">Locked</abbr> </li>
                            <li class="list-group-item catamaran" style="padding:0">
                            <form method="POST" action="">
                            <input type="hidden" name="pack_id" value='.$row->id.'">
                            <input type="text" class="form-control" style="height: 50px;font-size: 20px;border-color: #218838;color: #218838;border-top-width: 4.111px; border-bottom-width: 4.111px;border-left-width: 4.111;border-right-width: 4.111;" name="pack_idCode" placeholder="Unclock Code">
                            <input type="submit" name="submitLocked" class="btn btn-success btn-block btn-lg" style="border-radius: 0px !important;" value="Subscribe" /></li>
                          </form>';
                             }
                             else{
                              echo '  <li class="list-group-item catamaran" style="padding:0">
                            <form method="POST" action="">
                            <input type="hidden" name="pack_id" value='.$row->id.'">
                            <input type="submit" name="submit" class="btn btn-success btn-block btn-lg" style="border-radius: 0px !important;" value="Subscribe" /></li>
                          </form>';
                             }
                           
                        echo '</ul>
                    </div>
                </div>';
	}

}


//Margin Request functions
function Requestmargin($user_id) 
{
$settings = DB::table('settings')->where('id', 1)->first();
	$user_request = DB::table('requestMaching')->where('userid', $user_id)->take(10)->orderBy('id', 'DESC')->get();
	foreach ($user_request as $row) {
      $user_row = DB::table('userdetails')->where('userid', $user_id)->first();
     


      $timeCreated = $row->timeReq;
      $timeNow = date('Y-m-d H:i:s');
		echo ' <div class="col-lg-12">
                  <div class="work-amount card"><i class="fa fa-bolt" style="color: green; font-size: 35px;margin-top: -10px;"></i>
                    <div class="card-close">
                      <div class="dropdown">
                        <button type="button" id="closeCard1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                        <div aria-labelledby="closeCard1" class="dropdown-menu dropdown-menu-right has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a><a href="#" class="dropdown-item edit"> <i class="fa fa-gear"></i>Edit</a></div>
                      </div>
                    </div>
                    <div class="card-body">
                      <h3>Payout Request</h3>
                     <ul style="padding-left: 10px;">
                       <li>Full Name: '.$user_row->firstname.' '.$user_row->lastname.'</li>
                       <li>Account Number: '.$user_row->accountnumber.'</li>
                       <li>Package Name: '.$row->pack_name.'</li>
                       <li>Amount: '.$settings->currency.' '.$row->amount.'</li>
                       <li>Balance: '.$settings->currency.' '.$row->balance.'</li>
                       <li>Date Created: '.$row->date.'</li>
                     </ul>';
                    ?>
                    <?php if ($row->status == "active") {
                     echo '<center style="margin-left: -20px;margin-right: -20px;margin-bottom: -20px;border-radius: 0px !important;"><button class="btn btn-success btn-lg btn-block" style="border-radius: 0px !important;">Request Approved</button></center>';
                     }
                     elseif ($row->status =="pending") {
                     	echo '<center style="margin-left: -20px;margin-right: -20px;margin-bottom: -20px;border-radius: 0px !important;"><button class="btn btn-danger btn-lg btn-block" style="border-radius: 0px !important;"><span id="ProvideHelp'.$row->id.'"></span></button></center>';
                     }
 if($row->status =="pending"){
    $date=date_create($row->timeReq);
      $timenow = date_format($date,"Y/m/d H:i:s");?>
  <script>
  jQuery(document).ready(function(){
    jQuery("#ProvideHelp<?php echo $row->id; ?>").jCountdown({
      timeText: "<?php echo $timenow; ?>",
      timeZone:8,
      style:"Crystal",
      color:"black", 
      width:250,
      textGroupSpace:5,
      textSpace:0,
      reflection:false,
      reflectionOpacity:10,
      reflectionBlur:0,
      dayTextNumber:2,
      displayDay:true,
      displayHour:true,
      displayMinute:true,
      displaySecond:true,
      displayLabel:false,
      onFinish:function(){
        document.getElementById("ProvideHelp<?php echo $row->id; ?>").innerHTML = "Waiting Margin";
      } 
    });
  });
</script>
<?php } echo '</div>
                  </div>
                </div>';
	}
	
}




//Mergin View for each member
function GetMargin($user_id)
{
  $settings = DB::table('settings')->where('id', 1)->first();
	$user_request = DB::table('marching')->where('sender_id', $user_id)->take(10)->orderBy('id', 'DESC')->get();
	if ($user_request >0) {
		foreach ($user_request as $row) {
			$receiver = $row->receiver_id;
			$packageID = $row->package_id;
      $timeCreated = $row->expiringTime;

     


      $timeNow = date('Y-m-d H:i:s');
      $userTimer = DB::table('marching')->where('id', $row->id)->first();
      if ($timeCreated < $timeNow and $row->expiringTime != "NULL") {
         $BlockU = DB::table('users')->where('id', $user_id)->update(array('status' => '2'));
        if ($BlockU) {
          echo '<meta http-equiv="refresh" content="0">';
        }
      }else{
     
	        $user_sender = DB::table('userdetails')->where('userid', $user_id)->first();
	        $user_receiver = DB::table('userdetails')->where('userid', $receiver)->first();
	        $user_package = DB::table('packages')->where('id', $packageID)->first();
	        $user = DB::table('users')->where('id', $receiver)->first();
	
	echo '<div class="col-md-12"> 
                    <div class="work-amount card"><i class="fa fa-fire" style="color: red; font-size: 35px;margin-top: -10px;"></i>
                    <div class="card-close">
                      <div class="dropdown">
                        <button type="button" id="closeCard1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                        <div aria-labelledby="closeCard1" class="dropdown-menu dropdown-menu-right has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a></div>
                      </div>
                    </div>
                    <div class="card-body">
                      <h3>Approved Payouts</h3>';
      if ($row->payment_status == "pending") {
          $date=date_create($row->expiringTime);
      $timenowss = date_format($date,"Y/m/d H:i:s");
      	echo '  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;">You are to make payment to the receiver below<br>Time left to complete payment<br>
                     <p class="timerSet" id="PayoutPayment'.$row->id.'" style="font-size: 25px;"></p></div>';}
    elseif ($row->payment_status == "waiting") {
    	echo '  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;">Your payment is waiting for Approval<br>Please wait or call your downline for confirmation</div>';
    }
    elseif ($row->payment_status == "confirm") {
    	echo '  <div class="card-body text-center" style="background-color: #218838; color: #fff;">Your payment is fully confirmed '.$settings->currency.''.$user_package->price.' confirmed paid to '.$user_receiver->accountname.'</div></div></div></div>';
    }else{
    	echo '  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;">Your request is not understood<br>Please try reload the page or contact for help</div>';
    }
     
     if ($row->payment_status == "pending" || $row->payment_status == "waiting") {
     
     
     if ($row->payment_status == "pending") { ?>
     <script>
  jQuery(document).ready(function(){
    jQuery("#PayoutPayment<?php echo $row->id; ?>").jCountdown({
      timeText: "<?php echo $timenowss; ?>",
      timeZone:8,
      style:"Crystal",
      color:"black", 
      width:230,
      textGroupSpace:15,
      textSpace:0,
      reflection:false,
      reflectionOpacity:10,
      reflectionBlur:0,
      dayTextNumber:2,
      displayDay:true,
      displayHour:true,
      displayMinute:true,
      displaySecond:true,
      displayLabel:false,
      onFinish:function(){
         document.getElementById("PayoutPayment<?php echo $row->id; ?>").innerHTML = "Blocked Soon";
      }
    });
  });
</script>
      <?php }
                    
                       
                 echo   '<div class="card-body">
                  <ul class="list-group list-group-unbordered">
                  <li class="list-group-item">
  <i class="fa fa-usd"></i> <a class="pull-right"> '.$settings->currency.' '.$row->amount.'</a></li>
<li class="list-group-item">
  <i class="fa fa-user"></i> <a class="pull-right">'.$user_receiver->accountname.'</a></li>
  <li class="list-group-item">
  <i class="fa fa-phone"></i> <a class="pull-right">'.$user_receiver->phonenumber.'</a></li>
  <li class="list-group-item">
  <i class="fa fa-envelope-o"></i> <a class="pull-right">'.$user->email.'</a> </li>
  <li class="list-group-item">
  <div class="row">
  <div class="col-sm-6">
  <i class="fa fa-bank"></i> <strong>Bank</strong>
  <br>
  <span>'.$user_receiver->bankname.'</span>
  </div>
  <div class="col-sm-6">
  <i class="fa fa-archive"></i> <strong>Account Number</strong>
  <br>
  <span>'.$user_receiver->accountnumber.'</span>
  </div>
    </div>
  </li>
  <li class="list-group-item">
  <div class="row">
  <div class="col-sm-6">
  <i class="fa fa-th"></i> <strong>Account Name</strong>
  <br>
  <span>'.$user_receiver->accountname.'</span>
  </div>
  <div class="col-sm-6">
  <i class="fa fa-th"></i> <strong>Account Type</strong>
  <br>
  <span>'.$user_receiver->accounttype.'</span>
  </div>
  </div>
  </li>
  </ul>
  <br> ';?>         <?php       if ($row->payment_status == "pending") {
  echo '  <form method="POST" action="">
                        <div class="form-group"> 
                        <input type="hidden" name="merching_ID" value="'.$row->id.'">      
                          <input type="submit" name="submitMaching" value="Payment Page" class="btn btn-primary btn-lg btn-block">
                        </div>
                      </form>';
                       }
                       echo'
                    </div>
                  </div>
                </div>
             
              </div>';
         
              	}
              }
}
}
}

function NewMemberMatching($user_id)
{

		  echo '  <div class="col-md-6">
                  <div class="card">
                   
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Provide Donation</h3>
                    </div>
                    <div class="card-body text-center">
                      <a href="donor.php" class="btn btn-danger">Choose Package </a>

                  </div>
                </div>
               </div>

                 <div class="col-md-6">
                  <div class="card">
                   
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Get Donation</h3>
                    </div>
                    <div class="card-body text-center">
                      <a href="getdonation.php" class="btn btn-primary">Get Donation </a>

                  </div>
                </div>
                </div>
                ';
	
}


 

function RequestHelp($user_id)
{  
$settings = DB::table('settings')->where('id', 1)->first();
  $user_request = DB::table('requestHelp')->where('userid', $user_id)->take(10)->orderBy('id', 'DESC')->get();
  foreach ($user_request as $row) {
      $user_row = DB::table('userdetails')->where('userid', $user_id)->first();
         $timeCreatedsss = $row->timeReq;
                        $timeNow = date('Y-m-d H:i:s');
    echo ' <div class="col-lg-12">
                  <div class="work-amount card"><i class="fa fa-fire" style="color: red; font-size: 35px;margin-top: -10px;"></i>
                    <div class="card-close">
                      <div class="dropdown">
                        <button type="button" id="closeCard1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                        <div aria-labelledby="closeCard1" class="dropdown-menu dropdown-menu-right has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a><a href="#" class="dropdown-item edit"> <i class="fa fa-gear"></i>Edit</a></div>
                      </div>
                    </div>
                    <div class="card-body">
                      <h3>Get Help</h3>
                     <ul style="padding-left: 10px;">
                       <li>Full Name: '.$user_row->firstname.' '.$user_row->lastname.'</li>
                       <li>Account Number: '.$user_row->accountnumber.'</li>
                       <li>Package Name: '.$row->pack_name.'</li>
                       <li>Amount: '.$settings->currency.' '.$row->amount.'</li>
                       <li>Balance: '.$settings->currency.' '.$row->balance.'</li>
                       <li>Date Created: '.$row->date.'</li>
                     </ul>';
                    ?>
                    <?php if ($row->status == "active") {
                     echo '<center style="margin-left: -20px;margin-right: -20px;margin-bottom: -20px;border-radius: 0px !important;"><button class="btn btn-success btn-lg btn-block" style="border-radius: 0px !important;">Request Approved</button></center>';
                     }
                     elseif ($row->status =="pending") {
                      echo '<center style="margin-left: -20px;margin-right: -20px;margin-bottom: -20px;border-radius: 0px !important;"><a href="getdonation.php" class="btn btn-danger btn-lg btn-block" style="border-radius: 0px !important;"><p class="timerSet" id="GetHelpTimer'.$row->id.'" style="font-size: 25px;"></p></a></center>';
                     }
if ($row->status =="pending") {
   $date=date_create($row->timeReq);
      $timenow = date_format($date,"Y/m/d H:i:s");
      ?>
 <script>
  jQuery(document).ready(function(){
    jQuery("#GetHelpTimer<?php echo $row->id; ?>").jCountdown({
      timeText: "<?php echo $timenow; ?>",
      timeZone:8,
      style:"Crystal",
      color:"black", 
      width:250,
      textGroupSpace:15,
      textSpace:0,
      reflection:false,
      reflectionOpacity:10,
      reflectionBlur:0,
      dayTextNumber:2,
      displayDay:true,
      displayHour:true,
      displayMinute:true,
      displaySecond:true,
      displayLabel:false,
      onFinish:function(){
         document.getElementById("GetHelpTimer<?php echo $row->id; ?>").innerHTML = "Marging soon";
      }
    });
  });
</script>
<?php }
 echo '</div>
                  </div>
                </div>

                ';
  }
   
}

//Receiver Homepage view of the sender information
function ReceiverView($user_id){
  $settings = DB::table('settings')->where('id', 1)->first();
  $users = DB::table('marching')->where('receiver_id', $user_id)->orderBy('id', 'DESC')->get();

  foreach ($users as $row) {
    $senderusers = DB::table('userdetails')->where('userid', $row->sender_id)->first();
    $Susers = DB::table('users')->where('id', $row->sender_id)->first();
    echo ' 
         <div class="col-md-6"> 
                    <div class="work-amount card"><i class="fa fa-fire" style="color: red; font-size: 35px;margin-top: -10px;"></i>
                    <div class="card-close">
                      <div class="dropdown">
                        <button type="button" id="closeCard1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                        <div aria-labelledby="closeCard1" class="dropdown-menu dropdown-menu-right has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a></div>
                      </div>
                    </div>
                   
                     <div class="card-body">
                      <h3>Receive Payment</h3>

';



 if ($row->payment_status =="confirm") {
    echo '  <div class="card-body text-center" style="background-color: #218838; color: #fff;">You have confirmed and received payment of '.$settings->currency.''.$row->amount.' from '.$senderusers->accountname.'</div>';
  }
  else{

    if ($row->payment_status =="waiting") {
    echo '  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;">This payment have been sent and waiting your confirmation '.$settings->currency.''.$row->amount.' from '.$senderusers->accountname.'</div>';
  }
  echo '
                  <ul class="list-group list-group-unbordered">
                  <li class="list-group-item">
  <i class="fa fa-usd"></i> <a class="pull-right"> '.$settings->currency.' '.$row->amount.'</a></li>
<li class="list-group-item">
  <i class="fa fa-user"></i> <a class="pull-right">'.$senderusers->accountname.'</a></li>
  <li class="list-group-item">
  <i class="fa fa-phone"></i> <a class="pull-right">'.$senderusers->phonenumber.'</a></li>
  <li class="list-group-item">
  <i class="fa fa-envelope-o"></i> <a class="pull-right">'.$Susers->email.'</a> </li>
  
  </ul>
  <br> 
  

 
  <form method="POST" action="">
                        <div class="form-group"> 
                        <input type="hidden" name="merching_ID" value="'.$row->id.'">      
                          <input type="submit" name="confirmMaching" value="Approve Payment" class="btn btn-primary btn-lg btn-block">
                        </div>
                      </form>
                   
             
              ';
  }
  echo " </div>
                  </div>
                </div>";
}
}



function MessageView($user_id){
    $users = DB::table('messagess')->where('from_user', $user_id)->orWhere('to_user', $user_id)->get();
    if ($users) {
      
  foreach ($users as $row) {
    if ($row->from_user == $user_id) {
      $classcss = 'right';
    }elseif ($row->to_user == $user_id) {
      $classcss = 'left';
    }
     $user = DB::table('users')->where('id', $row->from_user)->first();

    $time_ago = strtotime($row->date);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60 );
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400 );
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640 );
    $years      = round($time_elapsed / 31207680 );
    // Seconds
    if($seconds <= 60){
        $PostedTime=  "just now";
    }
    //Minutes
    else if($minutes <=60){
        if($minutes==1){
            $PostedTime=  "one minute ago";
        }
        else{
            $PostedTime=  "$minutes minutes ago";
        }
    }
    //Hours
    else if($hours <=24){
        if($hours==1){
            $PostedTime=  "an hour ago";
        }else{
            $PostedTime=  "$hours hrs ago";
        }
    }
    //Days
    else if($days <= 7){
        if($days==1){
            $PostedTime=  "yesterday";
        }else{
            $PostedTime=  "$days days ago";
        }
    }
    //Weeks
    else if($weeks <= 4.3){
        if($weeks==1){
            $PostedTime=  "a week ago";
        }else{
            $PostedTime=  "$weeks weeks ago";
        }
    }
    //Months
    else if($months <=12){
        if($months==1){
            $PostedTime=  "a month ago";
        }else{
            $PostedTime=  "$months months ago";
        }
    }
    //Years
    else{
        if($years==1){
            $PostedTime=  "one year ago";
        }else{
            $PostedTime=  "$years years ago";
        }
    }

    echo ' <li class="'.$classcss.' clearfix" style="background-color: #fff;padding-left: 5px;padding-right: 5px;padding-top: 5px;border-radius: 5px;">

                            <div class="chat-body clearfix">
                                <div class="header">
                                    <small class=" text-muted"><span class="glyphicon glyphicon-time"></span>'.$PostedTime.'</small>
                                    <strong class="pull-right primary-font">'.$user->username.'</strong>
                                </div>
                                <p>
                                  '.$row->message.'
                                </p>
                            </div>
                        </li>';
  }
}
else{
  echo "<h1>Your Inbox is empty";
}
}

function ReferralView($user_id){
  $settings = DB::table('settings')->where('id', 1)->first();
  $user = DB::table('referral')->where('sponsor', $user_id)->get();
   $UserU = DB::table('referral')->where('userid', $user_id)->first();
  if ($UserU) {
   
  
  $parents = DB::table('userdetails')->where('userid', $UserU->parent)->first();
  if ($parents) {
   echo '<center><h1>Parent Guider (  <img src="'.asset_url('img/guider.png').'">'.$parents->accountname.' | <img src="'.asset_url('img/phone.png').'"> '.$parents->phonenumber.'   )</h1></center>';
  }
  }
 
  
  if ($user >0) {
    foreach ($user as $row) {
      $users = DB::table('userdetails')->where('userid', $row->userid)->first();
      $parent = DB::table('userdetails')->where('userid', $row->parent)->first();
      $spon = DB::table('userdetails')->where('userid', $row->sponsor)->first();
      $percentage = $settings->referralProfit;
      $totalWidth = $row->amount;
      $new_amount = ($percentage / 100) * $totalWidth;

      if ($row->status ==1) {
       $statusU = '<span style="color: red;">Pending</span>';
      }else{
         $statusU = '<span style="color: green;"><b>Paid</b></span>';
       }
       echo '<tr>
                              <th scope="row">'.$row->id.'</th>
                              <td>'.date("Y-m-d",strtotime($row->date)).'</td>
                              <td>'.$settings->currency.' '.$row->amount.'</td>
                              <td>'.$settings->currency.''.$new_amount.'</td>
                              <td>'.$users->accountname.'</td>
                              <td>'.$spon->accountname.'</td>
                              <td>'.$parent->accountname.'</td>
                              <td>'.$row->package.'</td>
                              <td>'.$statusU.'</td>
                            </tr>';
     
    }
  }
    else {
      echo "<h1>You dont have any transaction recorded";
    }

}


function TicketReplies($user_id,$ticketID){

  $users = DB::table('ticketsreply')->where('ticketid', $ticketID)->where('userid', $user_id)->get();
  if ($users) {
    
  
      foreach ($users as $row) {

          echo '<div class="comment-wrap">
        <div class="photo">
           <i class="fa fa-comments" style="font-size: 40px;"></i>
        </div>
        <div class="comment-block">
         <h3>'.$row->admin.' <small style="color: green; font-size: 15px;">Replied</small></h3>
            <p class="comment-text">'.$row->reply.'</p>
            <div class="bottom-comment">
                <div class="comment-date">'.$row->date.'</div>
               
            </div>
        </div>
    </div> ';
      }
}
else{

  echo '<div class="alert alert-success" role="alert">
 This Ticket has not been answered, please wait for some moment
</div>';
}
}




function fectAllTickets($user_id){
  $users = DB::table('tickets')->where('userid', $user_id)->take(10)->orderBy('id', 'DESC')->get();  if ($users) {
   foreach ($users as $row) {
    
     echo '<a href="tickets.php?view='.$row->id.'" class="list-group-item">'.$row->subject.'  <span class="fa-stack fa-1x has-badge" data-count="'.$row->replied.'">
  <i class="fa fa-circle fa-stack-2x"></i>
  <i class="fa fa-bell fa-stack-1x fa-inverse"></i>
</span>';
if ($row->status =="0") {
  echo '<code style="background-color: #f40000 !important;color: #fff !important;">Closed</code>';
}
echo '</a>';
  }
  }
  else{
    echo "<h1>You have create an support ticket</h1>";
  }
 
  
} 



function GetMarginView($user_id){
$settings = DB::table('settings')->where('id', 1)->first();
$user = DB::table('requestHelp')->where('userid', $user_id)->orderBy('id', 'DESC')->first();
$users = DB::table('requestHelp')->where('userid', $user_id)->count();
$id = DB::table('requestMaching')->where('userid', $user_id)->sum('balance');
$ids = DB::table('requestMaching')->where('userid', $user_id)->orderBy('id', 'DESC')->first();
$CheckReqeust  = DB::table('requestMaching')->where('userid', $user_id)->count();
if ($CheckReqeust >=1) {
   echo '  <div class="col-lg-6">
                  <div class="card">
                    
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Get-Help</h3>
                    </div>
                    <div class="card-body">
                     <p>Request Help</p>
                      <form method="POST" action="">
                        <div class="form-group">
                          <label class="form-control-label">Amount</label>
                          <input type="text" name="amount" value="'.$ids->profit.'" placeholder="Amount to withdraw" class="form-control">
                        </div>
                       
                        <div class="form-group">       
                          <input type="submit" value="withdraw" name="withdrawNow" class="btn btn-primary btn-block">
                        </div>
                      </form>
                    </div>
                  </div>
                </div>';
}

if ($CheckReqeust >=1) {

if ($id == 0 || $id <0) {

    
              if ($users >= 1) { 
    $user = DB::table('requestHelp')->where('userid', $user_id)->orderBy('id', 'DESC')->get();
     echo '<div class="col-lg-6">';
    foreach ($user as $key) {
         $timeNow = date('Y-m-d H:i:s');
                if ($key->timeReq >=  $timeNow) {
                    $date=date_create($key->timeReq);
                    $timenow = date_format($date,"Y/m/d H:i:s");
                  ?>
                  
                  <div class="col-lg-12">
                  <div class="card">
                    
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Get-Help #NIF<?php echo  $key->id * 4300; ?> <?php echo $settings->currency. '' .$key->amount; ?></h3>
                    </div>
                    <div class="card-body">
                  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;">Your withdrawal margin time estimated left <br> <p id="GetHelTimer<?php echo  $key->id; ?>";" style="font-size: 25px;"></p></div>
           <script>
  jQuery(document).ready(function(){
    jQuery("#GetHelTimer<?php echo  $key->id; ?>").jCountdown({
      timeText: "<?php echo $timenow; ?>",
      timeZone:8,
      style:"Crystal",
      color:"black", 
      width:230,
      textGroupSpace:5,
      textSpace:0,
      reflection:false,
      reflectionOpacity:10,
      reflectionBlur:0,
      dayTextNumber:2,
      displayDay:true,
      displayHour:true,
      displayMinute:true,
      displaySecond:true,
      displayLabel:false,
      onFinish:function(){
        document.getElementById("GetHelTimer").innerHTML = "marge soon";
      }
    });
  });
</script>
                  <?

                  echo '</div></div></div>';
                }
    }
            
 echo '</div>';
}
else {
                 
}
}
else{
    echo '        <div class="col-lg-6">
                  <div class="card">
                    
                    <div class="card-header d-flex align-items-center" style="background-color: #dc3545 !important; color: #fff;">
                      <h3 class="h4">Get-Help</h3>
                    </div>
                      <div class="card-body">
                        <h3 class="h4">You currently can\'t get help below are the possible reasons</h3>
                        <ol>
                          <li>Wait till your earning grow</li>
                          <li>please provide donation</li>
                          <li>wait till your payment is confirmed</li>
                          <li>Once your are entitled to Get-Help withdrawal page will appear</li>
                        </ol>
                     
                    </div>
                  </div>
                </div>
        ';
}
  
}
else{
    echo '        <div class="col-lg-6">
                  <div class="card">
                    
                    <div class="card-header d-flex align-items-center" style="background-color: #dc3545 !important; color: #fff;">
                      <h3 class="h4">Get-Help</h3>
                    </div>
                      <div class="card-body">
                        <h3 class="h4">You currently can\'t get help below are the possible reasons</h3>
                        <ol>
                          <li>Wait till your earning grow</li>
                          <li>please provide donation</li>
                          <li>wait till your payment is confirmed</li>
                          <li>Once your are entitled to Get-Help withdrawal page will appear</li>
                        </ol>
                     
                    </div>
                  </div>
                </div>
        ';
}
}




function PaymentLogs($user_id){
  $settings = DB::table('settings')->where('id', 1)->first();
  $user = DB::table('marching')->where('sender_id', $user_id)->orWhere('receiver_id', $user_id)->get();


  
  if ($user >0) {
    foreach ($user as $row) {
      $sender = DB::table('userdetails')->where('userid', $row->sender_id)->first();
      $receiver = DB::table('userdetails')->where('userid', $row->receiver_id)->first();
      $package = DB::table('packages')->where('id', $row->package_id)->first();
     
      if ($row->payment_status =='pending') {
       $statusU = '<span style="color: red;">Pending</span>';
      }else if ($row->payment_status =='waiting') {
       $statusU = '<span style="color: yellow;">Waiting</span>';
      }else{
         $statusU = '<span style="color: green;"><b>Paid</b></span>';
       }
       echo '<tr>
                              <th scope="row">'.$row->id.'</th>
                              <td>'.date("Y-m-d",strtotime($row->expiringTime)).'</td>
                              <td>'.$settings->currency.' '.$row->amount.'</td>
                              <td>'.$sender->accountname.'</td>
                              <td>'.$receiver->accountname.'</td>
                              <td>'.$settings->currency.' '.$row->amount.'</td>
                              <td>'.$package->packname.'</td>
                              <td>'.$statusU.'</td>
                            </tr>';
     
    }
  }
    else {
      echo "<h1>You dont have any transaction recorded";
    }

}

function ActivationLogs($user_id){
  $settings = DB::table('settings')->where('id', 1)->first();
  $user = DB::table('activationFee')->where('sender_id', $user_id)->orWhere('receiver_id', $user_id)->get();
  

  
  if ($user >0) {
    foreach ($user as $row) {
      $sender = DB::table('userdetails')->where('userid', $row->sender_id)->first();
      $receiver = DB::table('userdetails')->where('userid', $row->receiver_id)->first();
     
      if ($row->payment_status =='pending') {
       $statusU = '<span style="color: red;">Pending</span>';
      }else if ($row->payment_status =='waiting') {
       $statusU = '<span style="color: yellow;">Waiting</span>';
      }else{
         $statusU = '<span style="color: green;"><b>Paid</b></span>';
       }
       echo '<tr>
                              <th scope="row">'.$row->id.'</th>
                              <td>'.date("Y-m-d",strtotime($row->expiringTime)).'</td>
                              <td>'.$settings->currency.' '.$row->amount.'</td>
                              <td>'.$sender->accountname.'</td>
                              <td>'.$receiver->accountname.'</td>
                              <td>'.$settings->currency.' '.$row->amount.'</td>
                              <td>Activation Fees</td>
                              <td>'.$statusU.'</td>
                            </tr>';
     
    }
  }
    else {
      echo "<h1>You dont have any transaction recorded";
    }

}



function GuiderDetailsView($user_id) 
{
    $UserU = DB::table('referral')->where('userid', $user_id)->first();
  if ($UserU) {
   
  
  $parents = DB::table('userdetails')->where('userid', $UserU->parent)->first();
  $Henry = DB::table('users')->where('id', $UserU->parent)->first();
  if ($parents) {
   echo '<div class="col-md-12">
                   
                    <div class="card-header d-flex align-items-center" style="margin-top: -40px;">
                      <h3 class="h4">My Guider</h3>
                    </div>
                    <div class="card-body text-center" style="padding-bottom: 0px;padding-top: 10px;">
                      <center><h1>Parent Guider | <img src="'.asset_url('img/guider.png').'">'.$Henry->username.' | <img src="'.asset_url('img/phone.png').'"> '.$parents->phonenumber.'  </h1></center>

                
                </div>
               </div>

              ';
  }
  }
}
?>
