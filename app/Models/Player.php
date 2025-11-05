<?php

namespace App\Models;

use App\Enums\PlayerPositions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = ['first_name','last_name','position','birth_date','team_id'];

    protected $casts = [
        'birth_date' => 'date',
        'position'   => PlayerPositions::class,
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function attendances() {
        return $this->hasMany(Attendance::class);
    }

    public function gameStats() {
        return $this->hasMany(GamePlayerStats::class);
    }

    public function aggregatedStats()
    {
        return [
            'goals'        => $this->gameStats()->sum('goals'),
            'assists'      => $this->gameStats()->sum('assists'),
            'yellow_cards' => $this->gameStats()->sum('yellow_cards'),
            'red_cards'    => $this->gameStats()->sum('red_cards'),
        ];
    }

}
