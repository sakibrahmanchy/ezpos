<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sitting extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    
    protected $fillable = [
        'name', 'sit_count', 'busy', 'sit_busy', 'position_x', 'position_y'
    ];


    public function orders() {
        return $this->hasMany('App\Model\Order');
    }
}
