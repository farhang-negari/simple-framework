<?php
namespace FarhangNegari\App\Controller;

use FarhangNegari\App\Base\Request;
use FarhangNegari\App\Controller\BaseController;
use FarhangNegari\App\Repo\UrlRepo;

class ApiController extends BaseController
{
    public $repo;
    public function __construct()
    {
        $this->repo = new UrlRepo;
    }
    
    public function index(Request $request)
    {
        $data = $this->repo->checkCode($request);
        $this->respons($data['message'], $data['status']);
    }
}
?>