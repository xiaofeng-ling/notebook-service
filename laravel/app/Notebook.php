<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notebook extends Model
{
    // 软删除trait
    use SoftDeletes;

    /**
     * 数据表名
     *
     * @var string
     */
    protected $table = 'notebook_data';

    protected $dates = ['deleted_at'];
}
