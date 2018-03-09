(function () {
    var step = 100, is_end = false;

    var loadTitle = function (notebook_id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebook/getList',
            type: "POST",
            data: {
                notebook_id: notebook_id,
                start: 0,
                end: step
            },

            dataType: 'json',

            success: function (result) {
                for (var i = 0; i < result.length; i++) {
                    var li = $("<li></li>").text(result[i].title)
                    li.attr('data-id', result[i].id)

                    // 绑定点击事件
                    li.click(function (e) {
                        var self = $(this)
                        loadContent(self.attr('data-id'));
                    })

                    $(".list > ul").append(li)
                }
            }
        });
    }

    /**
     * 滚动加载
     * @param notebook_id
     * @param force  强制请求，忽略is_end
     * @returns {boolean}
     */
    var loadNext = function (notebook_id, force) {
        if (is_end && !force)
            return is_end;

        start = $(".list > ul").children().length;

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebook/getList',
            type: "POST",
            data: {
                notebook_id: notebook_id,
                start: start,
                end: start + step
            },

            dataType: 'json',

            success: function (result) {
                // 已经到底了
                if (result.length === 0)
                    is_end = true;
                for (var i = 0; i < result.length; i++) {
                    var li = $("<li></li>").text(result[i].title)
                    li.attr('data-id', result[i].id)

                    // 绑定点击事件
                    li.click(function (e) {
                        var self = $(this)
                        loadContent(self.attr('data-id'));
                    })

                    $(".list > ul").append(li)
                }
            }
        });
    }

    /**
     * 加载内容
     * @param id
     */
    var loadContent = function (id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebook/' + id,

            dataType: 'json',

            success: function (result) {
                $('.text').val(result.content)
                $('.select').removeClass('select');
                $("[data-id='" + id + "'").addClass('select');
            }
        });
    }

    var saveContent = function (notebook_id, id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebook/' + id,
            type: "PUT",
            data: {
                notebook_id: notebook_id,
                content: $(".text").val(),
                title: $("[data-id='" + id + "'").text(),
            },

            dataType: 'json',

            success: function (result) {
                if (result.code === 0) {
                    alert("保存成功!");
                }
            }
        });
    }

    var deletePage = function (id) {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebook/' + id,

            type: "DELETE",

            dataType: 'json',

            success: function (result) {
                if (result.code === 0) {
                    $("[data-id='" + id + "'").remove();
                    alert("删除成功");
                }
                else
                    alert(result.data)
            }
        });
    }

    var createPage = function (notebook_id, title, content) {
        if (title === undefined || title === '') {
            alert("请输入标题！");
            return false;
        }

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebook',
            type: "POST",
            data: {
                notebook_id: notebook_id,
                title: title
            },

            dataType: 'json',

            success: function (result) {
                if (result.code === 0) {
                    loadNext(notebook_id, true);
                }
            }
        });
    }

    var getSelectObject = function () {
        return $(".select");
    }

    $(window).ready(function (e) {
        loadTitle(26);

        $("#create").click(function (e) {
            createPage(26, "你好呀");
        });

        $("#delete").click(function (e) {
            deletePage(getSelectObject().attr('data-id'));
        })

        $("#save").click(function (e) {
            saveContent(26, getSelectObject().attr('data-id'));
        })
    });

})();