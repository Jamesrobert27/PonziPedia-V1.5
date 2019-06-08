<?php if (!Auth::userCan('manage_settings')) page_restricted();

$settings = DB::table('settings')->where('id', 1)->first();

?>
<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header">Create Packages & View Packages</h3>
<?php 
$id = $settings->id;
if (isset($_POST['submit'])) {
	$price  = $_POST['amount'];
	$name  = $_POST['name'];
	$lockPack  = $_POST['lockPackage'];
	if ($lockPack ==1) {
		$codes = mt_rand(10000000, 99999999);
	}
	elseif ($lockPack ==0) {
		$codes ="";
	}
	CreatePackages($price,$name,$codes); 
  }

  if (isset($_POST['Deletesubmit'])) {
	$id  = $_POST['pack_id'];
	DeletePackages($id); 
  }

?>
<div class="row">
	<div class="col-md-6">
	

		<?php packagesView(); ?>
	</div>


	<div class="col-md-6">
	
      <div class="alert alert-danger alert-dismissable" style="background-color: #222;border-color: #222;color: #fff;">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <center><strong>Important!</strong> Set your website settings and others before creating package,<br><a href="?page=settings" class="btn btn-success btn-sm">Website Settings</a></center> 
            </div>
		<form action="" method="POST">
			<?php csrf_input() ?>
			
			

				<div class="form-group">
		        <label for="url">Package name </label>
		        <input type="text" name="name" class="form-control">
		        <p class="help-block">Set simple and unique name for your packages</p>
		    </div>
			 
			<div class="form-group">
		        <label for="url">Package Price</label>
		        <input type="text" name="amount" class="form-control">
		        <p class="help-block">You can set any amount for this package</p>
		    </div>

		  	<div class="form-group">
				<label for="debug">Lock Package</label>
				<select name="lockPackage" class="form-control">
	        		<option value="1"><?php _e('admin.yes') ?></option>
	        		<option value="0"><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block">(Yes|No ) If value is yes the package will be locked and user will need codes to unclock the packages, code will be generated automatically and can only be view by administrators</p>
			</div>

			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary">Create Package</button>
			</div>
		</form>
	</div>
</div>
<?php echo View::make('admin.footer')->render() ?>