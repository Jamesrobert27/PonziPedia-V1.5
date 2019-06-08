<?php if (!Auth::userCan('message_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>
<?php
$users = DB::table('users')->get();

 
if (isset($_POST['SingleMail'])) {
	$messages       = $_POST['messages'];
	$subjectss       = $_POST['subject'];
	$email          = $_POST['usermail'];
	SingleMailNow($messages,$subjectss,$email);
}


if (isset($_POST['MassMail'])) {
	$messagesss       = $_POST['message'];
	$subjectss       = $_POST['subject'];
	MassMailNow($messagesss,$subjectss);
}

if (isset($_POST['delete'])) {
  $id = $_POST['id'];
 $sql = DB::table('subscriber')->where('id', $id)->delete();
if ($sql) {
  echo '<div class="alert alert-success alert-dismissable">
     <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
      <strong>Well done!</strong> You successfully deleted user request mergin.
   </div>';
}else{
    echo ' <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <strong>Oh snap!</strong> Change a few things up and try submitting again.
            </div>';
}
} 
?>
<div class="modal fade" id="sendMass" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onClick="window.location.reload()">×</button>
        <h4 class="modal-title" id="myModalLabel">Send Mass Mail</h4>
      </div>
      <div class="modal-body">
     <h2>Send Mass Mail to all your subscribers</h2>
       <form role="form" method="POST" action="">
        <fieldset>
        	 <div class="form-group">
    <input type="text" name="subject" class="form-control" placeholder="Your subject here">
     </div>
       <div class="form-group">
    <textarea class="form-control"  name="message" rows="3" placeholder="Write in your wall" autofocus=""></textarea>
     </div>
     <input  type="submit" class="[ btn btn-success ]" name="MassMail" value="Send Mass Mail">
     </fieldset>
     </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal" onClick="window.location.reload()">Close</button>
      </div>
    </div>
  </div>
</div>

 <h3 class="page-header">
	All request Margin    <a data-toggle="collapse" data-parent="#accordion" href="#sendMass" class="btn btn-primary">Send Mass Mail</a>
</h3>

<form action="" method="POST" id="messages_form">
	<table class="table table-striped table-bordered table-hover table-dt" id="messages">
		<thead>
			<tr>
				<th><input type="checkbox" class="select-all" value="1"></th>
				<th>Email</th>
				<th>Joined</th>
        <th>delete</th>
				<th>Action</th>
			</tr>
		</thead>

		   <?php SubscriberView(); ?>
		<tbody>
		</tbody>
	</table>
</form>






<?php echo View::make('admin.footer')->render() ?>