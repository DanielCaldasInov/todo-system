<?php

use App\Models\Task;
use App\Models\Subtask;
use App\Models\User;
use Carbon\Carbon;

test('tasks page can be rendered for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('tasks.index'));

    $response->assertOk();
});

test('unauthenticated users are redirected to login', function () {
    $response = $this->get(route('tasks.index'));

    $response->assertRedirect('/login');
});

test('user can create a new task without subtasks', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/tasks', [
        'title' => 'Test Task',
        'description' => 'This is a test description',
        'due_date' => Carbon::tomorrow()->toDateString(),
        'priority' => 'high',
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', [
        'title' => 'Test Task',
        'user_id' => $user->id,
        'priority' => 'high',
    ]);
});

test('user can create a new task with subtasks', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/tasks', [
        'title' => 'Task with Checklist',
        'priority' => 'medium',
        'subtasks' => [
            ['title' => 'First subtask'],
            ['title' => 'Second subtask'],
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', ['title' => 'Task with Checklist']);
    $this->assertDatabaseHas('subtasks', ['title' => 'First subtask', 'is_completed' => false]);
    $this->assertDatabaseHas('subtasks', ['title' => 'Second subtask', 'is_completed' => false]);
});

test('user can update a task and modify subtasks', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);
    $subtask = Subtask::factory()->create(['task_id' => $task->id, 'title' => 'Old Title']);

    $response = $this->actingAs($user)->put("/tasks/{$task->id}", [
        'title' => 'Updated Task Title',
        'priority' => 'low',
        'status' => 'completed',
        'subtasks' => [
            ['id' => $subtask->id, 'title' => 'Updated Subtask Title', 'is_completed' => true], // Update existing
            ['title' => 'Brand New Subtask'], // Create new
        ],
    ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'Updated Task Title', 'status' => 'completed']);
    $this->assertDatabaseHas('subtasks', ['id' => $subtask->id, 'title' => 'Updated Subtask Title', 'is_completed' => true]);
    $this->assertDatabaseHas('subtasks', ['title' => 'Brand New Subtask']);
});

test('user can delete their own task', function () {
    $user = User::factory()->create();
    $task = Task::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->delete("/tasks/{$task->id}");

    $response->assertRedirect();
    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('user cannot update or delete tasks belonging to others', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $task = Task::factory()->create(['user_id' => $user1->id]);

    $updateResponse = $this->actingAs($user2)->put("/tasks/{$task->id}", [
        'title' => 'Hacked Title',
        'priority' => 'low',
        'status' => 'pending',
    ]);

    $deleteResponse = $this->actingAs($user2)->delete("/tasks/{$task->id}");

    $updateResponse->assertForbidden();
    $deleteResponse->assertForbidden();

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => $task->title]);
});
