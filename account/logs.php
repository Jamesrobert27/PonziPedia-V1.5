<?php include '_header.php';

  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
      
 ?>

<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Payment Logs</h2>
            </div>
          </header>
     
    <!-- Content Header (Page header) -->
   
    <!-- Main content -->
          <!-- Dashboard Counts Section-->
          <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
    
                 <div class="col-lg-12">
                  <div class="card">
                   
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">All Marching Payments</h3>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Date</th>
                              <th>Amount</th>
                              <th>Sender</th>
                              <th>Receiver</th>
                              <th>Total Payment</th>
                              <th>Package</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php PaymentLogs($user_id); ?>
                          </tbody>
                        </table>
                      </div>
                    </div>

                       <div class="card-header d-flex align-items-center">
                      <h3 class="h4">All Activation Fees Payments</h3>
                    </div>
                    <div class="card-body">
                      <div class="table-responsive">
                        <table class="table">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Date</th>
                              <th>Amount</th>
                              <th>Sender</th>
                              <th>Receiver</th>
                              <th>Total Payment</th>
                              <th>Package</th>
                              <th>Status</th>
                            </tr>
                          </thead>
                          <tbody>
                          <?php ActivationLogs($user_id); ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              
</section>


<?php include '_footer.php'; ?>
