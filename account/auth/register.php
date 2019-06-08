<?php include '_header.php';
require_once '../../app/core.php'; 
CheckSiteRegistration();
if (isset($_GET['ref'])) {
  if (!empty($_GET['ref'])){
$referralU = DB::table('userdetails')->where('refid', $_GET['ref'])->first();
 if ($referralU->id != "") {
  Session::set('ref', $referralU->userid);
 }
}
}

?>

<?php 


if (Auth::check()) redirect_to(App::url('account'));

if (isset($_POST['submit']) && csrf_filter()) {
  
  Register::signup($_POST);

  if (Register::passes()) {
   
    if (Config::get('auth.email_activation')) {
      redirect_to('signup.php', array('signup_complete' => true));
    } else {
      Auth::login($_POST['email'], $_POST['pass1']);

      $redirect = Config::get('auth.login_redirect');
      redirect_to($redirect != '' ? $redirect : App::url());
    }
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
                  <p>The best <?php echo $settings->profit; ?>% ROI platform with flexible packages</p>
                </div>
              </div>
            </div>
            <!-- Form Panel    -->
         <div class="col-lg-6 bg-white">
          <?php if (Session::has('signup_complete')): Session::deleteFlash(); ?>
    <h3><?php _e('main.check_email') ?></h3>
    <?php _e('main.activation_check_email') ?>
  <?php else: ?>

    <?php if (Register::fails()) {
      foreach (Register::errors()->all('<div class="alert alert-danger alert-dismissable" style="background-color: #dc3545; color: #fff;">
                         <button aria-hidden="true" data-dismiss="alert" class="close" style="color: #fff;" type="button">×</button>
                             :message
                       </div> ') as $error) {
         echo $error;
      }
    } ?>

              <div class="form d-flex align-items-center">
                <div class="content">
                  <form action="" method="POST" class="form-validate">
                    <?php csrf_input() ?>

                    <div class="form-group">
                      <input id="signup-username" type="text" name="username" required data-msg="Please enter your username" class="input-material">
                      <label for="register-username" class="label-material">User Name</label>
                    </div>
                    <div class="form-group">
                      <input id="signup-email" type="email" name="email" required data-msg="Please enter a valid email address" class="input-material">
                      <label for="register-email" class="label-material">Email Address      </label>
                    </div>
                    <div class="form-group">
                      <input id="signup-pass1" type="password" name="pass1" required data-msg="Please enter your password" class="input-material" autocomplete="off">
                      <label for="register-password" class="label-material">password        </label>
                    </div>
                     <div class="form-group">
                      <input id="signup-pass2" type="password" name="pass2" required data-msg="Please enter your password" class="input-material" autocomplete="off">
                      <label for="register-password" class="label-material">Confirm password</label>
                    </div>
                    <div class="form-group terms-conditions">
                      <input id="register-agree" name="registerAgree" type="checkbox" required value="1" data-msg="Your agreement is required" class="checkbox-template">
                      <label for="register-agree">Agree the terms and policy</label>
                    </div>
                       <?php echo UserFields::build('signup') ?>

      <?php if (Config::get('auth.captcha')): ?>
        <p>
          <?php display_captcha(); ?>
        </p>
      <?php endif ?>
                    <div class="form-group">
                      <button type="submit" name="submit" class="btn btn-primary">Register</button>
                    </div>

                      <?php if (count(Config::get('auth.providers'))): ?>
            <p><?php _e('main.login_with2') ?></p>
           
           <p>
              <?php foreach (Config::get('auth.providers', array()) as $key => $provider): ?>
                <a href="<?php echo App::url("oauth.php?provider={$key}") ?>"><?php echo $provider ?></a>
              <?php endforeach ?>
            </p>
        <?php endif ?>
  <?php endif ?>
                  </form><small>Already have an account? </small><a href="login.php" class="signup">Login</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
<?php include '_footer.php'; ?>