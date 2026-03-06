<?php

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

test('send task reminders command logs simulated emails for tasks due tomorrow', function () {
    Log::shouldReceive('info')->once()->withArgs(function ($message) {
        return str_contains($message, 'Simulation: Email sent to');
    });

    $user = User::factory()->create(['email' => 'test@reminder.com']);

    Task::factory()->create([
        'user_id' => $user->id,
        'due_date' => Carbon::tomorrow(),
        'status' => 'pending',
    ]);

    Task::factory()->create([
        'user_id' => $user->id,
        'due_date' => Carbon::today(),
        'status' => 'pending',
    ]);

    Task::factory()->create([
        'user_id' => $user->id,
        'due_date' => Carbon::tomorrow(),
        'status' => 'completed',
    ]);

    $this->artisan('app:send-task-reminders')
        ->expectsOutputToContain("Simulation complete! 1 reminders 'sent' to the logfile.")
        ->assertSuccessful();
});
