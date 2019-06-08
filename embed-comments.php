<?php require_once 'app/init.php'; 
// +------------------------------------------------------------------------+
// | @author Olakunlevpn (Olakunlevpn)
// | @author_url 1: http://www.maylancer.cf
// | @author_url 2: https://codecanyon.net/user/gr0wthminds
// | @author_email: olakunlevpn@live.com   
// +------------------------------------------------------------------------+
// | PonziPedia - Peer 2 Peer 50% ROI Donation System
// | Copyright (c) 2018 PonziPedia. All rights reserved.
// +------------------------------------------------------------------------+

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<meta name="csrf-token" content="<?php echo csrf_token() ?>">

	<link href="<?php echo asset_url('css/vendor/bootstrap.min.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/bootstrap-custom.css') ?>" rel="stylesheet">
	<link href="<?php echo asset_url('css/main.css') ?>" rel="stylesheet">

	<style>body{padding:0px;background:#fff;}</style>

	<script src="<?php echo asset_url('js/vendor/jquery-1.11.1.min.js') ?>"></script>
	<script src="<?php echo asset_url('js/vendor/bootstrap.min.js') ?>"></script>
	<script src="<?php echo asset_url('js/vendor/iframeResizer.contentWindow.min.js') ?>"></script>
	<script src="<?php echo asset_url('js/easylogin.js') ?>"></script>
	<script src="<?php echo asset_url('js/main.js') ?>"></script>
	<script>
		EasyLogin.options = {
			ajaxUrl: '<?php echo App::url("ajax.php") ?>',
			lang: <?php echo json_encode(trans('main.js')) ?>,
			debug: <?php echo Config::get('app.debug')?1:0 ?>,
		};
	</script>
</head>
<body>
	<?php 
		$page      = isset($_GET['page'])       ? $_GET['page']       : get_current_url();
		$pageTitle = isset($_GET['page_title']) ? $_GET['page_title'] : '';

		echo ajax_comments($page, $pageTitle);
	?>
</body>
</html>