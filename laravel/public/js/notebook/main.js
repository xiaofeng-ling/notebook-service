(function () {
    var step = 100,                     // 每次加载的数据量
        is_end = false,
        notebook_id = sessionStorage.notebook_id,
        loadNextAjaxLock = false,       // 滚动加载的ajax锁，防止滚动的时候出现多个请求
        last_notice_timeout_id = 0;             // 上一次通知的超时id，用于清除

    /**
     * 获取格式化后的当前日期
     * @returns {string}
     */
    var getFormatDate = function() {
        var date = new Date();

        var month = (date.getMonth()+1).toString().padStart(2, 0);
        var day = date.getDate().toString().padStart(2, 0);

        return date.getFullYear()+'年'
            +month+'月'
            +day+'日';
    };

    /**
     * 输出错误
     * @param result
     */
    var echoError = function (result) {
        if (result.status === 422) { // 数据验证失败
            var errors = result.responseJSON.errors;

            for (var error in errors) {
                alert(errors[error]);      // todo 先临时这么写，具体的错误提示等到之后再弄
            }
        }
    };

    /**
     * 加载标题
     * @param notebook_id
     */
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
                // 事先清空所有标题，防止因为新增出现的重复数据
                $(".list > ul").text("");
                // 同时重置结尾标识符
                is_end = false;

                for (var i = 0; i < result.length; i++) {
                    var li = $("<li></li>").text(result[i].title);
                    li.attr({
                        'data-id': result[i].id,
                        'updated-at': result[i].updated_at
                    });

                    $(".list > ul").append(li)
                }
            }
        });
    };

    /**
     * 滚动加载
     * @param notebook_id
     * @param force  强制请求，忽略is_end
     * @param callback 回调函数，用于在加载完成后执行
     * @returns {boolean}
     */
    var loadNext = function (notebook_id, force, callback) {
        if (is_end && !force)
            return is_end;

        if (loadNextAjaxLock)
            return false;

        loadNextAjaxLock = true;

        var start = $(".list > ul").children().length;

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
                    var li = $("<li></li>").text(result[i].title);
                    li.attr({
                        'data-id': result[i].id,
                        'updated-at': result[i].updated_at
                    });

                    // 绑定点击事件
                    li.click(function (e) {
                        var self = $(this);
                        loadContent(self.attr('data-id'));
                    });

                    $(".list > ul").append(li)
                }

                if (callback) callback();

                loadNextAjaxLock = false;
            }
        });
    };

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
                $('.text').val(result.content);
                $('.select').removeClass('select');
                $("[data-id='" + id + "'").addClass('select');
            }
        });
    };

    /**
     * 保存内容，会涉及到冲突的处理
     * @param notebook_id
     * @param id
     * @param updated_at
     */
    var saveContent = function (notebook_id, id, updated_at) {
        if (id === undefined)
            return false;

        notice("保存中……", 30000);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            timeout: 5000,
            url: '/notebook/' + id,
            type: "PUT",
            data: {
                notebook_id: notebook_id,
                content: $(".text").val(),
                title: $("[data-id='" + id + "'").text(),
                updated_at: updated_at
            },

            dataType: 'json',

            success: function (result) {
                if (result.code === 0) {
                    getSelectObject().attr('updated-at', result.data.updated_at);
                    notice("保存成功!");
                }
                else if (result.code === 2) {
                    alert("文件已被修改，有冲突");
                    // loadNext(notebook_id, true);
                    loadTitle(notebook_id);
                }
            },

            error: echoError
        });
    };

    /**
     * 软删除一页日记
     * @param id
     */
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
                    notice("删除成功");
                }
                else
                    alert(result.data)
            }
        });
    };

    /**
     * 新建一页日记
     * @param notebook_id
     * @param title
     * @param content
     * @returns {boolean}
     */
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
                    // loadNext(notebook_id, true, function() {
                    //     // 滚动条到最底部，方便寻找到新建的日记
                    //     $(".list").scrollTop(999999999);
                    // });
                    // 清空所有标题，重新加载，以便最新的直接出现在顶部
                    loadTitle(notebook_id);
                }
            },

            error: echoError
        });
    };

    /**
     * 左上角的通知
     * @param text 要通知的文本
     * @param time 持续时间，默认两秒
     * @returns {boolean}
     */
    var notice = function(text, time) {
        time = time || 2000;

        if (text === undefined || text === '')
            return false;

        if (last_notice_timeout_id > 0) {
            clearTimeout(last_notice_timeout_id);
            last_notice_timeout_id = 0;
        }

        $(".time").addClass("notice");
        $(".notice").removeClass("time");
        $(".notice").text(text);

        last_notice_timeout_id = setTimeout(function() {
            $(".notice").addClass("time");
            $(".time").removeClass("notice");
            $(".time").text((new Date()).toLocaleString());
        }, time);
    };

    /**
     * 获取被选择的对象
     * @returns {*|jQuery|HTMLElement}
     */
    var getSelectObject = function () {
        return $(".select");
    };

    /**
     * 修改标题
     * @param title
     * @returns {boolean}
     */
    var modifyTitle = function(title) {
        if (title === undefined || title === '') {
            alert("请输入标题！");
            return false;
        }

        id = getSelectObject().attr('data-id');

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: '/notebook/modifyTitle/'+ id,
            type: "POST",
            data: {
                title: title
            },

            dataType: 'json',

            success: function (result) {
                if (result.code === 0) {
                    getSelectObject().text(title);
                }
            },

            error: echoError
        });
    };

    /**
     * 全局初始化
     */
    $(window).ready(function (e) {
        if (notebook_id === 0 || notebook_id === undefined)
            window.location.href = "/notebookMain";

        if (!$('#is_search_page').val())
            loadTitle(notebook_id);

        $("#create").click(function (e) {
            var title = prompt("请输入标题", getFormatDate());

            if (title !== null) {
                createPage(notebook_id, title);
            }
        });

        $("#delete").click(function (e) {
            deletePage(getSelectObject().attr('data-id'));
        });

        $("#save").click(function (e) {
            saveContent(notebook_id, getSelectObject().attr('data-id'), getSelectObject().attr('updated-at'));
        });

        $("#modify").click(function (e) {
            if (getSelectObject().attr('data-id') === undefined)
                return false;

            var title = prompt("请输入新标题", getSelectObject().text());

            if (title !== null) {
                modifyTitle(title);
            }
        });

        // 滚动加载事件
        $(".list").on("scroll", function(e) {
            var scrollTop = $(this).scrollTop();                        // 滚动条高度
            var height = $(this).height();                              // 元素高度
            var scrollHeight = $(this)[0].scrollHeight;                 // 可滚动的总高度

            var scrollOffsetButtom = scrollHeight - (scrollTop+height); // 滚动条距离底部的距离

            // 滚动加载！
            if (scrollOffsetButtom < 200)
                loadNext(notebook_id);
        });

        // 搜索label事件
        $('#search_label').on('click', function() {
            $(this).hide();
            var input = $(this).next();
            input.show();
        });

        // 搜索事件
        $('#search').keydown(function(e) {
            e = e || window.event;

            // Entry
            if (e.keyCode === 0x0D) {
                var value = $(this).val();
                if (value.length > 0) {
                    var url = '/notebook/search/' + notebook_id + '?keywords=' + value;
                    if ($('#is_search_page').val())
                        window.location.href = url;
                    else
                        window.open(url);
                }
            }
        });

        // 日记本点击事件
        $('.list > ul').on('click', 'li', function() {
            loadContent($(this).attr('data-id'));
            return false;
        });
    });

    $(document).keydown(function(e) {
            e = e || window.event;

            // ctrl+s
            if (e.keyCode === 83 && e.ctrlKey === true) {
                saveContent(notebook_id, getSelectObject().attr('data-id'), getSelectObject().attr('updated-at'));

                // 阻止事件继续传播
                return false;
            }
        });

    // 定时保存, 每一分钟保存一次
    setInterval(function() {
        saveContent(notebook_id, getSelectObject().attr('data-id'), getSelectObject().attr('updated-at'));
    }, 60000);

    // 退出时保存
    window.onbeforeunload = function(e) {
        saveContent(notebook_id, getSelectObject().attr('data-id'), getSelectObject().attr('updated-at'));
    };

})();