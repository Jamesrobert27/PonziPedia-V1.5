<?php include '_header.php';
 $messageR ="";


  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
      

 ?>

<div class="content-inner">
          <!-- Page Header-->

          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Support System</h2>
            </div>
          </header>
      
     <section class="dashboard-counts no-padding-bottom">
        <div class="row">
       <div class="statistics col-lg-3 col-12">
                  <div class="statistic d-flex align-items-center bg-white has-shadow">
                    <img src="<?php echo asset_url('img/libra.png') ?>" alt="..." class="img-fluid img-responsive" style="width: 40px;margin-right: 20px;">
                    <div class="text"><strong><?php CourtCaseCount($user_id); ?></strong><br><small>My Court Cases</small></div>
                  </div>
                 
                 
                </div>
         <div class="col-md-9">

          <?php  CourtCaseView($user_id); ?>
        </div>
       
     </section>


<?php include '_footer.php'; ?>
