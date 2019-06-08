<?php if (!Auth::userCan('list_users')) page_restricted(); ?>

<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header">
	<?php _e('admin.users') ?>
	<a href="?page=user-new" class="btn btn-default btn-sm"><?php _e('admin.add_new') ?></a>
</h3>

<link href="<?php echo asset_url('css/vendor/dataTables.bootstrap.css') ?>" rel="stylesheet">
<script src="<?php echo asset_url('js/vendor/jquery.dataTables.min.js') ?>"></script>
<script src="<?php echo asset_url('js/vendor/dataTables.bootstrap.js') ?>"></script>
<script>
	$(document).ready(function() {
		EasyLogin.options.datatables = <?php echo json_encode(trans('datatables')); ?>;
		EasyLogin.admin.usersDT();
	});
</script>

<?php if (Session::has('user_added')): ?>
	<div class="alert alert-success alert-dismissible">
		<span class="close" data-dismiss="alert">&times;</span>
		<?php _e('admin.user_created') ?>
		<a href="?page=user-edit&id=<?php echo Session::get('user_id'); ?>" class="alert-link"><?php _e('admin.edituser') ?></a>
	</div>
	<?php Session::deleteFlash(); ?>
<?php endif ?>

<?php if (!Config::get('auth.require_username')): ?>
	<style>
		#users tr th:nth-child(2) {display: none;}
		#users tr td:nth-child(2) {display: none;}
	</style>
<?php endif; ?>

<form action="" method="POST" id="users_form">
	<ul class="dt-filter role-filter">
		<?php $roles = Role::all(); $k = 0; ?>
		<li class="active">
			<a href="#" data-role=""><?php _e('admin.all') ?>
				<span class="count">(<?php echo User::count('id') ?>)</span>
			</a> |
		</li>
		<?php foreach ($roles as $role): $k++; ?>
			<li>
				<a href="#" data-role="<?php echo $role->name ?>"><?php echo $role->name ?> 
					<span class="count">(<?php echo User::where('role_id', $role->id)->count('id') ?>)</span>
				</a>
				<?php echo count($roles) > $k ? '|':'' ?>
			</li>
		<?php endforeach ?>
	</ul>
	<table class="table table-striped table-bordered table-hover table-dt" id="users">
		<thead>
			<tr>
				<th><input type="checkbox" class="select-all" value="1"></th>
				<th><?php _e('admin.username') ?></th>
				<th><?php _e('admin.email') ?></th>
				<th><?php _e('admin.display_name') ?></th>
				<th><?php _e('admin.joined') ?></th>
				<th><?php _e('admin.account_status') ?></th>
				<th><?php _e('admin.role') ?></th>
				<th><?php _e('admin.action') ?></th>
			</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</form>


<!-- Delete user Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="deleteUser" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('admin.confirm_action') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>
					<input type="hidden" name="user_id">
	          		<p><?php _e('admin.confirm_delete_user', array('user' => '<b class="user"></b>')) ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('admin.no'); ?></button>
					<button type="submit" class="btn btn-danger"><?php _e('admin.yes') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>


<!-- Delete users Modal -->
<div class="modal fade" id="deleteUsersModal" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<form action="deleteUsers" class="ajax-form">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title"><?php _e('admin.confirm_action') ?></h4>
				</div>
				<div class="modal-body">
					<div class="alert"></div>
					<input type="hidden" name="users">
	          		<p><?php _e('admin.confirm_delete_users', array('users' => '<b class="users"></b>')) ?></p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('admin.no') ?></button>
					<button type="submit" class="btn btn-danger"><?php _e('admin.yes') ?></button>
				</div>
			</form>
		</div>
	</div>
</div>

<?php echo View::make('admin.footer')->render() ?>
