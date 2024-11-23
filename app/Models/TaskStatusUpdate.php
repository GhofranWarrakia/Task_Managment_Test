<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class TaskStatusUpdate
 *
 * يمثل نموذج تحديث حالة المهمة، حيث يتم تتبع حالة المهمة في أوقات مختلفة.
 *
 * @package App\Models
 */
class TaskStatusUpdate extends Model
{
    use HasFactory;

    /**
     * الخصائص القابلة للتعبئة.
     *
     * @var array
     */
    protected $fillable = [
        'status',  // حالة المهمة
        'task_id'  // معرف المهمة المرتبط
    ];

    /**
     * علاقة تحديث الحالة بالمهمة.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
