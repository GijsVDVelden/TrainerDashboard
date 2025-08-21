<?php

namespace App\Models;

use App\Enums\AgeCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name','season_id','age_category'];

    protected $casts = [
        'age_category' => AgeCategory::class,
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function trainers()
    {
        return $this->hasMany(Trainer::class);
    }
}
