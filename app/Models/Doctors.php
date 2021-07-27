<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Departments;

class Doctors extends Model
{
    use HasFactory;
    protected $table = 'doctors';
    protected $guarded = [];

    public function department()
    {
        return $this->belongsTo(Departments::class, 'department_id');
    }
}
