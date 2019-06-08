<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo (isset($pageTitle) ? $pageTitle .' | ' : '') . Config::get('app.name') ?> - Trusted and Genuine Returns</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo asset_url('frontpage/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="<?php echo asset_url('frontpage/vendor/font-awesome/css/font-awesome.min.css') ?>">
    <!-- Custom Font Icons CSS-->
    <link rel="stylesheet" href="<?php echo asset_url('frontpage/css/landy-iconfont.css') ?>">
    <!-- Google fonts - Open Sans-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700,800">
    <!-- owl carousel-->
    <link rel="stylesheet" href="<?php echo asset_url('frontpage/vendor/owl.carousel/assets/owl.carousel.css') ?>">
    <link rel="stylesheet" href="<?php echo asset_url('frontpage/vendor/owl.carousel/assets/owl.theme.default.css') ?>">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="<?php echo asset_url('frontpage/css/style.default.css') ?>" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="<?php echo asset_url('frontpage/css/custom.css') ?>">
    <!-- Favicon-->
     <link rel="shortcut icon" href="<?php echo asset_url('img/favicon.ico') ?>">
    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
        <style type="text/css">
          .alert-success {

    color: #fff;
    background-color: #218838;
    border-color: #218838;

}
        </style>
  </head> 
  <body>
    <!-- navbar-->
    <header class="header">
     <link rel="shortcut icon" href="<?php echo asset_url('img/favicon.ico') ?>">
     <link rel="shortcut icon" href="<?php echo asset_url('img/favicon.ico') ?>">
      <nav class="navbar navbar-expand-lg fixed-top"><a href="<?php echo App::url(); ?>" class="navbar-brand"><img src="<?php echo asset_url('img/logo.png') ?>" style=" width: 200px;"></a>
        <button type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation" class="navbar-toggler navbar-toggler-right"><span></span><span></span><span></span></button>
        <div id="navbarSupportedContent" class="collapse navbar-collapse">
          <ul class="navbar-nav ml-auto align-items-start align-items-lg-center"> 
            <li class="nav-item"><a href="<?php echo App::url(); ?>#about-us" class="nav-link link-scroll">About Us</a></li>
            <li class="nav-item"><a href="<?php echo App::url(); ?>#features" class="nav-link link-scroll">Features</a></li>
            <li class="nav-item"><a href="<?php echo App::url(); ?>#faq" class="nav-link link-scroll">FAQ's</a></li>
            <li class="nav-item"><a href="discussion.php" class="nav-link link-scroll">Discussion</a></li>
            <li class="nav-item"><a href="about.php" class="nav-link">About</a></li>
            <?php if (Auth::check()): ?>
           <li class="nav-item"><a href="account/index.php" class="btn btn-primary navbar-btn btn-shadow btn-gradient">Dashboard</a></li>
            <?php endif; ?>
          </ul>
          <?php if (!Auth::check()): ?>
      <div class="navbar-text">   
           <a href="account/auth/login.php"  class="btn btn-primary navbar-btn btn-shadow btn-gradient">Sign In</a>
          </div>
            <?php endif; ?>
         
        </div>
      </nav>
    </header>
   
   