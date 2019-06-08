<?php include '_header.php';

if (empty($_GET['u'])) redirect_to(App::url('account/index.php'));
$user = DB::table('marching')->where('id', $_GET['u'])->first();
?>
<?php if (is_null($user)) redirect_to(App::url('account/index.php')); ?>
<?php  

$Validmaching = DB::table('marching')->where('receiver_id', $user_id)->where('id', $_GET['u'])->first(); 
if (is_null($Validmaching)) redirect_to(App::url('account/index.php'));

$PackId = $Validmaching->package_id;
$ValidPack = DB::table('packages')->where('id', $PackId)->first(); 

  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
  
  $ProfileReceiver = DB::table('userdetails')->where('userid', $Validmaching->sender_id)->first();
  $userReceiver = DB::table('users')->where('id', $Validmaching->receiver_id)->first();
  $sender_userid = $Validmaching->sender_id;
if (isset($_POST['submitTest'])) {
  Session::set('Testimony', 'yes');
  ?>

  <script>window.location.href ='<?php echo Config::get('app.telegram'); ?>';</script>
  <?php
}

$Testimony = Session::get('Testimony');
if ($Testimony =="") {
     if (isset($_GET['validation'])){ 
   ?>

<script type="text/javascript">
    $(window).on('load',function(){
        $('#myModal').modal('show');
    });
</script>


<div id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" class="modal fade text-left show" style="padding-right: 15px; display: block;">
                        <div role="document" class="modal-dialog">
                          <div class="modal-content">
                            <div class="modal-header">
                              <h4 id="exampleModalLabel" class="modal-title">Payment Confirmed</h4>
                             
                            </div>
                            <div class="modal-body">
                              <p>You have successfully confirm your downline payment, before you left It has been made compulsory on all members to share their testimonial screenshot on <?php echo Config::get('app.name'); ?> telegram or our facebook otherwise, your recommittment balance would never ever be released, dropping your testimonial screenshot is free and cost nothing , it rather secures your investment and boost morale of other members <?php echo Config::get('app.name'); ?>. Join our official telegram page now and share your testimonial sceenshot for this new GH.</p>
      
                              
                            </div>
                            <div class="modal-footer">
                              <form method="POST" action="">
     <input type="submit" class="btn btn-primary" name="submitTest" value="Share Now!" />
   </form>
    </div>
    </div>
    </div>
    </div>
   <?php
  }
}
?>

 <div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Confirm Payment - <?php echo $ValidPack->packname; ?> Pack
            (<?php echo $settings->currency; ?> <?php echo $Validmaching->amount; ?>)</h2>
            </div>
          </header>
    
    <!-- Content Header (Page header) -->
   
    <!-- Main content -->
          <!-- Dashboard Counts Section-->
          <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
               <?php if (isset($_POST['confirmUser'])){
                $id =$Validmaching->id; 
                 $ForceTestimoney = DB::table('testimonyforce')->where('receiver_id', $user_id)->where('margin_id', $id)->first(); 
                 if (is_null($ForceTestimoney)){
                  Session::set('redirect', $_SERVER['REQUEST_URI']); 
                  Session::set('sender_id', $sender_userid); 
                  redirect_to(App::url('account/testimony.php?create&valid='.$_GET['u'].'&session=testimony'));
                } 
                else
                {
                     confirmUserPay($user_id, $sender_userid, $id);
                }
                }
             ?>

              <?php if (isset($_POST['Report'])){
                    $id =$Validmaching->id;
                    $enquiry =$_POST['enquiry'];
                    $des =$_POST['des'];
                     ReportUserMargin($user_id, $sender_userid, $id,$enquiry,$des);
                }
             ?>
              <div class="row">
           <div class="col-lg-6">
                  <div class="card">
                    <div class="card-close">
                      <div class="dropdown">
                        <button type="button" id="closeCard1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                        <div aria-labelledby="closeCard1" class="dropdown-menu dropdown-menu-right has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-leanpub"></i>HowToPay</a><a href="#" class="dropdown-item edit"> <i class="fa fa-gear"></i>Account Settings</a>
                            <a href="#" class="dropdown-item edit"> <i class="fa fa-envelope"></i>Get Support</a></div>
                      </div>
                    </div>
                    <div class="card-header d-flex align-items-center">
                      <h3 class="h4">Sender Profile</h3>
                    </div>
                    <div class="card-body" style="padding-left: 3px;padding-right: 0px;">
                       <center><img class="img-fluid rounded-circle" src="<?php echo $ProfileReceiver->avater; ?>" style="width: 80px;" alt="User profile picture"></center>

                        <h3 class="profile-username text-center"><?php echo $ProfileReceiver->accountname;  ?></h3>

                        <ul class="list-group list-group-unbordered">
                            <li class="list-group-item">
                                <i class="fa fa-user"></i> <a class="pull-right"><?php echo $ProfileReceiver->accountname;  ?></a>
                            </li>
                                                            <li class="list-group-item">
                                    <i class="fa fa-phone"></i> <a class="pull-right"><?php echo $ProfileReceiver->phonenumber;  ?></a>
                                </li>
                                                        <li class="list-group-item">
                                <i class="fa fa-envelope-o"></i> <a class="pull-right"><?php echo $userReceiver->email;  ?></a>
                            </li>


                            <div class="box box-success">

                                <div class="box-header with-border">
                                                                            <h3 class="box-title">Default Bank Account</h3>
                                                                    </div>
                                <div class="box-body">
                                    
                                        <ul class="list-group list-group-unbordered">
                                            <li class="list-group-item">
                                                <i class="fa fa-bank"></i> Bank Name <a class="pull-right"><?php echo $ProfileReceiver->bankname;  ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fa fa-bank"></i> Account Name <a class="pull-right"><?php echo $ProfileReceiver->accountname;  ?></a>
                                            </li>
                                            <li class="list-group-item">
                                                <i class="fa fa-bank"></i> Account Number <a class="pull-right"><?php echo $ProfileReceiver->accountnumber;  ?></a>
                                            </li>

                                            <li class="list-group-item">
                                                <i class="fa fa-bank"></i> Account Type <a class="pull-right"><?php echo $ProfileReceiver->accounttype;  ?></a>
                                            </li>
                                        </ul>
                    </div>
                  </div>
                </div>


                
              
            </div>
        </ul>
    </div>


  <!-- Line Chart            -->
                <div class="chart col-lg-6 col-12">

                  <?php 
                  $CheckingMarching = DB::table('marching')->where('receiver_id', $user_id)->where('id', $user->id)->first(); 
                        $timeCreated = $CheckingMarching->expiringTime;
                        $timeNow = date('Y-m-d H:i:s');

                  
                  if ($CheckingMarching->payment_status == "pending") {
                   echo '
                    <div class="card-body text-center" style="background-color: #218838; color: #fff;"><h3>Payment is still pending and waiting for sender to make payment</h3></div>';
                  }
                   if ($CheckingMarching->payment_status == "waiting") {
                   echo '
                    <div class="card-body text-center" style="background-color: #218838; color: #fff;"><h3>Please check below information and confirm user payment</h3></div>';
                  }
                   if ($CheckingMarching->payment_status == "pending" || $CheckingMarching->payment_status == "waiting") {
                  ?>
                  <div class="work-amount card">
                   
                    <div class="card-body" style="padding-left: 3px;padding-right: 0px;">

                    
                    
                  
                     <ul class="list-group list-group-unbordered">
                      <?php if ($CheckingMarching->paymentMethod !="") {
                       ?>
                   <li class="list-group-item">
                    Sender's Method: <a class="pull-right"><?php echo $CheckingMarching->paymentMethod;  ?></a>
                     </li>
                       <?
                      }
                      ?>

                       <?php if ($CheckingMarching->senderBank !="") {
                       ?>
                   <li class="list-group-item">
                    Sender's Bank Bame: <a class="pull-right"><?php echo $CheckingMarching->senderBank;  ?></a>
                     </li>
                       <?
                      }
                      ?>


                       <?php if ($CheckingMarching->accountNumber !="") {
                       ?>
                   <li class="list-group-item">
                    Sender's Account Number: <a class="pull-right"><?php echo $CheckingMarching->accountNumber;  ?></a>
                     </li>
                       <?
                      }
                      ?>

                       <?php if ($CheckingMarching->AccountName !="") {
                       ?>
                   <li class="list-group-item">
                    Sender's Account Name: <a class="pull-right"><?php echo $CheckingMarching->AccountName;  ?></a>
                     </li>
                       <?
                      }
                      ?>

                       <?php if ($CheckingMarching->depositorsName !="") {
                       ?>
                   <li class="list-group-item">
                    Sender's Depositor Name: <a class="pull-right"><?php echo $CheckingMarching->depositorsName;  ?></a>
                     </li>
                       <?
                      }
                      ?>

                       <?php if ($CheckingMarching->paymentLocation !="") {
                       ?>
                   <li class="list-group-item">
                    Sender's Location: <a class="pull-right"><?php echo $CheckingMarching->paymentLocation;  ?></a>
                     </li>
                       <?
                      }
                      ?>

                      <?php if ($CheckingMarching->ProofPic !="") {
                       ?>
                   <li class="list-group-item">
                    <a href="<?php echo App::url(); ?>/images/<?php echo $CheckingMarching->ProofPic;  ?>" target="_BLANK" class="pull-right"><img src="<?php echo App::url(); ?>/images/<?php echo $CheckingMarching->ProofPic;  ?>" alt="<?php echo $CheckingMarching->ProofPic;  ?>" class="img-responsive" style="width: 90%;"></a>
                     </li>
                       <?
                      }
                      ?>
                    </ul>

                    <form method="POST" action="">
                    <input type="hidden" name="id" value="<?php echo $CheckingMarching->id; ?>">
                        <div class="form-group row" style="padding: 5px 15px;padding-left: 5px;padding-right: 5px;">       
                          
                            <input type="submit" name="confirmUser" value="Confirm Payment" class="btn btn-primary btn-lg btn-block" style="margin-bottom: 5px;">
                          </div>
                    </form>
                 <?php 
               $reported = DB::table('courtcase')->where('margin_id', $Validmaching->id)->first();
                  if ($reported) {
                    if ($reported->status ==2 || $reported->status ==1) {
                       echo'<div class="alert alert-danger" role="alert">
  You have already report this payment to court and the judges are talking bold steps about, you will get notification when it has been resolved 
</div>';
                    }elseif ($reported->status ==0) {
                       echo'<div class="alert alert-success" role="alert">
 This case has been resolved by administrator, you can appeal by using the support ticket
</div>';
                    }
                 
                  }
                  else{
                 ?>
                    <button data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" class="btn btn-danger btn-lg btn-block" >Report</button>
                       <div id="collapseTwo" class="panel-collapse collapse">

      <div class="panel-body">
            <form method="POST" action="" role="form" class="col-md-12">
                    <input type="hidden" name="id" value="<?php echo $CheckingMarching->id; ?>">
          <hr><h1>Report Court Case</h1><hr>
                <div class="form-group">
                    <label class="form-label">Report Reason</label>
                    <div class="controls">
                        <select id="enquiry" class="form-control" name="enquiry" class="input-xlarge">
                            <option value="NULL" selected="selected">(please select)</option>
                            <option value="fakePOF">Fake POF</option>
                            <option value="PaymentDelayed">Payment Delayed</option>
                            <option value="MemberNotReachable">Member Not Reachable</option>
                            <option value="Others">Others</option>
                        </select>
                    </div>
                </div>

   <hr>
  <div class="form-group">
    <label for="exampleInputPassword1">Brief Description</label>
    <textarea type="text" name="des" class="form-control" id="text" placeholder="Write your description here"></textarea>
  </div>
 <hr>
  <input type="hidden" name="id" value="<?php echo $CheckingMarching->id; ?>">
  <input type="submit" value="Sue User" name="Report" class="btn btn-danger btn-lg btn-block" style="margin-bottom: 5px;">
              <hr>            
</form>
        
        
      </div>
      <?php } ?>
    </div>
  </div>

</div>

             
             
              </div>
                    <?php
                  }
                 
                   elseif ($CheckingMarching->payment_status == "confirm") {
                    echo '  <div class="card-body text-center" style="background-color: #218838; color: #fff;"><h3>Welldone! you have successfully confirm this payment</h3></div>';
                  }else{
                     echo '  <div class="card-body text-center" style="background-color: #dc3545; color: #fff;"><h3>This payment is not understood, please contact support for possible assistant</h3></div>';
                    }
                    ?>
            </div>
                  
          </section>


    <?php include '_footer.php'; ?>