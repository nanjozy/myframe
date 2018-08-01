var alist = new Ajaxer({
    main: function () {
        this.rander();
    },
    url: "/admin/user/getright2",
    id: false,
    model: '',
    after: function () {
        layui.config({
            base: "js/"
        }).use(['form', 'layer', 'jquery'], function () {
            var form = layui.form;
            var layer = parent.layer === undefined ? layui.layer : parent.layer;
            var $ = layui.jquery;
            $form = $('form');
            form.on('submit(addUser)', function (data) {
                $('#form').ajaxSubmit({
                    type: 'post',
                    beforeSubmit: checkForm, // 此方法主要是提交前执行的方法，根据需要设置
                    success: complete, // 这是提交后的方法
                    dataType: 'json',
                    error: function () {
                        layer.close(index);
                        layer.alert('error');
                    }
                });
                return false;
            });
            form.verify({
                username: function (value, item) {
                    if (value.length < 4) {
                        return "用户名能小于4位";
                    }
                },
                password: function (value, item) {
                    if (value.length < 4) {
                        return "密码长度不能小于4位";
                    }
                },
                password2: function (value, item) {
                    if (!new RegExp($("#password").val()).test(value)) {
                        return "两次输入密码不一致，请重新输入！";
                    }
                }
            });

            function checkForm(formData, jqForm, options) {
                index = layer.load({shade: [0.3, '#000']});
            }

            function complete(res) {
                layer.close(index);
                if (res['code'] == 1) {
                    $ico = 1;
                    $("[type = 'reset']").trigger("click");
                } else {
                    $ico = 5;
                }
                layer.alert(res['msg'], {icon: $ico});
            }

            form.render();
        });
        var index = null;
        $(function () {
            var uploader = new Uploader();
            uploader.bind();
            var imgsize = new imgAutoSize();
        })
    }
});

