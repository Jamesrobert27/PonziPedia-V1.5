<?php if (!Auth::userCan('message_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>
<?php
$users = DB::table('users')->get();

if (isset($_POST['Deletesubmit'])) { 
 $id  = $_POST['test_id'];
 $sql = DB::table('testimony')->where('id', $id)->delete();
 if ($sql) {
 	 DB::table('testimoneytvotes')->where('comment_id', $id)->delete();
 	 echo'<div class="alert alert-success" role="alert">
      You have successfully deleted user testimony
      </div>';
 }
}

?>
<h3 class="page-header">

	All Members testimony
   
</h3>

<form action="" method="POST" id="messages_form">
	<ul class="dt-filter role-filter">
				<li class="active">
			<a href="admin.php?page=testimonyall" data-role="">All <span class="count">(<?php TestimonyAllCount(); ?>)</span>
			</a> |
		</li>
		<li>
				<a href="admin.php?page=testimonies" data-role="User">Approved 
					<span class="count">(<?php TestimonyApprovedCount(); ?>)</span>
				</a>
				|	</li>
					<li>
				<a href="admin.php?page=testimony" data-role="3">Pending Approval
					<span class="count">(<?php TestimonyPeningCount(); ?>)</span>
				</a>
					</li>
			
			</ul>
	<table class="table table-striped table-bordered table-hover table-dt" id="messages">
		<thead>
			<tr>
				<th><input type="checkbox" class="select-all" value="1"></th>
				<th>Username</th>
				<th>Title</th>
				<th>Testimoney</th>
				<th>Date</th>
				<th><span class="glyphicon glyphicon-thumbs-up"></span></th>
				<th><span class="glyphicon glyphicon-thumbs-down"></span></th>
				<th>Status</th>
				<th>Delete</th>
			</tr>
		</thead>

		   <?php MembersTestimonyAll(); ?>
		<tbody>
		</tbody>
	</table>
</form>



<?php echo View::make('admin.footer')->render() ?>