<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenAdmin\Admin\Auth\Database\Administrator;

class Deprivation extends Model
{
    public function student(){

        return $this->belongsTo(Administrator::class);


    }
    public function course(){

        return $this->belongsTo(Course::class);


    }

    public function excuse()
    {
        return $this->hasOne(Excuse::class, 'deprivation_id');
    }



    
}
