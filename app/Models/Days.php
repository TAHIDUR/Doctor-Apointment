<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Days;

class Days extends Model
{
    use HasFactory;
    protected $table = 'days';
    protected $guarded = [];

    public function schedule()
    {
        return $this->belongsTo(Schedules::class);
    }
}
