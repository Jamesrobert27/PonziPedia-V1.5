<?php require_once 'app/init.php';
// +------------------------------------------------------------------------+
// | @author Olakunlevpn (Olakunlevpn)
// | @author_url 1: http://www.maylancer.cf
// | @author_url 2: https://codecanyon.net/user/gr0wthminds
// | @author_email: olakunlevpn@live.com   
// +------------------------------------------------------------------------+
// | PonziPedia - Peer 2 Peer 50% ROI Donation System
// | Copyright (c) 2018 PonziPedia. All rights reserved.
// +------------------------------------------------------------------------+

 ?>

<?php echo View::make('header')->render() ?>

<section style="padding-top: 20px;">
      <div class="container">
  <head>

<script src="<?php echo asset_url('js/vendor/jquery-1.11.1.min.js') ?>"></script>
</head> 
<div class="row">
  <div class="col-md-8">
    <h3 class="page-header">Forum Discussion</h3>

    <p>This page shows the forum discussion feature. You can only post your reveiw or reply to other member post.</p>
    
    <div id="embed_comments" style="width: 100% !important; height: 100% !important;"></div>
  <script src="<?php echo asset_url('js/embed-commentss.js') ?>"></script>
  <script>
    var page    = '1';    // Page identifier
    var pageTitle = 'My Page';  // A name for the page

    embedComments('#embed_comments', page, pageTitle);
  </script>

       
          </div>
  </div>
</div>

        </div>
    </section>
<?php echo View::make('footer')->render() ?> 