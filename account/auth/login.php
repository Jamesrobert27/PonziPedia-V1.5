<?php include '_header.php';

if (Auth::check()) redirect_to(App::url('account'));

if (isset($_POST['submit']) && csrf_filter()) {
  Auth::login($_POST['email'], $_POST['password'], isset($_POST['remember']));

  if (Auth::passes()) {
    $url = Config::get('auth.login_redirect');
    
    $url = empty($url) ? App::url() : $url;
    
    redirect_to($url);
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


                   <div class="col-lg-6 bg-white">
                      <?php if (Auth::fails()) {
                      
                     foreach (Auth::errors()->all('<div class="alert alert-danger alert-dismissable" style="background-color: #dc3545; color: #fff;">
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
                      <input name="email" id="email" value="<?php echo set_value('email') ?>" required data-msg="Please enter your username" class="input-material">
                      <label for="login-username" class="label-material">Username</label>
                    </div>
                    <div class="form-group">
                      <input id="login-password" type="password" name="password" required data-msg="Please enter your password" class="input-material">
                      <label for="login-password" class="label-material">Password</label>
                    </div>

                     <div class="form-group">
                      <label><input type="checkbox" name="remember" value="1" <?php echo set_checkbox('remember', '1') ?>> <?php _e('main.remember') ?></label>
                    </div>

                    <button type="submit" name="submit" class="btn btn-primary btn-lg">Login</button>

  <?php if (count(Config::get('auth.providers'))): ?>
      <p><?php _e('main.login_with2') ?></p>
      
      <p>
        <?php foreach (Config::get('auth.providers', array()) as $key => $provider): ?>
          <a href="<?php echo App::url("oauth.php?provider={$key}") ?>"><?php echo $provider ?></a>
        <?php endforeach ?>
      </p>
  <?php endif ?>
                    <!-- This should be submit button but I replaced it with <a> for demo purposes-->
                  </form><a href="reminder.php"><?php _e('main.forgot_pass') ?></a><br>
                  <a href="activation.php"><?php _e('main.resend_activation') ?></a><br>
                  Do not have an account? <a href="register.php"> Signup</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

<?php include '_footer.php'; ?>