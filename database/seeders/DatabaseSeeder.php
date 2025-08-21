<?php

namespace Database\Seeders;

use App\Models\Season;
use App\Models\Team;
use App\Models\Trainer;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'g.velden28@hotmail.com'],
            [
                'name' => 'Gijs van der Velden',
                'password' => Hash::make('Test123!'),
            ]
        );

        $user = User::first();

        // 2. Maak een seizoen aan
        $season = Season::create([
            'name' => '2024/2025',
            'start_date' => '2024-08-01',
            'end_date' => '2025-06-30',
        ]);

        // 3. Maak een team aan
        $team = Team::create([
            'name' => 'DTS JO17-1',
            'age_category' => 'JO17',
            'season_id' => $season->id,
        ]);

        // 4. Koppel de user als trainer
        Trainer::create([
            'team_id' => $team->id,
            'user_id' => $user->id,
        ]);
    }
}
