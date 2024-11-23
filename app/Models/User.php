<?php

namespace App\Models;

use App\Models\Role;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 *
 * يمثل نموذج المستخدم الذي يمكنه تسجيل الدخول وتعيين المهام.
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * الخصائص القابلة للتعبئة.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',      // اسم المستخدم
        'email',     // البريد الإلكتروني
        'password',  // كلمة المرور
    ];

    /**
     * الخصائص التي يجب إخفاؤها عند التسلسل.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',      // إخفاء كلمة المرور
        'remember_token', // إخفاء رمز التذكر
    ];

    /**
     * الخصائص التي يجب تحويلها.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime', // تحويل تاريخ التحقق من البريد الإلكتروني إلى كائن DateTime
    ];

    /**
     * علاقة المستخدم بالمهام.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_to'); // المستخدم يمكن أن يكون له العديد من المهام المعينة له
    }

    /**
     * علاقة المستخدم بالأدوار.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class); // المستخدم يمكن أن يكون له العديد من الأدوار
    }
}
