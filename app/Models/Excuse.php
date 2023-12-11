<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Excuse extends Model
{

    public function deprivation(){
        return $this->belongsTo(Deprivation::class);
    }

  

}
