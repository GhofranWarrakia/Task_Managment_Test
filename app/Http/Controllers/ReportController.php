<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Task; // Ensure the Task model is correctly imported

/**
 * Class ReportController
 *
 * Handles the generation of various task reports, including
 * completed tasks, overdue tasks, and user-specific tasks.
 */
class ReportController extends Controller
{
    /**
     * Generate a report of completed tasks.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing completed tasks.
     */
    public function completedTasksReport()
    {
        // Retrieve all tasks with a status of 'Completed'
        $completedTasks = Task::where('status', 'Completed')->get();

        return response()->json([
            'report' => 'Completed Tasks', // Title of the report
            'tasks' => $completedTasks // List of completed tasks
        ]);
    }

    /**
     * Generate a report of overdue tasks.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing overdue tasks.
     */
    public function overdueTasksReport()
    {
        // Retrieve tasks that are not completed and are past their due date
        $overdueTasks = Task::where('status', '!=', 'Completed')
                            ->where('due_date', '<', Carbon::now())
                            ->get();

        return response()->json([
            'report' => 'Overdue Tasks', // Title of the report
            'tasks' => $overdueTasks // List of overdue tasks
        ]);
    }

    /**
     * Generate a report of tasks assigned to a specific user.
     *
     * @param  int  $userId The ID of the user.
     * @return \Illuminate\Http\JsonResponse JSON response containing tasks assigned to the user.
     */
    public function userTasksReport($userId)
    {
        // Retrieve tasks assigned to the specified user
        $userTasks = Task::where('assigned_to', $userId)->get();

        return response()->json([
            'report' => 'Tasks for User ' . $userId, // Title of the report
            'tasks' => $userTasks // List of tasks assigned to the user
        ]);
    }
}
