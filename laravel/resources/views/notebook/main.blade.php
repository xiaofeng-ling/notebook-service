
@extends('layouts.notebook')

@section("content")
    <div class="container main">

        <div class="frame edit">
                <textarea class="text" title="在这里输入日记"></textarea>
        </div>

        <div class="frame right">
            <div class="operation">
                <button id="save">保存</button>
                <button id="delete">删除</button>
                <button id="create">新增</button>
            </div>

            <div class="list">
                <ul>
                </ul>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/notebook/main.js') }}"></script>
@endsection