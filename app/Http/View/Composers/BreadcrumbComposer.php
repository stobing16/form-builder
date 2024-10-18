<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\Route;

class BreadcrumbComposer
{
    public function compose(View $view)
    {
        $breadcrumbs = [];
        $routeName = Route::currentRouteName(); // Mendapatkan nama route saat ini

        // Menyesuaikan breadcrumb berdasarkan route
        switch ($routeName) {
            case 'home':
                $breadcrumbs[] = ['title' => 'Home', 'icon' => 'bi-house-fill', 'active' => true];
                break;

            case 'forms':
                $breadcrumbs[] = ['title' => 'Home', 'icon' => 'bi-house-fill', 'link' => route('home')];
                $breadcrumbs[] = ['title' => 'Forms', 'icon' => 'bi-clipboard-fill', 'active' => true];
                break;

                // case 'library':
                //     $breadcrumbs[] = ['title' => 'Home', 'link' => route('home')];
                //     $breadcrumbs[] = ['title' => 'Library', 'link' => route('library')];
                //     break;

                // case 'data':
                //     $breadcrumbs[] = ['title' => 'Home', 'link' => route('home')];
                //     $breadcrumbs[] = ['title' => 'Library', 'link' => route('library')];
                //     $breadcrumbs[] = ['title' => 'Data', 'active' => true];
                //     break;

                // Tambahkan lebih banyak case sesuai dengan rute Anda
        }

        $view->with('breadcrumbs', $breadcrumbs);
    }
}
