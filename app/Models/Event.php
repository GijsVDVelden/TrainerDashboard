<?php
// app/Models/Event.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\EventType;
use App\Enums\AttendanceStatus;

class Event extends Model
{
    protected $fillable = [
        'team_id','type', 'starts_at','ends_at','location','notes'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'type'      => EventType::class,
    ];

    public function attendances() {
        return $this->hasMany(Attendance::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function game()
    {
        return $this->hasOne(Game::class);
    }
}
