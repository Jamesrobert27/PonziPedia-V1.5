<?php include '_header.php';

  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
      
 ?>

<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Bank Account</h2>
            </div>
          </header>
    
     <?php 
           if (isset($_POST['withdrawNow'])){
           $amount = preg_replace("/[^a-zA-Z0-9]+/", "", $_POST['amount']);

            $TheBalance = DB::table('bank')->where('userid', $user_id)->first();
            if($amount == "0"){
                 echo'<div class="alert alert-danger" role="alert">
 Your cant withdraw '.$settings->currency.' '.$amount.', Please try exactly your pending balance
</div>';
            }
            elseif ($amount > $TheBalance->balance || $amount <$TheBalance->balance) {
              echo'<div class="alert alert-danger" role="alert">
 Your cant withdraw '.$settings->currency.' '.$amount.', as your total balance is '.$settings->currency.' '.$TheBalance->balance.'. Please try exactly your pending balance
</div>';
            }else{
               recomitmentWithdrawNow($amount, $user_id);
            }
          
              } 
      ?>
    <!-- Main content -->
          <!-- Dashboard Counts Section--> 
          <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
              <div class="row">
              <div class="col-md-6">
                   <div class="col-md-12">
                  <div class="card text-white bg-success">
                    <div class="card-header card-header-transparent">Available Balance</div>
                    <div class="card-body">
                     <center><h1 class="card-title"><?php echo $settings->currency; ?><?php echo $user_bank->balance; ?></h1></center> 
                      
                    </div>
                  </div>
                 </div>
                  
                    <?php recomitmentWithdrawPage($user_id); ?>
                       
                  

                 </div>
                <div class="col-md-6">
                  <div class="card text-white bg-danger">
                    <div class="card-header card-header-transparent"><?php echo Config::get('app.name'); ?> Account Details</div>
                    <p style=" padding-left: 10px;">Please note! this is not your personal account details, this is auto generated <?php echo Config::get('app.name'); ?> account details for each member, to view your bank account details <a href="account.php?edit=<?php echo $user_id; ?>">Click Here</a>, 
                    <div class="card-body">
                       <ul class="list-group" style="background-color: #000000; color: #000000;">
                            <li class="list-group-item">
                                Account Name <a class="pull-right"><?php echo Auth::user()->username; ?></a>
                            </li>
                                                            <li class="list-group-item">
                                    Account Number <a class="pull-right"><?php echo $user_bank->accountNum; ?></a>
                                </li>
                                                        <li class="list-group-item">
                                Account Status <a class="pull-right" style="color: green;"><?php echo $user_bank->status; ?></a>
                            </li>
                       </ul>
                    </div>
                  </div>
                </div>
                 <div class="col-lg-12">
                  <div class="card">
                   
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Account Statement/Transactions</h3>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Date</th>
                              <th>Amount</th>
                              <th>Type</th>
                              <th>Description</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php transactionView($user_id); ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
</section>


<?php include '_footer.php'; ?>
