<?php

use Illuminate\Database\Seeder;
use App\Model\PermissionCategory;

class CounterTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $counter = \App\Model\Counter::all();

        if(!is_null($counter)){

            $counter = new \App\Model\Counter();
            $counter->name= "Default";
            $counter->description = "";
            $counter->printer_ip = "";
            $counter->printer_port = "";
            $counter->isDefault = true;
            $counter->save();
        }

    }
}
