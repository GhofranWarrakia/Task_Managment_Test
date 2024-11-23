<?php

namespace App\Jobs;

use App\Models\Task; // استيراد نموذج Task
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon; // استيراد مكتبة Carbon للتعامل مع التواريخ
use Illuminate\Support\Facades\Log; // استيراد Log لتسجيل الأخطاء

/**
 * Class GenerateDailyReport
 *
 * هذه الوظيفة مسؤولة عن توليد تقرير يومي للمهام غير المكتملة.
 */
class GenerateDailyReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
       
    }

    /**
     * Execute the job.
     *
     * هذا هو المكان الذي يتم فيه تنفيذ منطق توليد التقرير.
     *
     * @return void
     */
    public function handle()
    {
        // استرجاع المهام غير المكتملة التي موعد تسليمها هو اليوم
        $tasks = Task::whereDate('due_date', Carbon::today())
                     ->where('status', '!=', 'Completed')
                     ->get();

        // تحقق من وجود مهام
        if ($tasks->isEmpty()) {
            Log::info('لا توجد مهام غير مكتملة لليوم.');
            return;
        }

        // توليد التقرير
        $this->generateReport($tasks);
    }

    /**
     * Generate the report based on the retrieved tasks.
     *
     * @param  \Illuminate\Support\Collection  $tasks
     * @return void
     */
    protected function generateReport($tasks)
    {
        $reportData = [];

        foreach ($tasks as $task) {
            $reportData[] = [
                'task_id' => $task->id,
                'title' => $task->title,
                'due_date' => $task->due_date,
                'status' => $task->status,
            ];
        }

        Log::info('تقرير يومي للمهام غير المكتملة:', $reportData);
        Mail::to('admin@example.com')->send(new DailyReport($reportData));
    }
}
