<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
   
    use HasApiTokens, HasFactory, Notifiable;
    protected $table = 'admin_users';
    protected $fillable = ['name', 'username', 'academic_number', 'password'];





    public function roles(): BelongsToMany
    {
        $pivotTable = config('admin.database.role_users_table');

        $relatedModel = config('admin.database.roles_model');

        return $this->belongsToMany($relatedModel, $pivotTable, 'user_id', 'role_id');
    }

    public function hasRole($slug)
    {
        return $this->roles->where('slug', $slug)->isNotEmpty();
    }

    public function scopeStudents($query)
    {
        return $query->whereHas('roles', function ($query) {
            $query->where('slug', 'student');
        });
    }


}
