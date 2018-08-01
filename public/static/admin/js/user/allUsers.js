layui.config({
    base: "js/"
}).use(['layer', 'jquery', 'table', 'laytpl'], function () {
    $ = layui.jquery;
    var table = layui.table;
    table.render({
        id: 'idTable',
        elem: '#table',
        skin: 'row',
        even: true,
        size: 'lg',
        cols: [[
            {checkbox: true, width: '5%'},
            {type: "numbers", title: '序号', width: '5%'},
            {field: 'id', title: 'ID', width: '5%', sort: true, align: 'center', unresize: true},
            {field: 'username', title: '用户名', width: '15%', sort: true, align: 'center', unresize: true},
            {field: 'nickname', title: '昵称', width: '15%', sort: true, align: 'center', unresize: true},
            {
                field: 'avatar',
                title: '头像',
                width: '10%',
                align: 'center',
                unresize: true,
                templet: '#avatarTpl'
            },
            {
                field: 'status',
                title: '状态',
                width: '7.5%',
                sort: true,
                align: 'center',
                unresize: true,
                templet: '#statusTpl'
            },
            {
                field: 'rightname',
                title: '角色',
                width: '10%',
                sort: true,
                align: 'center',
                unresize: true
            },
            {
                field: 'createtime',
                title: '创建时间',
                width: '15%',
                sort: true,
                align: 'center',
                unresize: true,
                templet: '#createTpl'
            },
            {
                field: 'updatetime',
                title: '更改时间',
                width: '15%',
                sort: true,
                align: 'center',
                unresize: true,
                templet: '#updateTpl'
            },
            {fixed: 'right', width: '12.5%', align: 'center', toolbar: '#toolbar', title: '操作', unresize: true}
        ]],
        url: '/admin/user/getUsers',
        where: {key: $('#key').val()},
        method: 'post',
        loading: true,
        page: true,
        limit: limit,
        limits: [Math.ceil(0.5 * limit), limit, Math.ceil(2 * limit), Math.ceil(5 * limit), Math.ceil(10 * limit), Math.ceil(20 * limit)],
        done: function (res, curr, count) {
            var imgsize = new imgAutoSize();
        }
    });
    table.on('sort(table)', function (obj) {
        var imgsize = new imgAutoSize();
    });
    table.on('tool(table)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值
        if (layEvent === 'del') { //删除
            layer.confirm('真的删除行么', function (index) {
                $.ajax({
                    url: '/admin/user/delete',
                    data: {id: data.id},
                    dataType: 'json',
                    type: 'post',
                    success: function (res) {
                        if (res.code == 1) {
                            layer.msg(res.msg, {icon: 1, time: 1000}, function (index) {
                                tablereload();
                            });
                        }
                    }
                })
            });
        } else if (layEvent === 'edit') { //编辑
            if (selfid == data.id) {
                location.href = "/admin/user/edit?self=1&id=" + data.id;
            } else {
                location.href = "/admin/user/edit?id=" + data.id;
            }

        }
    });
    table.on('checkbox(table)', function (obj) {
        if (obj.data.id == selfid || obj.type == 'all') {
            if (obj.checked) {
                $('#delete').addClass('layui-btn-disabled');
                $('#delete').css('pointer-events', 'none');
            } else {
                $('#delete').removeClass('layui-btn-disabled');
                $('#delete').css('pointer-events', 'auto');
            }
        }
    });
    // 批量操作
    $('#delete').on('click', function () {
        layer.confirm('真的删除么', function () {
            var checkStatus = table.checkStatus('idTable');
            if (checkStatus.data.length == 0) {
                return false;
            }
            var arrId = [];
            for (var i = 0; i < checkStatus.data.length; i++) {
                arrId.push(checkStatus.data[i].id);
            }
            $.ajax({
                url: '/admin/user/deleteAll',
                type: 'post',
                data: {arrId: arrId},
                dataType: 'json',
                success: function (data) {
                    layer.msg(data.msg, {icon: 1, time: 1000}, function (index) {
                        layer.close(index);
                        tablereload();
                    });
                }
            })
        })
    });
});

function user_state(id) {
    layer.confirm('确定修改状态吗', function () {
        $.ajax({
            url: '/admin/user/status',
            data: {
                id: id
            },
            type: 'post',
            dataType: 'json',
            success: function (data) {
                if (data.code == 1) {
                    var b = '<span class="layui-btn layui-btn-xs">开启</span>'
                    $('#zt' + id).html(b);
                    layer.msg(data.msg, {icon: 1, time: 1500, shade: 0.1,});
                    return false;
                } else {
                    var a = '<span class="layui-btn layui-btn-warm layui-btn-xs">禁用</span>'
                    $('#zt' + id).html(a);
                    layer.msg(data.msg, {icon: 2, time: 1500, shade: 0.1,});
                    return false;
                }
            }
        })

    })
}

function tablereload() {
    layui.table.reload('idTable', {
        url: '/admin/user/getUsers',
        where: {key: $('#key').val()}
    });
}
