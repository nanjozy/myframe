layui.config({
    base: url_jsbase
}).use(['form', 'layer', 'jquery'], function () {
    var index = null;
    var form = layui.form;
    var layer = parent.layer === undefined ? layui.layer : parent.layer;
    var $ = layui.jquery;
    form.on("submit(systemParameter)", function (data) {
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
    })

    function checkForm(formData, jqForm, options) {
        index = layer.load({shade: [0.3, '#000']});
    }

    function complete(res) {
        layer.close(index);
        if (res['code'] == 1) {
            $ico = 1;
        } else {
            $ico = 5;
        }
        layer.alert(res['msg'], {icon: $ico});
    }

    form.on('switch(safemode)', function (data) {
        safemode(data);
    });

    function safemode(datas) {
        var dom = datas.elem;
        layer.confirm('确定修改模式吗', function () {
            $.ajax({
                url: '/admin/main/safemode',
                data: {
                    data: datas.elem.checked
                },
                type: 'post',
                dataType: 'json',
                success: function (data) {
                    if (data.code == -1) {
                        datas.elem.checked = false;
                        form.render();
                        layer.msg(data.msg, {icon: 2, time: 1500, shade: 0.1,});
                        return false;
                    } else if (data.code == 1) {
                        datas.elem.checked = true;
                        form.render();
                        layer.msg(data.msg, {icon: 1, time: 1500, shade: 0.1,});
                        return false;
                    } else {
                        if (data.status == 1) {
                            datas.elem.checked = true;
                            form.render();
                        } else {
                            datas.elem.checked = false;
                            form.render();
                        }
                        layer.msg(data.msg, {icon: 2, time: 1500, shade: 0.1,});
                        return false;
                    }
                }
            })

        })
    }
});
