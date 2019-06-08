<?php

 include '_header.php';
  $maxlength = Config::get('pms.maxlength');


  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
  
if (!empty($_GET['react']) & !empty($_GET['id'])){
   $Post = DB::table('testimony')->where('id', $_GET['id'])->first();
   if ($Post) {
    if ($_GET['react'] =="liked" || $_GET['react'] =="disliked") {

      $CheckVotes = DB::table('testimoneytvotes')->where('comment_id', $_GET['id'])->where('userid', $user_id)->first();
      if ($CheckVotes) {
     $sql =  DB::table('testimoneytvotes')
        ->where('userid', $user_id)
        ->update(array('type' => $_GET['react']));
         if ($sql) {
        echo '<meta http-equiv="refresh" content="0;url=testimony.php"/>';
      }
      }else
      {
      $sql = DB::table('testimoneytvotes')->insert(
      array('type' => $_GET['react'],
            'comment_id' => $_GET['id'],
            'userid' => $user_id)
           );

      if ($sql) {
        DB::table('notification')->insert(
        array('userid' =>        $Post->userid,
         'type' =>        "pack",
          'details' =>    "Your testimony has been ".$_GET['react']." by ".$ProfileCh->accountname, 
          'status'  =>    "verify",
          'faIcon'     =>    "fa fa-thumbs-o-up")
       );
        echo '<meta http-equiv="refresh" content="0;url=testimony.php"/>';
      }
      }
    }
     
   }
}





?>



<div class="content-inner">
          <!-- Page Header--> 
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Testimony</h2>
            </div>
          </header>
      
<?php
if (isset($_GET['session'])) { 
   echo '<div class="alert alert-danger" role="alert">
     You will need to write testimony before you can confirm user payment, please write testimony and go back to verify payment.
    </div>';
}
if (isset($_POST['submit'])) {
	$redirect ="";

   $Subject =  htmlspecialchars($_POST['subject']);
   $message =  htmlspecialchars($_POST['testimony']);

   $length = strlen(trim($message));
    if ( $length < 50 || $length > 500) {
     echo '<div class="alert alert-danger" role="alert">
     Opps: Testimony must be minimum 50 characters and maximum 500 characters.
    </div>';
    }else{
      if (isset($_GET['session'])) {
      	$sender_id = Session::get('sender_id');
        $Validmaching = DB::table('marching')->where('receiver_id', $user_id)->where('sender_id', $sender_id)->where('id', $_GET['valid'])->first();
        if ($Validmaching) {
          $sql = DB::table('testimonyforce')->insert( 
        array('margin_id' => $Validmaching->id,
              'receiver_id' => $user_id, 
              'sender_id' => $Validmaching->sender_id,
               'status' => 1)
           );
         } 
       if ($sql) {
       $redirect = Session::get('redirect');
       }
      }
   CreateTestiony($Subject,$message,$user_id,$redirect);
  }
 } 
?>
     <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
         <div class="row">


<?php
 if (isset($_GET['create'])){ 
?><div class="col-lg-12">
                  <div class="card">
                    <div class="card-close">
                      <div class="dropdown">
                        <button type="button" id="closeCard1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                        <div aria-labelledby="closeCard1" class="dropdown-menu dropdown-menu-right has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a></div>
                      </div>
                    </div>
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Write testimony</h3>
                    </div>
                    <div class="card-body">
                      <p>Please write your testimony in details and minimum text is 50 and maximum is 500 words.</p>
                      <form method="POST" action="">
                        <div class="form-group">
                          <label class="form-control-label">Subject: </label>
                          <input type="text" name="subject" placeholder="Your Subject" class="form-control">
                        </div>
                        <div class="form-group">       
                          <label class="form-control-label">Testimony:</label>
                          <textarea type="text" name="testimony" class="form-control pm-textarea" rows="3" <?php if ($maxlength) echo 'maxlength="'.$maxlength.'"'; ?>></textarea>
                        </div>
                        <div class="form-group">
                        
                          <input type="submit" name="submit" value="Submit" class="btn btn-primary">
                           <?php if ($maxlength): ?>
                   <span class="counter"><?php echo $maxlength; ?></span>
                  <?php endif ?>    
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
    <?php

        }
else{   
 echo '<div class="col-lg-12">
                  <div class="card">
                   
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Whats on your mind? </h3>
                    </div>
                    <div class="card-body text-center">
                      <a href="testimony.php?create" class="btn btn-primary">Write Testimony </a>

                  </div>
                </div>
                </div>';
TestimoneyView();

}
?>
 </div>

</section>




  </body>
<?php include '_footer.php'; ?>
