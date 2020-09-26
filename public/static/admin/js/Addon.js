layui.define(["element", "jquery",'form', 'table'], function (exports) {
    var element = layui.element,
        $ = layui.$,
        layer = layui.layer,
        form = layui.form,
        table = layui.table;
        form.render();
        /*表单*/
        var tableIn = table.render({
            elem: '#list',
            url: 'index',
            method: 'post',
            cols: [[
                {checkbox: true, fixed: true},
                {field: 'title', title: '名称', width: 120,sort:true,},
                {field: 'name', title: '标识', width: 100,sort:true,templet:'#name'},
                {field: 'description', title: '描述', minWidth: 220, sort:true,},
                {field: 'version', title: '插件版本', width: 60,sort:true,},
                {field: 'require', title: '适用版本', width: 60,sort:true},
                {field: 'author', title: '作者', width: 120,sort:true},
                {field: 'create_time', title: '添加时间', width: 180,templet:'#create_time'},
                // {field: 'update_time', title: '修改时间', width: 180,templet:'#update_time'},
                {title:'操作',width:250, toolbar: '#action',align:"center"}

            ]],
            limits: [10, 15, 20, 25, 50, 100],
            limit: 15,
            page: true
        });

        table.on('tool(list)', function(obj){
            var data = obj.data;
            if(obj.event === 'install'){
                if(Addon.getUserinfo() && Addon.getUserinfo().hasOwnProperty('client')){
                    layer.confirm('Are you sure you want to install it', function(index){
                        loading =layer.load(0, {shade: [0.1,'#fff']});

                        $.post("install",{name:data.name},function(res){
                            layer.close(loading);
                            layer.close(index);
                            if(res.code>0){
                                layer.msg(res.msg,{time:1000,icon:1});
                                tableIn.reload()
                            }else{
                                layer.msg(res.msg,{time:1000,icon:2});
                            }
                        });
                    });
                }else{
                    layer.open({
                        type: 1,
                        content: $("#login"),
                        zIndex: 9999,
                        area: ['450px', '350px'],
                        title: ['登录lemocms','text-align:center'],
                        resize: false,
                        btn: ['登录', '注册'],
                        yes: function (index, layero) {
                            var url =  Addon.config.api_url + Addon.config.login_url;
                            var nonce =  Addon.getNonce();
                            var timestamp = Addon.getTimestamp();

                            var data = {
                                appid:Addon.config.appid,
                                appsecret:Addon.config.appsecret,
                                username: $("#inputUsername", layero).val(),
                                password: $("#inputPassword", layero).val(),
                                nonce:nonce,
                                key:Addon.config.appsecret,
                                timestamp:timestamp,
                            };
                            var sign = Addon.getSign(data);
                            data.sign = sign;
                            $.post(url, data , function(res) {
                                res = JSON.parse(res)
                                if(res.code==200){
                                    Addon.setUserinfo(res.data);
                                    layer.closeAll();
                                    layer.alert(res.message);
                                }else{
                                    // layer.closeAll();
                                    layer.alert(res.message);
                                }
                            });

                        },
                        btn2: function () {
                            return false;
                        },
                        success: function (layero, index) {
                            $(".layui-layer-btn1", layero).prop("href", "https://bbs.lemocms.com/login/reg.html").prop("target", "_blank");
                        }

                    });
                }

            }
            if(obj.event === 'uninstall'){
                layer.confirm('Are you sure you want to uninstall it', function(index){
                    loading =layer.load(1, {shade: [0.1,'#fff']});
                    $.post("uninstall",{id:data.id,name:data.name},function(res){
                        layer.close(loading);
                        layer.close(index);
                        if(res.code>0){
                            layer.msg(res.msg,{time:2000,icon:1});
                            tableIn.reload()
                        }else{
                            layer.msg(res.msg,{time:2000,icon:2});
                        }
                    });
                });
            }
            if(obj.event === 'status'){
                layer.confirm('Are you sure you want to change it', function(index){
                    loading =layer.load(1, {shade: [0.1,'#fff']});
                    $.post("state",{id:data.id,name:data.name},function(res){
                        layer.close(loading);
                        layer.close(index);
                        if(res.code>0){
                            layer.msg(res.msg,{time:2000,icon:1});
                            tableIn.reload()
                        }else{
                            layer.msg(res.msg,{time:2000,icon:2});
                        }
                    });
                });
            }

            if(obj.event === 'config'){
                var id = data.id;
                var name =data.name;
                var index = layer.open({
                    type: 2,
                    content: 'config?id='+id+'&name='+name,
                    area: ['600px', '800px'],
                    maxmin: true
                });
                layer.full(index)

            }
            return false;
        });


        /*
        组件
         */
    Addon = new function () {
        // 配置
        this.config =  {
                api_url: 'https://www.lemocms.com',   // 接口地址
                login_url: '/api/v1.token/accessToken',   // 登陆地址获取token地址
                appid: 'lemocms',   // appid
                appsecret: 'L9EwqM1jQQFOvniYnpe6K0SavguQOgoS',   // appserct

            };

        /**
         * 初始化
         * @param data ;data
         */
        this.init = function (data) {
            if(data.length>0){
            var loading = layer.load(0, {shade: false, time: 2 * 1000});
            this.config.push(data);
            }
            layer.close(loading);
        };

        /*
      时间错
       */
        this.getTimestamp = function () {
           return  Date.parse(new  Date())/1000
        };
        /*
        随机数
         */
        this.getNonce = function (len) {
            var len = len || 8;
            var $chars = 'ABCDEFGHJKMNPQRSTWXYZabcdefhijkmnprstwxyz2345678';    /****默认去掉了容易混淆的字符oOLl,9gq,Vv,Uu,I1****/
            var maxPos = $chars.length;
            var nonce = '';
            for (i = 0; i < len; i++) {
               nonce += $chars.charAt(Math.floor(Math.random() * maxPos));
       　　  }
        　　 return nonce;
        };
        //获取签名
        this.getSign = function (obj) {
                //先用Object内置类的keys方法获取要排序对象的属性名，再利用Array原型上的sort方法对获取的属性名进行排序，newkey是一个数组
                var newkey = Object.keys(obj).sort();
                //console.log('newkey='+newkey);
                var newObj = {}; //创建一个新的对象，用于存放排好序的键值对
                //排序
                for(var i = 0; i < newkey.length; i++) {
                    //遍历newkey数组
                    newObj[newkey[i]] = obj[newkey[i]];
                    //向新创建的对象中按照排好的顺序依次增加键值对
                }
                var str = '';
                //拼接
                for(var key in newObj) {
                    str += key +'='+newObj[key]+'&';
                }
                str = str.substring(0, str.length-1);
                return md5(decodeURI(str)).toLowerCase();
        };
        //获取用户信息
        this.getUserinfo = function () {
            var userinfo = localStorage.getItem("lemocms_userinfo");
            return userinfo ? JSON.parse(userinfo) : null;

        };
        //设置用户信息
        this.setUserinfo = function (data) {
            if (data) {
                localStorage.setItem("lemocms_userinfo", JSON.stringify(data));
            } else {
                localStorage.removeItem("lemocms_userinfo");
            }

        };


    };
    exports("Addon", Addon);
});