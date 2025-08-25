<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'event_id',
        'player_id',
        'trainer_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'status' => AttendanceStatus::class,
    ];

    public function event() {
        return $this->belongsTo(Event::class);
    }

    public function player() {
        return $this->belongsTo(Player::class);
    }
}
