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
            {field: 'id', title: 'ID', width: '5%', sort: true, align: 'center', unresize: true},
            {field: 'name', title: '角色名', width: '15%', sort: true, align: 'center', unresize: true},
            {
                field: 'status',
                title: '状态',
                width: '7.5%',
                sort: true,
                align: 'center',
                unresize: true,
                templet: '#statusTpl'
            },
            {field: 'systeminfo', title: '系统信息权限', width: '10%', sort: true, align: 'center', unresize: true},
            {field: 'usercontroller', title: '用户权限', width: '10%', sort: true, align: 'center', unresize: true},
            {field: 'msgcontroller', title: '信息权限', width: '10%', sort: true, align: 'center', unresize: true},
            {field: 'raidercontroller', title: '攻略权限', width: '10%', sort: true, align: 'center', unresize: true},
            {field: 'bannercontroller', title: '轮播图权限', width: '10%', sort: true, align: 'center', unresize: true},
            {field: 'contentcontroller', title: '联系方式权限', width: '10%', sort: true, align: 'center', unresize: true},
            {field: 'casecontroller', title: '案例权限', width: '10%', sort: true, align: 'center', unresize: true},
            {field: 'classcontroller', title: '课堂权限', width: '10%', sort: true, align: 'center', unresize: true},
            {field: 'teamcontroller', title: '团队权限', width: '10%', sort: true, align: 'center', unresize: true},
            {field: 'abtcontroller', title: '关于我们权限', width: '10%', sort: true, align: 'center', unresize: true},
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
            {fixed: 'right', width: '15%', align: 'center', toolbar: '#toolbar', title: '操作', unresize: true}
        ]],
        url: '/admin/user/getright',
        where: {key: $('#key').val()},
        method: 'post',
        loading: true,
        page: true,
        limit: limit,
        limits: [Math.ceil(0.5 * limit), limit, Math.ceil(2 * limit)],
        done: function (res, curr, count) {
        }
    });
    table.on('tool(table)', function (obj) { //注：tool是工具条事件名，test是table原始容器的属性 lay-filter="对应的值"
        var data = obj.data; //获得当前行数据
        var layEvent = obj.event; //获得 lay-event 对应的值
        if (layEvent === 'del') { //删除
            layer.confirm('真的删除行么', function (index) {
                $.ajax({
                    url: '/admin/user/deleteright',
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
                location.href = "/admin/user/editright?self=1&id=" + data.id;
            } else {
                location.href = "/admin/user/editright?id=" + data.id;
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
                url: '/admin/user/deleteAllright',
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
            url: '/admin/user/rightstatus',
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
        url: '/admin/user/getright',
        where: {key: $('#key').val()}
    });
}
