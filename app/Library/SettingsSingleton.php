<?php
namespace App\Library;
use App\Model\Setting;
use Illuminate\Support\Facades\DB;
use Sabberworm\CSS\Settings;

class SettingsSingleton
{
    private static $settingsData;

    public static function get()
    {
        if (!isset(self::$settingsData)) {
            $items = DB::table('settings')->get();

            $settings = array();
            foreach($items as $item) {
                $settings[$item->key] = $item->value;
            }
            self::$settingsData = $settings;
        }
        return self::$settingsData;
    }

    public static function set($key, $value)
    {
        $settings = Setting::where("key",$key)->first();
        $settings->key = $key;
        $settings->value = $value;
        $settings->save();

        self::$settingsData[$key] = $value;

        //DB update
        //self::$settingsData = ['id' => 4, 'name' => 'yoo'];
    }
}