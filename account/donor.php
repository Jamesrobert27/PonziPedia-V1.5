<?php include '_header.php';
 $messageR ="";


  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
    
 $settings = DB::table('settings')->where('id', 1)->first();
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
              <h2 class="no-margin-bottom">Provide Donation</h2>
            </div>
          </header>
        <?php if (isset($_POST['submitLocked'])){
    $pack_id = $_POST['pack_id'];
    $pack_code = $_POST['pack_idCode'];
    $user_pack =  DB::table('packages')->where('id', $pack_id)->first();
    if ($pack_code =="") {
      echo'<div class="alert alert-danger" role="alert">
       Empty! Please insert your package activation code, 
</div>';
    }
    else if ($user_pack->codes ==$pack_code) {
     ChoosePackageNowLocked($pack_id, $user_id);
    }
    else{
         echo'<div class="alert alert-danger" role="alert">
         You have entered invalid package activation code, please contact administrator for valid codes
</div>';
        }
    }
    

 if (isset($_POST['submit'])){
    $pack_id = $_POST['pack_id'];
    ChoosePackageNow($pack_id, $user_id);
}
?>
     <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
        <div class="row">
       <?php  packagesView(); ?>
        </div>
         </div>
     </section>


<?php include '_footer.php'; ?>
