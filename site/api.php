<?php 

require_once 'vendor/autoload.php';

use SiteApp\Controllers\Route;
use SiteApp\Models\Auth;
use SiteApp\Models\Common;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

date_default_timezone_set('Europe/Kiev');
$log = new Logger('main_site_app');
$log->pushHandler(new StreamHandler('logs/main.log', Logger::INFO));


$signIn = new Auth($log);
if ($signIn->auth()){
  $user=$_SERVER['PHP_AUTH_USER'];
  $log->info('username = "'. $user.'"  auth - ok');
try {   
  $app = new Route($log,$user);
  $app->run();
} catch (\Exception $e) {
  $log->error('Global error: ' . $e->getMessage());
}
}
Common::response(['data' => 'error', 'message' => 'incorrect login or password', 'status' => 403]);
?>