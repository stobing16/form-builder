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
                'active' => false,
            ],
            [
                'title' => 'Forms',
                'icon' => 'bi-house-fill',
                'link' => route('forms'),
                'active' => request()->routeIs('forms'),
            ],
            [
                'title' => 'Users',
                'icon' => 'bi-people-fill',
                'link' => '#',
                'active' => false,
            ],
            [
                'title' => 'Settings',
                'icon' => 'bi-gear-fill',
                'dropdown' => [
                    [
                        'title' => 'Profile Settings',
                        'icon' => 'bi-sliders2',
                        'link' => '#',
                        'active' => false,
                    ],
                    [
                        'title' => 'Account Settings',
                        'icon' => 'bi-sliders2',
                        'link' => '#',
                        'active' => false,
                    ],
                ],
            ],
        ];

        // Periksa dropdown dan atur 'active' pada parent jika ada item aktif
        foreach ($sidebarItems as &$item) {
            if (isset($item['dropdown'])) {
                $item['active'] = collect($item['dropdown'])->contains('active', true);
            }
        }

        $view->with('sidebarItems', $sidebarItems);
    }
}
