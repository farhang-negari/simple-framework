<?php
namespace FarhangNegari\App\Repo;

use Exception;
use FarhangNegari\App\Models\Url;

class UrlRepo
{
    public $model;

    public function __construct()
    {
        $this->model = new Url;
    }

    public function all($where = 1)
    {
        return $this->model->get($where);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function pageinate($req , $where = 1)
    {
        $this->model->where($where);
        return $this->model->paginate($req);
    }

    public function insert($data)
    {
        if (!$data->has('url') || filter_var($data->get('url'), FILTER_VALIDATE_URL) === FALSE) 
            return false;

        try{
            $this->model->bginTransaction();
            $url = $this->validateURL($data->get('url'));

            if($url != null)
                throw new Exception('url is exist');

            $code = $this->generate();
            
            $check = true;
            while($check){
                $row = $this->model->where('short_code = \''.$code.'\'')->first();
                if(empty($row))
                    $check = false;
                $code = $this->generate();
            };
            
            $check = $this->model->insert(['url'=>$data->get('url') , 'short_code'=>$code]);
            $this->model->commit();
            return ['url'=>$data->get('url') , 'short_code'=>$code , 'id'=>$check];
        }catch(Exception $e){
            $this->model->rollback();
            return null;
        }
        
    }

    public function generate($length = 5) 
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function update($req)
    {
        if (!$req->has('url') || filter_var($req->get('url'), FILTER_VALIDATE_URL) === FALSE) 
            return false;

        try{
            $this->model->bginTransaction();

            $url = $this->validateURL($req->get('url'));
            if($url != null)
                throw new Exception('url is exist');

            $data = [
                'url' => $req->get('url')
            ];
            $id = $req->get('id');
            $data = $this->model->where("id = $id ")->update($data);

            $this->model->commit();
            return $data;
        }catch(Exception $e){
            $this->model->rollback();
            return null;
        }
        
    }

    public function delete($req)
    {
        $id = $req->get('id');
        return $this->model->where("id = $id")->delete();
    }

    public function validateURL($url)
    {
        $url = trim($url,'http://');
        $url = trim($url,'https://');
        $url = trim($url,'ftp://');
        $url = trim($url,'www.');
        $url = str_replace(' ','-',$url);
        $url = preg_replace('/-+/','-',$url);

        $get = $this->model->where(" `url` LIKE '%$url'")->first();
        return $get;
    }

    public function checkCode($reqest)
    {
        if(!$reqest->has('code'))
            return array('message'=>['message'=>'url not found ','status'=>'404'],'status'=>'404');
        $code = $reqest->get('code');
        $data = $this->model->where('short_code = \''.$code.'\'')->first();
        if($data == null)
            return array('message'=>['message'=>'url not found ','status'=>'404'],'status'=>'404');
            
        return array('message'=> ['url'=>$data['url'],'status'=>'200'],'status'=>'200');
    }
}