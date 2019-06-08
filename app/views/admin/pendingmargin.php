<?php if (!Auth::userCan('message_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>
<?php
$users = DB::table('users')->get();

 
if (isset($_POST['DisengageNow'])) {
	$id       = $_POST['id'];
	DisengageMargingNow($id);
}
 

if (isset($_POST['SetMargin'])) {
	$id       = $_POST['id'];
	SettMargin($id); 
  }

if (isset($_POST['UnsetMargin'])) {
	$id       = $_POST['id'];
	UnsettMargin($id); 
  }
?>

<?php 
$marginSet = DB::table('settings')->where('id', 1)->first(); 

if ($marginSet->margintype ==1) {
	?>
 <form method="POST" action=""> 
  	<input type="hidden" name="id" value="1">
	<input type="submit" class="btn btn-success btn-lg" name="UnsetMargin" value="Manual Margin" >
	 </form>
	<?php
}
elseif ($marginSet->margintype ==2) {
	?>
 <form method="POST" action=""> 
  	<input type="hidden" name="id" value="1">
	<input type="submit" class="btn btn-danger btn-lg" name="SetMargin" value="Auto Margin" >
	 </form>
	<?php
}
?>
 <h3 class="page-header">
	All Marged Members
</h3>

<form action="" method="POST" id="messages_form">
	
	<ul class="dt-filter role-filter">
				<li class="active">
			<a href="admin.php?page=allmargin" data-role="">All <span class="count">(<?php AllMarginCountCount(); ?>)</span>
			</a> |
		</li>
		<li>
				<a href="admin.php?page=confirmmargin" data-role="User">Approved 
					<span class="count">(<?php ConfirmmarchinCountCount(); ?>)</span>
				</a>
				|	</li>
					<li>
				<a href="admin.php?page=pendingmargin" data-role="3">Pending Approval
					<span class="count">(<?php pendingmarchinCountCount(); ?>)</span>
				</a>
					</li>

					<li>
				<a href="admin.php?page=waitingmargin" data-role="3">Waiting Approval
					<span class="count">(<?php WaitingmarchinCountCount(); ?>)</span>
				</a>
					</li>
			
			</ul>


	<table class="table table-striped table-bordered table-hover table-dt" id="messages">
		<thead>
			<tr>
				<th><input type="checkbox" class="select-all" value="1"></th>
				<th>Sender</th>
				<th>Receiver</th>
				<th>Amount</th>
				<th>Balance</th>
				<th>Status</th>
				<th>Action</th>
			</tr>
		</thead>

		   <?php GetAllMarginPeningReq(); ?>
		<tbody>
		</tbody>
	</table>
</form>



<?php echo View::make('admin.footer')->render() ?>