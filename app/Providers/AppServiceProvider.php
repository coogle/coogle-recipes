<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend('fractionVal', function($attribute, $value, $parameters, $validator) {
            
            $value = trim($value);
            $value = preg_replace('!\s+!', ' ', $value);
            
            $parts = explode(' ', $value);
            
            if(count($parts) == 1) {
                if((int)$parts[0] > 0) {
                    return true;
                } 
                
                return false;
            } elseif(count($parts) == 2) {
                
                if(!strpos($parts[1], '/')) {
                    return false;
                } else {
                    
                    if(preg_match_all('/(?<![\/\d])(?:\d+)\/(?:\d+)(?![\/\d])/', $parts[1])) {
                        list($top, $bottom) = explode('/', $parts[1]);
                        
                        if($bottom == 0 || $top == 0) {
                            return false;
                        } 
                        
                        return true;
                        
                    } 
                    
                    return false;
                    
                }
            }
            
            return false;
            
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
