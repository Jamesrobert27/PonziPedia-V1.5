<?php echo View::make('admin.header')->render() ?>
<?php  $settings = DB::table('settings')->where('id', 1)->first(); ?>
<h3 class="page-header"><?php _e('admin.dashboard') ?></h3>

<div class="row">

	<div class="col-md-12" style="padding-left: 0px;padding-right: 0px;">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php echo Config::get('app.name') ?> System Stats</h3>
			</div>
			<div class="panel-body">
				<div class="row text-center">
					<div class="col-md-3">
					<ul class="list-group">
					<li class="list-group-item" style="font-size: 30px !important;">
				    	
				    	Pending Balance<br>
				    	<?php
				    	 $py = DB::table('requestMaching')->sum('balance');
				    	 if ($py >0) {
				    	 	 echo $settings->currency."".$py;
				    	 }else{
				    	 	$py =0;
				    	 echo $settings->currency. $py; 
				    	}
				    	 ?>
				  	</li>
					</ul>
				</div>
					<div class="col-md-3">
					<ul class="list-group">
					<li class="list-group-item" style="font-size: 30px !important;">
				    	
				    	Pending Payout<br>
				    	<?php 
				    	$pp = DB::table('marching')->where('payment_status', '!=', 'confirm')->sum('amount');
				    	 if ($pp >0) {
				    	 	 echo $settings->currency."".$pp;
				    	 }else{
				    	 	$pp = 0;
				    	 echo $settings->currency. $pp; 
                           }
				    	  ?>
				  	</li>
					</ul>
				</div>
					<div class="col-md-3">
					<ul class="list-group">
					<li class="list-group-item" style="font-size: 30px !important;">
				    	
				    	Total Paid<br>
				    	<?php
				    	$ppaid = DB::table('marching')->where('payment_status', 'confirm')->sum('amount');
				    	if ($ppaid == 0) {
				    		$ppaid =0;
				    		echo $settings->currency. $ppaid;
				    	}
				    	else{
				    	 echo $settings->currency."".$ppaid; }?>
				    	
				  	</li>
					</ul>
				</div>
					<div class="col-md-3">
					<ul class="list-group">
					<li class="list-group-item" style="font-size: 30px !important;">
				    	
				    	Total Balance<br>
				    	<?php $pys = DB::table('requestMaching')->sum('balance');
				    	$pbank = DB::table('bank')->sum('balance');
				    	$balance =  $py + $pp +$ppaid +$pbank; 
				    	  echo $settings->currency."".$balance; ?>
				  	</li>
					</ul>
				</div>
			
			</div>
		</div>
	</div>
</div>

<div class="card">
                <div class="card-header">
                  <h4>Pending Notifications</h4>
                </div>
                <div class="card-body">
                  <p>
                    <a href="admin.php?page=getmargin" style="width: auto !important;" class="btn btn-primary btn-lg"><span class="ladda-label">GetHelp Request <span class="label label-danger"><?php getHelpCountCount(); ?></span></a>
                   <a href="admin.php?page=reqestmarging" style="width: auto !important;" class="btn btn-primary btn-lg"><span class="ladda-label">Provide-Help Request <span class="label label-danger"><?php ProvideHelpCount(); ?></span></a>
                   	<a href="admin.php?page=allmargin" style="width: auto !important;" class="btn btn-primary btn-lg"><span class="ladda-label">Pending Margin <span class="label label-danger"><?php PendingMarginCount(); ?></span></a>
                   	<a href="admin.php?page=support" style="width: auto !important;" class="btn btn-primary btn-lg"><span class="ladda-label">Pending Ticket <span class="label label-danger"><?php SeupportActiveCount(); ?></span></a>
                   	<a href="admin.php?page=testimony" style="width: auto !important;" class="btn btn-primary btn-lg"><span class="ladda-label">Pending Testimony <span class="label label-danger"><?php TestimonyPeningCount(); ?></span></a>
                   	<a href="admin.php?page=packages" style="width: auto !important;" class="btn btn-primary btn-lg"><span class="ladda-label">Package <span class="label label-danger"><?php AllPackagesCount(); ?></span></a>
                  </p>
                </div>
              </div>
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php _e('admin.user_stats') ?></h3>
			</div>
			<div class="panel-body">
				<ul class="list-group">
					<li class="list-group-item">
				    	<span class="badge badge-primary"><?php echo User::count(); ?></span>
				    	<?php _e('admin.total_users') ?>
				  	</li>
					<li class="list-group-item">
				    	<span class="badge badge-success"><?php echo User::where('status', 1)->count(); ?></span>
				    	<?php _e('admin.activated_users') ?>
				  	</li>
				  	<li class="list-group-item">
				    	<span class="badge badge-warning"><?php echo User::where('status', 0)->count(); ?></span>
				    	<?php _e('admin.unactivated_users') ?>
				  	</li>
				  	<li class="list-group-item">
				    	<span class="badge badge-danger"><?php echo User::where('status', 2)->count(); ?></span>
				    	<?php _e('admin.suspended_users') ?>
				  	</li>
				</ul>
			</div>
		</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title"><?php _e('admin.latest_users') ?></h3>
			</div>
			<div class="panel-body">
				<ul class="list-group">
					<?php $users = User::orderBy('joined', 'desc')->take(5)->get(); ?>
					<?php foreach ($users as $user) : ?>
						<li class="list-group-item">
							<a href="?page=user-edit&id=<?php echo $user->id ?>" class="pull-right"><?php _e('admin.edit') ?></a>
							<a href="profile.php?u=<?php echo $user->id ?>" target="_blank"><?php echo $user->display_name ?></a>
							<span class="help-block"><?php echo with(new DateTime($user->joined))->format('M j, Y \a\t h:i'); ?></span>
						</li>
					<?php endforeach ?>
				</ul>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<?php 
		$providers = Config::get('auth.providers', array());
		$oauth = array();

		if (count($providers)) {
			foreach ($providers as $key => $provider) {
				$value = Usermeta::newQuery()->where('meta_key', "{$key}_id")->count();
				
				if ($value > 0) $oauth[$key] = $value;
			}
		}
		?>
		<?php if (count($oauth)): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e('admin.connected_users') ?></h3>
				</div>
				<div class="panel-body">
					<ul class="list-group">
						<?php foreach ($oauth as $key => $value): ?>
							<li class="list-group-item">
						    	<span class="badge <?php echo $key ?>"><?php echo $value; ?></span>
						    	<?php echo Config::get("auth.providers.{$key}"); ?>
						  	</li>
						<?php endforeach ?>
					</ul>
				</div>
			</div>
		<?php endif ?>

		<?php
		$query = Message::newQuery()->where('to_user', Config::get('pms.webmaster'))->where('read', 0)->orderBy('date', 'desc')->take(5);

		$messages = array();

		foreach ($query->get() as $message) {
			$user = User::find($message->from_user);

			if (!$user || isset($messages[$user->id])) continue;

			$messages[$user->id] = $message;
			$messages[$user->id]->user = $user;
		}
		
		if (count($messages)): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?php _e('admin.unread_messages'); echo ' <span class="badge badge-danger">'.count($messages).'</span>'; ?></h3>
				</div>
				<div class="panel-body">
					<ul class="list-group">
						<?php foreach ($messages as $message): ?>
							<li class="list-group-item">
								<a href="?page=message-reply&id=<?php echo $message->user->id ?>" class="pull-right"><?php _e('admin.reply') ?></a>
								<p><a href="profile.php?u=<?php echo $message->user->id ?>" target="_blank"><?php echo $message->user->display_name; ?></a></p>
								<?php echo mb_strlen($message->message) > 70 ? mb_substr($message->message, 0, 70).'...' : $message->message; ?>
							</li>
						<?php endforeach ?>	
						
						<?php if (count($messages) > 5): ?>
							<li class="list-group-item text-center">
								<a href="?page=messages"><?php _e('admin.view_all_messages') ?></a>
							</li>
						<?php endif ?>
					</ul>
				</div>
			</div>
		<?php endif ?>
		
	</div>
</div>

<style>
	.panel-body { padding: 5px 15px; }
	.list-group { margin-bottom: 0px; }
	.list-group-item {
		word-break: break-all;
		border: none;
		border-bottom: 1px solid #ddd;
		padding: 10px 0px;
		margin-bottom: 0px;
	}
	.list-group>li:last-child { border-bottom: none; }
	.list-group-item .help-block { margin: 0px; font-size: 13px; }
	.panel .badge { font-weight: normal; }

	.badge.facebook {background: #3b5998;}
	.badge.google {background: #d34836;}
	.badge.twitter {background: #00aced;}
	.badge.linkedin {background: #007bb6;}
	.badge.microsoft {background: #007734;}
	.badge.instagram {background: #517fa4;}
	.badge.github {background: #333;}
	.badge.yammer {background: #396B9A;}
	.badge.foursquare {background: #0072b1;}
	.badge.vkontakte {background: #45668e;}
	.badge.soundcloud{background: #F76700;}
</style>

<?php echo View::make('admin.footer')->render() ?>