<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Gate::define('modules', function($user, $permisionName){
            // Kiểm tra user có publish = 0 thì không có quyền
            if($user->publish == 0) return false;
            
            // Đảm bảo relationships được load (lazy eager loading)
            if(!$user->relationLoaded('user_catalogues')){
                $user->load('user_catalogues');
            }
            
            // Kiểm tra user có user_catalogues relationship không
            if(!$user->user_catalogues) return false;
            
            // Đảm bảo permissions relationship được load
            if(!$user->user_catalogues->relationLoaded('permissions')){
                $user->user_catalogues->load('permissions');
            }
            
            // Kiểm tra user_catalogues có permissions relationship không
            if(!$user->user_catalogues->permissions || $user->user_catalogues->permissions->isEmpty()) return false;
            
            // Kiểm tra permission có chứa canonical tương ứng không
            $permissions = $user->user_catalogues->permissions;
            if($permissions->contains('canonical', $permisionName)){
                return true;
            }
            return false;
        });
    }
}
