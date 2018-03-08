<?php

namespace App\Http\Controllers;

use App\Notebook;
use Illuminate\Http\Request;

class NotebookController extends Controller
{
    private $request;

    /**
     * NotebookController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');

        $this->request = $request;
    }

    /**
     * 主页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public function index()
    {
        return view('notebook.main');
    }

    /**
     * 新建（保存）一页日记
     * @return mixed
     */
    public function store()
    {
        $this->validate($this->request, [
            'title' => 'required|unique:notebook_data',
            'content' => 'required',
            'notebook_id' => 'required|integer'
        ]);

        $notebook = new Notebook();
        $notebook->title = $this->request->post('title');
        $notebook->content = $this->request->post('content');
        $notebook->notebook_id = $this->request->post('notebook_id');
        $notebook->user_id = $this->request->user()->getUserId();

        if (!$notebook->save())
            return App()->make('Result', [1, '保存失败！']);

        return App()->make('Result', [0, '保存成功！']);
    }

    /**
     * 获取一页日记的详细内容
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $result = Notebook::find($id);

        return $result->getAttributes();
    }

    /**
     * 更新一页日记
     * @param $id
     * @return mixed
     */
    public function update($id)
    {
        $this->validate($this->request, [
            'title' => 'required',
            'content' => 'required',
        ]);

        $notebook = Notebook::find($id);
        $notebook->title = $this->request->input('title');
        $notebook->content = $this->request->input('content');

        if (!$notebook->save())
            return App()->make('Result', [1, '更新失败！']);

        return App()->make('Result', [0, '更新成功！']);
    }

    /**
     * 删除一页日记
     * @param $id
     * @return mixed
     */
    public function destroy($id)
    {
        $result = Notebook::find($id);

        return $result->delete() ?
            App()->make('Result', [0, '删除成功！']):
            App()->make('Result', [1, '删除失败！']);
    }

    /**
     * 获取一本日记中的页
     */
    public function getList()
    {
        $this->validate($this->request, [
            'notebook_id' => 'required|integer',
            'start' => 'required|integer',
            'end' => 'required|integer',
        ]);

        $notebook_id = $this->request->post('notebook_id');
        $start = $this->request->post('start');
        $end = $this->request->post('end');

        $results = Notebook::where(
            ['notebook_id' => $notebook_id,
            ])->get()->slice($start, $end);

        $data = [];
        foreach ($results as $result)
        {
            $data[] = $result->only(['id', 'title']);
        }

        return $data;
    }
}
