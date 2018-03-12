<?php

namespace Tests\Feature;

use App\NotebookMain;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotebookMainMainApiTest extends TestCase
{
    /**
     * 设置环境
     */
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
        $this->markTestIncomplete();

        $response = $this->get('notebookMain');

        $response->assertStatus(200);
    }

    public function testStore()
    {
        $name = '测试数据'.random_int(5, 200);
        $response = $this->json('POST', '/notebookMain', [
            'name' => $name,
            'description' => '测试内容',
        ], $this->defaultHeaders);

        $response->assertJson(['code' => 0]);

        $this->assertDatabaseHas('Notebook_Main', [
            'name' => $name]);
    }

    /**
     * @depends testStore
     */
    public function testShow()
    {
        $id = (NotebookMain::first())->id;

        $response = $this->get('/notebookMain/'.$id);

        $response->assertStatus(200);
    }

    /**
     * @depends testStore
     */
    public function testUpdate()
    {
        $id = (NotebookMain::first())->id;

        $name = '测试数据'.random_int(500, 2000);
        $response = $this->putJson('/notebookMain/'.$id,
            ['name' => $name,
                'description' => '1234',
            ]
        );

        $response->assertJson(['code' => 0]);
        $this->assertDatabaseHas('Notebook_Main', [
            'id' => $id,
            'name' => $name,
        ]);
    }

    /**
     * @depends testStore
     */
    public function testDestory()
    {
        $id = (NotebookMain::first())->id;

        $response = $this->json('DELETE', '/notebookMain/'.$id);

        $response->assertJson(['code' => 0]);
    }

    /**
     * @depends testStore
     */
    public function testGetList()
    {
        $id = (NotebookMain::first())->NotebookMain_id;

        $response = $this->postJson('/notebookMain/getList', [
        ]);

        $response->assertStatus(200);
    }

}
