<?php

namespace App\Http\Controllers;

use App\Models\Subtask;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Auth::user()->tasks()->with('subtasks');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }
        if ($request->filled('due_date')) {
            $query->whereDate('due_date', $request->due_date);
        }

        $tasks = $query->orderBy('due_date', 'asc')->get();

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only(['status', 'priority', 'due_date']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'subtasks' => 'nullable|array',
            'subtasks.*.title' => 'required|string|max:255',
        ]);

        $task = $request->user()->tasks()->create($request->only('title', 'description', 'due_date', 'priority'));

        if (! empty($validated['subtasks'])) {
            $task->subtasks()->createMany($validated['subtasks']);
        }

        return redirect()->back();
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
            'status' => 'required|in:pending,completed',
            'subtasks' => 'nullable|array',
            'subtasks.*.id' => 'nullable|integer',
            'subtasks.*.title' => 'required|string|max:255',
            'subtasks.*.is_completed' => 'boolean',
        ]);

        $task->update($request->only('title', 'description', 'due_date', 'priority', 'status'));

        $keptSubtaskIds = [];

        if ($request->has('subtasks') && is_array($request->subtasks)) {
            foreach ($request->subtasks as $subtaskData) {
                if (isset($subtaskData['id'])) {
                    /** @var Subtask $subtask */
                    $subtask = $task->subtasks()->find($subtaskData['id']);
                    if ($subtask) {
                        $subtask->update($subtaskData);
                        $keptSubtaskIds[] = $subtask->id;
                    }
                } else {
                    $newSubtask = $task->subtasks()->create($subtaskData);
                    $keptSubtaskIds[] = $newSubtask->id;
                }
            }
        }

        $task->subtasks()->whereNotIn('id', $keptSubtaskIds)->delete();

        return redirect()->back();
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->back();
    }
}
