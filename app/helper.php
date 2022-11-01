<?php
function config($key , $default = null){
    $app_config = require(App_Path.'/config_app.php');
    return isset($app_config[$key])? $app_config[$key]:$default;
}
?>