<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Exception;
use LucaDegasperi\OAuth2Server\Authorizer;

/**
 * @property mixed response
 */
class OAuthController extends Controller{
    protected $authorizer;

    public function __construct(Authorizer $authorizer){
        $this->authorizer = $authorizer;
    }

    public function accessToken() {
        try {
            return $this->authorizer->issueAccessToken();
        }catch (Exception $e) {
            return $this->response->errorUnauthorized('认证失败');
        }
    }

}