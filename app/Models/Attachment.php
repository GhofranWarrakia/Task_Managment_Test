<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Attachment
 *
 * يمثل نموذج Attachment المرفقات المرتبطة بالكيانات الأخرى (مثل المهام).
 *
 * @package App\Models
 */
class Attachment extends Model
{
    use HasFactory;

    /**
     * الخصائص القابلة للتعبئة.
     *
     * @var array
     */
    protected $fillable = ['file_path', 'attachable_id', 'attachable_type'];

    /**
     * تعريف العلاقة المورفية مع الكيانات القابلة للإرفاق.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function attachable()
    {
        return $this->morphTo();
    }

    /**
     * استرجاع اسم الملف الأصلي.
     *
     * @return string
     */
    public function getOriginalFileName()
    {
        return pathinfo($this->file_path, PATHINFO_BASENAME);
    }

    /**
     * تحقق مما إذا كان المرفق صورة.
     *
     * @return bool
     */
    public function isImage()
    {
        $imageMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $mimeType = mime_content_type($this->file_path);

        return in_array($mimeType, $imageMimeTypes);
    }
}
