<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendTaskReminders extends Command
{
    protected $signature = 'app:send-task-reminders';

    protected $description = 'Simulates sending email notifications for tasks due tomorrow';

    public function handle()
    {
        $tomorrow = Carbon::tomorrow()->toDateString();

        $tasks = Task::with('user')
            ->where('status', 'pending')
            ->whereDate('due_date', $tomorrow)
            ->get();

        if ($tasks->isEmpty()) {
            $this->info('No pending tasks for tomorrow. No notifications simulated.');
            return;
        }

        $count = 0;

        foreach ($tasks as $task) {
            $email = $task->user->email ?? 'unknown@user.com';
            $title = $task->title;

            $message = "Simulation: Email sent to {$email} - The task [{$title}] is due tomorrow ({$tomorrow})!";

            Log::info($message);

            $count++;
        }

        $this->info("Simulation complete! {$count} reminders 'sent' to the logfile.");
    }
}
