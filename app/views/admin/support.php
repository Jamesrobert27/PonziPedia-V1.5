<?php if (!Auth::userCan('message_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>
<?php
$users = DB::table('users')->get();


if (isset($_POST['margeNow'])) {
	$Amount       = $_POST['amount'];
	$username     = $_POST['sendername'];
	$receivername = $_POST['receivername'];
	$sendername   = $_POST['sendername'];
	$package_id   = $_POST['package_id'];
	StartMargin($Amount,$username,$receivername,$sendername,$package_id);
}


if (isset($_POST['SetMargin'])) { 
	$id       = $_POST['id'];
	SettMargin($id); 
  }

if (isset($_POST['UnsetMargin'])) {
	$id       = $_POST['id'];
	UnsettMargin($id); 
  }



if (isset($_POST['deleteUser'])) {
	$id  = $_POST['id'];
	DeleteMessageU($id);
}
?>


 <h3 class="page-header">
	Support System
</h3>
<div class="row">
<div class="col-md-3">
	<h3>Support Link</h3>
<div class="list-group">
 <a href="?page=support" class="list-group-item">Tickets <span class="badge"><?php SeupportTicketCount(); ?></span></a>
   <a href="?page=courtcase" class="list-group-item">Court Case<span class="badge"><?php SeupportCourtCaseCount(); ?></span></a>
</div>
</div>

<div class="col-md-9">
	<h3>All Member Tickets</h3>
	<ul class="list-group">
   <?php GetUserTickets(); ?>
</ul>
</div>

</div>



<?php echo View::make('admin.footer')->render() ?>