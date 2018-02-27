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
					"address" => "",
					"phone" => "",
                    "customer_loyalty_percentage"=>"1"
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
    }
}
