<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view)
    {
        $sidebarItems = [
            // [
            //     'title' => 'Dashboard',
            //     'icon' => 'bi-house-fill',
            //     'link' => '#',
            //     'active' => false,
            // ],
            [
                'title' => 'Forms',
                'icon' => 'bi-file-earmark-text',
                'link' => route('forms'),
                'active' => request()->routeIs('forms') || request()->routeIs('forms.*') || request()->routeIs('forms.question.*'),
            ],
            [
                'title' => 'Response',
                'icon' => 'bi-file-earmark-ruled',
                'link' => route('response'),
                'active' => request()->routeIs('response') || request()->routeIs('response.*'),
            ],
            [
                'title' => 'User Management',
                'icon' => 'bi-people',
                'link' => route('users'),
                'active' => request()->routeIs('users') || request()->routeIs('users.*'),
            ],
            [
                'title' => 'Account',
                'icon' => 'bi-person-circle',
                'link' => route('account'),
                'active' => request()->routeIs('account') || request()->routeIs('account.*'),
            ],
            // [
            //     'title' => 'Settings',
            //     'icon' => 'bi-gear-fill',
            //     'dropdown' => [
            //         [
            //             'title' => 'Profile Settings',
            //             'icon' => 'bi-sliders2',
            //             'link' => '#',
            //             'active' => false,
            //         ],
            //         [
            //             'title' => 'Account Settings',
            //             'icon' => 'bi-sliders2',
            //             'link' => '#',
            //             'active' => false,
            //         ],
            //     ],
            // ],
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
