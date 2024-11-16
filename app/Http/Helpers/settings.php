<?php

if (! function_exists('getSetting')) {
    /**
     * Get the value of a setting from the database.
     *
     * @param  string  $key
     * @return string|null
     */
    function getSetting($key)
    {
        return App\Models\Setting::getValue($key);
    }
}
