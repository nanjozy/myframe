var box1 = new Ajaxer({
    main: function () {
        this.rander();
    },
    url: "/admin/user/getright1",
    model: '',
    id: edit_id,
    ajaxid: [1],
    id: edit_id,
    after: function () {
        layui.config({
            base: "js/"
        }).use(['form', 'layer', 'jquery'], function () {
            var index = null;
            var form = layui.form;
            var layer = parent.layer === undefined ? layui.layer : parent.layer;
            var $ = layui.jquery;
            $form = $('form');
            form.on('submit(add)', function (data) {
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

            function checkForm(formData, jqForm, options) {
                index = layer.load({shade: [0.3, '#000']});
            }

            function complete(res) {
                layer.close(index);
                if (res['code'] == 1) {
                    $ico = 1;
                    box1.rander();
                } else {
                    $ico = 5;
                }
                layer.alert(res['msg'], {icon: $ico});
            }

            form.render();
        });
    }
});


