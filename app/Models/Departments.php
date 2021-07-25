<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Doctors;

class Departments extends Model
{
    use HasFactory;
    protected $table = 'departments';
    protected $guarded = [];

    public function doctor()
    {
        return $this->hasOne(Doctors::class, 'department_id', 'id');
    }
}
