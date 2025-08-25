<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = [
        'event_id', 'opponent', 'home', 'our_score', 'opponent_score',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function playerStats()
    {
        return $this->hasMany(GamePlayerStats::class);
    }
}
