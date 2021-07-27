<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Leaves;

class Leaves extends Model
{
    use HasFactory;
    protected $table = 'leaves';
    protected $guarded = [];
}
