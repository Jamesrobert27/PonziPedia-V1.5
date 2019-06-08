<?php include '_header.php'; 
// +------------------------------------------------------------------------+
// | @author Olakunlevpn (Olakunlevpn)
// | @author_url 1: http://www.maylancer.cf
// | @author_url 2: https://codecanyon.net/user/gr0wthminds
// | @author_email: olakunlevpn@live.com   
// +------------------------------------------------------------------------+
// | PonziPedia - Peer 2 Peer 50% ROI Donation System
// | Copyright (c) 2018 PonziPedia. All rights reserved.
// +------------------------------------------------------------------------+




  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
?>
        <div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Dashboard</h2>
            </div>
          </header>
      
          <?php
           AccusedCaseView($user_id);
            $TheBalance = DB::table('bank')->where('userid', $user_id)->first();
            if ($TheBalance->balance > "2000") {
             recomitmentWithdraw($user_id); 
            }
            
           ?>
          <!-- Dashboard Counts Section-->
          <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
              <div class="row bg-white has-shadow"> 
                <!-- Item -->
                <div class="col-xl-4 col-sm-6">
                  <div class="item d-flex align-items-center">
                    <img src="<?php echo asset_url('img/1.png') ?>" style="width: 40px;">
                    <div class="title"><span>Recommit<br>Balance</span>
                      <div class="progress">
                        <div role="progressbar" style="width: <?php BalanceSystem($user_id); ?>%; height: 4px;" aria-valuenow="<?php BalanceSystem($user_id); ?>" aria-valuemin="0" aria-valuemax="5000" class="progress-bar bg-violet"></div>
                      </div>
                    </div>
                    <div class="number"><strong><?php echo $settings->currency; ?><?php BalanceSystem($user_id); ?></strong></div>
                  </div>
                </div>
                <!-- Item -->
                <div class="col-xl-4 col-sm-6">
                  <div class="item d-flex align-items-center">
                    <img src="<?php echo asset_url('img/2.png') ?>" style="width: 40px;">
                    <div class="title"><span>Pending<br>Payout</span>
                      <div class="progress">
                        <div role="progressbar" style="width: <?php PendingRequestBalance($user_id); ?>%; height: 4px;" aria-valuenow="<?php PendingRequestBalance($user_id); ?>" aria-valuemin="0" aria-valuemax="10000" class="progress-bar bg-red"></div>
                      </div>
                    </div>
                    <div class="number"><strong><?php echo $settings->currency; ?><?php PendingRequestBalance($user_id); ?></strong></div>
                  </div>
                </div>
                <?php  
                  $idamount = DB::table('requestHelp')->where('userid', $user_id)->sum('amount');
                  if ($idamount >0) {
                   ?>
                  
                <div class="col-xl-4 col-sm-6">
                  <div class="item d-flex align-items-center">
                    <img src="<?php echo asset_url('img/3.png') ?>" style="width: 40px;">
                    <div class="title"><span>Pending<br>Received</span>
                      <div class="progress">
                        <div role="progressbar" style="width: <?php requestHelpBalance($user_id); ?>%; height: 8px;" aria-valuenow="<?php requestHelpBalance($user_id); ?>" aria-valuemin="0" aria-valuemax="10000" class="progress-bar bg-green"></div>
                      </div>
                    </div>
                    <div class="number"><strong><?php echo $settings->currency; ?><?php requestHelpBalance($user_id); ?></strong></div>
                  </div>
                </div>
                   <?php
                  }
                  else{
                      ?>
                         
                <div class="col-xl-4 col-sm-6">
                  <div class="item d-flex align-items-center">
                    <img src="<?php echo asset_url('img/3.png') ?>" style="width: 40px;">
                    <div class="title"><span>Profit<br>Balance</span>
                      <div class="progress">
                        <div role="progressbar" style="width: <?php ProfitBalance($user_id); ?>%; height: 8px;" aria-valuenow="<?php ProfitBalance($user_id); ?>" aria-valuemin="0" aria-valuemax="10000" class="progress-bar bg-green"></div>
                      </div>
                    </div>
                    <div class="number"><strong><?php echo $settings->currency; ?><?php ProfitBalance($user_id); ?></strong></div>
                  </div>
                </div>
                      <?php

                     }
                   ?>
                 
             
              </div>
            </div>
          </section>
        <!-- Dashboard Counts Section-->
          <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
              <div class="row bg-white has-shadow" style="margin-top: -30px;"> 
                <?php 
                  GuiderDetailsView($user_id);
                ?>

              </div>
            </div>
          </section>

          <!-- Dashboard Header Section    -->
          <section class="dashboard-header">
            <div class="container-fluid">
              <div class="row">
                <div class="col-md-12">
              
             

                <?php 
                  ActivationReceiverView($user_id); 
                
                ?>
              </div>
               
                   <?php 
                  
             
              
               if ($settings->activationFee ==1) {
               
              
                     GetActivationView($user_id);
                 }
          ?>
              
             
                <div class="col-md-12">
              
             

                <?php  ReceiverView($user_id);  ?>
              </div>
               <div class="col-lg-4">
              
               <?php RequestHelp($user_id); ?>
                <?php Requestmargin($user_id); ?>
              </div>
                <!-- Line Chart            -->
           <div class="chart col-lg-8 col-12">
           <?php GetMargin($user_id); ?>
         
          
            </div>
          
       <div class="col-md-12">
        <div class="row">
          <?php 
            NewMemberMatching($user_id);
          ?>
        </div>
        </div>
          </div>
          </section>

        


          <!-- Projects Section-->
          <section class="projects no-padding-top">
            <div class="container-fluid">
              <!-- Project-->
              <div class="project">
                <div class="row bg-white has-shadow">
                  <div class="left-col col-lg-6 d-flex align-items-center justify-content-between">
                    <div class="project-title d-flex align-items-center">
                      <div class="image has-shadow"><img src="<?php echo asset_url('img/megaphone.png') ?>" style="width: 40px;" alt="..." class="img-fluid"></div>
                      <div class="text">
                        <h3 class="h4"><?php echo Config::get('app.name'); ?></h3><small>Forum | Join Discussion</small>
                      </div>
                    </div>
                    <div class="project-date"><span class="hidden-sm-down"><?php echo Config::get('app.name'); ?></span></div>
                  </div>
                  <div class="right-col col-lg-6 d-flex align-items-center">
                    <div class="time"><i class="fa fa-clock-o"></i><?php echo date('Y-m-d H:i:s'); ?></div>
                    <div class="comments"><i class="fa fa-comment-o"></i><?php 
                    $users = DB::table('users')->count(); echo $users; ?></div>
                    <div class="project-progress">
                      <a href="discussion.php" class="btn btn-primary btn-block">Join Discussion</a>
                    </div>
                  </div>
                </div>
              </div>
         </div>
       </section>
<?php include '_footer.php'; ?>