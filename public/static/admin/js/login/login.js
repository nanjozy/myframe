layui.config({
    base: url_jsbase
}).use(['form', 'layer'], function () {
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : parent.layer,
        $ = layui.jquery;
    //登录按钮事件
    form.on("submit(login)", function (data) {
        $.ajax({
            url: url_ajax,
            data: {
                username: $.trim($('#username').val()),
                password: $.trim($('#password').val()),
                code: $.trim($('#code').val())
            },
            dataType: 'json',
            type: 'post',
            success: function (response) {
                if (response.code == -1) {
                    layer.msg(response.msg, {icon: 2, time: 500});
                } else if (response.code == 1) {
                    layer.msg(response.msg, {icon: 1, time: 500}, function () {
                        window.location.href = url_ind;
                    });
                } else if (response.code == -2) {
                    layer.msg(response.msg, {icon: 2, time: 500});
                }
            },
            error: function (error) {
                layer.msg('ERROR请重试', {icon: 2, time: 500});
            }
        })
        return false;
    })
});
