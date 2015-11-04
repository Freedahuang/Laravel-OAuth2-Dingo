<?php

Route::get('/', function () {
    return view('welcome');
});

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

Route::get('user', ['middleware' => 'oauth', function() {
    return 'hello world';
}]);
