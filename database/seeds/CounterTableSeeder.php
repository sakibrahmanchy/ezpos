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
            $counter->starting_id = 10000000;
            $counter->printer_ip = "";
            $counter->printer_port = "";
            $counter->isDefault = true;
            $counter->save();
        }
        else {

            $counters = \App\Model\Counter::all();
            $existingStartingIDs = \App\Model\Counter::pluck('starting_id', 'id')->toArray();
            dd($existingStartingIDs);
//            $counterInitiatingValue = 10000000;
//            foreach ($counters as $counter) {
//                if(is_null($counter->starting_id) || $counter->starting_id == 0 || $counter->starting_id == $counterInitiatingValue) {
//                    if($counter->starting_id == $counterInitiatingValue)
//                    {
//                        $counterInitiatingValue += 10000000;
//                        $counter->starting_id = $counterInitiatingValue;
//                    }
//                    else {
//                        $counter->starting_id = $counterInitiatingValue;
//                        $counterInitiatingValue += 10000000;
//                    }
//                }
//                $counter->save();
//            }
        }

        if(\App\Model\Counter::where("name","Default")->count()>1){
            $counters = \App\Model\Counter::where("name","Default")->where("id","<>",1)->get();

            foreach($counters as $aCounter) {
                $aCounter->delete();
            }
        }
    }
}
