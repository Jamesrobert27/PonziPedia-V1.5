<?php 
function active_menu($items) { 
	return in_array(@$_GET['page'], explode('|', $items))?'active':'';
}
function page_restricted() {
	echo '<h3 class="page-header">'.trans('admin.page_restricted').'</h3>';
	_e('admin.restricted'); exit;
}
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="<?php echo csrf_token() ?>">
	<link href="<?php echo asset_url('img/favicon.png') ?>" rel="icon">
	
	<title><?php SeupportActiveCount(); ?> | <?php echo Config::get('app.name') ?> | Admin-Panel</title>
	
	<link href="<?php echo asset_url('css/vendor/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/bootstrap-custom.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/admin.css') ?>" rel="stylesheet">
	<!-- <link href="<?php echo asset_url('css/flat.css') ?>" rel="stylesheet"> -->
	
	<?php $color = Config::get('app.color_scheme'); ?>
	<link href="<?php echo asset_url("css/colors/{$color}.css") ?>" rel="stylesheet">
	
	<script src="<?php echo asset_url("js/vendor/jquery-1.11.1.min.js") ?>"></script>
	<script src="<?php echo asset_url("js/vendor/bootstrap.min.js") ?>"></script>
	<script src="<?php echo asset_url('js/easylogin.js') ?>"></script>
	<script src="<?php echo asset_url("js/admin.js") ?>"></script>
	<script>
		EasyLogin.options = {
			baseUrl: '<?php echo App::url() ?>',
			ajaxUrl: '<?php echo App::url("ajax.php") ?>',
			lang: <?php echo json_encode(trans('admin.js')) ?>,
			debug: <?php echo Config::get('app.debug')?1:0 ?>
		};
	</script>
</head>
<body>
	<div class="navbar navbar-fixed-top navbar-top">
    	<div class="container-fluid">
        	<div class="navbar-header">
         		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            		<span class="sr-only">Toggle navigation</span>
            		<span class="icon-bar"></span>
            		<span class="icon-bar"></span>
            		<span class="icon-bar"></span>
          		</button>
          		<a href="<?php echo App::url() ?>" class="navbar-brand"><?php echo Config::get('app.name') ?> <sup>Panel</sup></a>
        	</div>
        	<div class="navbar-collapse collapse">
	          	<ul class="nav navbar-nav">
	            	<li class="<?php echo active_menu('dashboard') ?>">
	            		<a href="?page=dashboard"><span class="glyphicon glyphicon-home"></span> <?php _e('admin.dashboard') ?></a>
	            	</li>
	            	
	            	<?php if (Auth::userCan('list_users') || Auth::userCan('add_users') || Auth::userCan('manage_roles')): ?>
		            	<li class="dropdown <?php echo active_menu('users|user-new|user-edit|user-roles|user-fields') ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
								<span class="glyphicon glyphicon-user"></span> <?php _e('admin.users') ?> <b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<?php if (Auth::userCan('list_users')): ?>
									<li><a href="?page=users"><?php _e('admin.all_users') ?></a></li>
								<?php endif ?>
								
								<?php if (Auth::userCan('add_users')): ?>
									<li><a href="?page=user-new"><?php _e('admin.add_new') ?></a></li>
								<?php endif ?>
								
								<?php if (Auth::userCan('manage_roles')): ?>
									<li><a href="?page=user-roles"><?php _e('admin.roles') ?></a></li>
								<?php endif ?>

								<?php if (Auth::userCan('manage_fields') && Config::getLoader()->getDBLoader()): ?>
									<li><a href="?page=user-fields"><?php _e('admin.fields') ?></a></li>
								<?php endif ?>
							</ul>
						</li>
					<?php endif ?>

					<?php if (Auth::userCan('moderate')): ?>
						<li class="<?php echo active_menu('comments|comment-edit') ?>">
		            		<a href="?page=comments">
		            			<span class="glyphicon glyphicon-comment"></span>
		            			<?php _e('admin.comments') ?>
		            			<?php 
									$pending = Comments::countPending();
									if ($pending > 0) {
										echo '<span class="label label-danger">'.$pending.'</span>';
									}
								?>
		            		</a>
		            	</li>
		            	<?php if (Auth::userCan('message_users')): ?>
						<li class="dropdown <?php echo active_menu('messages|message-new|message-reply') ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
								<span class="glyphicon glyphicon-envelope"></span> <?php _e('admin.messages') ?>
								<?php 
									$unread = Message::countUnread(Config::get('pms.webmaster'));
									if ($unread > 0) {
										echo '<span class="label label-danger">'.$unread.'</span>';
									}
								?>
								<b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="?page=messages"><?php _e('admin.all_messages') ?></a></li>
								<li><a href="?page=message-new"><?php _e('admin.new_message') ?></a></li>
								<li><a href="javascript:EasyLogin.admin.composeEmail()"><?php _e('admin.compose_email') ?></a></li>
							</ul>
						</li>
					<?php endif ?>
					
					<?php endif ?>
                      
		            		<li><a href="?page=support">
		            			<span class="glyphicon glyphicon-envelope"></span>
		            			<span class="label label-danger"><?php SeupportActiveCount(); ?></span>
		            			Support
		            		
		            		</a>
		            	</li>

                      <li>
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
								<span class="glyphicon glyphicon-cog"></span> Manage Margin <b class="caret"></b>
							</a>
							<ul class="dropdown-menu"> 
								
					<li><a href="?page=reqestmarging"><span class="glyphicon glyphicon-th-large"></span> Provide Help Request</a></li>
						<li><a href="?page=getmargin"><span class="glyphicon glyphicon-th"></span>  Get-Help Request</a></li>
						<li><a href="?page=activationfees"><span class="glyphicon glyphicon-flag"></span> Activation Fees</a></li>
						<li><a href="?page=allmargin"><span class="glyphicon glyphicon-barcode"></span> All Margin</a></li>
						<li><a href="?page=gethelp"><span class="glyphicon glyphicon-plus-sign"></span> Set User Get-Help</a></li>
						<li><a href="?page=activationreceiver"><span class="glyphicon glyphicon-usd"></span> Activation Fees Reciver</a></li>
						<li><a href="?page=addbalance"><span class="glyphicon glyphicon-usd"></span> Add Balance</a></li>
						<li><a href="?page=userdetails"><span class="glyphicon glyphicon-user"></span> User Account Details</a></li>
								
							</ul>
						</li>

		            	<li>
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
								<span class="glyphicon glyphicon-cog"></span> Settings Settings <b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="?page=testimony">Pending Testimony</a></li>
								<li><a href="?page=packages">Create packsages</a></li>
								<li><a href="?page=subscriber">Email Subscriber</a></li>
								<li><a href="?page=settings">Website Settings</a></li>
							</ul>
						</li>
					<?php if (Auth::userCan('manage_settings') && Config::getLoader()->getDBLoader()): ?>
						<li class="dropdown <?php echo active_menu('options-app|options-auth|options-services|options-mail|options-pms|options-comments') ?>">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
								<span class="glyphicon glyphicon-cog"></span> Core <?php _e('admin.settings') ?> <b class="caret"></b>
							</a>
							<ul class="dropdown-menu">
								<li><a href="?page=settings">Website Settings</a></li>
								<li><a href="?page=options-app"><?php _e('admin.options_general') ?></a></li>
								<li><a href="?page=options-auth"><?php _e('admin.options_auth') ?></a></li>
								<li><a href="?page=options-comments"><?php _e('admin.options_comments') ?></a></li>
								<li><a href="?page=options-pms"><?php _e('admin.options_pms') ?></a></li>
								<li><a href="?page=options-services"><?php _e('admin.options_services') ?></a></li>
								<li><a href="?page=options-mail"><?php _e('admin.options_mail') ?></a></li>
							</ul>
						</li>
					<?php endif ?>
	          	</ul>
	          	<ul class="nav navbar-nav navbar-pull-right">
	          		<li class="dropdown <?php echo active_menu('profile') ?>">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown">
							<?php echo Auth::user()->display_name ?>
							<img src="<?php echo Auth::user()->avatar ?>" class="avatar"> <b class="caret"></b>
						</a>
						<ul class="dropdown-menu">
							<li><a href="account/account.php?edit=<?php echo Auth::user()->id ?>"><?php _e('admin.my_profile') ?></a></li>
							<li><a href="account/settings.php"><?php _e('admin.settings') ?></a></li>
							<li><a href="?logout"><?php _e('admin.logout') ?></a></li>
						</ul>
					</li>


	          	</ul>
        	</div>
      	</div>
    </div>
    <div class="container">

     <!-- Compose email Modal -->
	<div class="modal fade" id="composeModal" tabindex="-1" role="dialog" aria-hidden="true">
		<div class="modal-dialog modal-md">
			<div class="modal-content">
				<form action="sendEmail" class="ajax-form">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
						<h4 class="modal-title"><?php _e('admin.compose_email') ?></h4>
					</div>
					<div class="modal-body">
		          		<div class="alert"></div>
						
						<div class="form-group">
			                <input type="text" name="to" placeholder="<?php _e('admin.to') ?>" class="form-control">
			            </div>

			            <div class="form-group">
			                <input type="text" name="subject" placeholder="<?php _e('admin.subject') ?>" class="form-control">
			            </div>

			            <div class="form-group">
			                <textarea class="form-control" name="message" placeholder="<?php _e('admin.message') ?>" rows="5"></textarea>
			            </div>

			            <div class="help-block"><?php _e('admin.add_multiple_emails') ?></div>

					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('admin.cancel') ?></button>
						<button type="submit" class="btn btn-primary"><?php _e('admin.send') ?></button>
					</div>
				</form>
			</div>
		</div>
	</div>