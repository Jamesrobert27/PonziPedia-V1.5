<?php include '_header.php';
 $messageR ="";


  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
  




 ?>

<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Create New Ticket</h2>
            </div>
          </header>
      
     <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
           <?php     if (isset($_POST['createTicket'])) {
  $subject = $_POST['subject'];
  $des = $_POST['ticketmessage'];
     CreateTicket($user_id,$subject,$des);
}
?>
        <div class="row">
       <div class="col-md-4">
        <div class="list-group">
                    <a href="message.php" class="list-group-item">Messages</a>
                    <a href="newticket.php" class="list-group-item list-group-item-success">Create Ticket</a>
                    <a href="tickets.php" class="list-group-item">Tickets <span class="badge bg-red badge-corner"><?php replyTicketCount($user_id); ?></span></a>
                    <a href="courtcases.php" class="list-group-item">My Court Cases</a>
                </div>
        </div>
         <div class="col-md-8 card">
       <form class="form-horizontal" method="POST" action="">
<fieldset>

<!-- Form Name -->
<legend>Create Ticket</legend>

<!-- Text input-->
<div class="form-group">
  <label class="control-label" for="textinput">Subject</label>  
  
  <input id="textinput" name="subject" placeholder="Your subject here" class="form-control input-md" required="" type="text">
  <span class="help-block" style="font-size: 12px !important;">Make usre is 15 characters long or above</span>  
  </div>


<!-- Textarea -->
<div class="form-group">
  <label class="control-label" for="ticketmessage">Description</label>
                     
    <textarea class="form-control" id="ticketmessage" name="ticketmessage">Your request here</textarea>
  </div>


</fieldset>

<div class="form-group">
  <div class="col-md-8">
    <input type="submit" name="createTicket" class="btn btn-success" value="Create Ticket">
  </div>
</div>
</form>

        </div>
         </div>
     </section>


<?php include '_footer.php'; ?>
