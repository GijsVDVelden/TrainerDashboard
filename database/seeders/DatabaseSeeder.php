<?php

namespace Database\Seeders;

use App\Models\Season;
use App\Models\Team;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Maak of haal de user
        $user = User::firstOrCreate(
            ['email' => 'g.velden28@hotmail.com'],
            [
                'name' => 'Gijs van der Velden',
                'password' => Hash::make('Test123!'),
            ]
        );

        // 2. Maak een seizoen aan
        $season = Season::create([
            'name' => '2024/2025',
            'start_date' => '2024-08-01',
            'end_date' => '2025-06-30',
        ]);

        // 3. Maak teams aan
        $team1 = Team::create([
            'name' => 'JO17-1',
            'age_category' => 'JO17',
            'season_id' => $season->id,
        ]);

        $team2 = Team::create([
            'name' => 'JO13-3',
            'age_category' => 'JO13',
            'season_id' => $season->id,
        ]);

        // 4. Koppel de user als trainer aan beide teams
        Trainer::create([
            'team_id' => $team1->id,
            'user_id' => $user->id,
        ]);

        Trainer::create([
            'team_id' => $team2->id,
            'user_id' => $user->id,
        ]);

        // 5. Voeg spelers toe aan team
        $team1->players()->createMany([
            [
                'first_name' => 'Sebbe',
                'last_name' => 'van Rijn',
                'position' => 'Keeper',
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Emiel',
                'last_name' => 'Brouwer',
                'position' => 'Linksback',
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Jesse',
                'last_name' => 'de Vries',
                'position' => 'Middenvelder',
                'birth_date' => '2009-08-21',
            ],
        ]);

    }
}
