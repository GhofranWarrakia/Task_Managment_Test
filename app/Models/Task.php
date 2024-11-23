<?php

namespace App\Models;

use App\Models\Attachment;
use App\Models\TaskStatusUpdate;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Egulias\EmailValidator\Warning\Comment;

/**
 * Class Task
 *
 * يمثل نموذج Task المهام في النظام، بما في ذلك تفاصيلها وحالتها والاعتماديات المتعلقة بها.
 *
 * @package App\Models
 */
class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * الخصائص القابلة للتعبئة.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'type', 'status', 'priority',
    'due_date', 'assigned_to',
    ];

    /**
     * علاقة المستخدم بالمهام.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * علاقة التعليقات بالمهام.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * علاقة المرفقات بالمهام.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * علاقة تحديثات الحالة بالمهام.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function statusUpdates()
    {
        return $this->hasMany(TaskStatusUpdate::class);
    }

    /**
     * علاقة الاعتماديات الخاصة بالمهام.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dependencies()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'task_id', 'depends_on_task_id');
    }

    /**
     * علاقة المهام المعتمدة على هذه المهمة.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dependentTasks()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'depends_on_task_id', 'task_id');
    }

    /**
     * تحقق مما إذا كانت المهمة محجوبة بسبب اعتماديات غير مكتملة.
     *
     * @return bool
     */
    public function isBlocked()
{
    return $this->dependencies()->where('status', '!=', 'Completed')->exists();
}

}
