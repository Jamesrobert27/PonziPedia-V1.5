<?php
// +------------------------------------------------------------------------+
// | @author Olakunlevpn (Olakunlevpn)
// | @author_url 1: http://www.maylancer.cf
// | @author_url 2: https://codecanyon.net/user/gr0wthminds
// | @author_email: olakunlevpn@live.com   
// +------------------------------------------------------------------------+
// | PonziPedia - Peer 2 Peer 50% ROI Donation System
// | Copyright (c) 2018 PonziPedia. All rights reserved.
// +------------------------------------------------------------------------+



require_once '../app/init.php';

$page = basename($_SERVER['SCRIPT_NAME']);

if (Auth::guest()) redirect_to(App::url('account/auth/login.php'));
require_once  '../app/joiners.php';
require_once  'models.php'; 

 
 
?>
<!DOCTYPE html>
<html> 
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="csrf-token" content="<?php echo csrf_token() ?>">
    <title><?php echo (isset($pageTitle) ? $pageTitle .' | ' : '') . Config::get('app.name') ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo asset_url('May/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="<?php echo asset_url('May/vendor/font-awesome/css/font-awesome.min.css') ?>">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="<?php echo asset_url('May/css/fontastic.css') ?>">
    <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="<?php echo asset_url('May/css/style.default.css') ?>" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="<?php echo asset_url('May/css/custom.css') ?>">
    <!-- Favicon-->
    <link rel="shortcut icon" href="<?php echo asset_url('img/favicon.ico') ?>">

   
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
       <script src="<?php echo asset_url('js/vendor/jquery-1.11.1.min.js') ?>"></script>
  <script src="<?php echo asset_url('js/easylogin.js') ?>"></script>
  <script src="<?php echo asset_url('js/main.js') ?>"></script>

  <script src="<?php echo asset_url('js/jquery/jquery-1.8.2.min.js') ?>"></script>    
   <link rel="stylesheet" href="<?php echo asset_url('js/jcountdown/jcountdown.css') ?>">   
    <script src="<?php echo asset_url('js/jcountdown/jquery.jcountdown.min.js') ?>"></script>    
        <script>
    EasyLogin.options = {
      ajaxUrl: '<?php echo App::url("ajax.php") ?>',
      lang: <?php echo json_encode(trans('main.js')) ?>,
      debug: <?php echo Config::get('app.debug')?1:0 ?>,
    };
  </script>
  <style type="text/css"> 
   .alert-danger {
   color: #fff;
    background-color: #dc3545;
    border-color: ##dc3545;
    border-radius: 0px !important;
}

.alert-success {
     color: #fff;
    background-color: #218838;
    border-color: #218838
    border-radius: 0px !important;
}

.fa-stack[data-count]:after{
  position:absolute;
  right:0%;
  top:1%;
  content: attr(data-count);
  font-size:40%;
  padding:.6em;
  border-radius:999px;
  line-height:.75em;
  color: white;
  background:rgba(255,0,0,.85);
  text-align:center;
  min-width:2em;
  font-weight:bold;
}

/*Now the CSS Created by R.S*/
* {margin: 0; padding: 0;}

.tree ul {
    padding-top: 20px; position: relative;
  
  transition: all 0.5s;
  -webkit-transition: all 0.5s;
  -moz-transition: all 0.5s;
}

.tree li {
  float: left; text-align: center;
  list-style-type: none;
  position: relative;
  padding: 20px 5px 0 5px;
  
  transition: all 0.5s;
  -webkit-transition: all 0.5s;
  -moz-transition: all 0.5s;
}

/*We will use ::before and ::after to draw the connectors*/

.tree li::before, .tree li::after{
  content: '';
  position: absolute; top: 0; right: 50%;
  border-top: 1px solid #ccc;
  width: 50%; height: 20px;
}
.tree li::after{
  right: auto; left: 50%;
  border-left: 1px solid #ccc;
}

/*We need to remove left-right connectors from elements without 
any siblings*/
.tree li:only-child::after, .tree li:only-child::before {
  display: none;
}

/*Remove space from the top of single children*/
.tree li:only-child{ padding-top: 0;}

/*Remove left connector from first child and 
right connector from last child*/
.tree li:first-child::before, .tree li:last-child::after{
  border: 0 none;
}
/*Adding back the vertical connector to the last nodes*/
.tree li:last-child::before{
  border-right: 1px solid #ccc;
  border-radius: 0 5px 0 0;
  -webkit-border-radius: 0 5px 0 0;
  -moz-border-radius: 0 5px 0 0;
}
.tree li:first-child::after{
  border-radius: 5px 0 0 0;
  -webkit-border-radius: 5px 0 0 0;
  -moz-border-radius: 5px 0 0 0;
}

/*Time to add downward connectors from parents*/
.tree ul ul::before{
  content: '';
  position: absolute; top: 0; left: 50%;
  border-left: 1px solid #ccc;
  width: 0; height: 20px;
}

.tree li a{
  border: 1px solid #ccc;
  padding: 5px 10px;
  text-decoration: none;
  color: #666;
  font-family: arial, verdana, tahoma;
  font-size: 11px;
  display: inline-block;
  
  border-radius: 5px;
  -webkit-border-radius: 5px;
  -moz-border-radius: 5px;
  
  transition: all 0.5s;
  -webkit-transition: all 0.5s;
  -moz-transition: all 0.5s;
}

/*Time for some hover effects*/
/*We will apply the hover effect the the lineage of the element also*/
.tree li a:hover, .tree li a:hover+ul li a {
  background: #c8e4f8; color: #000; border: 1px solid #94a0b4;
}
/*Connector styles on hover*/
.tree li a:hover+ul li::after, 
.tree li a:hover+ul li::before, 
.tree li a:hover+ul::before, 
.tree li a:hover+ul ul::before{
  border-color:  #94a0b4;
}
.hidden {
    display: none!important;
    visibility: hidden!important;
}

.timerSet{
  width: auto;
}
  </style>
  </head>
  <body>
    <div class="page">
      <!-- Main Navbar-->
      <header class="header">
        <nav class="navbar">
       
          <div class="container-fluid">
            <div class="navbar-holder d-flex align-items-center justify-content-between">
              <!-- Navbar Header-->
              <div class="navbar-header">
                <!-- Navbar Brand --><a href="index.php" class="navbar-brand">
                  <div class="brand-text brand-big"><span><!--<img src="<?php echo asset_url('img/logoss.png') ?>" style="
    width: 35px;
" alt="..." class="img-responsive">--><?php echo Config::get('app.name'); ?> </span></div>
                  <div class="brand-text brand-small"><strong><?php echo Config::get('app.short_name'); ?></strong></div></a>
                <!-- Toggle Button--><a id="toggle-btn" href="#" class="menu-btn active"><span></span><span></span><span></span></a>
              </div>
              <!-- Navbar Menu -->
              <ul class="nav-menu list-unstyled d-flex flex-md-row align-items-md-center">
             
                <!-- Notifications-->
                <li class="nav-item dropdown"> <a id="notifications" rel="nofollow" data-target="#" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="nav-link"><i class="fa fa-bell-o"></i><span class="badge bg-red badge-corner"><?php notificationCount($user_id) ?></span></a>
                  <ul aria-labelledby="notifications" class="dropdown-menu">
                      <?php notificationView($user_id); ?>
                    <li><a href="activity.php" class="dropdown-item all-notifications text-center"> <strong>view all notifications                                            </strong></a></li>
                  </ul>
                </li>
                <!-- Messages                        -->
                <li class="nav-item dropdown">
                 <a href="#" class="nav-btn pm-open-modal nav-link" data-toggle="tooltip" data-placement="bottom" title="<?php _e('main.pms'); ?>">
                      <span class="badge bg-red badge-corner pm-notification"></span>
                      <span class="fa fa-envelope-open"></span>
                    </a>
                 
                </li>
               
                <!-- Logout    -->
                <li class="nav-item"><a href="logout.php" class="nav-link logout">Logout<i class="fa fa-sign-out"></i></a></li>
              </ul>
            </div>
          </div>
        </nav>
      </header>
      <div class="page-content d-flex align-items-stretch"> 
        <!-- Side Navbar -->
        <nav class="side-navbar"> 
          <!-- Sidebar Header-->
          <div class="sidebar-header d-flex align-items-center">
            <div class="avatar"><img src="<?php echo Auth::user()->avatar ?>" alt="..." class="img-fluid rounded-circle"></div>
            <div class="title">
              <h1 class="h4"><?php echo Auth::user()->username; ?><br> 
                <?php if (Auth::userCan('guider')) {
                  echo '<i class="fa fa-star" style="color: #fec42d;"></i> <i class="fa fa-star" style="color: #fec42d;"></i> <i class="fa fa-star" style="color: #fec42d;"></i> <i class="fa fa-star" style="color: #fec42d;"></i> <i class="fa fa-star" style="color: #fec42d;"></i><br>Guider</h1>';
                }  else{ ?>
              <p>Member Since <br><?php echo date("Y-m-d",strtotime(Auth::user()->joined)); ?></p>
              <?php } ?>
            </div>
          </div>
          <ul class="list-unstyled">
                    <li <?php if ($page == 'index.php') { ?>class="active"<?php } ?>><a href="index.php"> <i class="icon-home"></i>Dashboard </a></li>
                    <li <?php if ($page == 'activity.php') { ?>class="active"<?php } ?>><a href="activity.php"> <i class="fa fa-pie-chart"></i>Activity </a></li>
                    <li <?php if ($page == 'getdonation.php') { ?>class="active"<?php } ?>><a href="getdonation.php"> <i class="fa fa-handshake-o"></i>Get Donation </a></li>
                    <li <?php if ($page == 'donor.php') { ?>class="active"<?php } ?>><a href="donor.php"> <i class="fa fa-bolt"></i>Provide Donation </a></li>
                     <li <?php if ($page == 'logs.php') { ?>class="active"<?php } ?>><a href="logs.php"> <i class="fa fa-list"></i>Payment Logs </a></li>
                    <li <?php if ($page == 'wallet.php') { ?>class="active"<?php } ?>><a href="wallet.php"> <i class="fa fa-university"></i>Recommit balance </a></li>
                   
                     <?php if (Auth::userCan('guider')): ?>
                     <li <?php if ($page == 'guider-panel.php') { ?>class="active"<?php } ?>><a href="guider-panel.php"> <i class="fa fa-certificate"></i>Guider Panel</a>
                      
                    </li>
                  <?php endif ?> 
                    <li <?php if ($page == 'referral.php') { ?>class="active"<?php } ?>><a href="referral.php"> <i class="fa fa-users"></i>Referral</a></li>
                    <li <?php if ($page == 'account.php') { ?>class="active"<?php } ?>><a href="account.php?edit=<?php echo $user_id; ?>"> <i class="fa fa-user"></i>Profile </a></li>
                     <li <?php if ($page == 'testimony.php') { ?>class="active"<?php } ?>><a href="testimony.php"> <i class="fa fa-comments"></i>My Testimony </a></li>
                    <li <?php if ($page == 'settings.php') { ?>class="active"<?php } ?>><a href="settings.php"> <i class="fa fa-cog"></i>Settings </a></li>
                    <li <?php if ($page == 'support.php') { ?>class="active"<?php } ?>><a href="support.php"> <i class="fa fa-ticket"></i>Support </a> </li>
                    <li <?php if ($page == 'courtcases.php') { ?>class="active"<?php } ?>><a href="courtcases.php"> <i class="fa fa-gavel"></i>My Court Cases  </a> </li>

                    <?php if (Auth::userCan('dashboard')): ?>
                    <li><a href="../admin.php"> <i class="fa fa-cog"></i>Admin Panel </a>
                    </li>
                  <?php endif ?>
                    </li>
                    <li <?php if ($page == 'discussion.php') { ?>class="active"<?php } ?>><a href="discussion.php"> <i class="fa fa-comments"></i>Discussion <span class="badge bg-red badge-corner"><?php CommentSCount() ?></span></a>
                      
                    </li>
                    <li><a href="logout.php"> <i class="fa fa-power-off" style="color: #f40000 !important;"></i>Logout </a></li>
        
        </nav>