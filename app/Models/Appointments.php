<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Appointments;
use App\Models\Schedules;
use App\Models\Doctors;

class Appointments extends Model
{
    use HasFactory;
    protected $table = 'appointments';
    protected $guarded = [];

    public function doctor()
    {
        return $this->belongsTo(Doctors::class, 'doctor_id', 'id');
    }

    public function schedule()
    {
        return $this->belongsTo(Schedules::class, 'schedule_id', 'id');
    }
}
