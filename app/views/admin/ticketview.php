<?php if (!Auth::userCan('message_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>
<?php

if (empty($_GET['reply']) || !is_numeric($_GET['reply'])) {
	redirect_to('?page=support');
}
$userid = $_GET['reply'];


if (isset($_POST['replyTicket'])) {
	$message       = $_POST['message'];
	ReplyTickets($message,$userid);
}
?>


 <h3 class="page-header">
	Reply Ticket
</h3>
<div class="row">
<div class="col-md-2">
<div class="list-group">
</div>
</div>

<div class="col-md-8">
	


   
        <div class="panel panel-default widget">
            <div class="panel-heading">
                <span class="glyphicon glyphicon-comment"></span>
                <h3 class="panel-title">
                   Ticket Reply</h3>
            
                    
            </div>
            <div class="panel-body">
                <ul class="list-group">
                 
              <?php TicketReplyView($userid); ?>
              <ul class="list-group" style="margin-left: 25px;margin-top: 5px;margin-bottom: 5px;">
                 
              <?php TicketReplyViewReply($userid); ?>
                </ul>
                </ul>
                
            </div>
            <div class="col">


                    <div class="panel-body">
                        <form role="form" method="POST" action="">
                            <fieldset>
                                <div class="form-group">
    <textarea class="form-control"  name="message" rows="3" placeholder="Write in your wall" autofocus=""></textarea>
 	

                                </div>
                                
                            
                        <input  type="submit" class="[ btn btn-success ]" name="replyTicket" value="Post reply">
                            </fieldset>
                        </form>
                    </div>
                        </div>

                </div>
</div>
        </div>


</div>
</div>





<?php echo View::make('admin.footer')->render() ?>