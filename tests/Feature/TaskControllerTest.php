<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Database\Factories\TaskFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user); // تسجيل الدخول كمستخدم
    }

    /** @test */
    public function it_can_store_a_new_task()
    {
        $data = [
            'title' => 'Test Task',
            'description' => 'This is a test task.',
            'type' => 'feature',
            'status' => 'pending',
            'priority' => 'high',
            'due_date' => now()->addDays(7)->toDateString(),
            'assigned_to' => 1,
        ];

        $response = $this->postJson(route('tasks.store'), $data);
        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Test Task']);
        $this->assertDatabaseHas('tasks', $data);
    }

    /** @test */
    public function it_can_update_task_status()
    {
        $task = TaskFactory::factory()->create(['status' => 'Open']);

        $data = ['status' => 'Completed'];

        $response = $this->putJson(route('tasks.updateStatus', $task->id), $data);

        $response->assertStatus(200)
                 ->assertJsonFragment(['status' => 'Completed']);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'Completed']);
    }

    /** @test */
    public function it_can_reassign_a_task()
    {
        $task = TaskFactory::factory()->create(['assigned_to' => $this->user->id]);
        $newUser = User::factory()->create();

        $data = ['assigned_to' => $newUser->id];

        $response = $this->putJson(route('tasks.reassign', $task->id), $data);

        $response->assertStatus(200)
                 ->assertJsonFragment(['assigned_to' => $newUser->id]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'assigned_to' => $newUser->id]);
    }

    /** @test */
    public function it_can_add_a_dependency()
    {
        $task = TaskFactory::factory()->create();
        $dependency = TaskFactory::factory()->create();

        $data = ['depends_on_task_id' => $dependency->id];

        $response = $this->postJson(route('tasks.addDependency', $task->id), $data);

        $response->assertStatus(200);
        $this->assertTrue($task->dependencies()->where('id', $dependency->id)->exists());
    }

    /** @test */
    public function it_can_generate_a_daily_report()
    {
        $task = TaskFactory::factory()->create([
            'due_date' => now()->toDateString(),
            'status' => 'Open',
        ]);

        $response = $this->getJson(route('tasks.dailyReport'));

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $task->id]);
    }

    /** @test */
    public function it_can_restore_a_soft_deleted_task()
    {
        $task = TaskFactory::factory()->create();
        $task->delete();

        $response = $this->postJson(route('tasks.restore', $task->id));

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $task->id]);

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'deleted_at' => null]);
    }

    /** @test */
    public function it_can_add_an_attachment()
    {
        $task = TaskFactory::factory()->create();

        Storage::fake('local');

        $file = UploadedFile::fake()->image('test.jpg');

        $response = $this->postJson(route('tasks.addAttachment', $task->id), [
            'attachment' => $file,
        ]);

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Attachment uploaded successfully']);

        Storage::disk('local')->assertExists("attachments/{$file->hashName()}");
    }
}
