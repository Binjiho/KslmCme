<?php

// check Url
if (!function_exists('checkUrl')) {
    function checkUrl(): string
    {
        $uri = str_replace('://www.', '://', request()->getUri());

        if (strpos($uri, config('site.app.api.url')) !== false) {
            return 'api';
        }

        if (strpos($uri, config('site.app.admin.url')) !== false) {
            return 'admin';
        }

        return 'web';
    }
}

// global auth
if (!function_exists('thisAuth')) {
    function thisAuth()
    {
        if (checkUrl() == 'admin') {
            return auth('admin');
        }

        return auth('web');
    }
}

// get App Name
if (!function_exists('getAppName')) {
    function getAppName(): string
    {
        return config('site.app.' . checkUrl() . '.app_name');
    }
}

// get default url
if (!function_exists('getDefaultUrl')) {
    function getDefaultUrl($auth = false): string
    {
        if ($auth) {
            if (checkUrl() == 'admin') {
                return thisAuth()->check()
                    ? getDefaultUrl()
                    : env('APP_URL') . '/auth/login';
            }

            return thisAuth()->check()
                ? getDefaultUrl()
                : route('login');
        }

        return route('main');
    }
}

// thisLevel
if (!function_exists('thisLevel')) {
    function thisLevel(): string
    {
        return thisUser()->level ?? '';
    }
}

// isAdmin
if (!function_exists('isAdmin')) {
    function isAdmin(): bool
    {
        return ((thisUser()->level ?? '') === 'M');
    }
}


