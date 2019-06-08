<?php if (!Auth::userCan('message_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>
<?php
$users = DB::table('users')->get();
 

if (isset($_POST['ConfirmPayment'])) {
	$id       = $_POST['id'];
	ConfirmActivation($id);
}
 
?>



 <h3 class="page-header">
	All Activation Fees 
</h3>

<form action="" method="POST" id="messages_form">
	<ul class="dt-filter role-filter">
				<li class="active">
			<a href="admin.php?page=activationfees" data-role="">All <span class="count">(<?php AllActivationCountCount(); ?>)</span>
			</a> |
		</li>
		<li>
				<a href="admin.php?page=activationpaid" data-role="User">Approved 
					<span class="count">(<?php ApprovedActivationCountCount(); ?>)</span>
				</a>
				|	</li>
					<li>
				<a href="admin.php?page=activationpending" data-role="3">Pending Approval
					<span class="count">(<?php PendingActivationCountCount(); ?>)</span>
				</a>
					</li>

					<li>
				<a href="admin.php?page=activationwating" data-role="3">Waiting Approval
					<span class="count">(<?php WaitingActivationCountCount(); ?>)</span>
				</a>
					</li>
			
			</ul>
	<table class="table table-striped table-bordered table-hover table-dt" id="messages">
		<thead>
			<tr>
				<th><input type="checkbox" class="select-all" value="1"></th>
				<th>Username</th>
				<th>Names</th>
				<th>Receiver Name</th>
				<th>Amount</th>
				<th>Status</th>
				<th>POF</th>
				<th>Action</th>
			</tr>
		</thead>

		   <?php GetactivationPending(); ?>
		<tbody>
		</tbody>
	</table>
</form>



<?php echo View::make('admin.footer')->render() ?>