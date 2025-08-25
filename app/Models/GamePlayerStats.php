<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GamePlayerStats extends Model
{
    protected $fillable = [
        'game_id',
        'player_id',
        'goals',
        'assists',
        'yellow_cards',
        'red_cards',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
