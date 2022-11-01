<?php
namespace FarhangNegari\App\Controller;

use FarhangNegari\App\Base\Request;
use FarhangNegari\App\Controller\BaseController;
use FarhangNegari\App\Repo\UrlRepo;

class UrlController extends BaseController
{
    public $urlRepo;
    public function __construct()
    {
        $this->urlRepo = new UrlRepo;
    }

    public function index(Request $request)
    {
        $data = $this->urlRepo->pageinate($request);
        $this->respons(['message'=>'load data','data'=>$data]);
    }

    public function create(Request $request)
    {
        $data = $this->urlRepo->insert($request);
        if($data == false)
            $this->respons(['message'=>'url is not correct or exist','data'=>$data]);
        else{
            $this->respons(['message'=>'insert suceess','data'=>$data]);
        }
    }

    public function show(Request $request)
    {
        $data = $this->urlRepo->find($request->get('id'));
        if(!$data)
            $this->respons(['message'=>'url not found','data'=>$data],404);
        else
            $this->respons(['message'=>'get data suceess','data'=>$data]);
    }

    public function delete(Request $request)
    {
        $data = $this->urlRepo->find($request->get('id'));
        if(empty($data))
            $this->respons(['message'=>'url not found','status'=>404],404);
        $data = $this->urlRepo->delete($request);
        $this->respons(['message'=>'url deleted']);
    }

    public function update(Request $request)
    {
        $data = $this->urlRepo->find($request->get('id'));
        if(empty($data))
            $this->respons(['message'=>'url not found','status'=>404],404);
        $data = $this->urlRepo->update($request);
        if(!$data)
            $this->respons(['message'=>'url exist'],402);
        else
            $this->respons(['message'=>'suceess update url']);
    }
}
?>