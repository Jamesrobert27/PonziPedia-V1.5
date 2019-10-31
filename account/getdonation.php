<?php include '_header.php';
 $messageR ="";

$settings = DB::table('settings')->where('id', 1)->first();
  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
      
    

 if ($settings->activationFee ==1) {

    $ActivationFees = DB::table('activationFee')->where('sender_id', $user_id)->first();
   if ($ActivationFees->payment_status =='pending' || $ActivationFees->payment_status =='waiting')
  {
    $ActivationUser = DB::table('activationReceiver')->where('id', 1)->first();
    if ($ActivationUser->userid == $user_id) {}
    else{
       redirect_to(App::url('account/index.php'));
    }
 
  }
}
 ?>  

<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Get Donation</h2>
            </div>
          </header>
      
     <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
    <?php if (isset($_POST['withdrawNow'])){
    $amount = preg_replace("/[^a-zA-Z0-9]+/", "", $_POST['amount']);
    $userPac = DB::table('requestMaching')->where('userid', $user_id)->where('profit', '>', 0)->where('status', 'active')->first();
    if ($userPac) {
      
    
    if ($amount > $userPac->profit) {
     echo '<div class="alert alert-danger" role="alert">
  You want to withdraw '.$settings->currency.''.$amount.'  which is higher than your balance '.$settings->currency.''.$userPac->profit.', Please try withdraw '.$settings->currency.''.$userPac->profit.' or below
</div>';
    }elseif ($amount <=0){
      echo '<div class="alert alert-danger" role="alert">
  You can not withdraw zoro balance.
</div>';
    }
    elseif ($amount <= $userPac->profit) {
       GetMarginNow($amount, $user_id);
   }
 }
 else{
    echo '<div class="alert alert-danger" role="alert">
  You currently cant withdraw now, you currently have payment to payout or wait till your PH is matured.
</div>';
}
} 

?>
        <div class="row">

         <?php  GetMarginView($user_id); ?> 
       
          </div>
     </section>


<?php include '_footer.php'; ?>
