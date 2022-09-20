DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rest-api-laravel
DB_USERNAME=root
DB_PASSWORD=

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
}

'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'passport',
            'provider' => 'users',
            'hash' => false,
        ],
    ],
?> 

<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{

    protected function redirectTo($request)
    {

        if(empty($request->header('Authorization'))){
            header('HTTP/1.0 401');
            echo 'Unauthorization';
            die();
        }
    }
}
?>

<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class UserRepository
{

    public function createAndLogin($input)
    {
        try {
            $user = User::create([
            	'id' => $input['id'],
            	'title' => $input['title'],
            	'content' => $input['content'],
            	'image' => $input['image'],
            	'user_id' => $input['user_id'],
            	'category_id' => $input['category_id'],
            ]);

            if ($user->save()) {
                $result['status']   = true;
                $result['code']     = 201;
                $result['message']  = "User Berhasil Dibuat";

                $authUser = [
                    'id' => $input['id'],
                    'title' => $input['title'],
                ];

                if (auth()->attempt($authUser)) {
                    $user       = Auth::user();
                    $objToken   = auth()->user()->createToken('LaravelAuthApp');
                    $strToken   = $objToken->accessToken;
                    $expiration = $objToken->token->expires_at->diffInSeconds(Carbon::now());

                    $result = array_merge($result, [
                      'token'       => $strToken,
                      'expires_in'  => $expiration
                    ]);
                }

                return $result;
            } else {
                $result['status']   = false;
                $result['code']     = 500;
                $result['message']  = "User Gagal Dibuat";
                $result['user']     = (object)[];
                return $result;
            }
?>