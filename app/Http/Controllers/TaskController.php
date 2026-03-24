<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class TaskController extends Controller
{
    // 1. Display all tasks
    public function index()
    {
        $tasks = Task::where('user_id', Auth::id())->get();
        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks
        ]);
    }

    // 2. Show create form
    public function create()
    {
        return Inertia::render('Tasks/CreateTask');
    }

    // 3. Store new task
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_time' => 'nullable|date',
            'time_to_complete' => 'nullable|date',
            'priority' => 'required|in:low,medium,high',
        ]);

        Task::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'scheduled_time' => isset($validated['scheduled_time'])
                ? date('Y-m-d H:i:s', strtotime($validated['scheduled_time']))
                : null,
            'time_to_complete' => isset($validated['time_to_complete'])
                ? date('Y-m-d H:i:s', strtotime($validated['time_to_complete']))
                : null,
            'priority' => $validated['priority'],
            'is_completed' => false,
            'user_id' => Auth::id(),
        ]);

        return redirect()->back()->with('success', 'Task created successfully!');
    }

    // 4. Show edit form
    public function edit(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        return Inertia::render('Tasks/Edit', [
            'task' => $task
        ]);
    }

    // 5. Update task
    public function update(Request $request, Task $task)
{
    if ($task->user_id !== Auth::id()) {
        abort(403);
    }

    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'scheduled_time' => 'nullable|date',
        'time_to_complete' => 'nullable|date',
        'priority' => 'required|in:low,medium,high',
        'is_completed' => 'sometimes|boolean',
    ]);

    $task->update([
        'title' => $validated['title'],
        'description' => $validated['description'] ?? null,
        'scheduled_time' => isset($validated['scheduled_time'])
            ? date('Y-m-d H:i:s', strtotime($validated['scheduled_time']))
            : null,
        'time_to_complete' => isset($validated['time_to_complete'])
            ? date('Y-m-d H:i:s', strtotime($validated['time_to_complete']))
            : null,
        'priority' => $validated['priority'],
        'is_completed' => $validated['is_completed'] ?? false,
    ]);

    return redirect()->back()->with('success', 'Task updated successfully!');
}

    // 6. Delete task
    public function destroy(Task $task)
{
    if ($task->user_id !== Auth::id()) {
        abort(403);
    }

    $task->delete();

    return redirect()->back()->with('success', 'Task deleted successfully!');
}

    // 7. Tasks for today
    public function today()
{
    $todayStart = Carbon::now('Asia/Kuala_Lumpur')->startOfDay();
    $todayEnd = Carbon::now('Asia/Kuala_Lumpur')->endOfDay();

    $tasks = Task::where('user_id', Auth::id())
        ->whereBetween('scheduled_time', [$todayStart, $todayEnd])
        ->where('is_completed', false)
        ->orderBy('scheduled_time', 'asc')
        ->get();

    return Inertia::render('Tasks/Today', [
        'tasks' => $tasks
    ]);
}

    // 8. Upcoming tasks
    public function upcoming()
{
    $todayEnd = Carbon::today()->endOfDay();

    $tasks = Task::where('user_id', Auth::id())
        ->where('scheduled_time', '>', $todayEnd)
        ->where('is_completed', false)
        ->orderBy('scheduled_time', 'asc')
        ->get();

    return Inertia::render('Tasks/Upcoming', [
        'tasks' => $tasks
    ]);
}

    // 9. Completed tasks
    public function completed()
    {
        $tasks = Task::where('user_id', Auth::id())
            ->where('is_completed', true)
            ->get();

        return Inertia::render('Tasks/Completed', [
            'tasks' => $tasks
        ]);
    }

    // 10. Mark a task as completed
    public function markComplete(Task $task)
    {
        if ($task->user_id !== Auth::id()) {
            abort(403);
        }

        $task->update([
            'is_completed' => true,
            'updated_at' => now(),
        ]);

        return redirect()->route('tasks.today')->with('success', 'Task marked as completed.');
    }

    public function dashboard()
{
    $todayStart = Carbon::now('Asia/Kuala_Lumpur')->startOfDay();
    $todayEnd = Carbon::now('Asia/Kuala_Lumpur')->endOfDay();

    $todayTasks = Task::where('user_id', Auth::id())
        ->whereBetween('scheduled_time', [$todayStart, $todayEnd])
        ->where('is_completed', false)
        ->get();

    $upcomingTasks = Task::where('user_id', Auth::id())
        ->where('scheduled_time', '>', $todayEnd)
        ->where('is_completed', false)
        ->get();

    $completedTasks = Task::where('user_id', Auth::id())
        ->where('is_completed', true)
        ->get();

    return Inertia::render('Dashboard', [
        'todayTasks' => $todayTasks,
        'upcomingTasks' => $upcomingTasks,
        'completedTasks' => $completedTasks,
    ]);
}
}