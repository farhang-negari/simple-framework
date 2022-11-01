<?php
// error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_STRICT);

define('Public_Path',__DIR__);
define('App_Path',realpath(__DIR__).'../..');

require App_Path . '/vendor/autoload.php';
use FarhangNegari\App\Base\App;

App::run();

?>