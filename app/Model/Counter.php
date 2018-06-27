<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    const INITIAL_STARTING_ID = 10000000;
    public static $CURRENT_UNIQUE_STARTING_ID;

    protected $fillable = [
        'name', 'description','starting_id','printer_connection_type','printer_ip','printer_port',"isDefault"
    ];

    public function sale(){
        return $this->hasMany('App\Model\Sale');
    }

    public function employees() {
        return $this->belongsToMany('App\Model\Employee');
    }

    public function getMaxStartingID() {
        return Counter::max('starting_id');
    }

    public function doesStartingIdExist($startingId) {
        if(Counter::where("starting_id",$startingId)->count()>0)
            return true;
        return false;
    }

    public function isStartingIdUnique($startingId) {
        if(Counter::where("starting_id",$startingId)->count()>1)
            return false;
        return true;
    }

    public function getOccurrencesOfStartingId($startingId) {
        return Counter::where("starting_id",$startingId)->pluck("id")->toArray();
    }

    public function getListOfExistingStartingId() {
        return Counter::pluck('starting_id')->toArray();
    }

    public function generateUniqueStartingId() {
        $initStartingId = self::INITIAL_STARTING_ID;
        while($this->doesStartingIdExist($initStartingId)) {
            $initStartingId += self::INITIAL_STARTING_ID;
        }
        $this::$CURRENT_UNIQUE_STARTING_ID = $initStartingId;
    }

    public function replaceDuplicateStartingIds() {
        $existingStartingIDs = $this->getListOfExistingStartingId();
        foreach ($existingStartingIDs as $aStartingId) {
            if(!$this->isStartingIdUnique($aStartingId)) {
                $occurrences = $this->getOccurrencesOfStartingId($aStartingId);
                foreach ($occurrences as $anOccurrence) {
                    $this->generateUniqueStartingId();
                    $counter = Counter::where("id",$anOccurrence)->first();
                    $counter->starting_id = $this::$CURRENT_UNIQUE_STARTING_ID;
                    $counter->save();
                }
            }
        }
    }
}
