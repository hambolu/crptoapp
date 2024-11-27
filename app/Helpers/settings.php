<?php

use Illuminate\Support\Facades\Cache;
use App\Models\Setting;

if (! function_exists('getSetting')) {
    function getSetting($key)
    {
        return Cache::remember("settings.$key", 60 * 60, function () use ($key) {
            return Setting::getValue($key);
        });
    }
}

if (! function_exists('setSetting')) {
    function setSetting($key, $value)
    {
        $setting = Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        if ($setting) {
            Cache::forget("settings.$key");
            return true;
        }
        return false;
    }
}
