<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Attachment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttachmentControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * إعداد المستخدم والمهام قبل كل اختبار.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // إنشاء مستخدم لتسجيل الدخول
        $this->user = User::factory()->create();

        // تسجيل الدخول للمستخدم
        $this->actingAs($this->user);

        // إنشاء مهمة
        $this->task = Task::factory()->create(['assigned_to' => $this->user->id]);
    }

    /**
     * اختبار استرجاع جميع المرفقات المرتبطة بمهمة.
     */
    public function test_can_list_attachments()
    {
        // إنشاء مرفقات للمهمة
        Attachment::factory()->count(3)->create(['task_id' => $this->task->id]);

        // إرسال طلب للحصول على المرفقات
        $response = $this->getJson(route('attachments.index', $this->task->id));

        // تحقق من نجاح العملية وإرجاع البيانات
        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /**
     * اختبار إضافة مرفق جديد لمهمة.
     */
    public function test_can_store_attachment()
    {
        Storage::fake('local');

        // إنشاء ملف وهمي
        $file = UploadedFile::fake()->create('example.pdf', 1024);

        // إرسال طلب لإضافة المرفق
        $response = $this->postJson(route('attachments.store', $this->task->id), [
            'file' => $file,
        ]);

        // تحقق من نجاح العملية
        $response->assertStatus(201);

        // تحقق من تخزين الملف
        Storage::assertExists('attachments/' . $file->hashName());

        // تحقق من وجود المرفق في قاعدة البيانات
        $this->assertDatabaseHas('attachments', [
            'file_name' => 'example.pdf',
            'task_id' => $this->task->id,
        ]);
    }

    /**
     * اختبار عرض تفاصيل مرفق محدد.
     */
    public function test_can_show_attachment()
    {
        // إنشاء مرفق
        $attachment = Attachment::factory()->create(['task_id' => $this->task->id]);

        // إرسال طلب لعرض تفاصيل المرفق
        $response = $this->getJson(route('attachments.show', [$this->task->id, $attachment->id]));

        // تحقق من نجاح العملية
        $response->assertStatus(200)
                 ->assertJson([
                     'id' => $attachment->id,
                     'file_name' => $attachment->file_name,
                 ]);
    }

    /**
     * اختبار حذف مرفق.
     */
    public function test_can_delete_attachment()
    {
        Storage::fake('local');

        // إنشاء مرفق
        $attachment = Attachment::factory()->create([
            'task_id' => $this->task->id,
            'file_path' => 'attachments/example.pdf',
        ]);

        // تخزين الملف الوهمي
        Storage::put($attachment->file_path, 'dummy content');

        // إرسال طلب لحذف المرفق
        $response = $this->deleteJson(route('attachments.destroy', [$this->task->id, $attachment->id]));

        // تحقق من نجاح العملية
        $response->assertStatus(200)
                 ->assertJson(['message' => 'تم حذف المرفق بنجاح.']);

        // تحقق من حذف الملف
        Storage::assertMissing($attachment->file_path);

        // تحقق من حذف السجل من قاعدة البيانات
        $this->assertDatabaseMissing('attachments', ['id' => $attachment->id]);
    }

    /**
     * اختبار تنزيل مرفق.
     */
    public function test_can_download_attachment()
    {
        Storage::fake('local');

        // إنشاء مرفق
        $attachment = Attachment::factory()->create([
            'task_id' => $this->task->id,
            'file_path' => 'attachments/example.pdf',
        ]);

        // تخزين الملف الوهمي
        Storage::put($attachment->file_path, 'dummy content');

        // إرسال طلب لتنزيل المرفق
        $response = $this->get(route('attachments.download', [$this->task->id, $attachment->id]));

        // تحقق من نجاح العملية وتنزيل الملف
        $response->assertStatus(200)
                 ->assertHeader('Content-Disposition', 'attachment; filename=' . $attachment->file_name);
    }
}
