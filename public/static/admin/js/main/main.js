layui.config({
    base: "js/"
}).use(['form', 'element', 'layer', 'jquery'], function () {
    var form = layui.form,
        layer = parent.layer === undefined ? layui.layer : parent.layer,
        element = layui.element,
        $ = layui.jquery;

    // $(".panel a").on("click",function(){
    // 	window.parent.addTab($(this));
    // })

    // //填充数据方法
    // function fillParameter(data){
    // 	//判断字段数据是否存在
    // 	function nullData(data){
    // 		if(data == '' || data == "undefined"){
    // 			return "未定义";
    // 		}else{
    // 			return data;
    // 		}
    // 	}
    // 	$(".version").text(nullData(data.version));      //当前版本
    // 	$(".author").text(nullData(data.author));        //开发作者
    // 	$(".homePage").text(nullData(data.homePage));    //网站首页
    // 	$(".server").text(nullData(data.server));        //服务器环境
    // 	$(".dataBase").text(nullData(data.dataBase));    //数据库版本
    // 	$(".maxUpload").text(nullData(data.maxUpload));    //最大上传限制
    // 	$(".userRights").text(nullData(data.userRights));//当前用户权限
    // }

})
