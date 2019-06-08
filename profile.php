<?php
// +------------------------------------------------------------------------+
// | @author Olakunlevpn (Olakunlevpn)
// | @author_url 1: http://www.maylancer.cf
// | @author_url 2: https://codecanyon.net/user/gr0wthminds
// | @author_email: olakunlevpn@live.com   
// +------------------------------------------------------------------------+
// | PonziPedia - Peer 2 Peer 50% ROI Donation System
// | Copyright (c) 2018 PonziPedia. All rights reserved.
// +------------------------------------------------------------------------+

require_once 'app/init.php';

if (empty($_GET['u'])) redirect_to(App::url());

$user = User::where('id', $_GET['u'])->orWhere('username', $_GET['u'])->first();
?>

<?php echo View::make('header')->render() ?>
 <section style="padding-top: 50px;">
      <div class="container">
        <div class="row block">
          <div class="col-lg-9">
<?php if (is_null($user)): ?>
	<h3 class="page-header"><?php _e('errors.404') ?></h3>
	<?php _e('errors.page') ?>
<?php else: ?>
	<h3 class="page-header">
		<?php echo $user->display_name; echo empty($user->username)?'':" <small>({$user->username})</small>"; ?>

		<?php if (!empty($user->verified)): ?>
			<span class="verified-account" title="<?php _e('main.verified') ?>" data-toggle="tooltip">
				<span class="glyphicon glyphicon-ok"></span>
			</span>
		<?php endif ?>
	</h3>
	
	<div class="row">
		<div class="col-md-3">
			<img src="<?php echo $user->avatar ?>" class="img-thumbnail" style="margin-bottom: 10px;">
		</div>
		<div class="col-md-8">
			<p><span class="glyphicon glyphicon-envelope"></span> <?php echo $user->email ?></p>

			<?php if (!empty($user->phone)): ?>
				<p><span class="glyphicon glyphicon-phone-alt"></span> <?php echo $user->phone ?></p>
			<?php endif ?>
			
			<!-- 
			<?php if ($user->gender == 'M' || $user->gender == 'F'): ?>
				<p><b><?php _e('main.gender') ?>:</b> <?php echo trans("main.gender_{$user->gender}") ?></p>
			<?php endif ?>
			<?php if (!empty($user->birthday)): ?>
				<p><b><?php _e('main.birthday') ?>:</b> <?php echo $user->birthday ?></p>
			<?php endif ?>
			 -->

			<?php if (!empty($user->url)): ?>
				<p><span class="glyphicon glyphicon-link"></span> <a href="<?php echo $user->url ?>"><?php echo str_replace(array('http://', 'https://'), '', $user->url) ?></a></p>
			<?php endif ?>

			<?php if (!empty($user->location)): ?>
				<p><span class="glyphicon glyphicon-map-marker"></span> <?php echo $user->location ?></a></p>
			<?php endif ?>

			<?php if (!empty($user->joined)): ?>
				<p><span class="glyphicon glyphicon-time"></span> <?php echo with(new DateTime($user->joined))->format('F Y') ?></a></p>
			<?php endif ?>

			<p class="social-icons">
				<?php foreach (Config::get('auth.providers') as $key => $provider) {
					if (!empty($user->usermeta["{$key}_profile"])) {
						echo '<a href="'.$user->usermeta["{$key}_profile"].'" target="_blank" title="'.$provider.'"><span class="icon-'.$key.'"></span></a>';
					}
				} ?>
			</p>

			<?php if (Auth::check() && Auth::user()->id != $user->id): ?>
				<p>
					<?php $contact = Contact::find(Auth::user()->id, $user->id); ?>
					<?php if (!empty($contact) && !empty($contact->accepted)): ?>
						<a href="javascript:EasyLogin.removeContact(<?php echo $user->id ?>)" data-contact-id="<?php echo $user->id ?>" class="btn btn-xs btn-danger"><?php _e('main.remove_contact') ?></a>
					<?php elseif (!empty($contact)): ?>
						<a href="javascript:EasyLogin.removeContact(<?php echo $user->id ?>)" data-contact-id="<?php echo $user->id ?>" class="btn btn-xs btn-warning"><?php _e('main.cancel_contact') ?></a>
					<?php else: ?>
						<a href="javascript:EasyLogin.addContact(<?php echo $user->id ?>)" data-contact-id="<?php echo $user->id ?>" class="btn btn-xs btn-info"><?php _e('main.add_contact') ?></a>
					<?php endif ?>
				</p>
			<?php endif ?>
		</div>
	</div>
	
	<?php if (!empty($user->about)): ?>
		<p><?php echo $user->about ?></p>
	<?php endif ?>

<?php endif ?>

<style>
	.col-md-8 {padding-left: 10px;}
	.col-md-8 .glyphicon {opacity: .7; padding-right: 5px;}
</style>
</div>
</div>
</div>
</section>
<?php echo View::make('footer')->render() ?>