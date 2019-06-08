<?php include '_header.php';

if (isset($_GET['read'])) {
  $not_id = $_GET['read'];
  Notificationread($user_id, $not_id);
}
  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
      
 ?>

<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Timeline Activity</h2>
            </div>
          </header>
     <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
<div class="col-sm-12 col-md-12">
        <ul class="timeline">
            <li class="time-label">
                            <span class="bg-green">
                               <?php echo date("Y-m-d",strtotime(Auth::user()->joined)); ?>
                            </span>
            </li>
                <?php  TimelineView($user_id); ?>
              
              <!-- END timeline item -->
                      
          <li>
            <i class="fa fa-clock-o bg-gray"></i>
          </li>
        </ul>
      </div>
    </ul>
</div>
</section>


<?php include '_footer.php'; ?>
