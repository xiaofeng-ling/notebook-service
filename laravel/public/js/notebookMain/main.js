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
            '<img src="/images/add.png" />\n' +
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
                    var div = $("<div></div>").html("<span data-name='"+result[i].name+"' class='action modify'>M</span>" +
                        "<span class='text'>"+result[i].name+"</span>" +
                        "<span class='action delete'>X</span>");

                    div.attr('data-id', result[i].id);
                    div.addClass("notebookMain");

                    // 绑定点击事件
                    div.click(function (e) {
                        var self = $(this);
                        gotoNotebook(self.attr('data-id'), self.children('.notebookMain > .text').text());
                    });

                    // 鼠标移入事件
                    // 显示编辑与删除按钮
                    div.mousemove(function (e) {
                        $(this).children('.notebookMain > .delete').css("display", "block");
                        $(this).children('.notebookMain > .modify').css("display", "block");
                    });

                    // 鼠标移出事件css
                    // 隐藏编辑与删除按钮
                    div.mouseout(function (e) {
                        $(this).children('.notebookMain > .delete').css("display", "none");
                        $(this).children('.notebookMain > .modify').css("display", "none");
                    });

                    // 增加删除日记本事件
                    div.children('.notebookMain > .delete').click(function(e) {
                        var self = $(this);
                        var parent = self.parent();

                        if (confirm("确认您想要删除日记本《"+
                                self.prev().text()
                                +"》吗？")) {
                            removeNotebook(parent.attr('data-id'));
                        }

                        return false;
                    });

                    // 增加修改日记本标题事件
                    div.children('.notebookMain > .modify').click(function(e) {
                        var self = $(this);
                        var parent = self.parent();
                        var oldName = e.currentTarget.dataset.name;

                        var name = prompt("请输入新名称", oldName);

                        if (name !== null) {
                            modifyName(name, parent.attr('data-id'));
                        }

                        return false;
                    });

                    $(".main").append(div)
                }

                // 添加框
                addCreateDiv();
            }
        });
    };

    /**
     * 删除日记本
     * @param id
     */
    var removeNotebook = function(id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebookMain/'+id,
            type: "DELETE",

            dataType: 'json',

            success: function(result) {
                if (result.code === 0) {
                    loadNotebook();
                }
                else
                    alert("删除失败！");
            }
        });
    };

    /**
     * 修改日记本名
     * @param name
     * @param id
     */
    var modifyName = function(name, id) {
        if (name === undefined || name === '') {
            alert("名字不能为空！");
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebookMain/'+id,
            type: "PUT",

            data: {
                name: name
            },

            dataType: 'json',

            success: function(result) {
                if (result.code === 0) {
                    loadNotebook();
                }
                else
                    alert("修改名称失败！");
            }
        });
    };

    $(window).ready(function (e) {
            loadNotebook();
        }
    );

})();