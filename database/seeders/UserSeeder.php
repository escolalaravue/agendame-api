<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory(10)
            ->create()->each(function($user) {
                $team = Team::query()->create([
                    'token' => Str::uuid(),
                    'name' => $user->first_name . " Team"
                ]);

                setPermissionsTeamId($team->id);
                $user->assignRole('admin');
            });
    }
}
