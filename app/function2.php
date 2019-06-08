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



//Sending marching to the next page
if (isset($_POST['submitMaching'])) {
	$Maching_ID = $_POST['merching_ID'];
	header('location: payment.php?u='.$Maching_ID.'&session='.$RandomNumbers);
}

//Sending submitToActivation to the next page
if (isset($_POST['submitToActivation'])) {
  $Maching_ID = $_POST['merching_ID'];
  header('location: send-activation.php?u='.$Maching_ID.'&session='.$RandomNumbers);
}

//Restirct user from certain pages
function page_restricted() {
      redirect_to(App::url('account/restricted.php')); exit;
}
  
//Sending marching to the next page
if (isset($_POST['confirmMaching'])) {
  $senderID = $_POST['merching_ID'];
  header('location: paymentapprove.php?u='.$senderID.'&session='.$RandomNumbers);
}


//Sending confirmActivation to the next page
if (isset($_POST['confirmActivation'])) {
  $senderID = $_POST['merching_ID'];
  header('location: receive-activation.php?u='.$senderID.'&session='.$RandomNumbers);
}

//Payment confirmation and pending 

function confirmPay($payment_method,$bankname,$accountnumber,$accountname,$depositor ,$paymentlocal,$paymentpof, $user_id, $id)
{
  $Query = DB::table('marching')
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
     $subject = 'You have submit your payment information' .Config::get('app.name');
     
      $message = "<h3>You have successfully submit your payment information</h1><br><br>
                      <p> You have successfully submit your payment information, please call your downline confirmation.</p>";
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
          'details' =>    "You have submit your payment information", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd")
);
          echo'<div class="alert alert-success" role="alert">
  You have successfully submit your payment information, please call your downline confirmation.
</div>';
        }
else{
  echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
}
}

function ChoosePackageNow($pack_id, $user_id){
  $settings = DB::table('settings')->where('id', 1)->first();
	$user_package =  DB::table('packages')->where('id', $pack_id)->first();
	$Amount       =  $user_package->price;
	$Profit       =  $user_package->profit;
	$days         =  $user_package->days;
	$packagesName =  $user_package->packname;
	$packagesId   =   $user_package->id;
  $startDate = time();
	$timeX        =   date('Y-m-d H:i:s', strtotime('+'.$settings->ProvideHelpday.' day', $startDate));

$CheckRequest = DB::table('requestMaching')->where('userid', '=', $user_id)->where(function ($query) {$query->where('status', '=', 'pending')->orWhere('status', '=', 'waiting');})->count();   


if ($CheckRequest > 0) {
	echo '<div class="alert alert-danger" role="alert">
 You already have active or pending package with us, not allowed
</div>';
}
else{
   //User Recommitment Percentage
      $percentage = $settings->reccomitment;
      $totalWidth = $user_package->profit;
      $new_amount = ($percentage / 100) * $totalWidth;

      $new_amountUpdate = $user_package->profit- $new_amount;

 $Query =DB::table('requestMaching')->insert(
    array('userid' =>        $user_id,
          'package_id' =>    $packagesId, 
          'pack_name'  =>    $packagesName,
          'amount'     =>    $Amount,
          'profit'     =>    $new_amountUpdate,
          'balance'     =>   $Amount,
          'timeReq'     =>   $timeX,
          'status'     =>    "pending")
);

 if ($Query) {
    
  $getRef = DB::table('referral')->where('userid', $user_id)->count();
    if ($getRef >=1) {
      $getRefID = DB::table('referral')->where('userid', $user_id)->first();
      DB::table('referral')
        ->where('id', $getRefID->id)
        ->update(array('package' => $packagesId,
                       'amount' => $Amount));
    }
     
     $getBalance = DB::table('bank')->where('userid', $user_id)->first();
     $new_amountBl = $getBalance->balance + $new_amount;
     DB::table('bank')
        ->where('userid', $user_id)
        ->update(array('balance' => $new_amountBl));


    $user = DB::table('users')->where('id', $user_id)->first();
     $subject = 'You have subscribed to a package' .Config::get('app.name');
     
      $message = "<h3>You have successfully subscribed to a package</h1><br><br>
                      <p>You have successfully subscribed to a package and request is pending. kindly login to your acocunt and make payment as instructed</p>";
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
          'details' =>    "You have successfully subscribed to a package" , 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-fire")
);
 	echo'<div class="alert alert-success" role="alert">
  You have successfully subscribed to a package and request is pending.
</div>';
 }
 else{
 	echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
}
 }
}



//user Choose package with unlock code validation

function  ChoosePackageNowLocked($pack_id, $user_id){
  $settings = DB::table('settings')->where('id', 1)->first();
  $user_package =  DB::table('packages')->where('id', $pack_id)->first();
  $Amount       =  $user_package->price;
  $Profit       =  $user_package->profit;
  $days         =  $user_package->days;
  $packagesName =  $user_package->packname;
  $packagesId   =   $user_package->id;
  $startDate = time();
  $timeX        =   date('Y-m-d H:i:s', strtotime('+'.$settings->ProvideHelpday.' day', $startDate));

$CheckRequest =  DB::table('requestMaching')->where('userid', '=', $user_id)->where(function ($query) {$query->where('status', '=', 'pending')->orWhere('status', '=', 'waiting');})->count();   
if ($CheckRequest > 0) {
  echo '<div class="alert alert-danger" role="alert">
 You already have active or pending package with us, not allowed
</div>';
}
else{
   //User Recommitment Percentage
      $percentage = $settings->reccomitment;
      $totalWidth = $user_package->profit;
      $new_amount = ($percentage / 100) * $totalWidth;

      $new_amountUpdate = $user_package->profit- $new_amount;

 $Query =DB::table('requestMaching')->insert(
    array('userid' =>        $user_id,
          'package_id' =>    $packagesId, 
          'pack_name'  =>    $packagesName,
          'amount'     =>    $Amount,
          'profit'     =>    $new_amountUpdate,
          'balance'     =>   $Amount,
          'timeReq'     =>   $timeX,
          'status'     =>    "pending")
);

 if ($Query) {
    
  $getRef = DB::table('referral')->where('userid', $user_id)->count();
    if ($getRef >=1) {
      $getRefID = DB::table('referral')->where('userid', $user_id)->first();
      DB::table('referral')
        ->where('id', $getRefID->id)
        ->update(array('package' => $packagesId,
                       'amount' => $Amount));
    }
     
     $getBalance = DB::table('bank')->where('userid', $user_id)->first();
     $new_amountBl = $getBalance->balance + $new_amount;
     DB::table('bank')
        ->where('userid', $user_id)
        ->update(array('balance' => $new_amountBl));


    $user = DB::table('users')->where('id', $user_id)->first();
     $subject = 'You have subscribed to a package' .Config::get('app.name');
     
      $message = "<h3>You have successfully subscribed to a package</h1><br><br>
                      <p>You have successfully subscribed to a package and request is pending. kindly login to your acocunt and make payment as instructed</p>";
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
          'details' =>    "You have successfully subscribed to a package", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-fire")
);
  echo'<div class="alert alert-success" role="alert">
  You have successfully subscribed to a package and request is pending.
</div>';
 }
 else{
  echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
}
 }
}






//Profile completion

function ProfileComplete($firstname,$lastname,$phonenumber,$bankname,$accountnumber,$accountname,$accounttype,$country,$state,$user_id)
{
  $settings = DB::table('settings')->where('id', 1)->first();

  if (empty($firstname) || empty($lastname) || empty($phonenumber) || empty($bankname) || empty($accountnumber) || empty($accountname) || empty($accounttype) || empty($country) || empty($state)) {
    echo '<div class="alert alert-danger" role="alert">
                Please check your submited information and try again
         </div>';
      }

      $Query = DB::table('userdetails')->insert(
               array('userid' => $user_id, 
                      'firstname' => $firstname,
                      'lastname' => $lastname,
                      'phonenumber' => $phonenumber,
                      'bankname' => $bankname,
                      'accounttype' => $accounttype,
                      'accountname' => $accountname,
                      'accountnumber' => $accountnumber,
                      'country' => $country,
                      'state' => $state,
                       'status' => 1)
       );
    if ($Query) {
       $value = Session::get('ref');
       if ($value != "") {
       $Userref = DB::table('users')->where('id', $value)->first();
      $refs = DB::table('referral')->where('userid', $value)->first();

      if ($Userref->role_id ==4 or $Userref->role_id ==1)
        {
      $receiver = $value;
       DB::table('referral')->insert(
     array('userid' => $user_id, 
     'sponsor' => $value,
     'parent' => $value,
     'package' => '0',
     'amount' => '0',
      'status' => 1)
      );
        }
     else
    {
      $receiver = $value;
         DB::table('referral')->insert(
     array('userid' => $user_id, 
     'sponsor' => $value,
     'parent' => $refs->parent,
     'package' => '0',
     'amount' => '0',
      'status' => 1)
      );
  }
    }
    else{
      $receiver = 1;
        DB::table('referral')->insert(
     array('userid' => $user_id,
     'sponsor' => 1,
     'parent' => 1,
     'package' => '0',
     'amount' => '0',
      'status' => 1)
      );
    }
    if ($receiver != 1) {
     $receeiversNew = $receiver;
    }
    else{
      $receiver = DB::table('activationReceiver')->where('id', 1)->first();
      $receeiversNew =  $receiver->userid;
    }
        $settings = DB::table('settings')->where('id', 1)->first();
      $receiver = DB::table('activationReceiver')->where('id', 1)->first();
         $user = DB::table('users')->where('id', $user_id)->first();
      $settings = DB::table('settings')->where('id', 1)->first();
      $startDate = time();
      $timeNow = date('Y-m-d H:i:s', strtotime('+'.$settings->timeMargin.'day', $startDate));
     $random = DB::table('activationFee')->insert(
               array('receiver_id' => $receeiversNew,
                     'sender_id' => $user->id,
                     'amount' => $settings->activationPrice,
                     'payment_status' => 'pending',
                     'expiringTime' => $timeNow, 
                     'active' => 1)
     );
       //SMS Notification settings allow or disabled 
      if ($settings->smsallow ==1) {
        
      $message = urlencode("Hi, ".$firstname."  You have successfully registered to NiFunds, you have a pending activation fee. check your dashboard to proceed. https://bit.ly/2FDnK5U");
      $sender= urlencode("NiFunds");
      $mobile = $phonenumber;
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
           array('userid' =>        $user_id,
         'type' =>        "Welcome",
          'details' =>    "You have successfully become a registered member on " .Config::get('app.name'), 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-user-plus")
         );

       DB::table('notification')->insert(
           array('userid' =>        $user_id,
         'type' =>        "pack",
          'details' =>    "You have pending activation payment ".$settings->currency."".$settings->activationPrice, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd")
         );

     
      }
     if ($random) {
      $accountNum = rand(0000000000,9999999999);
    $user = DB::table('bank')->where('accountNum', $accountNum)->first();
    if ($user) {
      $users = DB::table('bank')->where('accountNum', $accountNum)->first();
      if ($users) {
       DB::table('bank')->where('accountNum', $accountNum)->first();
      }
      else{
         DB::table('bank')->insert(
               array('userid' => $user_id,
                'balance' => 0,
                'pending' => 'none',
                'accountNum' => $accountNum,
                'status' => 1)
);
      }
    }else{
       DB::table('bank')->insert(
               array('userid' => $user_id,
                'balance' => 0,
                'pending' => 'none',
                'accountNum' => $accountNum,
                'status' => 'active')
);
    }
     
     echo'<div class="alert alert-success" role="alert">
  You have successfully update your account.
</div>';
redirect_to(App::url('account/index.php'));
    }

    
}


function ProfileUpdate($firstname,$lastname,$phonenumber,$bankname,$accountnumber,$accountname,$accounttype,$country,$state,$user_id)
{
      $Query = DB::table('userdetails')->where('userid', $user_id)
        ->update(array('userid' => $user_id, 
                      'firstname' => $firstname,
                      'lastname' => $lastname,
                      'phonenumber' => $phonenumber,
                      'bankname' => $bankname,
                      'accounttype' => $accounttype,
                      'accountname' => $accountname,
                      'accountnumber' => $accountnumber,
                      'country' => $country,
                      'state' => $state,
                       'status' => 1)
       );
    if ($Query) {
        DB::table('notification')->insert(
    array('userid' =>        $user_id,
         'type' =>        "update",
          'details' =>    "You have successfully update your account.", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-pencil-square-o")
);
     echo'<div class="alert alert-success" role="alert">
  You have successfully update your account.
</div>
';
    }
    else{
      echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
    }
}





//Confirm user payment EG receiver confirm sender
function confirmUserPay($user_id, $sender_userid, $id){
  $settings = DB::table('settings')->where('id', 1)->first();
 $sql = DB::table('marching')
        ->where('id', $id)->where('receiver_id', $user_id)->where('sender_id', $sender_userid)
        ->update(array('payment_status' => 'confirm','expiringTime' => 'NULL'));
        if ($sql) {
      $TheBalance = DB::table('bank')->where('userid', $user_id)->first();
       $UpdateConfimed = $TheBalance->confirmed + 1;
          DB::table('bank')
        ->where('userid', $sender_userid)
        ->update(array('confirmed' => $UpdateConfimed));
         
       $getAmount = DB::table('marching')->where('id', $id)->where('receiver_id', $user_id)->where('sender_id', $sender_userid)->first();
       $getRef = DB::table('referral')->where('userid', $sender_userid)->where('status', 1)->count();
       if ($getRef >=1) {

        
        $getRefSpensor = DB::table('referral')->where('userid', $sender_userid)->first();
        $BankSpensor = DB::table('bank')->where('userid', $getRefSpensor->sponsor)->first(); 

        $BankParent = DB::table('bank')->where('userid', $getRefSpensor->parent)->first();
    


   
        if ($getRefSpensor->status ==1) {
           //Parent balance top up and getting user
         $percentagP= $settings->guiderProfit;
        $totalP = $getAmount->amount;
       $new_amountP = ($percentagP / 100) * $totalP;
        $balanceP = $BankParent->balance + $new_amountP;
        



         //User Recommitment Percentage
       $percentage = $settings->referralProfit;
       $totalWidth = $getAmount->amount;
       $new_amount = ($percentage / 100) * $totalWidth;
        $balance = $BankSpensor->balance + $new_amount;

       
         DB::table('bank')
        ->where('userid', $getRefSpensor->parent)
        ->update(array('balance' => $balanceP));
      

        DB::table('referral')
        ->where('userid', $sender_userid)
        ->update(array('status' => 0));

        }else
        {
           DB::table('bank')
        ->where('userid', $getRefSpensor->parent)
        ->update(array('balance' => $balanceP));
        }
        

      
       }
         

           echo'<div class="alert alert-success" role="alert">
  You have successfully confirm your upline.
</div>';
     $user = DB::table('users')->where('id', $sender_userid)->first();
     $subject = 'Your payment is confirmed' .Config::get('app.name');
     
      $message = "<h3>Your payment is confirmed</h1><br><br>
                      <p>Your payment has been received and confirmed, you can now get donation from other members when you investment is mature.";
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
          'details' =>    "Your payment is confirmed", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd")
);
          $RecBalance = DB::table('bank')->where('userid', $user_id)->first();
          DB::table('notification')->insert(
          array('userid' =>        $user_id,
         'type' =>        "confirm",
          'details' =>    "You have Recommitment balance of ".$RecBalance->balance, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd")
       );

            $Query =DB::table('notification')->insert(
    array('userid' =>        $user_id,
         'type' =>        "confirm",
          'details' =>    "You have successfully confirm your upline", 
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


function ReportUserMargin($user_id, $sender_userid, $id,$enquiry,$des){


    $user = DB::table('userdetails')->where('userid', $sender_userid)->first();
    $users = DB::table('userdetails')->where('userid', $user_id)->first();
     $pack = DB::table('marching')->where('receiver_id', $user_id)->where('id', $id)->first();
      $row = DB::table('users')->where('id', $user_id)->first();
      $rows = DB::table('users')->where('id', $sender_userid)->first();
    
    DB::table('courtcase')->insert(
             array('userid' => $user_id, 
                   'accused' => $sender_userid,
                   'margin_id' => $id,
                   'type' => $enquiry,
                   'details' => $des,
                   'replys' => 0,
                   'status' => 2)
    );


     $subject = 'Reported User for margin unpaid '.$users->accountname.' ' .Config::get('app.name');
     
      $message = "<h3>Reported User for margin unpaid ".$users->accountname." Amount  ".$pack->amount." </h1><br><br>
                      <p>My name is ".$users->accountname." and phone number ".$users->phonenumber.",  Below is this information of the reported user .</p>
                      <ul>
                      <li>Name: ".$user->accountname."</li>
                      <li>Phone Number: ".$user->phonenumber."</li>
                      <li>Email: ".$rows->email."</li>
                      <li>Location: ".$user->country."</li>
                      </ul>";
      include 'emails.php';
      $headers  = 'MIME-Version: 1.0' . "\r\n";
      $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
      $to = Config::get('app.webmail');
      $headers .= 'From: '.$row->email."\r\n".
       'Reply-To: '.Config::get('app.webmail')."\r\n" .
        'X-Mailer: PHP/' . phpversion();
        $mail = mail($to, $subject, $EmaiMessage, $headers);
        if ($mail) {
          DB::table('marching')
        ->where('id', $id)
        ->update(array('active' => 2));
        DB::table('notification')->insert(
    array('userid' =>        $user_id,
         'type' =>        "confirm",
          'details' =>    "You have successfully report your upline and will be resolved soon", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-gavel")
);

         DB::table('notification')->insert(
    array('userid' =>        $sender_userid,
         'type' =>        "confirm",
          'details' =>    "You have been reported to court for ".$enquiry." by ".$users->accountname." Amount ".$pack->amount, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-gavel")
);
             echo'<div class="alert alert-success" role="alert">
 We have receive your case report and we will look forwark to contact both party. you can check your case here <a href="courtcases.php">My Court Cases</a>
</div>';
        }
        else{
          echo'<div class="alert alert-danger" role="alert">
  We cannot process your request now please try again.
</div>';
        }

}

function recomitmentWithdraw($user_id){
   $settings = DB::table('settings')->where('id', 1)->first();
     $id = DB::table('requestMaching')->where('userid', $user_id)->where('status', 'active')->count();
   if ($id) {
    $TheBalance = DB::table('bank')->where('userid', $user_id)->first();
  if ($id =="2" || $id=="4" || $id == "6" || $id == "8" || $id == "10" || $id == "12" || $id == "14" || $id == "16" || $id == "18" || $id == "20") {
        echo'<div class="alert alert-success" role="alert">
 Your Recommitment balance is '.$TheBalance->balance.' And you ready to withdraw, click here to withdraw <a href="wallet.php" class="btn btn-danger btn-sm">withdraw</a>
</div>';
     }
   }
  
} 

function recomitmentWithdrawPage($user_id){
   $settings = DB::table('settings')->where('id', 1)->first();
   $id = DB::table('requestMaching')->where('userid', $user_id)->where('status', 'active')->count();
   if ($id) {
    $TheBalance = DB::table('bank')->where('userid', $user_id)->first();
  if ($id =="2" || $id=="4" || $id == "6" || $id == "8" || $id == "10" || $id == "12" || $id == "14" || $id == "16" || $id == "18" || $id == "20") {
 
        echo '  <div class="col-md-12">
                  <div class="card text-white bg-default">
                    <div class="card-header card-header-transparent">Withdraw Balance</div>
                    <div class="card-body">
                    <form method="POST" action="">
                        <div class="form-group">
                          <label class="form-control-label">Amount</label>
                          <input type="text" name="amount" value="'.$TheBalance->balance.'" placeholder="Amount to withdraw" class="form-control">
                        </div>
                       
                        <div class="form-group">       
                          <input type="submit" value="withdraw" name="withdrawNow" class="btn btn-primary btn-block">
                        </div>
                      </form>
                        </div>
                  </div>
                 </div>';
     }else{
       echo'<div class="alert alert-danger" role="alert">
 Your Recommitment balance is not mature enough to withdraw kindly PH more to be able to withdraw your Recommitment balance.
</div>';
     }
   }
  
}


function recomitmentWithdrawNow($amount, $user_id){
$settings = DB::table('settings')->where('id', 1)->first();
$userPac = DB::table('requestMaching')->where('userid', $user_id)->first();
$startDate = time();
$timeX        =   date('Y-m-d H:i:s', strtotime('+'.$settings->getHelpDay.' day', $startDate));
$user = DB::table('users')->where('id', $user_id)->first();

 $sql =  DB::table('requestHelp')->insert(
                  array('userid' => $user_id, 
                         'package_id' => $userPac->package_id,
                         'pack_name' => $userPac->pack_name,
                         'amount' => $amount,
                         'profit' => $userPac->profit,
                         'timeReq' => $timeX,
                         'balance' => $amount,
                         'status' => 'pending')
);
if ($sql) {


 $users = DB::table('bank')->where('userid', $user_id)->first();
 $NewBalance = $users->balance - $amount;
   $Updater =  DB::table('bank')->where('userid', $user_id)->update(array('balance' => $NewBalance,'confirmed' => 0));

  if ($Updater) {
   
  $subject = 'You have successfully withdraw Recommitment balance ' .Config::get('app.name');
     
      $message = "<h3>You have successfully withdraw Recommitment ".$settings->currency."".$amount."</h1><br><br>
                      <p>You have successfully withdraw Recommitment balance ".$settings->currency."".$amount." and request is pending approval, kindly login to your acocunt to check current status</p>";
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
          'details' =>    "You have successfully withdraw Recommitment balance ".$settings->currency."".$amount, 
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
}
 


function  SponsorStats($user_id)
{

  $sponsorSt = DB::table('referral')->where('sponsor', $user_id)->where('userid', '!=', $user_id)->get();

    $parentD = DB::table('userdetails')->where('userid', $user_id)->first();
    echo '
   <div class="tree" style="
    margin-bottom: 15px;
">
     <ul>
     <li>
      <a href="#">Parent <br> '.$parentD->accountname.'<br>
      <i class="fa fa-star" style="color: red;"></i> <i class="fa fa-star" style="color: red;"></i> <i class="fa fa-star" style="color: red;"></i> <i class="fa fa-star" style="color: red;"></i> <i class="fa fa-star" style="color: red;"></i></a>
      <ul>';

  foreach ($sponsorSt as $key) {

      $spon = DB::table('userdetails')->where('userid', $key->userid)->first();
    echo '<li>
          <a href="#">Child <br> '.$spon->accountname.'</a>
          <ul>';
       
    $Grand = DB::table('referral')->where('sponsor', $key->userid)->get();
   foreach ($Grand as $rows) {
    $GrandDetails = DB::table('userdetails')->where('userid', $rows->userid)->first();
       echo '<li>
              <a href="#">Grand Child <br> '.$GrandDetails->accountname.'</a>
            </li>';

   }
        echo '  </ul>
        </li>
';
    }
     
  

    echo "  </li>
  </ul>
  </div>";
 
}
?>