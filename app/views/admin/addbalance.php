<?php if (!Auth::userCan('manage_settings')) page_restricted();

$settings = DB::table('settings')->where('id', 1)->first();

?>
<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.general_settings') ?></h3>
<?php 
$id = $settings->id;
if (isset($_POST['submit'])) {
	$balance  = $_POST['balance'];
	$username  = $_POST['username'];
	
   	AddBanlanceGetHelp($username,$balance); 
 
  }


if (isset($_POST['bankBalance'])) {
	$balance  = $_POST['balance'];
	$username  = $_POST['username'];
	
   	AddBanlance($username,$balance); 
 
  }
?>
<div class="row">
	<div class="col-md-6">
	

		<form action="" method="POST">
			<?php csrf_input() ?>
			
			

			<div class="form-group">
				<label for="debug">Add Balance to user Get-Help</label>
			  
		        <input type="text" name="balance" class="form-control">
		        <p class="help-block">Please enter user balance</p>
		    </div>
				
			<div class="form-group">
		        <label for="url">Member Username</label>
		        <input type="text" name="username" class="form-control">
		        <p class="help-block">Please enter correct username to avoid error</p>
		    </div>

		
			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary">Add Get-Help Balance</button>
			</div>
		</form>
	</div>



		<div class="col-md-6">
	

		<form action="" method="POST">
			<?php csrf_input() ?>
			


			<div class="form-group">
				<label for="debug">Add Balance to user Bank Balance</label>
			  
		        <input type="text" name="balance" class="form-control">
		        <p class="help-block">Please enter user balance</p>
		    </div>
				
			<div class="form-group">
		        <label for="url">Member Username</label>
		        <input type="text" name="username" class="form-control">
		        <p class="help-block">Please enter correct username to avoid error</p>
		    </div>

		
			<div class="form-group">
				<button type="submit" name="bankBalance" class="btn btn-primary">Add Bank Balance</button>
			</div>
		</form>
	</div>
</div>
<?php echo View::make('admin.footer')->render() ?>