<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Doctors;
use App\Models\Days;

class Schedules extends Model
{
    use HasFactory;
    protected $table = 'schedules';
    protected $guarded = [];

    public function day()
    {
        return $this->hasOne(Days::class, 'day_id', 'id');
    }

    public function doctor()
    {
        return $this->hasOne(Doctors::class, 'doctor_id', 'id');
    }
}
