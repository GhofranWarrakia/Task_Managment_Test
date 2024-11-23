<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class Role
 *
 * يمثل نموذج Role الأدوار المختلفة التي يمكن أن يحملها المستخدمون في النظام.
 *
 * @package App\Models
 */
class Role extends Model
{
    use HasFactory;

    /**
     * الخصائص القابلة للتعبئة.
     *
     * @var array
     */
    protected $fillable = ['name'];

    /**
     * تعريف العلاقة العديدة إلى العديدة مع نموذج User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * إضافة دور جديد إلى قاعدة البيانات.
     *
     * @param array $data
     * @return Role
     */
    public static function createRole(array $data)
    {
        return self::create($data);
    }

    /**
     * تحديث اسم الدور.
     *
     * @param string $name
     * @return bool
     */
    public function updateRoleName(string $name): bool
    {
        return $this->update(['name' => $name]);
    }

    /**
     * حذف الدور من قاعدة البيانات.
     *
     * @return bool|null
     */
    public function deleteRole()
    {
        return $this->delete();
    }
}
