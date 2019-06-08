<?php include '_header.php';

require_once '../../app/init.php';

if (Auth::check() || (empty($_GET['reminder']) && !Session::has('password_updated'))) {
  redirect_to(App::url());
}

if (isset($_POST['submit']) && csrf_filter()) {
  
  Password::reset($_POST['pass1'], $_POST['pass2'], $_POST['reminder']);
        
  if (Password::passes()) {
    redirect_to('reset.php', array('password_updated' => true));
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
                   <h3><?php echo _e('main.recover_pass') ?></h3>
                </div>
              </div>
            </div>
            <!-- Form Panel    -->
            <div class="col-lg-6 bg-white">


  <?php if (Session::has('password_updated')): Session::deleteFlash(); ?>
     <div class="alert alert-success alert-dismissable" style="background-color: #dc3545; color: #fff;">
                         <button aria-hidden="true" data-dismiss="alert" class="close" style="color: #fff;" type="button">×</button>
                         <h3><?php _e('main.reset_success') ?></h3>
    <p><?php _e('main.reset_success_msg') ?></p>
    <p><a href="login.php"><?php _e('main.login') ?></a></p>
     </div>
  <?php else: ?>
   
    
    <?php if (Password::fails()) {
      echo '<ul style="padding-left: 0px !important;">';
      foreach (Password::errors()->all('<div class="alert alert-danger alert-dismissable" style="background-color: #dc3545; color: #fff;">
                         <button aria-hidden="true" data-dismiss="alert" class="close" style="color: #fff;" type="button">×</button>
                             :message
                       </div> ') as $error) {
         echo $error;
      }
      echo '</ul>';
    } ?> 
    
              <div class="form d-flex align-items-center">
                <div class="content">
                  <form method="post" class="form-validate">
                    <div class="form-group">
                      <input id="login-username" type="password" name="pass1" id="reset-pass1" class="input-material">
                      <label for="login-username" class="label-material"><?php _e('main.newpassword') ?></label>
                    </div>
                     <div class="form-group">
                      <input id="login-username" type="password" name="pass2" id="reset-pass2" class="input-material">
                      <label for="login-username" class="label-material"><?php _e('main.newpassword_confirmation') ?></label>
                    </div>
                    <input type="hidden" name="reminder" value="<?php echo escape($_GET['reminder']) ?>">
                   
                    <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.change_pass') ?></button>
                    <!-- This should be submit button but I replaced it with <a> for demo purposes-->
                  </form><a href="recovery.php" class="forgot-pass"><?php _e('main.new_reminder') ?></a>
                </div>
              </div>
            </div>
          </div>
        </div>
          <?php endif ?>
      </div>

<?php include '_footer.php'; ?>