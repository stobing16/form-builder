<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view)
    {
        $sidebarItems = [
            [
                'title' => 'Dashboard',
                'icon' => 'bi-house-fill',
                'link' => '#',
                'active' => true,
            ],
            [
                'title' => 'Users',
                'icon' => 'bi-people-fill',
                'link' => '#',
            ],
            [
                'title' => 'Settings',
                'icon' => 'bi-gear-fill',
                'dropdown' => [
                    [
                        'title' => 'Profile Settings',
                        'icon' => 'bi-sliders2',
                        'link' => '#',
                    ],
                    [
                        'title' => 'Account Settings',
                        'icon' => 'bi-sliders2',
                        'link' => '#',
                    ],
                ],
            ],
        ];

        $view->with('sidebarItems', $sidebarItems);
    }
}
