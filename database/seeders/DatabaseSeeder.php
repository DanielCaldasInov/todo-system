<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Task;
use App\Models\Subtask;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Daniel Silva',
            'email' => 'daniel@example.com',
            'password' => Hash::make('password'),
        ]);

        $task1 = Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Finalize Project Presentation',
            'description' => 'Review all the code, test dark mode, and ensure subtasks are working perfectly before the evaluation.',
            'due_date' => Carbon::tomorrow(),
            'priority' => 'high',
            'status' => 'pending',
        ]);

        Subtask::factory()->create(['task_id' => $task1->id, 'title' => 'Review Vue.js code', 'is_completed' => true]);
        Subtask::factory()->create(['task_id' => $task1->id, 'title' => 'Test simulated email sending', 'is_completed' => false]);
        Subtask::factory()->create(['task_id' => $task1->id, 'title' => 'Commit the Factories', 'is_completed' => false]);

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Study Laravel 12 new features',
            'description' => 'Read the documentation about the new Starter Kits and the simplified structure.',
            'due_date' => Carbon::now()->addDays(7),
            'priority' => 'medium',
            'status' => 'pending',
        ]);

        Task::factory()->create([
            'user_id' => $user->id,
            'title' => 'Organize the desk',
            'description' => 'Dust, organize cables, and prepare the setup for programming.',
            'due_date' => Carbon::yesterday(),
            'priority' => 'low',
            'status' => 'completed',
        ]);
    }
}
