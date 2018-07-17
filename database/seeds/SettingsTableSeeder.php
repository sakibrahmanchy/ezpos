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

    protected function updateDotEnv($key, $newValue, $delim='')
    {

        $path = base_path('.env');
        // get old value from current env
        $oldValue = env($key);

        // was there any change?
        if ($oldValue === $newValue) {
            return;
        }

        // rewrite file content with changed data
        if (file_exists($path)) {
            // replace current value with new value
            file_put_contents(
                $path, str_replace(
                    $key.'='.$delim.$oldValue.$delim,
                    $key.'='.$delim.$newValue.$delim,
                    file_get_contents($path)
                )
            );
        }
    }

    public function run()
    {
		$settingsArr = [
					"company_name" => "EZ POS",
					"company_logo" => "logo.png",
					"tax_rate" => 15,
					"address_line_1" => "",
					"address_line_2" => "",
					"email_address" => "",
					"phone" => "",
                    "website" => "",
                    "customer_loyalty_percentage"=>"1",
                    "negative_inventory" => false,
                    "scan_price_from_barcode" => false,
                    "upc_code_prefix"=>"200",
                    "item_size" => "lbs",
                    "default_opening_amount" => "100",
                    "session_lifetime"=>1
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
		//$this->updateDotEnv('SESSION_LIFETIME',$settingsArr['session_lifetime']);
//        \Config::set('session.lifetime', $settingsArr['session_lifetime'] );
//
////        config(['session.lifetime' => $settingsArr['session_lifetime']]);
////        \Illuminate\Support\Facades\Artisan::call('cache:clear');
////        \Illuminate\Support\Facades\Artisan::call('config:clear');
////        \Illuminate\Support\Facades\Artisan::call('config:cache');
//        echo \Config::get('session.lifetime');

    }
}
