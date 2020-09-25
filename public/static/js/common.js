var ns = window.ns_url;


/**
 * 解析URL
 * @param  {string} url 被解析的URL
 * @return {object}     解析后的数据
 */
ns.parse_url = function (url) {
	var parse = url.match(/^(?:([0-9a-zA-Z]+):\/\/)?([\w-]+(?:\.[\w-]+)+)?(?::(\d+))?([\w-\/]+)?(?:\?((?:\w+=[^#&=\/]*)?(?:&\w+=[^#&=\/]*)*))?(?:#([\w-]+))?$/i);
	parse || $.error("url格式不正确！");
	return {
		"scheme": parse[1],
		"host": parse[2],
		"port": parse[3],
		"path": parse[4],
		"query": parse[5],
		"fragment": parse[6]
	};
};
ns.parse_str = function (str) {
	var value = str.split("&"), vars = {}, param;
	for (var i = 0; i < value.length; i++) {
		param = value[i].split("=");
		vars[param[0]] = param[1];
	}
	return vars;
};

ns.parse_name = function (name, type) {
	if (type) {
		/* 下划线转驼峰 */
		name = name.replace(/_([a-z])/g, function ($0, $1) {
			return $1.toUpperCase();
		});
		/* 首字母大写 */
		name = name.replace(/[a-z]/, function ($0) {
			return $0.toUpperCase();
		});
	} else {
		/* 大写字母转小写 */
		name = name.replace(/[A-Z]/g, function ($0) {
			return "_" + $0.toLowerCase();
		});
		/* 去掉首字符的下划线 */
		if (0 === name.indexOf("_")) {
			name = name.substr(1);
		}
	}
	return name;
};

//scheme://host:port/path?query#fragment
ns.url = function (url, vars, suffix) {
	if (url.indexOf('http://') != -1 || url.indexOf('https://') != -1) {
		return url;
	}
	
	var info = this.parse_url(url), path = [], param = {}, reg;
	
	/* 验证info */
	info.path || alert("url格式错误！");
	url = info.path;
	
	// /* 解析URL */
	// path = url.split("/");
	// path = [path.pop(), path.pop(), path.pop()].reverse();
	// path[1] = path[1] || this.route[1];
	// path[0] = path[0] || this.route[0];
	// param[this.route[0]] = path[0];
	// param[this.route[1]] = path[1];
	// param[this.route[2]] = path[2].toLowerCase();
	// url = param[this.route[0]] + '/' + param[this.route[1]] + '/' + param[this.route[2]];
	
	// param[this.route[2]] = path[0];
	// param[this.route[3]] = path[1];
	// param[this.route[4]] = path[2].toLowerCase();
	// url = param[this.route[2]] + '/' + param[this.route[3]] + '/' + param[this.route[4]];
	/* 解析参数 */
	if (typeof vars === "string") {
		vars = this.parse_str(vars);
	} else if (!$.isPlainObject(vars)) {
		vars = {};
	}
	/* 添加伪静态后缀 */
	if (false !== suffix) {
		suffix = suffix || 'html';
		if (suffix) {
			url += "." + suffix;
		}
	}
	/* 解析URL自带的参数 */
	info.query && $.extend(vars, this.parse_str(info.query));
	
	var addon = '';
	if (info.scheme != '' && info.scheme != undefined) {
		addon = info.scheme + '/';
	}
	url = addon + url;
	if (vars) {
		var param_str = $.param(vars);
		if ('' !== param_str) {
			url += ((this.baseUrl + url).indexOf('?') !== -1 ? '&' : '?') + param_str;
		}
	}
	url = this.baseUrl + url;
	return url;
};

/**
 * 处理图片路径
 * type 类型 big、mid、small
 */
// ns.img = function (path, type = '') {
ns.img = function (path, type) {
	var type = type || ''
	var start = path.lastIndexOf('.');
	type = type ? '_' + type : '';
	var suffix = path.substring(start);
	var path = path.substring(0, start);
	var first = path.split("/");
	path += type + suffix;
	
	if (path.indexOf("http://") == -1 && path.indexOf("https://") == -1) {
		
		var base_url = this.baseUrl.replace('/?s=', '');
		var base_url = base_url.replace('/index.php', '');
		if (isNaN(first[0])) {
			var true_path = base_url + path;
		} else {
			var true_path = base_url + 'attachment/' + path;
		}
	} else {
		var true_path = path;
	}
	return true_path;
};

/**
 * 时间戳转时间
 *
 */
var default_time_format = 'YYYY-MM-DD h:m:s';
// ns.time_to_date = function (timeStamp, time_format = '') {
ns.time_to_date = function (timeStamp, time_format) {
	var time_format = time_format || ''
	time_format = time_format == '' ? default_time_format : time_format;
	if (timeStamp > 0) {
		var date = new Date();
		date.setTime(timeStamp * 1000);
		var y = date.getFullYear();
		var m = date.getMonth() + 1;
		m = m < 10 ? ('0' + m) : m;
		var d = date.getDate();
		d = d < 10 ? ('0' + d) : d;
		var h = date.getHours();
		h = h < 10 ? ('0' + h) : h;
		var minute = date.getMinutes();
		var second = date.getSeconds();
		minute = minute < 10 ? ('0' + minute) : minute;
		second = second < 10 ? ('0' + second) : second;
		var time = '';
		time += time_format.indexOf('Y') > -1 ? y : '';
		time += time_format.indexOf('M') > -1 ? '-' + m : '';
		time += time_format.indexOf('D') > -1 ? '-' + d : '';
		time += time_format.indexOf('h') > -1 ? ' ' + h : '';
		time += time_format.indexOf('m') > -1 ? ':' + minute : '';
		time += time_format.indexOf('s') > -1 ? ':' + second : '';
		return time;
	} else {
		return "";
	}
};

/**
 * 日期 转换为 Unix时间戳
 * @param <string> 2014-01-01 20:20:20  日期格式
 * @return <int>        unix时间戳(秒)
 */
ns.date_to_time = function (string) {
	var f = string.split(' ', 2);
	var d = (f[0] ? f[0] : '').split('-', 3);
	var t = (f[1] ? f[1] : '').split(':', 3);
	return (new Date(
		parseInt(d[0], 10) || null,
		(parseInt(d[1], 10) || 1) - 1,
		parseInt(d[2], 10) || null,
		parseInt(t[0], 10) || null,
		parseInt(t[1], 10) || null,
		parseInt(t[2], 10) || null
	)).getTime() / 1000;
};

/**
 * url 反转义
 * @param url
 */
ns.urlReplace = function (url) {
	var url = decodeURIComponent(url);
	var new_url = url.replace(/%2B/g, "+");//"+"转义
	new_url = new_url.replace(/%26/g, "&");//"&"
	new_url = new_url.replace(/%23/g, "#");//"#"
	new_url = new_url.replace(/%20/g, " ");//" "
	new_url = new_url.replace(/%3F/g, "?");//"#"
	new_url = new_url.replace(/%25/g, "%");//"#"
	new_url = new_url.replace(/&3D/g, "=");//"#"
	new_url = new_url.replace(/%2F/g, "/");//"#"
	return new_url;
};

/**
 * 需要定义APP_KEY,API_URL
 * method 插件名.控制器.方法
 * data  json对象
 * async 是否异步，默认true 异步，false 同步
 */
ns.api = function (method, param, callback, async) {
	// async true为异步请求 false为同步请求
	var async = async != undefined ? async : true;
	param.app_key = APP_KEY;
	$.ajax({
		type: 'get',
		url: API_URL + '?s=/api/index/get/method/' + method + '/version/1.0',
		dataType: "JSON",
		async: async,
		data: {'param': JSON.stringify(param), method: method},
		success: function (res) {
			if (callback) callback(eval("(" + res + ")"));
		}
	});
};

/**
 * url 反转义
 * @param url
 */
ns.append_url_params = function (url, params) {
	if (params != undefined) {
		var url_params = '';
		for (var k in params) {
			url_params += "&" + k + "=" + params[k];
		}
		url += url_params;
	}
	return url;
};

/**
 * 生成随机不重复字符串
 * @param len
 * @returns {string}
 */
ns.gen_non_duplicate = function (len) {
	var num = Number(Math.random().toString().substr(3, len) + Date.now()).toString(36);
	var arr = num.split(".");
	if (arr.length == 1) {
		num = num.substr(0, 12)
	} else {
		num = arr[0] + arr[1].substr(0, 11)
	}
	return num;
};

/**
 * 获取分页参数
 * @param param 参数
 * @returns {{layout: string[]}}
 */
ns.get_page_param = function (param) {
	var obj = {
		layout: ['count', 'limit', 'prev', 'page', 'next']
	};
	if (param != undefined) {
		if (param.limit != undefined) {
			obj.limit = param.limit;
		}
	}
	return obj;
};

/**
 * 弹出框，暂时没有使用
 * @param options 参数，参考layui：https://www.layui.com/doc/modules/layer.html
 */
ns.open = function (options) {
	if (!options) options = {};
	
	options.type = options.type || 1;
	
	//宽高，小、中、大
	// options.size
	options.area = options.area || ['500px'];
	layer.open(options);
};

/**
 * 上传
 * @param id
 * @param method
 * @param param
 * @param callback
 * @param async
 */
ns.upload_api = function (id, method, param, callback, async) {
	// async true为异步请求 false为同步请求
	var async = async != undefined ? async : true;
	param.app_key = APP_KEY;
	var file = document.getElementById(id).files[0];
	var formData = new FormData();
	formData.append("file", file);
	formData.append("method", method);
	formData.append("param", JSON.stringify(param));
	$.ajax({
		url: API_URL + '?s=/api/index/get/method/' + method + '/version/1.0',
		type: "post",
		data: formData,
		dataType: "JSON",
		contentType: false,
		processData: false,
		async: async,
		mimeType: "multipart/form-data",
		success: function (res) {
			if (callback) callback(eval("(" + res + ")"));
		},
		// error: function (data) {
		//     console.log(data);
		// }
	});
};

/**
 * 复制
 * @param dom
 * @param callback
 */
ns.copy = function JScopy(dom, callback) {
	var url = document.getElementById(dom);
	url.select();
	document.execCommand("Copy");
	var o = {
		url: url.value
	};
	
	if (callback) callback.call(this, o);
	layer.msg('复制成功');
};


ns.int_to_float = function (val){
	return new Number(val).toFixed(2);
}
var show_link_box_flag = true;
/**
 * 弹出框-->选择链接
 * @param link
 * @param support_diy_view
 * @param callback
 * @param post 端口：admin、shop
 */
ns.select_link = function (link, support_diy_view, callback, link_url) {
	
	var url = ns.url(link_url);
	if (show_link_box_flag) {
		show_link_box_flag = false;
		$.post(url, {link: JSON.stringify(link)}, function (str) {
			window.linkIndex = layer.open({
				type: 1,
				title: "选择链接",
				content: str,
				btn: [],
				area: ['850px', '630px'], //宽高
				maxWidth: 1920,
				cancel: function (index, layero) {
					show_link_box_flag = true;
				},
				end: function () {
					if (window.linkData) {				
						if (callback) callback(window.linkData);
					}
					
					show_link_box_flag = true;
					
				}
			});
		});
	}
};

var show_promote_flag = true;

/**
 * 推广链接
 * @param data
 */
ns.page_promote = function (data) {
	
	var url = ns.url("admin/diy/promote");
	if (show_promote_flag) {
		show_promote_flag = false;
		$.post(url, {data: JSON.stringify(data)}, function (str) {
			window.promoteIndex = layer.open({
				type: 1,
				title: "推广链接",
				content: str,
				btn: [],
				area: ['680px', '600px'], //宽高
				maxWidth: 1920,
				cancel: function (index, layero) {
					show_promote_flag = true;
				},
				end: function () {
					show_promote_flag = true;
				}
			});
		});
	}
};
//存储单元单位转换
ns.sizeformat = function (limit) {
	if (limit == null || limit == "") {
		return "0KB"
	}
	var index = 0;
	var limit = limit.toUpperCase();//转换为小写
	if (limit.indexOf('B') == -1) { //如果无单位,加单位递归转换
		limit = limit + "B";
		//unitConver(limit);
	}
	var reCat = /[0-9]*[A-Z]B/;
	if (!reCat.test(limit) && limit.indexOf('B') != -1) { //如果单位是b,转换为kb加单位递归
		limit = limit.substring(0, limit.indexOf('B')); //去除单位,转换为数字格式
		limit = (limit / 1024) + 'KB'; //换算舍入加单位
		//unitConver(limit);
	}
	var array = new Array('KB', 'MB', 'GB', 'TB', 'PT');
	for (var i = 0; i < array.length; i++) { //记录所在的位置
		if (limit.indexOf(array[i]) != -1) {
			index = i;
			break;
		}
	}
	var limit = parseFloat(limit.substring(0, (limit.length - 2))); //得到纯数字
	
	while (limit >= 1024) {//数字部分1到1024之间
		limit /= 1024;
		index += 1;
	}
	limit = limit.toFixed(2) + array[index]
	return limit;
}


/**
 * 数据表格
 * layui官方文档：https://www.layui.com/doc/modules/table.html
 * @param options
 * @constructor
 */
function Table(options) {
	
	if (!options) return;
	var _self = this;
	
	options.parseData = options.parseData || function (data) {
		return {
			"code": data.code,
			"msg": data.message,
			"count": data.data.count,
			"data": data.data.list
		};
	};
	
	options.request = options.request || {
		limitName: 'page_size' //每页数据量的参数名，默认：limit
	};
	
	if (options.page == undefined) {
		options.page = {
			layout: ['count', 'limit', 'prev', 'page', 'next'],
			limit: 10
		};
	}
	
	options.defaultToolbar = options.defaultToolbar || [];//'filter', 'print', 'exports'
	
	options.toolbar = options.toolbar || "";//头工具栏事件
	
	options.skin = options.skin || 'line';
	options.size = options.size || 'lg';
	options.async = (options.async != undefined) ? options.async : true;
	options.done = function (res, curr, count) {
		//加载图片放大
		loadImgMagnify();
		if (options.callback) options.callback(res, curr, count);
	};
	
	layui.use('table', function () {
		_self._table = layui.table;
		_self._table.render(options);
	});
	
	this.filter = options.filter || options.elem.replace(/#/g, "");
	this.elem = options.elem;
	
	
	//获取当前选中的数据
	this.checkStatus = function () {
		return this._table.checkStatus(_self.elem.replace(/#/g, ""));
	};
}

/**
 * 监听头工具栏事件
 * @param callback 回调
 */
Table.prototype.toolbar = function (callback) {
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.on('toolbar(' + _self.filter + ')', function (obj) {
				var checkStatus = _self._table.checkStatus(obj.config.id);
				obj.data = checkStatus.data;
				obj.isAll = checkStatus.isAll;
				if (callback) callback.call(this, obj);
			});
			clearInterval(interval);
		}
	}, 50);
};

/**
 * 监听底部工具栏事件
 * @param callback 回调
 */
Table.prototype.bottomToolbar = function (callback) {
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.on('bottomToolbar(' + _self.filter + ')', function (obj) {
				var checkStatus = _self._table.checkStatus(obj.config.id);
				obj.data = checkStatus.data;
				obj.isAll = checkStatus.isAll;
				if (callback) callback.call(this, obj);
			});
			clearInterval(interval);
		}
	}, 50);
};

/**
 * 绑定layui的on事件
 * @param name
 * @param callback
 */
Table.prototype.on = function (name, callback) {
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.on(name + '(' + _self.filter + ')', function (obj) {
				if (callback) callback.call(this, obj);
			});
			clearInterval(interval);
		}
	}, 50);
};


/**
 * //监听行工具事件
 * @param callback 回调
 */
Table.prototype.tool = function (callback) {
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.on('tool(' + _self.filter + ')', function (obj) {
				if (callback) callback.call(this, obj);
			});
			clearInterval(interval);
		}
	}, 50);
};

/**
 * 刷新数据
 * @param options 参数，参考layui数据表格参数
 */
Table.prototype.reload = function (options) {
	options = options || {
		page: {
			curr: 1
		}
	};
	var _self = this;
	var interval = setInterval(function () {
		if (_self._table) {
			_self._table.reload(_self.elem.replace(/#/g, ""), options);
			clearInterval(interval);
		}
	}, 50);
};

var layedit;


/**
 * 富文本编辑器
 * https://www.layui.com/v1/doc/modules/layedit.html
 * @param id
 * @param options 参数，参考layui
 * @param callback 监听输入回调
 * @constructor
 */
function Editor(id, options, callback) {
	options = options || {};
	this.id = id;
	var _self = this;
	layui.use(['layedit'], function () {
		layedit = layui.layedit;
		layedit.set({
			uploadImage: {
				url: ns.url("file://common/File/image")
			},
			callback: callback
		});
		_self.index = layedit.build(id, options);
	});
}

/**
 * 设置内容
 * @param content 内容
 * @param append 是否追加
 */
Editor.prototype.setContent = function (content, append) {
	var _self = this;
	var time = setInterval(function () {
		layedit.setContent(_self.index, content, append);
		clearInterval(time);
	}, 150);
};

Editor.prototype.getContent = function () {
	return layedit.getContent(this.index);
};

Editor.prototype.getText = function () {
	return layedit.getText(this.index);
};

$(function () {
	loadImgMagnify();
});

//图片最大递归次数
var IMG_MAX_RECURSIVE_COUNT = 6;
var count = 0;

/**
 * //加载图片放大
 */
function loadImgMagnify() {
	setTimeout(function () {
		try {
			if (layer) {
				$("img[src!=''][layer-src]").each(function () {
					var id = getId($(this).parent());
					layer.photos({
						photos: "#" + id,
						anim: 5
					});
					count = 0;
				});
			}
		} catch (e) {
		
		}
	}, 200);
}

function getId(o) {
	count++;
	var id = o.attr("id");
	// console.log("递归次数:", count,id);
	if (id == undefined && count < IMG_MAX_RECURSIVE_COUNT) {
		id = getId(o.parent());
	}
	if (id == undefined) {
		id = ns.gen_non_duplicate(10);
		o.attr("id", id);
	}
	return id;
}

// 返回(关闭弹窗)
function back() {
	layer.closeAll('page');
}

/**
 * 自定义分页
 * @param options
 * @constructor
 */
function Page(options) {
	
	if (!options) return;
	var _self = this;
	
	options.elem = options.elem.replace(/#/g, "");// 注意：这里不能加 # 号
	options.count = options.count || 0;// 数据总数。一般通过服务端得到
	options.limit = options.limit || 10;// 每页显示的条数。laypage将会借助 count 和 limit 计算出分页数。
	options.limits = options.limits || [];// 每页条数的选择项。如果 layout 参数开启了 limit，则会出现每页条数的select选择框
	options.curr = location.hash.replace('#!page=', '');// 起始页。一般用于刷新类型的跳页以及HASH跳页
	options.hash = options.hash || 'page';// 开启location.hash，并自定义 hash 值。如果开启，在触发分页时，会自动对url追加：#!hash值={curr} 利用这个，可以在页面载入时就定位到指定页
	options.groups = options.groups || 5;// 连续出现的页码个数
	options.prev = options.prev || '<i class="layui-icon layui-icon-left"></i>';// 自定义“上一页”的内容，支持传入普通文本和HTML
	options.next = options.next || '<i class="layui-icon layui-icon-right"></i>';// 自定义“下一页”的内容，同上
	options.first = options.first || 1;// 自定义“首页”的内容，同上
	
	// 自定义排版。可选值有：count（总条目输区域）、prev（上一页区域）、page（分页区域）、next（下一页区域）、limit（条目选项区域）、refresh（页面刷新区域。注意：layui 2.3.0 新增） 、skip（快捷跳页区域）
	options.layout = options.layout || ['count', 'prev', 'page', 'next'];
	
	options.jump = function (obj, first) {
		
		//首次不执行，一定要加此判断，否则初始时会无限刷新
		if (!first) {
			obj.page = obj.curr;
			options.callback.call(this, obj);
		}
	};
	
	layui.use('laypage', function () {
		_self._page = layui.laypage;
		_self._page.render(options);
	});
	
}


/**
 * 表单验证
 * @value options
 * @item
 */
layui.use('form', function () {
	var form = layui.form;
	form.verify({
		required: function (value, item) {
			var str = $(item).parents(".layui-form-item").find("label").text().split("*").join("");
			str = str.substring(0, str.length - 1);
			
			if (value.trim() == "" || value == undefined || value == null) return str + "不能为空";
		}
	});
});


/**
 * 面板折叠
 * @value options
 * @item
 */
layui.use('element', function () {
	var element = layui.element;
	element.on('collapse(selection_panel)', function (data) {
		if (data.show) {
			$(data.title).find("i").removeClass("layui-icon-up").addClass("layui-icon-down");
		} else {
			$(data.title).find("i").removeClass("layui-icon-down").addClass("layui-icon-up");
		}
		$(data.title).find("i").text('');
	});
});


/**
 * 判断当前浏览器是否为ie浏览器
 */
function IEVersion() {
	var userAgent = navigator.userAgent; //取得浏览器的userAgent字符串  
	var isIE = userAgent.indexOf("compatible") > -1 && userAgent.indexOf("MSIE") > -1; //判断是否IE<11浏览器  
	var isEdge = userAgent.indexOf("Edge") > -1 && !isIE; //判断是否IE的Edge浏览器  
	var isIE11 = userAgent.indexOf('Trident') > -1 && userAgent.indexOf("rv:11.0") > -1;
	if(isIE) {
		return 1;
	} else if(isEdge) {
		return 'edge';//edge
	} else if(isIE11) {
		return 1; //IE11  
	}else{
		return -1;//不是ie浏览器
	}
}


/* 添加图片到列表界面上 */
function setList(con, list, ue) {
	var _url = '', _con = '';
    var i;
    for (i = 0; i < list.length; i++) {
        if(list[i]) {
            _url += '\<img width="113" src="'+ list[i] +'" \/\>';
            
        }
    }
	
	_con = con + _url;
	
    ue.setContent(_con);
}

/**
 * 商品选择器
 * @param callback 回调函数
 * @param selectId 已选商品id
 * @param params mode：模式(spu、sku), max_num：最大数量，min_num 最小数量, is_virtual 是否虚拟 0 1, disabled: 开启禁用已选 0 1
 */
function goodsSelect(callback, selectId, params) {
	layui.use(['layer'], function () {
		if (selectId.length) {
			params.select_id = selectId.toString();
		}
		var url = ns.url("admin/goods/goodsselect", params);
		//iframe层-父子操作
		layer.open({
			title: "商品选择",
			type: 2,
			area: ['1000px', '600px'],
			fixed: false, //不固定
			btn: ['保存', '返回'],
			content: url,
			yes: function (index, layero) {
				var iframeWin = window[layero.find('iframe')[0]['name']];//得到iframe页的窗口对象，执行iframe页的方法：
				
				iframeWin.selectGoods(function (obj) {
					if (typeof callback == "string") {
						try {
							eval(callback + '(obj)');
							layer.close(index);
						} catch (e) {
							console.error('回调函数' + callback + '未定义');
						}
					} else if (typeof callback == "function") {
						callback(obj);
						layer.close(index);
					}
					
				});
			}
		});
	});
}