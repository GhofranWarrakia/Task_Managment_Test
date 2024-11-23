<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCommentRequest;

/**
 * Class CommentController
 *
 * Handles operations related to comments on tasks, including
 * listing, storing, showing, updating, and deleting comments.
 */
class CommentController extends Controller
{
    /**
     * Display all comments associated with a specific task.
     *
     * @param  int  $taskId The ID of the task.
     * @return \Illuminate\Http\JsonResponse JSON response containing the comments.
     */
    public function index($taskId)
    {
        // Retrieve the task along with its associated comments
        $task = Task::with('comments')->findOrFail($taskId);

        return response()->json($task->comments);
    }

    /**
     * Store a new comment for a specific task.
     *
     * @param  StoreCommentRequest  $request The validated request containing the comment data.
     * @param  Task  $task The task to which the comment will be added.
     * @return \Illuminate\Http\JsonResponse JSON response containing the created comment.
     */
    public function store(StoreCommentRequest $request, Task $task)
    {
        // Create the new comment associated with the task
        $comment = $task->comments()->create([
            'content' => $request->content,
            'user_id' => auth()->id(), // Set the current user as the creator of the comment
        ]);

        return response()->json($comment, 201);
    }

    /**
     * Display details of a specific comment.
     *
     * @param  Task  $task The task associated with the comment.
     * @param  Comment  $comment The comment to display.
     * @return \Illuminate\Http\JsonResponse JSON response containing the comment details.
     */
    public function show(Task $task, Comment $comment)
    {
        // Verify that the comment is associated with the specified task
        if ($comment->task_id !== $task->id) {
            return response()->json(['error' => 'التعليق غير مرتبط بهذه المهمة.'], 404); // "The comment is not associated with this task."
        }

        return response()->json($comment);
    }

    /**
     * Update an existing comment.
     *
     * @param  StoreCommentRequest  $request The validated request containing the updated comment data.
     * @param  Task  $task The task associated with the comment.
     * @param  Comment  $comment The comment to update.
     * @return \Illuminate\Http\JsonResponse JSON response containing the updated comment.
     */
    public function update(StoreCommentRequest $request, Task $task, Comment $comment)
    {
        // Verify that the comment is associated with the specified task
        if ($comment->task_id !== $task->id) {
            return response()->json(['error' => 'التعليق غير مرتبط بهذه المهمة.'], 404); // "The comment is not associated with this task."
        }

        // Update the comment
        $comment->update([
            'content' => $request->content,
        ]);

        return response()->json($comment);
    }

    /**
     * Remove a specific comment.
     *
     * @param  Task  $task The task associated with the comment.
     * @param  Comment  $comment The comment to be deleted.
     * @return \Illuminate\Http\JsonResponse JSON response indicating the result of the deletion.
     */
    public function destroy(Task $task, Comment $comment)
    {
        // Verify that the comment is associated with the specified task
        if ($comment->task_id !== $task->id) {
            return response()->json(['error' => 'التعليق غير مرتبط بهذه المهمة.'], 404); // "The comment is not associated with this task."
        }

        // Delete the comment
        $comment->delete();

        return response()->json(['message' => 'تم حذف التعليق بنجاح.']); // "Comment deleted successfully."
    }
}
