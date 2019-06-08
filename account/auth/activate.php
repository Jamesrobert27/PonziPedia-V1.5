<?php include '_header.php';

require_once '../../app/init.php';

if (Auth::check() || (empty($_GET['reminder']) && !isset($_GET['activated']))) {
  redirect_to(App::url('account/auth/login.php'));
}

if (isset($_GET['reminder'])) {
  
  Register::activate($_GET['reminder']);
  
  if (Register::passes()) {
    redirect_to('activate.php?activated=1');
  }
}
?> 

    <div class="page login-page">
      <div class="container d-flex align-items-center">
        <div class="form-holder has-shadow">
          <div class="row">
            <!-- Logo & Information Panel-->
            <div class="col-lg-6">
              <div class="info d-flex align-items-center">
                <div class="content">
                  <div class="logo">
                    <h1><?php echo Config::get('app.name'); ?></h1>
                  </div>
                    <h3><?php _e('main.activate_success') ?></h3>
                </div>
              </div>
            </div>
            <!-- Form Panel    -->
            <div class="col-lg-6 bg-white">
    <?php if (isset($_GET['activated'])): ?>
    <div class="alert alert-success alert-dismissable" style="background-color: #dc3545; color: #fff;">
                         <button aria-hidden="true" data-dismiss="alert" class="close" style="color: #fff;" type="button">×</button>
                         <p><?php _e('main.activate_success_msg') ?></p>
                          <p><a href="login.php"><?php _e('main.login') ?></a></p>
     </div>
   
  <?php else: ?>
    <h3><?php _e('main.activate_account') ?></h3>
    <?php if (Register::fails()) {
      echo Register::errors()->first(null, '<div class="alert alert-danger alert-dismissable" style="background-color: #dc3545; color: #fff;">
                         <button aria-hidden="true" data-dismiss="alert" class="close" style="color: #fff;" type="button">×</button>
                             :message
                       </div> ');
    } ?>
    <p><a href="activation.php"><?php _e('main.resend_activation') ?></a></p>
  <?php endif ?>
      </div>

<?php include '_footer.php'; ?>