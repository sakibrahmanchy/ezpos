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

        if(is_null($counter)){

            $counter = new \App\Model\Counter();
            $counter->name= "Default";
            $counter->description = "";
            $counter->printer_ip = "";
            $counter->printer_port = "";
            $counter->isDefault = true;
            $counter->save();
        }

        if(\App\Model\Counter::where("name","Default")->count()>1){
            $counters = \App\Model\Counter::where("name","Default")->where("id","<>",1)->get();

            foreach($counters as $aCounter) {
                $aCounter->delete();
            }
        }

        $counters = \App\Model\Counter::all();
        $user = \App\Model\User::where("email","algrims@gmail.com")->first();
        $employee = \App\Model\Employee::where("user_id",$user->id)->first();
        $employee->counters()->attach($counters);

    }
}
