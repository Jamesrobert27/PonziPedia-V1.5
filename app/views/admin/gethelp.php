<?php if (!Auth::userCan('manage_settings')) page_restricted();

$settings = DB::table('settings')->where('id', 1)->first();

?>
<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.general_settings') ?></h3>
<?php 
$id = $settings->id;
if (isset($_POST['submit'])) {
	$packages  = $_POST['packages'];
	$username  = $_POST['username'];
	  if ($packages =="NULL") {
                echo '<div class="alert alert-danger" role="alert">
                You need to choose user package
              </div>';
   }elseif ($username =="") {
                echo '<div class="alert alert-danger" role="alert">
                You need to center username and make sure its valid
              </div>';
   }
   else {
   	SetmemberMargin($username,$packages); 
   }
	
  }

?>
<div class="row">
	<div class="col-md-6">
	

		<form action="" method="POST">
			<?php csrf_input() ?>
			
			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary">Create Get-Help</button>
			</div>

			<div class="form-group">
				<label for="debug">Set User To receive Marging </label>
				<select name="packages" class="form-control">

	        		<option value="NULL">Please Select Package</option>
					<?php  
					$packagess =DB::table('packages')->orderBy('id', 'DESC')->get();
                     foreach ($packagess as $row) {
	        		echo '<option value="'.$row->id.'">'.$row->packname.' | '.$settings->currency.''.$row->price.'</option>';
	        	       }
	        	       $settings = DB::table('settings')->where('id', 1)->first();
	        		?>
				</select>
				<p class="help-block">Package determine amount user will get and this will include the <?php echo $settings->profit; ?>% ROI which means <?php $packages =DB::table('packages')->orderBy('id', 'DESC')->get();
                     foreach ($packages as $row) {
                      
                     	 $percentage = $settings->profit;
                         $totalWidth = $row->price;
                         $new_amount = ($percentage / 100) * $totalWidth;


                      $total = $row->price + $new_amount;
                      	$prints = '( '.$settings->currency.'' . $row->price . ' =  '.$settings->currency.'' .  $total  . ' )';
                      	echo $prints;
                      	}
                      	?>
                      </p>
			</div>
			
			<div class="form-group">
		        <label for="url">Member Username</label>
		        <input type="text" name="username" class="form-control">
		        <p class="help-block">Please enter correct username to avoid error</p>
		    </div>

		
			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary">Create Get-Help</button>
			</div>
		</form>
	</div>
</div>
<?php echo View::make('admin.footer')->render() ?>