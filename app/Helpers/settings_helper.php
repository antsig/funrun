<?php

use App\Models\SettingModel;

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        // Cache settings to avoid query loop if possible, or just rely on DB query cache if configured.
        // For simplicity, we'll fetch from DB (or simple static cache in function).
        static $settingsCache = [];

        if (empty($settingsCache)) {
            $model = new SettingModel();
            $all = $model->findAll();
            foreach ($all as $s) {
                $settingsCache[$s['key']] = $s['value'];
            }
        }

        return $settingsCache[$key] ?? $default;
    }
}
