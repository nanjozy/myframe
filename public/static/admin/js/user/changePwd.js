layui.config({
    base: url_jsbase
}).use(['form', 'layer', 'jquery'], function () {
    var form = layui.form;
    var layer = parent.layer === undefined ? layui.layer : parent.layer;
    var $ = layui.jquery;

    //添加验证规则
    form.verify({
        oldPwd: function (value, item) {
            if (!value) {
                return "请输入原密码！";
            }
        },
        newPwd: function (value, item) {
            if (value.length < 4) {
                return "密码长度不能小于4位";
            }
        },
        confirmPwd: function (value, item) {
            if (!new RegExp($("#oldPwd").val()).test(value)) {
                return "两次输入密码不一致，请重新输入！";
            }
        }
    })

    var index = null;
    $(function () {
        $('#form').ajaxForm({
            type: 'post',
            beforeSubmit: checkForm, // 此方法主要是提交前执行的方法，根据需要设置
            success: complete, // 这是提交后的方法
            dataType: 'json',
            error: function () {
                layer.close(index);
                layer.alert('error');
            }
        });
    })

    function checkForm(formData, jqForm, options) {
        index = layer.load({shade: [0.3, '#000']});
    }

    function complete(res) {
        layer.close(index);
        if (res['code'] == 1) {
            $ico = 1;
            document.getElementById("form").reset();
        } else {
            $ico = 5;
        }
        layer.alert(res['msg'], {icon: $ico});
    }
})