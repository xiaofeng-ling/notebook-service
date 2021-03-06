<?php

namespace Tests\Feature;

use App\Notebook;
use App\NotebookMain;
use App\User;
use Illuminate\Foundation\Testing\Concerns\InteractsWithConsole;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class notebookApiTest extends TestCase
{
//    use DatabaseMigrations;
    public function setUp()
    {
        parent::setUp();

        $user = factory(User::class)->create();

        // 初始化登陆
        $this->actingAs($user);

        // 禁用中间件检测，特别是针对CSRF令牌
        $this->withoutMiddleware();
    }

    public function testIndex()
    {
        $response = $this->get('/notebook');

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $title = '测试数据'.random_int(5, 200);
        $notebookMain = NotebookMain::first();
        $response = $this->json('POST', '/notebook', [
            'title' => $title,
            'content' => '测试内容',
            'notebook_id' => $notebookMain->id,
        ], $this->defaultHeaders);

        $response->assertJson(['code' => 0]);

        $this->assertDatabaseHas('notebook_data', [
            'title' => $title]);
    }

    /**
     * @depends testStore
     */
    public function testShow()
    {
        $id = (Notebook::first())->id;

        $response = $this->get('/notebook/'.$id);

        $response->assertStatus(200);
    }

    /**
     * @depends testStore
     */
    public function testUpdate()
    {
        $this->markTestIncomplete();

        $notebook = Notebook::first();

        $id = $notebook->id;

        $title = '测试数据'.random_int(500, 2000);
        $response = $this->putJson('/notebook/' . $id,
            [
                'title' => $title,
                'content' => '1234',
                'updated_at' => strtotime($notebook->updated_at),
            ]
        );

        $response->assertJson(['code' => 0]);
        $this->assertDatabaseHas('notebook_data', [
            'id' => $id,
            'title' => $title,
        ]);

        $response = $this->putJson('/notebook/' . 72,
            [
                'title' => $title,
                'content' => '1234',
                'updated_at' => strtotime($notebook->updated_at),
            ]
        );

        $response->assertJson(['code' => 2]);
    }

    /**
     * @depends testStore
     */
    public function testModifyTitle()
    {
        $notebook = Notebook::first();

        $id = $notebook->id;
        $title = $notebook->title.'修改后';

        $response = $this->json('POST', '/notebook/modifyTitle/'.$id, [
            'title' => $title,
            ]);

        $response->assertJson(['code' => 0]);
        $this->assertDatabaseHas("notebook_data", ['id' => $notebook->id, 'title' => $title]);
    }

    /**
     * @depends testStore
     */
    public function testDestory()
    {
        $this->markTestIncomplete();

        $id = (Notebook::first())->id;

        $response = $this->json('DELETE', '/notebook/'.$id);

        $response->assertJson(['code' => 0]);
    }

    /**
     * @depends testStore
     */
    public function testGetList()
    {
        $id = (NotebookMain::first())->id;

        $response = $this->postJson('/notebook/getList', [
            'notebook_id' => $id,
            'start' => 0,
            'end' => 10,
        ]);

        $response->assertStatus(200);
    }
}
