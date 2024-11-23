<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task; // Corrected import for the Task model
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\ReassignTaskRequest;
use App\Http\Requests\UpdateTaskStatusRequest;
use Illuminate\Support\Facades\Cache; // Importing Cache facade
use Carbon\Carbon; // Import Carbon for date handling

/**
 * Class TaskController
 *
 * Manages the operations related to tasks, including creating, updating,
 * assigning, and reporting tasks.
 */
class TaskController extends Controller
{
    /**
     * Store a new task.
     *
     * @param  StoreTaskRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreTaskRequest $request)
    {
        // Create a new task using validated data from the request
        $task = Task::create($request->validated());

        return response()->json($task, 201);
    }

    /**
     * Update the status of an existing task.
     *
     * @param  UpdateTaskStatusRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(UpdateTaskStatusRequest $request, $id)
    {
        // Find the task by ID or fail if not found
        $task = Task::findOrFail($id);

        // Update the task's status with validated data
        $task->update(['status' => $request->validated()->status]);

        // Create a record of the status update
        $task->statusUpdates()->create(['status' => $request->status]);

        // If the task is completed, update dependent tasks
        if ($task->status == 'Completed') {
            $task->dependentTasks()->where('status', 'Blocked')->each(function($dependentTask) {
                // If the dependent task is not blocked, update its status
                if (!$dependentTask->isBlocked()) {
                    $dependentTask->update(['status' => 'Open']);
                }
            });
        }

        return response()->json($task);
    }

    /**
     * Reassign an existing task to a different user.
     *
     * @param  ReassignTaskRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function reassign(ReassignTaskRequest $request, $id)
    {
        // Find the task by ID or fail if not found
        $task = Task::findOrFail($id);

        // Update the assigned user for the task
        $task->update(['assigned_to' => $request->validated()->assigned_to]);

        return response()->json($task);
    }

    /**
     * Add a dependency to a task.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addDependency(Request $request, $id)
    {
        // Find the task by ID or fail if not found
        $task = Task::findOrFail($id);

        // Validate the dependency task ID
        $request->validate([
            'depends_on_task_id' => 'required|exists:tasks,id',
        ]);

        // Attach the dependency to the task
        $task->dependencies()->attach($request->depends_on_task_id);

        // If the task is blocked, set its status to Blocked
        if ($task->isBlocked()) {
            $task->update(['status' => 'Blocked']);
        }

        return response()->json($task);
    }

    /**
     * Generate a daily report of tasks due today that are not completed.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateDailyReport()
    {
        $tasks = Task::whereDate('due_date', Carbon::today())
                     ->where('status', '!=', 'Completed')
                     ->get();

        return response()->json($tasks);
    }

    /**
     * Retrieve all tasks based on query parameters.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $cacheKey = 'tasks_' . md5(json_encode($request->all()));

        $tasks = Cache::remember($cacheKey, now()->addMinutes(10), function () use ($request) {
            $query = Task::query();

            // Filter tasks based on request parameters
            if ($request->has('type')) {
                $query->where('type', $request->type);
            }

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('assigned_to')) {
                $query->where('assigned_to', $request->assigned_to);
            }

            if ($request->has('due_date')) {
                $query->whereDate('due_date', $request->due_date);
            }

            if ($request->has('priority')) {
                $query->where('priority', $request->priority);
            }

            return $query->get();
        });

        return response()->json($tasks);
    }

    /**
     * Restore a soft-deleted task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restore($id)
    {
        // Find the soft-deleted task by ID or fail if not found
        $task = Task::withTrashed()->findOrFail($id);

        // Restore the task
        $task->restore();

        return response()->json($task);
    }

    /**
     * Add an attachment to a task.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addAttachment(Request $request, $id)
    {
        // Find the task by ID or fail if not found
        $task = Task::findOrFail($id);

        // Validate the attachment file
        $request->validate([
            'attachment' => 'required|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        // Store the attachment and create a record for it
        $path = $request->file('attachment')->store('attachments');

        $task->attachments()->create([
            'filename' => $request->file('attachment')->getClientOriginalName(),
            'file_path' => $path,
        ]);

        return response()->json(['message' => 'Attachment uploaded successfully']);
    }

    /**
     * Show the details of a specific task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        // Find the task by ID
        $task = Task::find($id);

        if (!$task) {
            throw new TaskNotFoundException(); // Custom exception for not found
        }

        return response()->json($task);
    }

    /**
     * Soft delete a task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteTask($id)
    {
        // Find the task by ID or fail if not found
        $task = Task::findOrFail($id);
        $task->delete(); // Soft delete the task

        return response()->json(['message' => 'Task soft deleted successfully']);
    }

    /**
     * Restore a previously deleted task.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function restoreTask($id)
    {
        // Find the soft-deleted task by ID or fail if not found
        $task = Task::withTrashed()->findOrFail($id);

        // Restore the task if it is deleted
        if ($task->trashed()) {
            $task->restore();
            return response()->json(['message' => 'Task restored successfully']);
        }

        return response()->json(['message' => 'Task is not deleted']);
    }
}
