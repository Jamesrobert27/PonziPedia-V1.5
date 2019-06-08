<?php include '_header.php';
 $messageR ="";


  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
      

 ?>

<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Support System</h2>
            </div>
          </header>
      
     <section class="dashboard-counts no-padding-bottom" style="background-image: url(<?php echo asset_url('img/back.png') ?>);">
            <div class="container-fluid">
               <?php     
               if (isset($_POST['SendMessage'])) {
                 $MessagesNew = $_POST['MessagesNew'];
                  CreateMessage($user_id,$MessagesNew);
              }
             ?>
        <div class="row">
       <div class="col-md-4">
        <div class="list-group">
                    <a href="message.php" class="list-group-item">Messages</a>
                    <a href="newticket.php" class="list-group-item">Create Ticket</a>
                    <a href="tickets.php" class="list-group-item">Tickets <span class="badge bg-red badge-corner"><?php replyTicketCount($user_id); ?></span></a>
                </div>
        </div>
        
          <div class="col-md-8">
       
    <div class="col-lg-12">
                  <div class="work-amount card">
                   
                    <div class="card-body">
                      <h3>Support Tips</h3>
                     <ol style="padding-left: 10px;">
                      <li>Please write directly to your case, Message such as "Hello", "Hi", "How you doing" will be ignor</li>
                       <li>Always use positive language</li>
                       <li>We credence to customer complaints</li>
                       <li>Respond time is approximately 20 minutes </li>
                       
                     </ol></div>
                  </div>
                </div>
        </div>

        
     </section>


<?php include '_footer.php'; ?>
