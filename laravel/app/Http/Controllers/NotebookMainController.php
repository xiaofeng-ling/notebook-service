<?php

namespace App\Http\Controllers;

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
        $this->middleware('auth');

        $this->request = $request;
    }

    public function index(Request $request)
    {
        return view('notebookMain.main');
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
            return App()->make('Result', [1, '保存失败！']);

        $ret = App()->make('Result', [0, '保存成功！']);

        return $ret;
    }

    /**
     * 获取日记本的详细内容
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $result = NotebookMain::find($id);

        return $result->getAttributes();
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
            'description' => 'required',
        ]);

        $notebook = NotebookMain::find($id);
        $notebook->name = $this->request->input('name');
        $notebook->description = $this->request->input('description');

        if (!$notebook->save())
            return App()->make('Result', [1, '更新失败！']);

        return App()->make('Result', [0, '更新成功！']);
    }

    /**
     * 删除一本日记
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $result = NotebookMain::find($id);

        return $result->delete() ?
            App()->make('Result', [0, '删除成功！']):
            App()->make('Result', [1, '删除失败！']);
    }

    /**
     * 获取用户所有的日记本
     */
    public function getList()
    {
        $results = NotebookMain::all();

        return $results;
    }
}
