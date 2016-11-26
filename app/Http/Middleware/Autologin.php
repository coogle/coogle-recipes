<?php

namespace App\Http\Middleware;

use App\User;
use Illuminate\Support\Facades\Auth;

class Autologin extends \Illuminate\Auth\Middleware\Authenticate
{
    public function handle($request, \Closure $next, ...$guards)
    {
        if(!Auth::check()) {
            $autoLogin = config('auth.autologin');
            
            if(!empty($autoLogin)) {
                $user = User::where('email', '=', $autoLogin)->first();
            
                if($user instanceof User) {
                    \Auth::login($user, true);
                }
            }
        }
        
        return $next($request);
    }
}