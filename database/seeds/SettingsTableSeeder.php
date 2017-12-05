<?php

use Illuminate\Database\Seeder;
use App\Model\PermissionCategory;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $setting = new \App\Model\Setting();
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
        $setting->save();


    }
}
