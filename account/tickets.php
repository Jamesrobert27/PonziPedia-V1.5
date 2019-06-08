<?php include '_header.php';
 $messageR ="";


  $ProfileCh = DB::table('userdetails')->where('userid', $user_id)->first();
     if (is_null($ProfileCh)) redirect_to(App::url('account/account.php'));
      

?>
<div class="content-inner">
          <!-- Page Header-->
          <header class="page-header">
            <div class="container-fluid">
              <h2 class="no-margin-bottom">Ticket Reply</h2>
            </div>
          </header>
      
    <section class="dashboard-counts no-padding-bottom" style="background-image: url(<?php echo asset_url('img/back.png') ?>);">
            <div class="container-fluid">
      <?php     if (isset($_POST['TicketReply'])) {
                $ticketpost = $_POST['ticketpost'];
                $tiketId = $_POST['tiketId'];
                 UserTicketReply($user_id,$tiketId,$ticketpost);
              }


            if (isset($_POST['CloseTicket'])) {
                $tiketId = $_POST['tiketId'];
                 CloserUsrTicket($user_id,$tiketId);
              }
         ?>
     <?php
     if (isset($_GET['view'])){
$TicketId = $_GET['view'];
$user = DB::table('tickets')->where('id', $TicketId)->where('userid', $user_id)->first();
     if (is_null($user)){
redirect_to(App::url('account/newticket.php')); 
} 
elseif ($user){
  $ticketID = $user->id;
  ?>
  <head><style class="cp-pen-styles">html, body {
  background-color: #f0f2fa;
  font-family: "PT Sans", "Helvetica Neue", "Helvetica", "Roboto", "Arial", sans-serif;
  color: #555f77;
  -webkit-font-smoothing: antialiased;
}

input, textarea {
  outline: none;
  border: none;
  display: block;
  margin: 0;
  padding: 0;
  -webkit-font-smoothing: antialiased;
  font-family: "PT Sans", "Helvetica Neue", "Helvetica", "Roboto", "Arial", sans-serif;
  font-size: 1rem;
  color: #555f77;
}
input::-webkit-input-placeholder, textarea::-webkit-input-placeholder {
  color: #ced2db;
}
input::-moz-placeholder, textarea::-moz-placeholder {
  color: #ced2db;
}
input:-moz-placeholder, textarea:-moz-placeholder {
  color: #ced2db;
}
input:-ms-input-placeholder, textarea:-ms-input-placeholder {
  color: #ced2db;
}

p {
  line-height: 1.3125rem;
}

.comments {
  margin: 2.5rem auto 0;
  max-width: 60.75rem;
  padding: 0 1.25rem;
}

.comment-wrap {
  margin-bottom: 1.25rem;
  display: table;
  width: 100%;
  min-height: 5.3125rem;
}

.photo {
  padding-top: 0.625rem;
  display: table-cell;
  width: 3.5rem;
}
.photo .avatar {
  height: 2.25rem;
  width: 2.25rem;
  border-radius: 50%;
  background-size: contain;
}

.comment-block {
  padding: 1rem;
  background-color: #fff;
  display: table-cell;
  vertical-align: top;
  border-radius: 0.1875rem;
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.08);
}
.comment-block textarea {
  width: 100%;
  resize: none;
}

.comment-text {
  margin-bottom: 1.25rem;
}

.bottom-comment {
  color: #acb4c2;
  font-size: 0.875rem;
}

.comment-date {
  float: left;
}

.comment-actions {
  float: right;
}
.comment-actions li {
  display: inline;
  margin: -2px;
  cursor: pointer;
}
.comment-actions li.complain {
  padding-right: 0.75rem;
  border-right: 1px solid #e1e5eb;
}
.comment-actions li.reply {
  padding-left: 0.75rem;
  padding-right: 0.125rem;
}
.comment-actions li:hover {
  color: #0095ff;
}
</style></head>
<div class="comments">
    
<?php

if ($user->status =="0") {
   echo '<div class="alert alert-danger" role="alert">
  This ticket has been closed, please open another ticket for possible support
</div>';
  }  
?>
    <div class="comment-wrap">
       
        <div class="comment-block">
          <h3><?php echo $user->subject; ?> </h3>
            <p class="comment-text"><?php echo $user->description; ?></p>
            <div class="bottom-comment">
                <div class="comment-date"><?php echo $user->date; ?></div>
                <ul class="comment-actions">
                  <?php if ($user->status ==1) {
                    echo '<li class="complain" stye="font-size: 20px;">Status : <span style="color: green; font-size: 20px;">Open</span></li>';
                  }
                  else{
                    echo '<li class="complain" stye="font-size: 20px;">Status : <span style="color: red; font-size: 20px;">Closed</span></li>';
                  }
                    ?>
                    <li class="reply"><form method="POST" action="">
                      <input type="hidden" name="tiketId" value="<?php echo $user->id; ?> ">
                       <input type="submit" name="CloseTicket" value="Close" class="btn btn-danger">
                    </form></li>
                </ul>
            </div>
        </div>
    </div>

    <?php TicketReplies($user_id,$ticketID); ?>
<?php 
if ($user->status =="1") {
  ?>
   <div class="comment-wrap">
        <div class="photo">
           <i class="fa fa-pencil-square-o" style="font-size: 40px;"></i>
        </div>
        <div class="comment-block">
            <form action="" method="POST">
              <input type="hidden" name="tiketId" value="<?php echo $user->id; ?> ">
                <textarea name="ticketpost" id="" cols="30" rows="3" placeholder="Add comment..."></textarea>
                <input type="submit" name="TicketReply" value="Post reply" class="btn btn-primary btn-lg">
            </form>
        </div>
    </div>
    <?php
  } 
    
 
}
}
else
{
 ?>
   <div class="row">
       <div class="col-md-4">
        <div class="list-group">
                    <a href="message.php" class="list-group-item">Messages</a>
                    <a href="newticket.php" class="list-group-item">Create Ticket</a>
                    <a href="tickets.php" class="list-group-item list-group-item-success">Tickets <span class="badge bg-red badge-corner"><?php replyTicketCount($user_id); ?></span></a>
                </div>
        </div>

         <div class="col-md-8">
        <div class="list-group">
               <?php  fectAllTickets($user_id); ?>
                </div>
        </div>

       
     <?php
}

?>
  </div>
     </section>
<?php include '_footer.php'; ?>
