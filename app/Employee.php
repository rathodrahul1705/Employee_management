<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $dates = ['created_at', 'dob','updated_at', 'join_date'];
    protected $fillable = ['user_id', 'first_name', 'last_name', 'sex', 'dob', 'join_date', 'desg', 'department_id', 'salary', 'photo'];
    public function user() {
        return $this->belongsTo('App\User','user_id');
    }

    public function department() {
        // return $this->hasOne('App\Department');
        return $this->belongsTo('App\Department','department_id');
    }

}
