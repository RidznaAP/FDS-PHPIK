<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Perencanaan;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share pendingApprovalCount ke semua view — menggantikan query di sidebar.blade.php
        // Hanya dijalankan sekali per request, bukan per komponen/include
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                if ($user->isBbkhit() || $user->isPusat()) {
                    $pendingApprovalCount = Perencanaan::where('status', 'waiting')
                        ->when($user->isBbkhit(), function ($q) use ($user) {
                            $q->whereIn('user_id', function ($rq) use ($user) {
                                $rq->select('id')->from('users')
                                   ->where('id', $user->id)
                                   ->orWhere('parent_id', $user->id);
                            });
                        })
                        ->count();
                } else {
                    $pendingApprovalCount = 0;
                }
                $view->with('pendingApprovalCount', $pendingApprovalCount);
            }
        });
    }
}

