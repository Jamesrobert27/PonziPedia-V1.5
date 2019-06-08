<?php if (!Auth::userCan('manage_settings')) page_restricted();

$settings = DB::table('settings')->where('id', 1)->first();

?>
<?php echo View::make('admin.header')->render() ?>

<h3 class="page-header"><?php _e('admin.general_settings') ?></h3>
<?php 
$id = $settings->id;
if (isset($_POST['submit'])) { 
	$margintype            = $_POST['margintype'];
	$profit                = $_POST['profit'];
	$getHelpDay            = $_POST['getHelpDay'];
	$ProvideHelpday        = $_POST['ProvideHelpday'];
	$reccomitment          = $_POST['reccomitment'];
	$referralProfit          = $_POST['referralProfit'];
	$guiderProfit          = $_POST['guiderProfit'];
	$GuiderMin              = $_POST['GuiderMin'];
	$days                  = $_POST['days'];
	$activationFeeSwitch   = $_POST['activationFeeSwitch'];
	$activationAmount      = $_POST['activationAmount'];
	$activationdays        = $_POST['activationdays'];
	$currency              = $_POST['currency'];
	$site              = $_POST['site'];
	$regmode              = $_POST['regmode'];
	$invitecode              = $_POST['invitecode'];
	$sms              = $_POST['sms'];
	SettingMain($id,$margintype,$profit,$getHelpDay,$ProvideHelpday,$reccomitment,$referralProfit,$guiderProfit,$GuiderMin,$days,$activationFeeSwitch,$activationAmount,$activationdays,$site,$currency,$regmode,$invitecode,$sms); 
  }

?>
<div class="row">
	<div class="col-md-6">
	

		<form action="" method="POST">
			<?php csrf_input() ?>
			
			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.save_changes') ?></button>
			</div>

          <div class="form-group">
				<label for="debug">Website Status </label>
				<select name="site" class="form-control">
	        		<option value="1" <?php echo $settings->site_status == '1' ? 'selected':'' ?>>Live Mode</option>
	        		<option value="0" <?php echo $settings->site_status == '0' ? 'selected':'' ?>>Mentainace </option>
				</select>
				<p class="help-block">Set website to mentainance mode if you are working on settings and others</p>
			</div>
			<div class="form-group">
				<label for="debug">Marging Settings </label>
				<select name="margintype" class="form-control">
	        		<option value="1" <?php echo $settings->margintype == '1' ? 'selected':'' ?>><?php _e('admin.yes') ?></option>
	        		<option value="0" <?php echo $settings->margintype == '2' ? 'selected':'' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block">Yes|No the setting automatic Margin is Yes and NO means Setting to Manual Margin</p>
			</div>
			
			 <div class="form-group">
				<label for="debug">Allow Registeration</label>
				<select name="regmode" class="form-control">
	        		<option value="0" <?php echo $settings->registration == '0' ? 'selected':'' ?>><?php _e('admin.yes') ?></option>
	        		<option value="1" <?php echo $settings->registration == '1' ? 'selected':'' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block">Yes|No the setting Allow Registeration is Yes and NO means turn off website Registeration</p>
			</div>
             
             <div class="form-group">
		        <label for="url">Invitation code</label>
		        <input type="text" name="invitecode" value="<?php echo $settings->invitecode; ?>" class="form-control">
		        <p class="help-block">Invitation code to allow member with invite link register account on your website, auto generate random invitation code <a href="admin.php?page=generate_key" target="_BLANK">here</a> <br><a href="<?php echo Config::get('app.url'); ?>account/auth/register.php?invite=<?php echo $settings->invitecode; ?>" target="_BLANK"><?php echo Config::get('app.url'); ?>account/auth/register.php?invite=<?php echo $settings->invitecode; ?></a></p>
		    </div>

			<div class="form-group">
		        <label for="url">Setting ROI Percentage </label>
		        <input type="text" name="profit" value="<?php echo $settings->profit; ?>" class="form-control">
		        <p class="help-block">Set user profit of percentage of their packages. 40 means ROI</p>
		    </div>

		    <div class="form-group">
		        <label for="name">Setting Days To Get ROI</label>
		        <input type="text" name="getHelpDay" id="name" value="<?php echo $settings->getHelpDay; ?>" class="form-control">
		        <p class="help-block">The total days user have to wait before they get marging to receive</p>
		    </div>


		  		    <div class="form-group">
		        <label for="name">Setting Days To Payout</label>
		        <input type="text" name="ProvideHelpday" id="name" value="<?php echo $settings->ProvideHelpday; ?>" class="form-control">
		        <p class="help-block">The total days user have to wait before they get marging to payout</p>
		    </div>

            
		    <div class="form-group">
		        <label for="name">Recommitment percentage</label>
		        <input type="text" name="reccomitment"  value="<?php echo $settings->reccomitment; ?>" class="form-control">
		        <p class="help-block">The total percentage user will recommit automatically EG: 10 or 20</p>
		    </div>

		      <div class="form-group">
		        <label for="name">Days To Release Packages After Created</label>
		        <input type="text" name="days"  value="<?php echo $settings->days; ?>" class="form-control">
		        <p class="help-block">This setting work with the packages and it determine the day it can be available for purchase to users</p>
		    </div>
            
             
             <div class="form-group">
		        <label for="name">Referral percentage</label>
		        <input type="text" name="referralProfit"  value="<?php echo $settings->referralProfit; ?>" class="form-control">
		        <p class="help-block">The total percentage user will get on each member referred EG: 10 or 20</p>
		    </div>

             <div class="form-group">
		        <label for="name">Parent guide percentage</label>
		        <input type="text" name="guiderProfit"  value="<?php echo $settings->guiderProfit; ?>" class="form-control">
		        <p class="help-block">The total percentage parent guider will get on each users under their tree EG: 10 or 20</p>
		    </div>


           <div class="form-group">
		        <label for="name">Parent guide Minimum Withdrawal</label>
		        <input type="text" name="GuiderMin"  value="<?php echo $settings->GuiderMin; ?>" class="form-control">
		        <p class="help-block">The total parent guider Minimum Withdrawal EG: 1000 or 2000</p>
		    </div>


            <div class="form-group">
				<label for="debug">Activation Fees Settings </label>
				<select name="activationFeeSwitch" class="form-control">
	        		<option value="1" <?php echo $settings->activationFee == '1' ? 'selected':'' ?>><?php _e('admin.yes') ?></option>
	        		<option value="0" <?php echo $settings->activationFee == '0' ? 'selected':'' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block">Yes|No the setting turn on activation fees is Yes and NO means turn off activation fees</p>
			</div>


		     <div class="form-group">
		        <label for="name">Activation Fees Amount</label>
		        <input type="text" name="activationAmount"  value="<?php echo $settings->activationPrice; ?>" class="form-control">
		        <p class="help-block">Activation Fees Amount only numbers EG: 200</p>
		    </div>

		     <div class="form-group">
		        <label for="name">Activation Fees Expiring days</label>
		        <input type="text" name="activationdays"  value="<?php echo $settings->activationFeeExp; ?>" class="form-control">
		        <p class="help-block">The total time user have to pay activation fees before they got bllocked EG: 1 or 2 </p>
		    </div>
            
             <div class="form-group">
		        <label for="name">System Currency sign</label>
		        <input type="text" name="currency"  value="<?php echo $settings->currency; ?>" class="form-control">
		        <p class="help-block">System price and amount currency Symbol. EG: $, ₦, ¥, ₡, £, €</p>
		    </div>
          
             <div class="form-group">
				<label for="debug">Allow SMS Notofication</label>
				<select name="sms" class="form-control">
	        		<option value="1" <?php echo $settings->smsallow == '1' ? 'selected':'' ?>><?php _e('admin.yes') ?></option>
	        		<option value="0" <?php echo $settings->smsallow == '0' ? 'selected':'' ?>><?php _e('admin.no') ?></option>
				</select>
				<p class="help-block">Set your SMS APi details from your config file located <code>app/config/app.php</code> before using this feature</p>
			</div>
		
			<div class="form-group">
				<button type="submit" name="submit" class="btn btn-primary"><?php _e('admin.save_changes') ?></button>
			</div>
		</form>
	</div>
</div>
<?php echo View::make('admin.footer')->render() ?> 