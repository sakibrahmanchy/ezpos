<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CurrencyDenomination extends Model
{
    protected $fillable = ["denomination_name","denomination_value"];

    /**
     * @return mixed
     */
    public function getDenominationName()
    {
        return $this->denomination_name;
    }

    /**
     * @param mixed $denomination_name
     */
    public function setDenominationName($denomination_name)
    {
        $this->denomination_name = $denomination_name;
    }

    /**
     * @return mixed
     */
    public function getDenominationValue()
    {
        return $this->denomination_value;
    }

    /**
     * @param mixed $denomination_value
     */
    public function setDenominationValue($denomination_value)
    {
        $this->denomination_value = $denomination_value;
    }


}
