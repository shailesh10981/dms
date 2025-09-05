<?php

use App\Helpers\SettingsHelper;

if (!function_exists('sys_setting')) {
  function sys_setting($key, $default = null)
  {
    return SettingsHelper::get($key, $default);
  }
}
