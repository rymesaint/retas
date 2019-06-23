<?php

if(!function_exists('getMenu')) {
    function getMenu($role) {
        switch($role) {
            case 'owner':
                return menusOwner();
            break;
            case 'cashier':
                return menusCashier();
            break;
        }
    }
}

if(!function_exists('calculatePrice')) {
    // Formula to multiplied the price based on percentage
    function calculatePrice($price, $percentage) {
        $percentagePrice = ($price * $percentage) / 100;
        return round($price + $percentagePrice);
    }
}

if(!function_exists('isActiveRoute')) {
    function isActiveRoute($route, $output = 'active')
    {
        if (Route::currentRouteName() == $route) {
            return $output;
        }
    }
}

if (!function_exists('format_number_in_k_notation')) {
    function format_number_in_k_notation(int $number): string
    {
        $suffixByNumber = function () use ($number) {
            if ($number < 1000) {
                return sprintf('%d', $number);
            }

            if ($number < 1000000) {
                return sprintf('%d%s', floor($number / 1000), 'K+');
            }

            if ($number >= 1000000 && $number < 1000000000) {
                return sprintf('%d%s', floor($number / 1000000), 'M+');
            }

            if ($number >= 1000000000 && $number < 1000000000000) {
                return sprintf('%d%s', floor($number / 1000000000), 'B+');
            }

            return sprintf('%d%s', floor($number / 1000000000000), 'T+');
        };

        return $suffixByNumber();
    }
}

if(!function_exists('checkModule')) {
    function checkModule($moduleName) {
        if(Module::has($moduleName) && Module::find($moduleName)->active == 1) {
            return true;
        } else {
            return false;
        }
    }
}

if(!function_exists('getModule')) {
    function requireModule($moduleName) {
        if(Module::has($moduleName) && Module::find($moduleName)->active == 1) {
            return true;
        }
        throw new Exception('This section require module '.$moduleName.' to run.');
    }
}

if(!function_exists('menusOwner')) {
    function menusOwner() {
        $listNavigation = [];
        array_push($listNavigation, new App\Models\Navigation('Dashboard', 'dashboard', route('dashboard'), isActiveRoute('dashboard')));

        if(checkModule('Order') && checkModule('Menu') && checkModule('Branch')) {
            array_push($listNavigation, new App\Models\Navigation('Manage Order', 'shopping_basket', route('order'), isActiveRoute('order')));
        }

        if(checkModule('Menu') && checkModule('Branch')) {
            array_push($listNavigation,
                new App\Models\Navigation('Manage Menu', 'restaurant_menu', null,
                '#',
                true,
                [
                    new App\Models\Navigation('Master Menu', null, route('menu'), isActiveRoute('menu')),
                    new App\Models\Navigation('Branch Menu', null, route('menu.branch'), isActiveRoute('menu.branch'))
                ]
            ));
        }

        if(checkModule('Category')) {
            array_push($listNavigation, new App\Models\Navigation('Manage Category', 'local_library', route('category'), isActiveRoute('category')));
        }

        if(checkModule('Branch')) {
            array_push($listNavigation, new App\Models\Navigation('Manage Branch', 'store_mall_directory', route('branch'), isActiveRoute('branch')));
        }

        if(checkModule('Order')) {
            array_push($listNavigation,
                new App\Models\Navigation('Reports', 'assessment', null,
                '#',
                true,
                [
                    new App\Models\Navigation('Order History', null, route('order.history'), isActiveRoute('order.history')),
                    new App\Models\Navigation('Report Sales', null, '#')
                ]
            ));
        }

        array_push($listNavigation,
            new App\Models\Navigation('Settings', 'settings', null,
            '#',
            true,
            [
                new App\Models\Navigation('Users', null, '#'),
                new App\Models\Navigation('Application', null, '#')
            ]
        ));
        return $listNavigation;
    }
}

if(!function_exists('menusCashier')) {
    function menusCashier() {
        $listNavigation = [];
        array_push($listNavigation, new App\Models\Navigation('Dashboard', 'dashboard', route('dashboard')));
        array_push($listNavigation, new App\Models\Navigation('Manage Order', 'shopping_basket', route('dashboard')));
        return $listNavigation;
    }
}
