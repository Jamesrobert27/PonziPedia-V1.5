<?php include '_header.php';
 $messageR ="";


  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
      

 ?> 

<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Forum Discussion</h2>
            </div>
          </header>
      
     <section class="dashboard-counts no-padding-bottom"> 
            <div class="container-fluid">
  

        <div class="row" style="background-color: #fff;">

<head>

<script src="<?php echo asset_url('js/vendor/jquery-1.11.1.min.js') ?>"></script>
</head>
<body>

  <div id="embed_comments" style="width: 100% !important; height: 100% !important;"></div>
  <script src="<?php echo asset_url('js/embed-comments.js') ?>"></script>
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
</div>


  </body>

