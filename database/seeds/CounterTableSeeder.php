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

        $counterObject = new \App\Model\Counter();
        if(\App\Model\Counter::all()->count()==0){

            $counter = new \App\Model\Counter();
            $counter->name= "Default";
            $counter->description = "";
            $counter->starting_id = 10000000;
            $counter->printer_ip = "192.168.10.51";
            $counter->printer_port = "9100";
            $counter->isDefault = true;
            $counter->save();
        }
        else {

            $counters = \App\Model\Counter::all();
            foreach ($counters as $counter) {
                if(is_null($counter->starting_id) || $counter->starting_id == 0) {
                   $counterObject->generateUniqueStartingId();
                   $counter->starting_id = $counter::$CURRENT_UNIQUE_STARTING_ID;
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
        $counterObject->replaceDuplicateStartingIds();
    }
}
