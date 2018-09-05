<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\NotebookMain;
use Illuminate\Http\Request;

class NotebookMainController extends Controller
{
    private $request;

    /**
     * NotebookMainController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->middleware('jwtauth');

        $this->request = $request;
    }

    /**
     * 获取用户所有的日记本
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $results = NotebookMain::all();

        return apiJson($results);
    }

    /**
     * 新建一本日记
     * @return mixed
     */
    public function store()
    {
        $this->validate($this->request, [
            'name' => 'required',
        ],
            ['name.required' => '名字是必须的！',]
        );

        $notebook = new NotebookMain();
        $notebook->name = $this->request->post('name');
        $notebook->description = $this->request->post('description');
        $notebook->user_id = $this->request->user()->getUserId();

        if (!$notebook->save())
            return apiJson([], '保存失败', 1002);

        return apiJson(['id' => $notebook->id], '保存成功');
    }

    /**
     * 获取日记本的详细内容
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $result = NotebookMain::find($id);

        return apiJson($result);
    }

    /**
     * 更新一本日记的描述
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $this->validate($this->request, [
            'name' => 'required',
        ]);

        $notebook = NotebookMain::find($id);
        $notebook->name = $this->request->input('name');
        $notebook->description = $this->request->input('description');

        if (!$notebook->save())
            return apiJson([], '更新失败', 1002);

        return apiJson(['id' => $notebook->id], '更新成功');
    }

    /**
     * 删除一本日记
     * @return mixed
     */
    public function destroy()
    {
        $id = (int)$this->request->post('id');
        $result = NotebookMain::find($id);

        return $result->delete() ?
            apiJson([], '删除成功！', 1000) :
            apiJson([], '删除失败！', 1002);
    }
}
