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



include 'init.class.php';


/*
|--------------------------------------------------------------------------
| Set PHP Error Reporting
|--------------------------------------------------------------------------
*/

error_reporting(-1);

/*
|--------------------------------------------------------------------------
| Check Extensions
|--------------------------------------------------------------------------
*/

if (!extension_loaded('mcrypt')) {
	echo 'Mcrypt PHP extension required.';
	exit(1);
}

/*
|--------------------------------------------------------------------------
| Register Class Imports
|--------------------------------------------------------------------------
*/

use Hazzard\Foundation\Application;
use Hazzard\Foundation\AliasLoader;
use Hazzard\Foundation\ClassLoader;
use Hazzard\Support\Facades\Facade;
use Hazzard\Config\Repository as Config;
use Hazzard\Config\LoaderManager as ConfigLoader;

/*
|--------------------------------------------------------------------------
| Install Paths
|--------------------------------------------------------------------------
*/

$paths = array(
	'base' => __DIR__.'/..',
	'app'  => __DIR__.'/../app',
	'storage' => __DIR__.'/../app/storage'
);

/*
|--------------------------------------------------------------------------
| Composer Autoload
|--------------------------------------------------------------------------
*/

require_once $paths['base'] .'/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Set internal character encoding
|--------------------------------------------------------------------------
*/

if (function_exists('mb_internal_encoding')) {
	mb_internal_encoding('utf-8');
}

/*
|--------------------------------------------------------------------------
| Create New Application
|--------------------------------------------------------------------------
*/

$app = new Application;
$app->instance('app', $app);
$app->bindInstallPaths($paths);

/*
|--------------------------------------------------------------------------
| Load Facades
|--------------------------------------------------------------------------
*/
Facade::setFacadeApplication($app);

/*
|--------------------------------------------------------------------------
| Register The Config Manager
|--------------------------------------------------------------------------
*/

$loader = new ConfigLoader($app['path'].'/config');
$app->instance('config', new Config($loader));

/*
|--------------------------------------------------------------------------
| Database Config Loader
|--------------------------------------------------------------------------
|
| Enabling this might affect the performance of your website.
|
*/

//$app->register('Hazzard\Database\DatabaseServiceProvider');
//$loader->setConnection($app['db']);
//$app->instance('config', new Config($loader));

/*
|--------------------------------------------------------------------------
| Register Custom Exception Handling
|--------------------------------------------------------------------------
*/

$app->startExceptionHandling();

if (!$app['config']['app.debug']) ini_set('display_errors', 'Off');

/*
|--------------------------------------------------------------------------
| Set The Default Timezone
|--------------------------------------------------------------------------
*/

$config = $app['config']['app'];
if (!empty($config['timezone'])) {
	date_default_timezone_set($config['timezone']);
}

/*
|--------------------------------------------------------------------------
| Register The Alias Loader
|--------------------------------------------------------------------------
*/

$aliases = $config['aliases'];

AliasLoader::getInstance($aliases)->register();

/*
|--------------------------------------------------------------------------
| Register The Core Service Providers
|--------------------------------------------------------------------------
*/

$providers = $config['providers'];

$app->getProviderRepository()->load($app, $providers);

/*
|--------------------------------------------------------------------------
| Register The Class Loader
|--------------------------------------------------------------------------
*/

$dirs = array(
	$app['path'].'/models'
);

ClassLoader::getInstance($dirs)->register();

/*
|--------------------------------------------------------------------------
| Load The Events File
|--------------------------------------------------------------------------
*/

if (file_exists($app['path'].'/events.php')) {
	require_once $app['path'].'/events.php';
}

/*
|--------------------------------------------------------------------------
| Fire Init Event
|--------------------------------------------------------------------------
*/

$app['events']->fire('app.init');



function Template()
{
	echo "../themes/May/";
}