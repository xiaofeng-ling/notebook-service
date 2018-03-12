(function () {
    /**
     * 前往日记本的编辑界面
     * @param notebook_id
     * @param notebook_name
     */
    var gotoNotebook = function(notebook_id, notebook_name) {
        sessionStorage.notebook_id = notebook_id;
        sessionStorage.notebook_name = notebook_name;
        window.location.href = '/notebook';
    };

    /**
     * 增加添加按钮
     */
    var addCreateDiv = function() {
        $(".main").append('<div id="create" class="notebookMain">\n' +
            '<img src="http://cn.cgright.com/icon/898_thum.jpg" />\n' +
            '</div>');

        $("#create").click(function(e) {
            // 弹出输入框
            var name = prompt("请输入日记本的名称");

            if (name !== null) {
                createNotebook(name)
            }
        });
    };

    var createNotebook = function (name) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebookMain',
            type: "POST",
            data: {
                name: name
            },

            dataType: 'json',

            success: function (result) {
                if (result.code === 0) {
                    alert("创建成功!");
                    loadNotebook();
                }
            }
        });
    };

    /**
     * 加载所有的日记本
     */
    var loadNotebook = function() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebookMain/getList',
            type: "POST",

            dataType: 'json',

            success: function (result) {
                $(".main").empty();  // 清空内容

                for (var i = 0; i < result.length; i++) {
                    var div = $("<div></div>").html("<span class='text'>"+result[i].name+"</span>");
                    div.attr('data-id', result[i].id);
                    div.addClass("notebookMain");

                    // 绑定点击事件
                    div.click(function (e) {
                        var self = $(this);
                        gotoNotebook(self.attr('data-id'), self.text());
                    });

                    $(".main").append(div)
                }

                // 添加框
                addCreateDiv();
            }
        });
    };

    $(window).ready(function (e) {
            loadNotebook();
        }
    );

})();