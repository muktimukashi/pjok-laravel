<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'student_id', 'name', 'gender', 'email', 'status', 'class_name', 'year', 'semester',
        'attendance', 'cognitive', 'affective', 'psychomotor', 'final_score', 'predicate', 'predicate_class',
    ];
}
