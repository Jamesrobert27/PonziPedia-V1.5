<?php include '_header.php';
if (!Auth::userCan('guider')) page_restricted();

  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
  
 ?>
 
<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Guider Panel</h2>
            </div>
          </header>
     

          <?php if (isset($_POST['WithdrawGuider']) && csrf_filter()) {
                $amount = preg_replace("/[^a-zA-Z0-9]+/", "", $_POST['amount']);

                if ($amount > $user_bank->balance) {
                   echo '<div class="alert alert-danger" role="alert">
           Your balance '.$settings->currency.' '.$user_bank->balance.' is lower than the amount you want to withdraw '.$settings->currency.' '.$amount.'
             </div>';
                }else if ($amount < $settings->GuiderMin) {
                   echo '<div class="alert alert-danger" role="alert">
           Minimum withdraw set to be '.$settings->currency.' '.$settings->GuiderMin.' above, please try other balance
             </div>';
                }else{
                     WithdrawGuider($user_id, $amount);
                }
              }
             ?>


    <!-- Content Header (Page header) -->
             <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
              <div class="row bg-white has-shadow"> 
                <!-- Item -->
                <div class="col-xl-4 col-sm-6">
                  <div class="item d-flex align-items-center">
                    <img src="<?php echo asset_url('img/1.png') ?>" style="width: 40px;">
                    <div class="title"><span>Total<br>Earned</span>
                      <div class="progress">
                        <div role="progressbar" style="width: <?php BalanceEarnedGuiders($user_id); ?>%; height: 4px;" aria-valuenow="<?php BalanceEarnedGuiders($user_id); ?>" aria-valuemin="0" aria-valuemax="5000" class="progress-bar bg-violet"></div>
                      </div>
                    </div>
                    <div class="number"><strong><?php echo $settings->currency; ?><?php BalanceEarnedGuiders($user_id); ?></strong></div>
                  </div>
                </div>
                <!-- Item -->
                <div class="col-xl-4 col-sm-6">
                  <div class="item d-flex align-items-center">
                    <img src="<?php echo asset_url('img/2.png') ?>" style="width: 40px;">
                    <div class="title"><span>Under<br>My Guide</span>
                      <div class="progress">
                        <div role="progressbar" style="width: <?php GuiderTotalUser($user_id); ?>%; height: 4px;" aria-valuenow="<?php GuiderTotalUser($user_id); ?>" aria-valuemin="0" aria-valuemax="10000" class="progress-bar bg-red"></div>
                      </div>
                    </div>
                    <div class="number"><strong><?php  GuiderTotalUser($user_id); ?></strong></div>
                  </div>
                </div>
              
                  
                <div class="col-xl-4 col-sm-6">
                  <div class="item d-flex align-items-center">
                    <img src="<?php echo asset_url('img/3.png') ?>" style="width: 40px;">
                    <div class="title"><span>Current<br>Balance</span>
                      <div class="progress">
                        <div role="progressbar" style="width: <?php BalanceSystem($user_id); ?>%; height: 8px;" aria-valuenow="<?php BalanceSystem($user_id); ?>" aria-valuemin="0" aria-valuemax="10000" class="progress-bar bg-green"></div>
                      </div>
                    </div>
                    <div class="number"><strong><?php echo $settings->currency; ?><?php BalanceSystem($user_id); ?></strong></div>
                  </div>
                </div>
                  
                   
             
              </div>

            </div>
          </section>


           <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
              <div class="row bg-white has-shadow" style="
    padding-bottom: 0px;
    padding-top: 0px;
"> 
                <div class="col-lg-12">                           
               
                    <div class="card-body">
                      <label class="form-control-label">Your Referral Link</label>
                        <div class="input-group">
                              
                                <input type="text" value="<?php
                                $refs = DB::table('userdetails')->where('userid', $user_id)->first();
                                 echo App::url('account/auth/register.php?ref='.$refs->refid); ?>" id="ReferralLink" class="form-control">
                                <div class="input-group-append">
                                  <button onclick="myFunction()" class="btn btn-primary">copy!</button>
                                </div>
                                  <script>
                        function myFunction() {
                           var copyText = document.getElementById("ReferralLink");
                          copyText.select();
                          document.execCommand("Copy");
                          alert("Copied Referral Link: " + copyText.value);
                         }
                        </script>
                              </div>
                    </div>
                  </div>
              
</div>
</div>

</section>


  <?php if (!isset($_GET['view'])){
    ?>
           <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
              <div class="row bg-white has-shadow" style="
    padding-bottom: 0px;
    padding-top: 0px;
"> 
                <div class="col-lg-12">                           
               
                    <div class="card-body">
                      <form class="form-inline" method="POST" action="">
                        <?php csrf_input() ?>
                        <div class="form-group">
                          <label for="inlineFormInput" class="sr-only">Amount (Minimum <?php echo $settings->currency; ?>10000)</label>
                          <input id="inlineFormInput" name="amount" value="<?php echo $user_bank->balance; ?>" type="number" placeholder="Withdrawal Balance" class="mr-3 form-control" style="
    width: 800px;
" <?php if($user_bank->balance < $settings->GuiderMin){echo "disabled";} ?>>
                        </div>
                        <div class="form-group">
                          <button type="submit" <?php if($user_bank->balance < $settings->GuiderMin){echo "disabled";} ?> name="WithdrawGuider" class="btn btn-primary">Withdraw</button>
                        </div>
                        <?php if($user_bank->balance < $settings->GuiderMin){echo '<span style="color: #f40000;">You cant withdraw till you blance is more than '.$settings->currency.' '.$settings->GuiderMin.' </span>';} ?>
                      </form>
                    </div>
                  </div>
             
</div>
</div>

</section>
<?php
}
?>
    <!-- Main content -->
          <!-- Dashboard Counts Section-->
          <section class="dashboard-counts no-padding-bottom">
           
              <div class="row">
   <?php 

   if (!empty($_GET['view'])){ 

      $viewBank = DB::table('bank')->where('userid', $_GET['view'])->first(); 
    $viewU = DB::table('userdetails')->where('userid', $_GET['view'])->first();
        if ($viewU) {
     $viewUSER = DB::table('users')->where('id', $_GET['view'])->first(); 
    
    ?>
<div class="col-lg-6">
                  <div class="card">
                    <div class="card-close">
                      <div class="dropdown">
                        <button type="button" id="closeCard1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                        <div aria-labelledby="closeCard1" class="dropdown-menu dropdown-menu-right has-shadow"></div>
                      </div>
                    </div>
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4"><?php echo $viewU->accountname; ?> | Profile</h3>
                    </div>
                    <div class="card-body" style="padding-left: 3px;padding-right: 0px;">
                       <center><img class="img-fluid rounded-circle" src="<?php echo asset_url('img/avatar.png') ?>" style="width: 80px;" alt="User profile picture"></center>

                        <h3 class="profile-username text-center"><?php echo $viewU->accountname;  ?></h3>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <i class="fa fa-user"></i> <a class="pull-right"><?php echo $viewU->accountname;  ?></a>
                            </li>
                                                            <li class="list-group-item">
                                    <i class="fa fa-phone"></i> <a class="pull-right"><?php echo $viewU->phonenumber;  ?></a>
                                </li>
                                                        <li class="list-group-item">
                                <i class="fa fa-envelope-o"></i> <a class="pull-right"><?php echo $viewUSER->email;  ?></a>
                            </li>
                              </li>
                                                        <li class="list-group-item">
                                <i class="fa fa-map"></i> <a class="pull-right"><?php echo $viewU->country;  ?></a>
                            </li>
                              </li>
                                                        <li class="list-group-item">
                                <i class="fa fa-map-marker"></i> <a class="pull-right"><?php echo $viewU->state;  ?></a>
                            </li>


                            <div class="box box-success">

                                <div class="box-header with-border">
                                                                            <h3 class="box-title">Default Bank Account</h3>
                                                                    </div>
                                <div class="box-body">
                                    
                                        <ul class="list-group list-group-unbordered">
                                            <li class="list-group-item">
                                                <i class="fa fa-bank"></i> Bank Name <a class="pull-right"><?php echo $viewU->bankname;  ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fa fa-bank"></i> Account Name <a class="pull-right"><?php echo $viewU->accountname;  ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fa fa-bank"></i> Account Number <a class="pull-right"><?php echo $viewU->accountnumber;  ?></a>
                                            </li>

                                            <li class="list-group-item">
                                                <i class="fa fa-bank"></i> Account Type <a class="pull-right"><?php echo $viewU->accounttype;  ?></a>
                                            </li>
                                        </ul>
                    </div>
                  </div>
                </div>
                
              
            </div>
        </ul>
    </div> 

        <!-- Line Chart            -->
                <div class="chart col-lg-6 col-12">

                 
                   <div class="work-amount card">
                   
                    <div class="card-body" style="padding-left: 3px;padding-right: 0px;">
                       <div class="col-md-12">
                   <div class="col-md-12">
                  <div class="card text-white bg-success">
                    <div class="card-header card-header-transparent"><?php echo $viewU->accountname; ?> Balance</div>
                    <div class="card-body">
                     <center><h1 class="card-title"><?php echo $settings->currency; ?><?php echo $viewBank->balance; ?></h1></center> 
                      
                    </div>
                  </div>
                 </div>
                    
                    </div>
                  </div>
                
             
             
                    <?php
                   if ($viewUSER->role_id ==3) {
                      echo '  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;"><h3>YThis account has been blocked for not providing payment donation to others and its can be retrieve by contacting the online support</h3></div>';
                  
                  
                   }
                   
                    ?>
            </div>
                  <?php
   }
   else{
redirect_to(App::url('account/guide-panel.php'));

   }
 }else{
 
        SponsorStats($user_id);

        echo '<br><hr><div class="clearfix"></div>';
  ?>


                 <div class="col-lg-12">
                  <div class="card">
                   
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">All User User Your Guidiance</h3>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Date</th>
                              <th>Amount</th>
                              <th>Earned</th>
                              <th>User</th>
                              <th>Sponser</th>
                              <th>Guider</th>
                              <th>Package</th>
                              <th>Status</th>
                              <th>Action </th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php GuiderTreeView($user_id); ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
  <?php } ?>
           
              

            </div>
     
</section>


<?php include '_footer.php'; ?>
