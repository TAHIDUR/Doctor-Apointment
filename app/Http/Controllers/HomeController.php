<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Departments;
use App\Models\Doctors;

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
}
