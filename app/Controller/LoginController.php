<?php
namespace FarhangNegari\App\Controller;

use Firebase\JWT\JWT;
use FarhangNegari\App\Base\Request;
use FarhangNegari\App\Controller\BaseController;
use FarhangNegari\App\Models\Users;

class LoginController extends BaseController
{
    public $userModel;
    public function __construct()
    {
        $this->userModel = new Users;
    }

    public function index(Request $reqest)
    {
        if(!$reqest->has('username')  || !$reqest->has('password'))
            $this->respons(['message'=>'error in input data','status'=>'404'],'404');

        $user = $this->userModel->where('username = \''.$reqest->get('username').'\'')->first();
        
        if($user == null || !password_verify($reqest->get('password'),$user['password']))
            $this->respons(['message'=>'user not found','status'=>'404'],'404');
            
        $peyload = [
            'iss' =>    $_SERVER['SERVER_NAME'],
            'aud' =>    $_SERVER['SERVER_NAME'],
            'iat' =>    time(),
            // 'nbf' =>    time() + (60 * 60) * 10,
            'exp' =>    time() + (60 * 60) * 10,
            'userId' => $user['id'],
            'scopes' => $user
        ];
        $salt = config('JWT_SECRET_KEY','far2d81404c4ca0ba23b85b9416965ng');
        $token = JWT::encode($peyload,$salt,'HS256');
        // $token = (new JWT($salt, 'HS512', time() + 60 * 60))->encode(['uid' => $user['id'], 'scopes' => $user]);
        $this->respons(['message'=>'success user login','token'=>$token]);
    }

    public function register(Request $reqest)
    {
        if(!$reqest->has('username') || !$reqest->has('password') || !$reqest->has('user_full_name'))
            $this->respons(['message'=>'error in input data','status'=>'404'],'404');

        $user = $this->userModel->where('username = \''.$reqest->get('username').'\'')->first();
    
        if($user != null)
            $this->respons(['message'=>'user exist','status'=>'402'],'402');

        $check = $this->userModel->insert(
            [
                'username' => $reqest->get('username'),
                'password' => password_hash($reqest->get('password'),PASSWORD_BCRYPT),
                'full_name' => $reqest->get('user_full_name'),
            ]
        );

        $this->respons(['message'=>'success user create']);
    }
}
?>