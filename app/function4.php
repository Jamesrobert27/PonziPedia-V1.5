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


  

//Mergin View for each member
function GetActivationView($user_id)
{
  $settings = DB::table('settings')->where('id', 1)->first();
	$user_request = DB::table('activationFee')->where('sender_id', $user_id)->where('payment_status', '!=', 'confirm')->take(1)->orderBy('id', 'DESC')->get();
	if ($user_request) {
		foreach ($user_request as $row) {
			$receiver = $row->receiver_id;
      $timeCreated = $row->expiringTime;
      $timeNow = date('Y-m-d H:i:s');
      $userTimer = DB::table('activationFee')->where('id', $row->id)->first();
      if ($timeCreated < $timeNow and $row->expiringTime != "NULL") {
         $BlockU = DB::table('users')->where('id', $user_id)->update(array('role_id' => '3'));
        if ($BlockU) {
          echo '<meta http-equiv="refresh" content="0">';
        }
      }else{
     
	        $user_sender = DB::table('userdetails')->where('userid', $user_id)->first();
	        $user_receiver = DB::table('userdetails')->where('userid', $receiver)->first();
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
                      <h3>Activation Fees '.$settings->currency.''.$row->amount.'</h3>';
      if ($row->payment_status == "pending") {
      	echo '  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;">You are to make payment to the receiver below<br>Time left to complete payment<br>
                     <p id="demo'.$row->id.'" style="font-size: 25px;"></p></div>';}
    elseif ($row->payment_status == "waiting") { 
    	echo '  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;">Your activation fees payment is waiting for Approval<br>Please wait while we check and confirm your account</div>';
    }
    elseif ($row->payment_status == "confirm") {
    	echo '  <div class="card-body text-center" style="background-color: #218838; color: #fff;">Your payment is fully confirmed '.$settings->currency.''.$row->amount.' confirmed paid to '.$user_receiver->accountname.'</div></div></div></div>';
    }else{
    	echo '  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;">Your request is not understood<br>Please try reload the page or contact for help</div>';
    }
     
     if ($row->payment_status == "pending") {
     
     
      ?>
      <script>
// Set the date we're counting down to
var countDownDate = new Date("<?php echo $userTimer->expiringTime; ?>").getTime();

// Update the count down every 1 second
var x = setInterval(function() {

    // Get todays date and time
    var now = new Date().getTime();
    
    // Find the distance between now an the count down date
    var distance = countDownDate - now;
    
    // Time calculations for days, hours, minutes and seconds
    var days = Math.floor(distance / (1000 * 60 * 60 * 24));
    var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
    var seconds = Math.floor((distance % (1000 * 60)) / 1000);  
    
    // Output the result in an element with id="demo"
    document.getElementById("demo<?php echo $row->id; ?>").innerHTML = days + "d |" + hours + "h | "
    + minutes + "m |" + seconds + "s ";
    
    // If the count down is over, write some text 
    if (distance < 0) {
        clearInterval(x);
        document.getElementById("demo<?php echo $row->id; ?>").innerHTML = "EXPIRED";
    }
}, 1000);
</script>  
      <?php 
                    
                       
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
                          <input type="submit" name="submitToActivation" value="Payment Page" class="btn btn-primary btn-lg btn-block">
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
             
              </div>';
          }
              	}
              }
}
}
}  




//Payment confirmation and pending 

function confirmMyActivationFee($payment_method,$bankname,$accountnumber,$accountname,$depositor ,$paymentlocal,$paymentpof, $user_id, $id)
{
  $Query = DB::table('activationFee')
        ->where('sender_id', $user_id)->where('id', $id)
        ->update(array('payment_status' => "waiting",
                        'ProofPic' => $paymentpof,
                        'paymentMethod' => $payment_method,
                       'senderBank' => $bankname,
                       'accountNumber' => $accountnumber,
                       'AccountName' => $accountname,
                       'depositorsName' => $depositor,
                       'paymentLocation' => $paymentlocal,
                       'expiringTime' => 'NULL',
                     ));

        if ($Query) {


    $user = DB::table('users')->where('id', $user_id)->first();
     $subject = 'You have submit your payment activation fees information' .Config::get('app.name');
     
      $message = "<h3>You have successfully submit your activation fees payment information</h1><br><br>
                      <p> You have successfully submit your payment information, Please wait while we check and confirm your account.</p>";
      include 'emails.php';
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

      $headers .= 'From: '.Config::get('app.webmail')."\r\n".
       'Reply-To: '.Config::get('app.webmail')."\r\n" .
        'X-Mailer: PHP/' . phpversion();
        $mail = mail($user->email, $subject, $EmaiMessage, $headers);

   $Querys =DB::table('notification')->insert(
    array('userid' =>        $user_id,
         'type' =>        "pack",
          'details' =>    "You have submit your activation fees payment information", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd")
);
          echo'<div class="alert alert-success" role="alert">
  You have successfully submit your activation fees payment information, Please wait while we check and confirm your account.
</div>';
        }
else{
  echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
}
}




//Receiver Homepage view of the sender information
function ActivationReceiverView($user_id){
  $settings = DB::table('settings')->where('id', 1)->first();
  $users = DB::table('activationFee')->where('receiver_id', $user_id)->take(10)->orderBy('id', 'DESC')->get();

  foreach ($users as $row) {
    $senderusers = DB::table('userdetails')->where('userid', $row->sender_id)->first();
    $Susers = DB::table('users')->where('id', $row->sender_id)->first();
    echo ' 
         <div class="col-md-12"> 
                    <div class="work-amount card"><i class="fa fa-fire" style="color: red; font-size: 35px;margin-top: -10px;"></i>
                    <div class="card-close">
                      <div class="dropdown">
                        <button type="button" id="closeCard1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                        <div aria-labelledby="closeCard1" class="dropdown-menu dropdown-menu-right has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a></div>
                      </div>
                    </div>
                   
                     <div class="card-body">
                      <h3>Confirm Activation Fees Payment</h3>

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
                          <input type="submit" name="confirmActivation" value="Approve Payment" class="btn btn-primary btn-lg btn-block">
                        </div>
                      </form>
                   
             
              ';
  }
  echo " </div>
                  </div>
                </div>";
}
}




//Confirm user payment EG receiver confirm sender
function confirmUserActivationPay($user_id, $sender_userid, $id){
 $sql = DB::table('activationFee')
        ->where('id', $id)->where('receiver_id', $user_id)->where('sender_id', $sender_userid)
        ->update(array('payment_status' => 'confirm'));
        if ($sql) {
      $BanalnceGet = DB::table('bank')->where('userid', $sender_userid)->first();
      $userActivation = DB::table('activationFee')->where('id', $id)->first();
      $BanalnceUp = $BanalnceGet->balance + $userActivation->amount;
       DB::table('bank')
        ->where('userid', $sender_userid)
        ->update(array('balance' => $BanalnceUp));


           echo'<div class="alert alert-success" role="alert">
  You have successfully confirm your upline.
</div>';
     $user = DB::table('users')->where('id', $sender_userid)->first();
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
    array('userid' =>        $sender_userid,
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


function ReportUserActivation($user_id, $sender_userid, $id){


    $user = DB::table('userdetails')->where('userid', $sender_userid)->first();
    $users = DB::table('userdetails')->where('userid', $user_id)->first();
     $pack = DB::table('marching')->where('receiver_id', $user_id)->first();
      $row = DB::table('users')->where('id', $user_id)->first();
      $rows = DB::table('users')->where('id', $sender_userid)->first();

    
         $sql =  DB::table('users')
        ->where('id', $sender_userid)
        ->update(array('role_id' => 3));

        if ($sql) {
        DB::table('notification')->insert(
    array('userid' =>        $user_id,
         'type' =>        "confirm",
          'details' =>    "You have successfully report activation fees and user has been banned", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-fire")
);
             echo'<div class="alert alert-success" role="alert">
 We have receive your report and we will look forwark to contact both party.
</div>';
        }
        else{
          echo'<div class="alert alert-danger" role="alert">
  We cannot process your request now please try again.
</div>';
        }

}



?>
