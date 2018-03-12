<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NotebookMain extends Model
{
    // 软删除trait
    use SoftDeletes;

    /**
     * 数据表名
     *
     * @var string
     */
    protected $table = 'notebook_main';

    protected $dates = ['deleted_at'];
}
