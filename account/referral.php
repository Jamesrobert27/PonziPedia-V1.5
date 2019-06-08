<?php include '_header.php';

  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
      
 ?>

<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Affiliate</h2>
            </div>
          </header>
     
    <!-- Content Header (Page header) -->
   
    <!-- Main content -->
          <!-- Dashboard Counts Section-->
          <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
              <div class="row">
   <div class="col-md-6">
                  <div class="card text-white bg-success">
                    <div class="card-header card-header-transparent">Available Balance</div>
                    <div class="card-body">
                     <center><h1 class="card-title"><?php echo $settings->currency; ?> <?php echo $user_bank->balance; ?></h1>
                      
                     </center> 
                      
                    </div>
                  </div>
                </div>


               <div class="col-md-6">
                  <div class="card text-white bg-danger">
                    <div class="card-header card-header-transparent">Total Referral</div>
                    <div class="card-body">
                     <center><h1 class="card-title">Total <?php ReferalCount($user_id); ?></h1></center> 
                        <div class="form-group">
                          <label class="form-control-label" style="color: #fff;">Your Referral Link</label>
                          <?php 
                          $user = DB::table('activationFee')->where('sender_id', $user_id)->where('payment_status', 'confirm')->first();
                          if ($user) {
                            ?>
                            
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
                            <?php
                          } else { echo '<br><h3>You must pay your activation fees to get your affiliate link</h3></center> '; }?>
                    </div>
                  </div>
                </div>
                  </div>
                </div>
                 <div class="col-lg-12">
                  <div class="card">
                   
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">All User Referral</h3>
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
                            </tr>
                          </thead>
                          <tbody>
                          <?php ReferralView($user_id); ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              
</section>


<?php include '_footer.php'; ?>
