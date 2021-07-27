<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departments;
use App\Models\Schedules;
use App\Models\Doctors;
use App\Models\Leaves;
use App\Models\Days;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $data['departments']    = Departments::get();

        return view('frontend.index', $data);
    }

    public function getDoctor(Request $request)
    {
        $departmentId   = $request->id;
        $department     = Departments::with('doctor')->find($departmentId);
        
        return $department->doctor ?? '';
    }

    public function getSchedule(Request $request)
    {
        $doctor = $request->id;
        $date   = $request->date;
        $searchDate = Carbon::parse($date)->format('Y-m-d');
        $day    = Carbon::parse($date)->format('l');
        $dayId  = Days::where('day', $day)->first()->id;

        $schedule = Schedules::where('doctor_id', $doctor)->where('day_id', $dayId)->first();

        if($schedule)
        {
            $leaveCheck = Leaves::where('doctor_id', $doctor)->where('date', $searchDate)->first();
            if($leaveCheck)
            {
                return $leaveCheck;
            }else
            {
                return $schedule;
            }
        }

        return $day;
    }
}
