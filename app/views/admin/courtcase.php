<?php if (!Auth::userCan('message_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>
<?php
$users = DB::table('users')->get();


if (isset($_POST['submit'])) {
	$id       = $_POST['id'];
	$status     = $_POST['status'];
	ChangeCaseStatus($id,$status);
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
	<h3>All Pending Court Case</h3>
    <?php CourtCaseShow(); ?>
<hr>
<hr>
<hr>
	<h3>All Resolved Court Case</h3>
    <?php ResolvedCourtCaseShow(); ?>
</div>


</div>



<?php echo View::make('admin.footer')->render() ?>