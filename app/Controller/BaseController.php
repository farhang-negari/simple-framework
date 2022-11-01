<?php
namespace FarhangNegari\App\Controller;

use FarhangNegari\App\Base\Response;

class BaseController
{
    public function getBearerToken()
    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            if (preg_match('/Bearer\s(\S+)/', $headers['Authorization'], $matches)) {
                return $matches[1];
            }
        }
        return $this->respons(['message'=>'Token is not found'], 404);
    }

    public function respons($message , $status = 200)
    {
        return (new Response)->toJSON(
            $message,  $status
        );
    }
}
?>