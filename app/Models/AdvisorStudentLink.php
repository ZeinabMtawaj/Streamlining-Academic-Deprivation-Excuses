<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OpenAdmin\Admin\Auth\Database\Administrator;

class AdvisorStudentLink extends Model
{
    protected $table = 'advisor_student_link';

    public function student(){

        return $this->belongsTo(Administrator::class, 'student_id');

    }

    public function advisor(){

        return $this->belongsTo(Administrator::class, 'advisor_id');

    }


}
