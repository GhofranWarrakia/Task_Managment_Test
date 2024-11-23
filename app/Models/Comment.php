<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Comment
 *
 * يمثل نموذج Comment التعليقات المرتبطة بالكيانات الأخرى (مثل المهام أو المقالات).
 *
 * @package App\Models
 */
class Comment extends Model
{
    use HasFactory;

    /**
     * الخصائص القابلة للتعبئة.
     *
     * @var array
     */
    protected $fillable = ['comment', 'user_id'];

    /**
     * تعريف العلاقة المورفية مع الكيانات القابلة للتعليق.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function commentable()
    {
        return $this->morphTo();
    }

    /**
     * استرجاع المستخدم الذي كتب التعليق.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * استرجاع التعليقات الفرعية المرتبطة بهذا التعليق.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id');
    }
}
