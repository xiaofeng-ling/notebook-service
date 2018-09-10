<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Notebook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;

class NotebookController extends Controller
{
    private $request;

    /**
     * NotebookController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 获取一本日记中的页
     */
    public function index()
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($this->request->all(), [
            'notebook_id' => 'required|integer',
            'start' => 'required|integer',
            'end' => 'required|integer',
        ]);

        if ($validator->fails())
            return apiJson([], $validator->errors()->first(), 1001);

        $notebook_id = $this->request->post('notebook_id');
        $start = $this->request->post('start');
        $end = $this->request->post('end');

        $limit = $end-$start;

        $results = Notebook::where(
            ['notebook_id' => $notebook_id,
            ])->get()->slice($start, $limit);

        $data = [];
        foreach ($results as $result)
        {
            $tempData = $result->only(['id', 'title', 'updated_at']);
            $tempData['updated_at'] = strtotime($tempData['updated_at']);
            $data[] = $tempData;
        }

        return apiJson($data);
    }

    /**
     * 新建（保存）一页日记
     * @return mixed
     */
    public function store()
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($this->request->all(), [
            'title' => 'required',
            'notebook_id' => 'required|integer|exists:notebook_main,id'
        ], [
            'title.required' => '标题是必须的！',
            'notebook_id.required' => '日记本的id是必须的！',
            'notebook_id.exists' => 'id不存在',
        ]);

        if ($validator->fails())
            return apiJson([], $validator->errors()->first(), 1001);

        $title = $this->request->post('title');
        $content = $this->encrypt((string)$this->request->post('content'));
        $notebook_id = $this->request->post('notebook_id');

        return $this->save($title, $content, $notebook_id);
    }

    /**
     * 获取一页日记的详细内容
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $result = Notebook::find((int)$id);

        $data = $result->getAttributes();
        $data['content'] = $this->decrypt((string)$data['content']);

        return apiJson($data);
    }

    /**
     * 更新一页日记
     * @return mixed
     */
    public function update()
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($this->request->all(), [
            'id' => 'required',
            'title' => 'required',
            'content' => 'required',
            'updated_at' => 'required',
        ]);

        if ($validator->fails())
            return apiJson([], $validator->errors()->first(), 1001);

        $id = (int)$this->request->post('id');
        $notebook = Notebook::find($id);

        // 产生了冲突
        if (strtotime($notebook->updated_at) > (int)$this->request->input('updated_at'))
        {
            $title = $this->request->input('title')."_冲突".mt_rand(100, 999);
            $content = $this->encrypt((string)$this->request->input('content'));
            $notebook_id = $notebook->notebookMain->id;
            $this->save($title, $content, $notebook_id);

            return apiJson([], '有冲突，冲突文件已被命名为：'.$title, 1003);
        }
        else
        {
            $notebook->title = $this->request->input('title');
            $notebook->content = $this->encrypt((string)$this->request->input('content'));
        }

        if (!$notebook->save())
            return apiJson([], '更新失败！', 1002);

        return apiJson(['updated_at' => strtotime($notebook->updated_at)]);
    }

    /**
     * 修改标题
     * @param $id
     * @return mixed
     */
    public function modifyTitle()
    {
        /** @var \Illuminate\Validation\Validator $validator */
        $validator = Validator::make($this->request->all(), [
            'title' => 'required',
        ]);

        if ($validator->fails())
            return apiJson([], $validator->errors()->first(), 1001);

        $id = (int)$this->request->input('id');
        $notebook = Notebook::find($id);

        $notebook->title = $this->request->input('title');

        if (!$notebook->save())
            return apiJson([], '更新失败！', 1002);

        return apiJson([], '更新成功！');
    }

    /**
     * 删除一页日记
     * @param $id
     * @return mixed
     */
    public function destroy()
    {
        $id = (int)$this->request->input('id');

        $result = Notebook::find($id);

        return $result->delete() ?
            apiJson([], '删除成功！', 1000) :
            apiJson([], '删除失败！', 1002);
    }

    /**
     * 加密数据
     * 由于直接使用的laravel自带函数，所以请确保env中的APP_KEY不要更改
     * 否则会导致数据无法解密
     * @param string $data
     * @return string
     */
    private function encrypt($data)
    {
        return Crypt::encryptString($data);
    }

    /**
     * 解密数据
     * 由于直接使用的laravel自带函数，所以请确保env中的APP_KEY不要更改
     * 否则会导致数据无法解密
     * @param string $data
     * @return string
     */
    private function decrypt(string $data)
    {
        return Crypt::decryptString($data);
    }

    /**
     * 保存至数据库
     * @param string $title
     * @param string $content
     * @param int $notebook_id
     * @return mixed
     */
    private function save(string $title, string $content, int $notebook_id)
    {
        $notebook = new Notebook();
        $notebook->title = (string)$title;
        $notebook->content = (string)$content;
        $notebook->notebook_id = (int)$notebook_id;
        $notebook->user_id = $this->request->user()->getUserId();

        if (!$notebook->save())
            return apiJson([], '保存失败', 1002);

        return apiJson(['id' => $notebook->id], '保存成功');
    }
}
