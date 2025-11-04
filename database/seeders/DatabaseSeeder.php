<?php

namespace Database\Seeders;

use App\Models\Season;
use App\Models\Team;
use App\Models\Trainer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Enums\PlayerPositions;
use App\Enums\AgeCategory;

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
            'name' => '2025/2026',
            'start_date' => '2025-08-01',
            'end_date' => '2026-06-30',
        ]);

        // 3. Maak teams aan
        $team1 = Team::create([
            'name' => 'JO17-1',
            'age_category' => AgeCategory::JO17->value,
            'season_id' => $season->id,
        ]);

        $team2 = Team::create([
            'name' => 'JO13-3',
            'age_category' => AgeCategory::JO13->value,
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
                'first_name' => 'Jaafar',
                'last_name' => 'Aamir',
                'position' => PlayerPositions::CM->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Samuel',
                'last_name' => 'Amanuel-Beyene',
                'position' => PlayerPositions::CAM->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Milan',
                'last_name' => 'van Dijk',
                'position' => PlayerPositions::RB->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Emiel',
                'last_name' => 'Brouwer',
                'position' => PlayerPositions::LB->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Roan',
                'last_name' => 'Folmer',
                'position' => PlayerPositions::CB->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Saifeddine',
                'last_name' => 'el Habachi',
                'position' => PlayerPositions::SP->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Stan',
                'last_name' => 'Kampert',
                'position' => PlayerPositions::CB->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Kuba',
                'last_name' => 'Kopiec',
                'position' => PlayerPositions::SP->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Roel',
                'last_name' => 'van Leuven',
                'position' => PlayerPositions::RB->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Daniël',
                'last_name' => 'Margaritidis',
                'position' => PlayerPositions::CVM->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Simon',
                'last_name' => 'Okbazgi',
                'position' => PlayerPositions::LW->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Sebbe',
                'last_name' => 'van Rijn',
                'position' => PlayerPositions::GK->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Jordan',
                'last_name' => 'Schreuders',
                'position' => PlayerPositions::CVM->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Sem',
                'last_name' => 'Valkenburg',
                'position' => PlayerPositions::RB->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Daan',
                'last_name' => 'Wagteveld',
                'position' => PlayerPositions::CVM->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Kyan',
                'last_name' => 'Wessels',
                'position' => PlayerPositions::CAM->value,
                'birth_date' => '2009-08-21',
            ],
            [
                'first_name' => 'Sverre',
                'last_name' => 'Wiersma',
                'position' => PlayerPositions::GK->value,
                'birth_date' => '2009-08-21',
            ],
        ]);


    }
}
