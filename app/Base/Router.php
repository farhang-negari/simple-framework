<?php 
namespace FarhangNegari\App\Base;

use Exception;
use FarhangNegari\App\Base\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Router
{

    public static function get($app_route, $app_callback , $method , $auth = 0)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'GET') !== 0) {
            return;
        }

        self::on($app_route, $app_callback , $method, $auth);
    }

    public static function post($app_route, $app_callback , $method, $auth = 0)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return;
        }

        self::on($app_route, $app_callback , $method, $auth);
    }

    public static function put($app_route, $app_callback , $method, $auth = 0)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'PUT') !== 0) {
            return;
        }

        self::on($app_route, $app_callback , $method, $auth);
    }

    public static function delete($app_route, $app_callback , $method, $auth = 0)
    {
        if (strcasecmp($_SERVER['REQUEST_METHOD'], 'DELETE') !== 0) {
            return;
        }

        self::on($app_route, $app_callback , $method, $auth);
    }

    public static function on($exprr, $call_back , $method, $auth = 0)
    {
        $getParam = $_GET;
        $url = $_SERVER['REQUEST_URI'];
        $url =explode('?',$url);

        $url = (stripos($url[0], "/") !== 0) ? "/" . $url[0] : $url[0];
        $parameters = array();

        if(preg_match('/{\w+\}/',$exprr))
        {
            $exprrParam = $imParam = explode('/',rtrim($exprr,'/'));
            $urlParam = explode('/',rtrim($url,'/'));
            if(count($exprrParam) == count($urlParam))
                {
                    foreach($exprrParam as $key=>$value)
                    {
                        if(preg_match('/{\w+\}/',$value))
                            {
                                $valTkey = ltrim(rtrim($value,'}'),'{',);
                                $parameters[$valTkey] = $urlParam[$key];
                                $imParam[$key] = $urlParam[$key];
                            }
                    }
                }
            $exprr = implode('/',$imParam);
        }
        
        $exprr = str_replace('/', '\/', $exprr);
        
        $parameters = array_merge($parameters,$getParam);
        $matched = preg_match('/^' . ($exprr) . '$/', $url, $is_matched, PREG_OFFSET_CAPTURE);

        if ($matched) {
            if($auth == 1)
            {
                try{
                    $token = self::getToken();
                    $salt = config('JWT_SECRET_KEY','far2d81404c4ca0ba23b85b9416965ng');
                    JWT::$leeway = 600;
                    $payload = JWT::decode($token, new Key($salt, 'HS256'));
                    $parameters = array_merge($parameters,['user'=> (array)$payload->scopes]);
                }catch(Exception $e){
                    (new Response)->toJSON([
                        'status'=>402,
                        'massage'=>'token invalid'
                    ],402);
                    die;
                }
            }
            array_shift($is_matched);
            $action = explode('::', $call_back);

            if(!class_exists($action[0]) || !method_exists($action[0] , $method))
            {
                (new Response)->toJSON([
                    'status'=>404,
                    'massage'=>'class or method not found'
                ],404);
                die;
            }

            call_user_func(array(new $action[0] , $method), new Request($parameters));
            die;
        }
    }

    private static function getToken()
    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }

        (new Response)->toJSON([
            'status'=>402,
            'massage'=>'pls login'
        ],402);
        die;
    }
}