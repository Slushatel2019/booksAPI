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
$sign = new Auth($log);
if ($sign->signIn()) {
  try {
    $app = new Route($log);
    $app->run();
  } catch (\Exception $e) {
    $log->error('Global error: ' . $e->getMessage());
  }
}
Common::response(['data' => 'error', 'message' => 'incorrect login or password or cookies is disabled', 'status' => 403]);
