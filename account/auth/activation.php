<?php include '_header.php';

require_once '../../app/init.php';



if (Auth::check()) redirect_to(App::url());

if (isset($_POST['submit']) && csrf_filter()) {
  
  Register::reminder($_POST['email'], @$_POST['g-recaptcha-response']);
        
  if (Register::passes()) {
    redirect_to('activation.php', array('activation_sent' => true));
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
                   <h3><?php echo _e('main.send_activation') ?></h3>
                </div>
              </div>
            </div>
            <!-- Form Panel    -->
            <div class="col-lg-6 bg-white">
    <?php if (Session::has('activation_sent')): Session::deleteFlash(); ?>
     
    <div class="alert alert-success alert-dismissable" style="background-color: #dc3545; color: #fff;">
                         <button aria-hidden="true" data-dismiss="alert" class="close" style="color: #fff;" type="button">×</button>
                              <h3><?php _e('main.check_email') ?></h3><br>
    <?php _e('main.activation_check_email') ?>
                       </div>

  <?php else: ?>
   
    
   <?php if (Register::fails()) {
      echo '<ul style="padding-left: 0px !important;">';
    foreach (Register::errors()->all('<div class="alert alert-danger alert-dismissable" style="background-color: #dc3545; color: #fff;">
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
                      <input type="text" name="email" id="activation-email" value="<?php echo set_value('email') ?>" class="input-material">
                      <label for="login-username" class="label-material"><?php _e('main.enter_email') ?></label>
                    </div>
                  
            <?php if (Config::get('auth.captcha')): ?>
          <div class="form-group recaptcha">
            <?php display_captcha_tag(); ?>
          </div>
        <?php endif ?>
                   
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