<?php 
namespace FarhangNegari\App\Base;

class Request
{
    public $requset_method;
    public $content_type;
    public $params;
    public $paramsPost;
    public $paramsJson;
    public static $obj;

    public function __construct($params = [])
    {
        $this->params = empty($params) ? $_GET : $params;
        $this->requset_method = trim($_SERVER['REQUEST_METHOD']);
        $this->content_type = !empty($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
        $this->getPostAllParams();
        $this->getJSONAllParams();
    }

    public static function getInstanse() {
        if (!isset(self::$obj)) {
            self::$obj = new Request();
        }
         
        return self::$obj;
    }

    public function isPost()
    {
        if ($this->requset_method == 'POST') {
            return true;
        }
        return false;
    }

    public function getPostAllParams()
    {
        if (!in_array($this->requset_method,['POST','PUT','DELETE'])) {
            return [];
        }

        $post_body = [];
        foreach ($_POST as $key => $value) {
            $this->params[$$key] = $this->paramsPost[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $this->params;
    }

    public function getJSONAllParams()
    {
        if (!in_array($this->requset_method,['POST','PUT','DELETE'])) {
            return [];
        }
        
        if (strpos(strtolower(';;'.$this->content_type),'application/json') == 0) {
            return [];
        }
        
        $post_content = trim(file_get_contents("php://input"));

        $this->paramsJson = json_decode($post_content,true);

        $this->params = array_merge($this->params,$this->paramsJson);

        return $this->paramsJson;
    }

    public function all()
    {
        return $this->params;
    }

    public function get($key , $defult = null)
    {
        return isset($this->params[$key]) ? $this->params[$key] : $defult;
    }

    public function has($key)
    {
        return isset($this->params[$key]) ? true : false;
    }
}