<?php

namespace Tests\Feature;

use App\Task;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{

    //use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * @test
     */
    public function it_will_show_all_tasks()
    {
        $task = factory(Task::class, 10)->create();
        $response = $this->get(route('tasks.index'));
        $response->assertStatus(200);
        $response->assertJson($task->toArray());
    }

    public function it_will_create_tasks()
    {
        $response = $this->post(route('tasks.store'),
            [
                'title' => 'this is title',
                'description' => 'this is a description'
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('tasks', [
            'title' => 'this is title',
            'description' => 'this is a description'
        ]);

        $response->assertJsonStructure([
            'message',
            'task' => [
                'title',
                'description',
                'updated_at'.
                'created_at',
                'id'
            ]
        ]);
    }

    /** @test */
    public function it_will_show_a_task()
    {
        $this->post(route('tasks.store'), [
            'title'       => 'This is a title',
            'description' => 'This is a description'
        ]);

        $task = Task::all()->first();

        $response = $this->get(route('tasks.show', $task->id));

        $response->assertStatus(200);

        $response->assertJson($task->toArray());
    }

    /** @test */
    public function it_will_update_a_task()
    {
        $this->post(route('tasks.store'), [
            'title'       => 'This is a title',
            'description' => 'This is a description'
        ]);

        $task = Task::all()->first();

        $response = $this->put(route('tasks.update', $task->id), [
            'title' => 'This is the updated title'
        ]);

        $response->assertStatus(302);

        $task = $task->fresh();

        $this->assertEquals($task->title, 'This is the updated title');

        $response->assertJsonStructure([
            'message',
            'task' => [
                'title',
                'description',
                'updated_at',
                'created_at',
                'id'
            ]
        ]);
    }

    /** @test */
    public function it_will_delete_a_task()
    {
        $this->post(route('tasks.store'), [
            'title'       => 'This is a title',
            'description' => 'This is a description'
        ]);

        $task = Task::all()->first();

        $response = $this->delete(route('tasks.destroy', $task->id));

        $task = $task->fresh();

        $this->assertNull($task);

        $response->assertJsonStructure([
            'message'
        ]);
    }
}
