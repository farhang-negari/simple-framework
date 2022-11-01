<?php

namespace FarhangNegari\App\Base;

class Response
{
    private $status = 200;
    private static $obj;

    public function __constants(){
        if($this->obj == null)
            $this->obj = new Response;
    }

    public static function getInstanse() {
        if (!isset(self::$obj)) {
            self::$obj = new Request();
        }
        return self::$obj;
    }

    public function status(int $headerCode)
    {
        $this->status = $headerCode;
        return $this;
    }
    
    public function toJSON($data = [] , $status = 200)
    {
        if($status != 200)
            $this->status($status);
            
        http_response_code($this->status);
        header('Content-Type: application/json');
        echo json_encode($data);
        die;
    }
}