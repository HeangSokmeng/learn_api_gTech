<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request as FacadesRequest;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Attendance::all();
    }
    public function checkAttendance(Request $request)
{
    $policyTime = (object)[
        'checkin_time' => '8:00',
        'checkout_time' => '17:00'
    ];

    $staff_id = $request->staff_id;
    $currentTime = Carbon::now();
    $today = Carbon::today();
    $attendance = Attendance::whereDate('created_at', $today)->where('staff_id', $staff_id)->first();

    if ($attendance) {
        if ($attendance->checkout_time) {
            return response()->json(['message' => 'Checkout already recorded for today'], 400);
        }
        $checkout_time = DateTime::createFromFormat('H:i', $policyTime->checkout_time);
        $currentTimeFormatted = DateTime::createFromFormat('H:i', $currentTime->format('H:i'));
        $checkOut_status = "early";
        // return "Arrived";
        if ($checkout_time < $currentTimeFormatted) {
            $checkOut_status = 'late';
        }
        $attendance->checkout_time = $currentTime;
        $attendance->attendances_status = 'Normal';
        $attendance->update([
            'staff_id' => $staff_id,
            'checkout_time' => $currentTime->format('H:i'),
            'checkout_status' => $checkOut_status,
            'attendances_status' => 'P',
        ]);
        return response()->json(['message' => 'Checkout Successfully'], 200);
    }
    else{
        $checkIn_status = "early";
        $checkinTime = DateTime::createFromFormat('H:i', $policyTime->checkin_time);
        $currentTimeFormatted = DateTime::createFromFormat('H:i', $currentTime->format('H:i'));
        
        if ($checkinTime < $currentTimeFormatted) {
            $checkIn_status = 'late';
        }
        $checkIn = Attendance::create([
            'staff_id' => $staff_id,
            'checkin_time' => $currentTime->format('H:i'),
            'checkin_status' => $checkIn_status,
            'attendances_status' => 'P',
        ]);
        return $checkIn;
    }
}

public function geListStaff()
{
    $staffStatus = DB::table('staff as s')
        ->join('attendances as att', 'att.staff_id', '=', 's.id')
        ->select(
            's.full_name',
            'att.checkin_time',
            'att.checkout_time',
            'att.checkin_status',
            'att.checkout_status',
            'att.attendances_status'
        )
        ->get();

    return $staffStatus;
}


function getStaff($attendance, $staff_id)
{
    $lst = [];
    foreach ($attendance as $att) {
        if ($att->staff_id == $staff_id) {
            $lst[] = $att;
        }
    }
    return $lst;
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}
