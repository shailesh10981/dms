<?php

namespace App\Helpers;

use App\Models\SystemSetting;

class SettingsHelper
{
  public static function get($key, $default = null)
  {
    return SystemSetting::getValue($key, $default);
  }

  public static function set($key, $value, $description = null)
  {
    return SystemSetting::setValue($key, $value, $description);
  }
}
