<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Skill;
use App\Models\User;

class SkillSeeder extends Seeder
{
    public function run()
    {
        $users = User::all();

        if ($users->count() === 0) {
            $this->command->info("No users found! Please create at least one user.");
            return;
        }

        $skills = ['Python', 'AI', 'UI Design', 'JavaScript', 'Laravel', 'Machine Learning', 'React', 'Django', 'PHP', 'SQL'];

        foreach ($skills as $index => $skillName) {
            $user = $users[$index % $users->count()]; // Assign skills to users in round-robin

            Skill::create([
                'user_id' => $user->id,
                'name' => $skillName,
                'has_certificate' => rand(0,1),
                'certificate_path' => null
            ]);
        }

        $this->command->info('Skills seeded successfully!');
    }
}
