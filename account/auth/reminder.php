<?php include '_header.php';

require_once '../../app/init.php';

if (Auth::check()) redirect_to(App::url());

if (isset($_POST['submit']) && csrf_filter()) {

  Password::reminder($_POST['email'], @$_POST['g-recaptcha-response']);
        
  if (Password::passes()) {
    redirect_to('reminder.php', array('reminder_sent' => true));
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
              
      <?php if (Session::has('reminder_sent')): Session::deleteFlash(); ?>
    
    <div class="alert alert-success alert-dismissable" style="background-color: #dc3545; color: #fff;">
                         <button aria-hidden="true" data-dismiss="alert" class="close" style="color: #fff;" type="button">×</button>
                             <h3><?php _e('main.check_email') ?></h3><br<
                             <?php _e('main.reminder_check_email') ?>
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
                      <input id="login-username" type="text" name="email" required data-msg="Please enter your email" class="input-material">
                      <label for="login-username" class="label-material"><?php _e('main.enter_email') ?></label>
                    </div>
                   
                    <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.continue') ?></button>
                    <!-- This should be submit button but I replaced it with <a> for demo purposes-->
                  </form>Registered Member? <a href="login.php" class="forgot-pass">Login</a><br><small>Do not have an account? </small><a href="register.php" class="signup">Signup</a>
                </div>
              </div>
            </div>
          </div>
        </div>
          <?php endif ?>
      </div>

<?php include '_footer.php'; ?>