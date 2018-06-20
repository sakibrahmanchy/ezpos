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
        if(\App\Model\Counter::all()->count()==0){

            $counter = new \App\Model\Counter();
            $counter->name= "Default";
            $counter->description = "";
            $counter->counter_code = "DFT";
            $counter->printer_ip = "";
            $counter->printer_port = "";
            $counter->isDefault = true;
            $counter->save();
        }
        else {

            $counters = \App\Model\Counter::all();
            foreach ($counters as $counter) {
                if(is_null($counter->counter_code) || $counter->counter_code == "") {
                    $num_padded = sprintf("%02d", $counter->id);
                    $counter->counter_code = "C".$num_padded;
                    $counter->save();
                }
            }
        }

        if(\App\Model\Counter::where("name","Default")->count()>1){
            $counters = \App\Model\Counter::where("name","Default")->where("id","<>",1)->get();

            foreach($counters as $aCounter) {
                $aCounter->delete();
            }
        }
    }
}
