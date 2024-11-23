<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
    }

    /** @test */
    public function it_can_list_all_comments_for_a_task()
    {
        $task = Task::factory()->create();
        Comment::factory(3)->create(['task_id' => $task->id]);

        $response = $this->getJson(route('comments.index', $task->id));

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    /** @test */
    public function it_can_add_a_comment_to_a_task()
    {
        $task = Task::factory()->create();
        $data = ['content' => 'Test comment'];

        $response = $this->postJson(route('comments.store', $task->id), $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['content' => 'Test comment']);

        $this->assertDatabaseHas('comments', [
            'content' => 'Test comment',
            'task_id' => $task->id,
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function it_can_show_a_specific_comment()
    {
        $task = Task::factory()->create();
        $comment = Comment::factory()->create(['task_id' => $task->id]);

        $response = $this->getJson(route('comments.show', [$task->id, $comment->id]));

        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $comment->id]);
    }

    /** @test */
    public function it_can_update_a_comment()
    {
        $task = Task::factory()->create();
        $comment = Comment::factory()->create(['task_id' => $task->id, 'content' => 'Old content']);
        $data = ['content' => 'Updated content'];

        $response = $this->putJson(route('comments.update', [$task->id, $comment->id]), $data);

        $response->assertStatus(200)
                 ->assertJsonFragment(['content' => 'Updated content']);

        $this->assertDatabaseHas('comments', ['content' => 'Updated content']);
    }

    /** @test */
    public function it_can_delete_a_comment()
    {
        $task = Task::factory()->create();
        $comment = Comment::factory()->create(['task_id' => $task->id]);

        $response = $this->deleteJson(route('comments.destroy', [$task->id, $comment->id]));

        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'تم حذف التعليق بنجاح.']);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    /** @test */
    public function it_cannot_access_comments_of_another_task()
    {
        $task1 = Task::factory()->create();
        $task2 = Task::factory()->create();
        $comment = Comment::factory()->create(['task_id' => $task2->id]);

        $response = $this->getJson(route('comments.show', [$task1->id, $comment->id]));

        $response->assertStatus(404)
                 ->assertJsonFragment(['error' => 'التعليق غير مرتبط بهذه المهمة.']);
    }
}
