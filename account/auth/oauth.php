<?php include '_header.php';

require_once '../../app/init.php';

$settingsPage = App::url('account/settings.php?p=connect');

$scopes = array(
  'facebook'  => array('email', 'user_about', 'user_birthday', 'user_website'),
  'google'    => array('email', 'profile', 'gplus_me', 'userinfo_profile'),
  'linkedin'  => array('r_fullprofile', 'r_emailaddress', 'r_contactinfo'),
  'microsoft' => array('basic', 'birthday', 'emails', 'postal_addresses'),
  'instagram' => array('basic'),
  'github'    => array('user')
);

$provider = isset($_GET['provider']) ? strtolower($_GET['provider']) : '';

$providers = Config::get('auth.providers', array());

unset($providers['yahoo']); 

if (array_key_exists($provider, $providers) && !isset($_GET['error']) && !isset($_GET['denied'])) {

  if (Auth::check() && isset($_GET['disconnect'])) {
    Usermeta::delete(Auth::user()->id, "{$provider}_id");
    Usermeta::delete(Auth::user()->id, "{$provider}_avatar");
    Usermeta::delete(Auth::user()->id, 'avatar_type', $provider);
    Usermeta::delete(Auth::user()->id, "{$provider}_profile");

    redirect_to($settingsPage);
  }

  Session::delete('oauth_user');

  $credentials = new OAuth\Common\Consumer\Credentials(
      Config::get("services.{$provider}.id"),
      Config::get("services.{$provider}.secret"),
      App::url("oauth.php?provider={$provider}")
  );

  $scope = isset($scopes[$provider]) ? $scopes[$provider] : array();
  $storage = new OAuth\Common\Storage\Session;
  $service = with(new OAuth\ServiceFactory)->createService($provider, $credentials, $storage, $scope);

  if ($provider == 'twitter') {
    if (empty($_GET['oauth_token'])) {
      $oauth_token = $service->requestRequestToken()->getRequestToken();
      $authUrl = $service->getAuthorizationUri(compact('oauth_token'));
    } else {
        try {
          $token = $storage->retrieveAccessToken(ucfirst($provider));
          $service->requestAccessToken(
              @$_GET['oauth_token'],
              @$_GET['oauth_verifier'],
              $token->getRequestTokenSecret()
          );
      } catch(Exception $e) {}
    }
  } else {
    if (empty($_GET['code'])) {
      $authUrl = $service->getAuthorizationUri();
    } else {
      try {
        $state = isset($_GET['state']) ? $_GET['state'] : null;
        $service->requestAccessToken($_GET['code'], $state);
      } catch(Exception $e) {}
    }
  }

  if (isset($authUrl)) redirect_to($authUrl);

  try {
    $user = with(new OAuth\UserData\ExtractorFactory)->get($service);
    
    $user = array(
      'id'       => $user->getUniqueId(),
      'email'    => $user->getEmail(),
      'username' => str_replace('.', '', $user->getUsername()),
      'first_name' => $user->getfirstName(),
      'last_name' => $user->getLastName(),
      'full_name' => $user->getfullName(),
      'about'    => $user->getDescription(),
      'profile'  => $user->getProfileUrl(),
      'avatar'   => $user->getImageUrl(),
      'location' => $user->getLocation(),
      'links'    => $user->getWebsites(),
      'birthday' => $user->getField('birthday'),
      'locale'   => $user->getField('locale'),
      'gender'   => $user->getField('gender'),
      'provider' => $provider,
    );

    if ($provider == 'twitter') {
      $user['avatar'] = str_replace('_normal', '', $user['avatar']);
    }

    Session::set('oauth_user', $user);

    $storage->clearAllTokens()->clearAllAuthorizationStates();

  } catch(Exception $e) {}

  redirect_to( App::url('oauth.php') );
}

$user = Session::get('oauth_user');

if (empty($user)) redirect_to(App::url());

$provider = $user['provider'];

$userId = Usermeta::newQuery()->where('meta_key', "{$provider}_id")->where('meta_value', $user['id'])->pluck('user_id');

if (Auth::check()) {
  if (is_null($userId)) {
    Usermeta::add(Auth::user()->id, "{$provider}_id", $user['id'], true);
    
    Usermeta::add(Auth::user()->id, "{$provider}_avatar", $user['avatar'], true);

    if (!empty($user['profile'])) {
      Usermeta::add(Auth::user()->id, "{$provider}_profile", $user['profile'], true);
    }
  }

  redirect_to($settingsPage);
}

if (is_null($userId)) {
  $data = array(
    'email' => $user['email'],
    'username' => $user['username'],
    'usermeta' => array(
      "{$provider}_id" => $user['id'],
      'avatar_type' => $provider,
      "{$provider}_avatar" => $user['avatar'],
    )
  );

  if (empty($data['username'])) {
    $data['username'] = sprintf('%s%s', $user['first_name'], $user['last_name']);
  }

  if (!empty($user['profile'])) {
    $data['usermeta']["{$provider}_profile"] = $user['profile'];
  }

  if (array_key_exists($user['locale'], Config::get('app.locales'))) {
    $data['usermeta']['locale'] = $user['locale'];
  }

  foreach (UserFields::all('user') as $key => $field) {
    if (isset($user[$key])) {
        $data['usermeta'][$key] = escape($user[$key]);
      }
    }

    if (isset($_POST['submit']) && csrf_filter()) {
    if (isset($_POST['username'])) {
      $data['username'] = $_POST['username'];
    }

    if (isset($_POST['email'])) {
      $data['email'] = $_POST['email'];
    }

    foreach (UserFields::all('signup') as $key => $field) {
        if (isset($_POST[$key])) {
          $data['usermeta'][$key] = escape($_POST[$key]);
        }
      }
  }

  if (OAuth::signup($provider, $data)) {
    OAuth::login($provider, $user['id']);
  }
} else {
  OAuth::login($provider, $user['id']);
}

if (OAuth::passes()) {
  $url = Config::get('auth.login_redirect');
  $url = empty($url) ? App::url() : $url;
  redirect_to($url);
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
    <h3><?php _e('main.check_email') ?></h3>
    <?php _e('main.reminder_check_email') ?>
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