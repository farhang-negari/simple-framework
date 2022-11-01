<?php 
namespace FarhangNegari\App\Base;
class App
{
    public static function run()
    {
        require_once(App_Path.'/app/apiRoute.php');
    }
}
