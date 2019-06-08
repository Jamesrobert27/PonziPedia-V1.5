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



//Margin Function Automated and Manual
/*
*/

function CreateTicket($user_id,$subject,$des){


	$sql = DB::table('tickets')->insert(
    array('userid' => $user_id,
    	'subject' => $subject,
    	'description' => $des,
    	'replied' => 0, 
      'moderator' => 'new', 
           'status' => 1)
);

if ($sql) {

	DB::table('notification')->insert(
    array('userid' =>        $user_id,
         'type' =>        "support",
          'details' =>    "You have submit support ticket and will be answered soon", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-envelope-o")
);

          echo'<div class="alert alert-success" role="alert">
  Your have succesfully create ticket, please wait for responds
</div>';
        }
else{
  echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
}

}





function UserTicketReply($user_id,$tiketId,$ticketpost){
     $user = DB::table('users')->where('id', $user_id)->first();
	$sql = DB::table('ticketsreply')->insert(
                  array('ticketid' => $tiketId,
                  	    'admin' => $user->username,
                  	    'userid' => $user_id,
                  	    'reply' =>  $ticketpost,
                        'replied' =>  'new',
                        'status' => 1)
         );
if ($sql) {
	$user = DB::table('tickets')->where('id', $tiketId)->first();
	$replied = $user->replied + 1;
	 DB::table('tickets')
        ->where('id', $tiketId)
        ->update(array('replied' => $replied));

DB::table('notification')->insert(
    array('userid' =>        $user_id,
         'type' =>        "support",
          'details' =>    "You replied to your ticket, we will respond soon", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-envelope-o")
);
          echo'<div class="alert alert-success" role="alert">
  Your have succesfully reply your ticket, please wait for responds
</div>';
        }
else{
  echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
}
}



function CloserUsrTicket($user_id,$tiketId){
	$sql = DB::table('tickets')
        ->where('id', $tiketId)
        ->update(array('status' => 0));

if ($sql) {
	DB::table('notification')->insert(
    array('userid' =>        $user_id,
         'type' =>        "support",
          'details' =>    "You have close your request ticket, you can always open another", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-window-close")
);
          echo'<div class="alert alert-success" role="alert">
  Your have succesfully close this support ticket
</div>';
        }
else{
  echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
}
}


function replyTicketCount($user_id){
	 $sql = DB::table('tickets')->where('userid', $user_id)->count();
	 echo $sql;
}






function CreateMessage($user_id,$MessagesNew){
	$sql = DB::table('messagess')->insert(
    array('from_user' => $user_id, 
    	   'to_user' => 1, 
    	   'message' => $MessagesNew,
         'reply' => 0, 
    	   'read' => 1)
);
	if ($sql) {
    DB::table('messagess')
        ->where('to_user', $user_id)
        ->update(array('reply' => 0));


	DB::table('notification')->insert(
    array('userid' =>        $user_id,
         'type' =>        "support",
          'details' =>    "New message sent to support by you", 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-comment")
);
          echo'<div class="alert alert-success" role="alert">
  Your have succesfully send support message
</div>';
        }
else{
  echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
}
}



function ReferalCount($user_id){
  $id = DB::table('referral')->where('sponsor', $user_id)->count();
  if ($id) {
    echo $id;
  }
  else{
    echo "0";
  }
}

function GetMarginNow($amount, $user_id){
$settings = DB::table('settings')->where('id', 1)->first();
$id = DB::table('requestHelp')->where('userid', $user_id)->sum('balance');
$userPac = DB::table('requestMaching')->where('userid', $user_id)->where('status', 'active')->orderBy('id', 'DESC')->first();
$user = DB::table('requestHelp')->where('userid', $user_id)->first();
$startDate = time();
$timeX        =   date('Y-m-d H:i:s', strtotime('+'.$settings->getHelpDay.' day', $startDate));
if ($userPac) {
 
 $sql =  DB::table('requestHelp')->insert(
                  array('userid' => $user_id,
                         'package_id' => $userPac->package_id,
                         'pack_name' => $userPac->pack_name,
                         'amount' => $amount,
                         'profit' => $amount,
                         'timeReq' => $timeX,
                         'balance' => $amount,
                         'status' => 'pending')
);


if ($sql) {
  $userGet = DB::table('requestMaching')->where('userid', $user_id)->orderBy('id', 'DESC')->first();
  $UpdateBalance = $userGet->profit - $amount;
  DB::table('requestMaching')
        ->where('userid', $userGet->userid)
        ->update(array('profit' => $UpdateBalance));

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


function BalanceSystem($user_id){
  $user_bank = DB::table('bank')->where('userid', $user_id)->first();
  echo $user_bank->balance;
}

function MessageNotofication($user_id){
  $users = DB::table('messagess')->where('to_user', $user_id)->where('reply', 1)->count();
  if ($users>0) {
   echo $users;
  }
  else{
    echo "0";
  }
}

function CommentSCount(){
  $users = DB::table('comments')->where('status', 1)->count();
  if ($users>0) {
   echo $users;
  }
  else{
    echo "0";
  }
}

 

function GuiderTreeView($user_id){
  $settings = DB::table('settings')->where('id', 1)->first();
  $user = DB::table('referral')->where('parent', $user_id)->get();

 
  if ($user >0) {
    foreach ($user as $row) {

      $users = DB::table('userdetails')->where('userid', $row->userid)->first();
      $parent = DB::table('userdetails')->where('userid', $row->parent)->first();
      $spon = DB::table('userdetails')->where('userid', $row->sponsor)->first();
      $percentage = $settings->guiderProfit;
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
                              <td> <a href="guider-panel.php?view='.$row->userid.'&id=1526132686xGXi9Kl9WganMjkgyDq3O9Bv8n7DAEDH1526132686xGXi9Kl9WganMjkgyDq3O9Bv8n7DAEDH&lan=ENG" class="btn btn-danger btn-block" style="border-radius: 0px !important;">View Details<a/></td>
                            </tr>';
     
    }
  }
    else {
      echo "<h1>You dont have any transaction recorded";
    }

} 

function WithdrawGuider($user_id, $amount){
  $settings = DB::table('settings')->where('id', 1)->first();
  $Pack = DB::table('packages')->first();
  
$startDate = time();
$timeX        =   date('Y-m-d H:i:s', strtotime('+'.$settings->getHelpDay.' day', $startDate));
$user = DB::table('users')->where('id', $user_id)->first();

 $sql =  DB::table('requestHelp')->insert(
                  array('userid' => $user_id,
                         'package_id' => $Pack->id,
                         'pack_name' => $Pack->packname,
                         'amount' => $amount,
                         'profit' => $amount,
                         'timeReq' => $timeX,
                         'balance' => $amount,
                         'status' => 'pending')
);
if ($sql) {


 $users = DB::table('bank')->where('userid', $user_id)->first();
 $NewBalance = $users->balance - $amount;
   $Updater =  DB::table('bank')->where('userid', $user_id)->update(array('balance' => $NewBalance,'confirmed' => 0));

  if ($Updater) {
   
  $subject = 'You have successfully withdraw Guider Profit balance ' .Config::get('app.name');
     
      $message = "<h3>You have successfully withdraw Guider Profit ".$settings->currency."".$amount."</h1><br><br>
                      <p>You have successfully withdraw Guider Profit balance ".$settings->currency."".$amount." and request is pending approval, kindly login to your acocunt to check current status</p>";
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
          'details' =>    "You have successfully withdraw Guider Profit balance ".$settings->currency."".$amount, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-usd")
);


  echo'<div class="alert alert-success" role="alert">
  You have successfully request help with your Guider Profit and you will Get-Help soon
</div>';
 }
 else{
  echo '<div class="alert alert-danger" role="alert">
  Theres error with your request, please try again later
</div>';
  }
}
}
?>