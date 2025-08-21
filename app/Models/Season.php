<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Season extends Model
{

    use HasFactory;

    protected $fillable = ['name', 'start_date', 'end_date'];

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}

