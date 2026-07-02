<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Models\Trainingrecord;
use App\Models\Trainingfkt;
use App\Models\Trainingfpkt;

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
        Paginator::useBootstrapFive();

        view()->composer('partials/*', function ($view){
            $user = auth()->user();
            //notif training
                //notif pti
                    //super user
                    if($user->roles()->pluck('id')->first() == '1'){
                        $query = Trainingfpkt::whereIn('status', [11,4,5]);
                        $notif_pti = $query->count();
                    }elseif($user->roles()->pluck('id')->first() == '2'){
                        $query = Trainingfpkt::where('status', 11);
                        $notif_pti = $query->count();
                    }elseif($user->roles()->pluck('id')->first() == '51'){
                        $query = Trainingfpkt::where('status', 4);
                        $notif_pti = $query->count();
                    }elseif($user->roles()->pluck('id')->first() == '49'){
                        $query = Trainingfpkt::where('status', 5);
                        $notif_pti = $query->count();
                    }else{
                        $notif_pti = 0;
                    }

                //notif ptt
                    //super user or pic hrd
                    $query = Trainingfkt::where('status', 3);
                    $notif_ptt = $query->count();

                //notif progress training
                    //notif verified pti
                    if($user->roles()->pluck('id')->first() == '2' || $user->roles()->pluck('id')->first() == '1'){
                        $notif_verified_pti = 0; 
                    }else{
                        $notif_verified_pti = 0;
                    }
                    //notif verified ptt
                    if($user->roles()->pluck('id')->first() == '2' || $user->roles()->pluck('id')->first() == '1'){
                        $notif_verified_ptt = 0; 
                    }else{
                        $notif_verified_ptt = 0;
                    }

                $view->with('notif_pti', $notif_pti);
                $view->with('notif_ptt', $notif_ptt);
                $view->with('notif_verified_pti', $notif_verified_pti);
                $view->with('notif_verified_ptt', $notif_verified_ptt);
                $view->with('user', $user);
                
        });
    }
}
