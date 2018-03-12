<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NotebookMainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notebook_main')->insert(
            [
                'name' => '测试数据'.rand(10, 200),
                'description' => '测试描述'.rand(10, 200),
                'user_id' => \App\User::first()->id,
            ]
        );
    }
}
