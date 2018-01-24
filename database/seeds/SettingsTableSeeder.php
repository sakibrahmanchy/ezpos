<?php

use Illuminate\Database\Seeder;
use App\Model\PermissionCategory;
use \App\Model\Setting;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$settingsArr = [
					"company_name" => "EZ POS",
					"company_logo" => "logo.png",
					"tax_rate" => 15,
					"address" => ""
				];
		foreach( $settingsArr as $key=>$value )
		{
			$aSettings = Setting::where("key",$key)->first();
			if( !$aSettings )
			{
				$setting = new \App\Model\Setting();
				$setting->key = $key;
				$setting->value = $value;
				$setting->save();
			}
		}
        /*$setting = new \App\Model\Setting();
        $setting->key = "company_name";
        $setting->value = "EZ POS";
        $setting->save();

        $setting = new \App\Model\Setting();
        $setting->key = "company_logo";
        $setting->value = "logo.png";
        $setting->save();

        $setting = new \App\Model\Setting();
        $setting->key = "tax_rate";
        $setting->value = "15";
        $setting->save();

        $setting = new \App\Model\Setting();
        $setting->key = "address";
        $setting->value = "";
        $setting->save();

        $setting = new \App\Model\Setting();
        $setting->key = "phone";
        $setting->value = "";
        $setting->save();*/
    }
}
