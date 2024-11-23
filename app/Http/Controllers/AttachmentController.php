<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreAttachmentRequest;

/**
 * Class AttachmentController
 *
 * Handles the operations related to attachments of tasks, including
 * listing, storing, showing, deleting, and downloading attachments.
 */
class AttachmentController extends Controller
{
    /**
     * Display all attachments associated with a specific task.
     *
     * @param  int  $taskId The ID of the task.
     * @return \Illuminate\Http\JsonResponse JSON response containing the attachments.
     */
    public function index($taskId)
    {
        // Retrieve the task and its associated attachments
        $task = Task::with('attachments')->findOrFail($taskId);

        return response()->json($task->attachments);
    }

    /**
     * Store a new attachment for a specific task.
     *
     * @param  StoreAttachmentRequest  $request The validated request containing the attachment.
     * @param  Task  $task The task to which the attachment will be added.
     * @return \Illuminate\Http\JsonResponse JSON response containing the created attachment.
     */
    public function store(StoreAttachmentRequest $request, Task $task)
    {
        // Upload the file and store it on the server
        $filePath = $request->file('file')->store('attachments');

        // Create the attachment associated with the task
        $attachment = $task->attachments()->create([
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $filePath,
            'file_size' => $request->file('file')->getSize(),
            'uploaded_by' => auth()->id(), // Set the current user as the uploader
        ]);

        return response()->json($attachment, 201);
    }

    /**
     * Display details of a specific attachment.
     *
     * @param  Task  $task The task associated with the attachment.
     * @param  Attachment  $attachment The attachment to display.
     * @return \Illuminate\Http\JsonResponse JSON response containing the attachment details.
     */
    public function show(Task $task, Attachment $attachment)
    {
        // Verify that the attachment is associated with the specified task
        if ($attachment->task_id !== $task->id) {
            return response()->json(['error' => 'المرفق غير مرتبط بهذه المهمة.'], 404); // "The attachment is not associated with this task."
        }

        return response()->json($attachment);
    }

    /**
     * Remove a specific attachment.
     *
     * @param  Task  $task The task associated with the attachment.
     * @param  Attachment  $attachment The attachment to be deleted.
     * @return \Illuminate\Http\JsonResponse JSON response indicating the result of the deletion.
     */
    public function destroy(Task $task, Attachment $attachment)
    {
        // Verify that the attachment is associated with the specified task
        if ($attachment->task_id !== $task->id) {
            return response()->json(['error' => 'المرفق غير مرتبط بهذه المهمة.'], 404); // "The attachment is not associated with this task."
        }

        // Delete the attachment from storage
        Storage::delete($attachment->file_path);

        // Delete the attachment from the database
        $attachment->delete();

        return response()->json(['message' => 'تم حذف المرفق بنجاح.']); // "Attachment deleted successfully."
    }

    /**
     * Download a specific attachment.
     *
     * @param  Task  $task The task associated with the attachment.
     * @param  Attachment  $attachment The attachment to be downloaded.
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse The file response for downloading the attachment.
     */
    public function download(Task $task, Attachment $attachment)
    {
        // Verify that the attachment is associated with the specified task
        if ($attachment->task_id !== $task->id) {
            return response()->json(['error' => 'المرفق غير مرتبط بهذه المهمة.'], 404); // "The attachment is not associated with this task."
        }

        // Return the attachment for download
        return Storage::download($attachment->file_path, $attachment->file_name);
    }
}
