<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    public function year_semester()
    {
        return $this->belongsTo(AcademicYearAndSemester::class);
    }


}
