<?php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {

    $api->post('user/access_token','App\Http\Controllers\Auth\OAuthController@accessToken');

    $api->get('/no_access',function(){
        return "no_access";
    });

    $api->group(['middleware' => 'api.auth'],function($api){
        $api->get('my',function(){
            return 'oauth my';
        });
    });
});
