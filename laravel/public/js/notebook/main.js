$(window).ready(function(e) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: '/notebook/getList',
        type: "POST",
        data: {
            notebook_id: 26,
            start: 0,
            end: 10
        },

        dataType: 'json',

        success: function(result) {
            for (var i=0; i<result.length; i++) {
                var li = $("<li></li>").text(result[i].title)
                li.attr('data-id', result[i].id)

                // 绑定点击事件
                li.click(function(e) {
                    var self = $(this)
                    console.log(this)
                    alert(self.text())
                })

                $(".list > ul").append(li)
            }
        }
    });
});