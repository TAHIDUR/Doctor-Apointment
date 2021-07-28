<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointments;
use App\Models\Departments;
use App\Models\Schedules;
use App\Models\Doctors;
use App\Models\Leaves;
use App\Models\Days;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $data['departments']    = Departments::get();

        if ($request->session()->has('users')) {
            $appointments = request()->session()->get('appointments');
        }else
        {
            $appointments = Appointments::with('doctor.department', 'schedule')->get();
            $request->session()->put('appointments', $appointments);
        }

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

        $schedule = Schedules::where('doctor_id', $doctor)->where('day_id', $dayId)->get() ?? '';

        if($schedule->toArray())
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

    public function checkAppointment(Request $request)
    {
        $scheduleId = $request->scheduleId;
        $date       = $request->date;

        $schedule = Schedules::find($scheduleId);
        $maximumPatitent = $schedule->maximum_patient;
        // $appointments = Appointments::get();
        $appointments = Appointments::where('schedule_id', $scheduleId)->where('appoint_date', $date)->get();

        $totalAppointment = $appointments->count();

        if($maximumPatitent > $totalAppointment)
        {
            return 'available';
        }else
        {
            return $maximumPatitent;
        }
    }

    public function setAppointment(Request $request)
    {
        $scheduleId = $request->scheduleId;
        $date       = $request->date;

        $schedule = Schedules::with('doctor')->find($scheduleId);
        $order_id = Carbon::now()->format('Ymdhis');

        $appointment = Appointments::create([
            'order_id'      => $order_id,
            'doctor_id'     => $schedule->doctor_id,
            'day_id'        => $schedule->day_id,
            'schedule_id'   => $schedule->id,
            'appoint_date'  => Carbon::parse($date),
            'fee'           => $schedule->doctor->fee,
        ]);

        $appointment = Appointments::with('doctor.department', 'schedule')->find($appointment->id);

        $savedData['sl']            = Appointments::get()->count();
        $savedData['id']            = $appointment->id;
        $savedData['department']    = $appointment->doctor->department->name;
        $savedData['doctor']        = $appointment->doctor->name;
        $savedData['date']          = $appointment->appoint_date;
        $savedData['schedule']      = $appointment->schedule->start_time .'-'.$appointment->schedule->end_time;

        return $savedData;
    }

    public function deleteAppointment(Request $request)
    {
        $scheduleId = $request->scheduleId;

        $appointment = Appointments::find($scheduleId);

        $appointment->delete();
    }
}
