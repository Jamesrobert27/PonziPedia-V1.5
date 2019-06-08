<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Check PHP Requirements</title>
	<style>
		body {
			font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;
			font-size: 14px;
			line-height: 1.42857143;
			color: #333;
			background: #f8f8f8;
			padding-top: 50px;
		}
		a {
			color: #428bca;
			text-decoration: none;
		}
		a:hover, a:focus {
			color: #2a6496;
			text-decoration: underline;
		}
		.container {
			max-width: 400px;
			margin: 0 auto;
		}
		.alert {
			padding: 10px;
			margin-bottom: 15px;
			background: #fff;
			-webkit-box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
			box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
			border-left-width: 4px;
			border-left-style: solid;
		}
		.alert.error { border-color: #d9534f; }
		.alert.warning { border-color: #f0ad4e; }
		.alert.success { border-color: #5cb85c; }
		.legend { text-align: center; }
		.label {
			padding: .2em .6em .3em;
			font-size: 75%;
			font-weight: 700;
			line-height: 1;
			color: #fff;
			text-align: center;
			white-space: nowrap;
			vertical-align: baseline;
			border-radius: .25em;
		}
		.label-error { background-color: #d9534f; }
		.label-warning { background-color: #f0ad4e; }
		.label-success { background-color: #5cb85c; }
	</style>
</head>
<body>
	<div class="container">
		<?php

		if (version_compare(PHP_VERSION, '5.3.3', '<')) {
			echo '<div class="alert error"><b>PHP Version:</b> '.PHP_VERSION.' <br> You need at least PHP 5.3.3.</div>';
		} elseif (version_compare(PHP_VERSION, '5.3.7', '<')) {
			echo '<div class="alert warning"><b>PHP Version:</b> '.PHP_VERSION.' <br> You can run the script but without Bcrypt. <br> Recommended: PHP >= 5.3.7</div>';
		} elseif (version_compare(PHP_VERSION, '5.4.0', '<')) {
			echo '<div class="alert warning"><b>PHP Version:</b> '.PHP_VERSION.' <br> You can run the script but without Mailgun & Mandrill. <br> Recommended: PHP >= 5.4.0</div>';
		} else {
			echo '<div class="alert success"><b>PHP Version:</b> '.PHP_VERSION.'</div>';
		}

		if (extension_loaded('mcrypt')) {
			echo '<div class="alert success">MCrypt extension loaded.</div>';
		} else {
			echo '<div class="alert error"><a href="http://php.net/manual/en/book.mcrypt.php" target="_blank">MCrypt</a> extension required.</div>';
		}

		if (extension_loaded('pdo_mysql')) {
			echo '<div class="alert success">PDO MYSQL extension loaded.</div>';
		} else {
			echo '<div class="alert error"><a href="http://php.net/manual/en/book.pdo.php" target="_blank">PDO MYSQL</a> extension required.</div>';
		}

		if (extension_loaded('openssl')) {
			echo '<div class="alert success">OpenSSL extension loaded.</div>';
		} else {
			echo '<div class="alert error"><a href="http://php.net/manual/en/book.openssl.php" target="_blank">OpenSSL</a> extension required.</div>';
		}

		if (extension_loaded('gd')) {
			echo '<div class="alert success">GD extension loaded.</div>';
		} else {
			echo '<div class="alert error"><a href="http://php.net/manual/en/book.image.php" target="_blank">GD</a> extension required.</div>';
		}

		if (extension_loaded('exif')) {
			echo '<div class="alert success">Exif extension loaded.</div>';
		} else {
			echo '<div class="alert warning"><a href="http://php.net/manual/en/book.exif.php" target="_blank">Exif</a> extension recommended to be installed.</div>';
		}

		if (extension_loaded('mbstring')) {
			echo '<div class="alert success">Multibyte String extension loaded.</div>';
		} else {
			echo '<div class="alert warning"><a href="http://php.net/manual/en/book.mbstring.php" target="_blank">Multibyte String</a> extension recommended to be installed.</div>';
		}

		if (extension_loaded('intl')) {
			echo '<div class="alert success">Internationalization extension loaded.</div>';
		} else {
			echo '<div class="alert warning"><a href="http://php.net/manual/en/book.intl.php" target="_blank">Internationalization</a> extension recommended to be installed.</div>';
		}
		?>

		<div class="legend">
			<span class="label label-error">required</span>
			<span class="label label-warning">recommended</span>
			<span class="label label-success">installed</span>
		</div>
	</div>
</body>
</html>