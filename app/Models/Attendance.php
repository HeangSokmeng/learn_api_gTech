<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'staff_id','checkin_time', 'checkout_time', 'checkin_status', 'checkout_status', 'attendances_status'
    ];
    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
