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
      
     <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
               <?php     
               if (isset($_POST['SendMessage'])) {
                 $MessagesNew = $_POST['MessagesNew'];
                  CreateMessage($user_id,$MessagesNew);
              }
             ?>
        <div class="row">
       <div class="col-md-2">
      <div class="list-group">
                    <a href="message.php" class="list-group-item list-group-item-success">Messages</a>
                    <a href="newticket.php" class="list-group-item">Create Ticket</a>
                    <a href="tickets.php" class="list-group-item">Tickets <span class="badge bg-red badge-corner"><?php replyTicketCount($user_id); ?></span></a>
                </div>
        </div>
       
           <div class="col-md-10">
    
                    <p class="help-block"><?php _e('main.webmaster_contact_help') ?></p>
      <br>
      <form action="webmasterContact" class="ajax-form">
        <div class="form-group">
            <label for="email"><?php _e('main.username') ?></label>
            <input type="text"  value="<?php echo Auth::user()->display_name ?>" class="form-control" disabled>
        </div>
        <div class="form-group">
                  <label for="message"><?php _e('main.message') ?></label>
                 <textarea class="form-control" name="message" id="message"></textarea>
              </div>
            <div class="form-group">
          <button type="submit" name="submit" class="btn btn-primary"><?php _e('main.send_message') ?></button>
        </div>
      </form>
                </div>
              </form>
            </div>
         


      
     </section>


<?php include '_footer.php'; ?>
