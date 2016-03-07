## Laravel
### install
```
composer create-project laravel/laravel --prefer-dist

chmod -R 777 storage
```
### success

return laravel hello world

## oauth2-server-laravel
### install
```
file: composer.json add

"lucadegasperi/oauth2-server-laravel": "5.0.*"

composer update
```
```
file: config/app.php

providers:
LucaDegasperi\OAuth2Server\Storage\FluentStorageServiceProvider::class,
LucaDegasperi\OAuth2Server\OAuth2ServerServiceProvider::class,

aliases:
'Authorizer' => LucaDegasperi\OAuth2Server\Facades\Authorizer::class,


```
```
file: app/Http/Kernel.php

$middleware:
\LucaDegasperi\OAuth2Server\Middleware\OAuthExceptionHandlerMiddleware::class
(remove)App\Http\Middleware\VerifyCsrfToken

$routeMiddleware:
'oauth' => \LucaDegasperi\OAuth2Server\Middleware\OAuthMiddleware::class,
'oauth-user' => \LucaDegasperi\OAuth2Server\Middleware\OAuthUserOwnerMiddleware::class,
'oauth-client' => \LucaDegasperi\OAuth2Server\Middleware\OAuthClientOwnerMiddleware::class,
'check-authorization-params' => \LucaDegasperi\OAuth2Server\Middleware\CheckAuthCodeRequestMiddleware::class,
'csrf' => \App\Http\Middleware\VerifyCsrfToken::class,
```
```
php artisan vendor:publish

php artisan migrate
```

### configure
```
file: config/oauth2.php

'grant_types' => [
    'password' => [
            'class' => '\League\OAuth2\Server\Grant\PasswordGrant',
            'callback' => '\App\Http\Controllers\Auth\PasswordGrantVerifier@verify',
            'access_token_ttl' => 3600
        ]
]

```
```
file: app/Http/Controller/Auth/PasswordGrantVerifier.php

namespace App\Http\Controllers\Auth;
use Illuminate\Support\Facades\Auth;

class PasswordGrantVerifier
{
  public function verify($username, $password)
  {
      $credentials = [
        'email'    => $username,
        'password' => $password,
      ];

      if (Auth::once($credentials)) {
          return Auth::user()->id;
      }

      return false;
  }
}

```
```
table:oauth_clients

id:1 secret:123 name:test

table:users

id:1 name:Jack email:jack@laravel.com password:Hash::make('password')
```
```
file: app/Http/routes.php

Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});
```
### success

```
post: api.laravel.app/oauth/access_token
grant_type:password
client_id:1
client_secret:123
password:password
username:jack@laravel.com

return:
{
  "access_token": "9CavxbvJ8sa1lrfLKbUlHd8QfjS1idUanH7jpVdd",
  "token_type": "Bearer",
  "expires_in": 3600
}
```

```
file: app/Http/routes.php
Route::get('user', ['middleware' => 'oauth', function() {
    return 'hello world';
}]);
```
```
get: api.laravel.app/user

header: Authorization: Bearer 9CavxbvJ8sa1lrfLKbUlHd8QfjS1idUanH7jpVdd

return :
hello world
```

## Dingo-Api

### install
```
file: composer.json
"require": {
    "dingo/api": "1.0.*@dev"
}

composer update
```
```
file: config/app.php
'providers' => [
    Dingo\Api\Provider\LaravelServiceProvider::class
]
```

### configure

```
php artisan vendor:publish --provider="Dingo\Api\Provider\LaravelServiceProvider"
```
```
file:.env

API_STANDARDS_TREE=vnd
API_SUBTYPE=laravel
API_DOMAIN=api.laravel.app
API_NAME=Laravel API
API_CONDITIONAL_REQUEST=false
API_STRICT=true
API_DEFAULT_FORMAT=json
API_DEBUG=true

```
```
file: app/Http/routes.php

$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    $api->get('/dingo',function(){
        return "hello world";
    });
});
```
```
file: app/Http/Controllers/Controller.php

use Dingo\Api\Routing\Helpers;

use Helpers;

```
### success
```
get: /dingo
header: Accept:application/vnd.laravel.v1+json

return:
hello world 
```

## Dingo Link OAuth2

### configure
```
php artisan make:provider OAuthServiceProvider

namespace App\Providers;

use Dingo\Api\Auth\Auth;
use Dingo\Api\Auth\Provider\OAuth2;
use Illuminate\Support\ServiceProvider;

class OAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app[Auth::class]->extend('oauth', function ($app) {
            $provider = new OAuth2($app['oauth2-server.authorizer']->getChecker());

            $provider->setUserResolver(function ($id) {
                // Logic to return a user by their ID.
            });

            $provider->setClientResolver(function ($id) {
                // Logic to return a client by their ID.
            });

            return $provider;
        });
    }

    public function register()
    {
        //
    }
}
```
```
file: app/Http/Controllers/Auth/OAuthController.php

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
```
```
file: app/Http/routes.php

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


```

### success
```
post: /user/access_token
header: Accept:application/vnd.laravel.v1+json
body:
grant_type:password
client_id:1
client_secret:123
password:password
username:jack@laravel.com

return:
{
  "access_token": "NRXfLpfH0AonXsyICKbwV3lFsGI2acIOmC5xm9tT",
  "token_type": "Bearer",
  "expires_in": 3600
}

```
```
get: /no_access
header:Accept:application/vnd.laravel.v1+json

return:
no_access
```

```
file: config/app.php

add App\Providers\OAuthServiceProvider::class, to providers array.

```

### success
```
get: /my
header:
Accept:application/vnd.laravel.v1+json
Authorization:Bearer NRXfLpfH0AonXsyICKbwV3lFsGI2acIOmC5xm9tT

return: 
oauth my success
```

