<?php /*a:3:{s:71:"E:\mi\company\SaaS\code\back-end\app\saas\view\sys\auth\admin_rule.html";i:1601197272;s:65:"E:\mi\company\SaaS\code\back-end\app\saas\view\common\header.html";i:1601084077;s:65:"E:\mi\company\SaaS\code\back-end\app\saas\view\common\footer.html";i:1601084092;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo config('admin.sys_name'); ?>后台管理</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="format-detection" content="telephone=no">
    <link rel="stylesheet" href="http://saas.com/public/static/plugins/layui/css/layui.css" media="all" />
    <link rel="stylesheet" href="http://saas.com/public/static/admin/css/main.css?v=<?php echo time(); ?>" media="all">
    <link rel="stylesheet" href="http://saas.com/public/static/plugins/font-awesome-4.7.0/css/font-awesome.min.css" media="all">
    <!--[if lt IE 9]>
    <script src="https://cdn.staticfile.org/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.staticfile.org/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style id="lemo-bg-color">
    </style>
</head>
<div class="lemo-container">
    <div class="lemo-main">
        <div class="admin-main layui-anim layui-anim-upbit">
            <fieldset class="layui-elem-field layui-field-title">
                <legend>权限列表</legend>
            </fieldset>
            <blockquote class="layui-elem-quote">
                <a data-href="<?php echo url('saas/sys.Auth/ruleAdd'); ?>" class="layui-btn layui-btn-sm add">添加路由</a>
                <a class="layui-btn layui-btn-normal layui-btn-sm" onclick="openAll();">展开或折叠全部</a>
            </blockquote>
            <table class="layui-table" id="list" lay-filter="list"></table>
        </div>
    </div>
</div>

<script type="text/html" id="auth">
    <input type="checkbox" name="auth_open" data-href="<?php echo url('ruleState'); ?>" value="{{d.id}}" lay-skin="switch" lay-text="是|否" lay-filter="status" {{ d.auth_open == 0 ? 'checked' : '' }}>
</script>
<script type="text/html" id="status">
    <input type="checkbox" name="menu_status" data-href="<?php echo url('ruleState'); ?>" value="{{d.id}}" lay-skin="switch" lay-text="显示|隐藏" lay-filter="status" {{ d.menu_status == 1 ? 'checked' : '' }}>
</script>
<script type="text/html" id="order">
    <input name="{{d.id}}" data-id="{{d.id}}" class="list_order layui-input" value=" {{d.sort}}" size="10"/>
</script>
<script type="text/html" id="icon">
    <span class="icon {{d.icon}}"></span>
</script>
<script type="text/html" id="action">
    <a class="layui-btn layui-btn-xs" data-href="<?php echo url('saas/sys.Auth/ruleEdit'); ?>?rule_id={{d.id}}&id={{d.id}}" lay-event="edit">编辑</a>
    <a class="layui-btn layui-btn-xs layui-btn-warm"  data-href="<?php echo url('saas/sys.Auth/ruleAdd'); ?>?rule_id={{d.id}}" lay-event="add">添加下级</a>
    <a class="layui-btn layui-btn-danger layui-btn-xs"  data-href="<?php echo url('saas/sys.Auth/ruleDel'); ?>" lay-event="del">删除</a>
</script>
<script type="text/html" id="topBtn">
    <a data-href="<?php echo url('saas/sys.Auth/ruleAdd'); ?>" class="layui-btn layui-btn-sm" lay-event="add">添加权限</a>
</script>
<script src="http://saas.com/public/static/plugins/layui/layui.js" charset="utf-8"></script>
<!--<script>-->
<!--    layui.config({-->
<!--        base: "/static/admin/js/",-->
<!--        version: true-->
<!--    }).extend({-->
<!--        Admin: 'Admin'-->
<!--    }).use(['Admin'], function () {-->
<!--        Admin = layui.Admin;-->
<!--    });-->
<!--</script>-->

<script>
    var tableIn=null, treeGrid=null,tableId='list',layer=null, Admin =null;

    layui.config({
        base: 'http://saas.com/public/static/',
        version: true
    }).extend({
        treeGrid: 'plugins/layui/extend/treeGrid/treeGrid',
    }).use(['jquery','treeGrid','layer','form'], function(){
        var $=layui.jquery ,form = layui.form;
        treeGrid = layui.treeGrid;
        layer=layui.layer;
        tableIn=treeGrid.render({
            id:tableId
            ,elem: '#'+tableId
            ,idField:'id'
            ,url:'<?php echo url("saas/sys.Auth/adminRule"); ?>'
            ,cellMinWidth: 100
            ,treeId:'id'//树形id字段名称
            ,treeUpId:'pid'//树形父id字段名称
            ,treeShowName:'title'//以树形式显示的字段
            ,height:'full-140'
            ,isFilter:false
            ,iconOpen:true//是否显示图标【默认显示】
            // ,isOpenDefault:true//节点默认是展开还是折叠【默认展开】
            ,cols: [[
                {field: 'id', title: '<?php echo lang("id"); ?>', width: 70, fixed: true},
                {field: 'icon', align: 'center',title: '<?php echo lang("icon"); ?>', width: 60,templet: '#icon'},
                {field: 'title', title: '权限名称', width: 200},
                {field: 'href', title: '控制器/方法', minwidth: 200},
                {field: 'auth_open',align: 'center', title: '是否验证权限', width: 150,toolbar: '#auth'},
                {field: 'menu_status',align: 'center',title: '菜单状态', width: 150,toolbar: '#status'},
                {field: 'sort',align: 'center', title: '排序', width: 80, templet: '#order'},
                {title:'操作',width:200, toolbar: '#action',align:"center"},
            ]]
            ,page:false
        });
        treeGrid.on('tool(list)', function(obj){
            var data = obj.data;
            var url = $(this).attr('data-href');
            if(obj.event === 'del'){
                layer.confirm('Are you sure you want to delete it', function(index){
                    loading =layer.load(1, {shade: [0.1,'#fff']});
                    $.post(url?url:'delete',{ids:[data.id]},function(res){
                        layer.close(loading);
                        layer.close(index);
                        if(res.code>0){
                            layer.msg(res.msg,{time:1000,icon:1});
                            obj.del();
                        }else{
                            layer.msg(res.msg,{time:1000,icon:2});
                        }
                    });
                });
            }else if(obj.event === 'add'){
                var iframe = layer.open({
                    type: 2,
                    content: url?url:'add',
                    area: ['800px', '600px'],
                    maxmin: true
                });
                layer.full(iframe);

            }else if(obj.event === 'edit'){

                var iframe = layer.open({
                    type: 2,
                    content: url?url:'edit',
                    area: ['600px', '800px'],
                    maxmin: true
                });
                layer.full(iframe);

            }

        });

    });
    layui.config({
        base: 'http://saas.com/public/static/',
        version: true
    }).extend({
        Admin: 'admin/js/Admin',
    }).use(['Admin'], function(){
        Admin = layui.Admin
    });
    function openAll() {
        var treedata=treeGrid.getDataTreeList(tableId);
        treeGrid.treeOpenAll(tableId,!treedata[0][treeGrid.config.cols.isOpen]);
    }

</script>