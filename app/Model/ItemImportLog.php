<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ItemImportLog extends Model
{
    public function user() {
        return $this->belongsTo('App\Model\User');
    }
}
