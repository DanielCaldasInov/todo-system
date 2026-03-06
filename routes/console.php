<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('app:send-task-reminders')->dailyAt('08:00');
