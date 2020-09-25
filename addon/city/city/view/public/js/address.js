/**
 * Created by Administrator on 2019/12/13.
 */
var area_form;

//获取所有的省
function getProvince(dom){
    var init_id = $(dom).attr('data-init');
    var filter = $(dom).attr('lay-filter');
    $.ajax({
        type : "post",
        url : ns.url('city://city/address/getProvince'),
        dataType : "json",
        success : function(data) {
            if (data != null && data.length > 0) {
                var str = "<option value='-1'>请选择省</option>";
                for (var i = 0; i < data.length; i++) {
                    if(init_id == data[i].id){
                        str += '<option value="'+data[i].id+'" selected>'+data[i].name+'</option>';
                    }else{
                        str += '<option value="'+data[i].id+'" >'+data[i].name+'</option>';
                    }
                }
                $(dom).html(str);
                $(dom).attr('data-init', '-1');
                area_form.render("select");
            }
        }
    });
}

//获取某个省所有的市
function getCity(dom, province_id) {
    var init_id = $(dom).attr('data-init');
    var filter = $(dom).attr('lay-filter');
	
    $.ajax({
        type : "post",
        url : ns.url('city://city/address/getcity'),
        dataType : "json",
        data : {
            "province_id" : province_id
        },
        async:false,
        success : function(data) {
            if (data != null && data.length > 0) {
                var str = "<option value='-1'>请选择市</option>";
                for (var i = 0; i < data.length; i++) {
                    if(init_id ==data[i].id){
                        str += '<option value="'+data[i].id+'" selected="selected">'+data[i].name+'</option>';
                    }else{
                        str += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                    }
                }
                $(dom).html(str);
                $(dom).attr('data-init', '-1');
                area_form.render("select");
            }
        }
    });
}

//获取某个市所有的区县
function getDistrict(dom, city_id) {
    var init_id = $(dom).attr('data-init');
    var filter = $(dom).attr('lay-filter');
    $.ajax({
        type : "post",
        url : ns.url('city://city/address/getdistrict'),
        dataType : "json",
        data : {
            "city_id" : city_id
        },
        async:false,
        success : function(data) {
            if (data != null && data.length > 0) {
                var str = "<option value='-1'>请选择区</option>";
                for (var i = 0; i < data.length; i++) {
                    if(init_id == data[i].id){
                        str += '<option value="'+data[i].id+'" selected="selected">'+data[i].name+'</option>';
                    }else{
                        str += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                    }
                }
                $(dom).html(str);
                $(dom).attr('data-init', '-1');
                area_form.render("select");
            }
        }
    });
}

//获取某个区县所有的街道
function getStreet(dom, district_id) {
    var init_id = $(dom).attr('data-init');
    var filter = $(dom).attr('lay-filter');
    $.ajax({
        type : "post",
        url : ns.url('city://city/address/getStreet'),
        dataType : "json",
        data : {
            "district_id" : district_id
        },
        async:false,
        success : function(data) {
            if (data != null && data.length > 0) {
                var str = "<option value='-1'>请选择街道</option>";
                for (var i = 0; i < data.length; i++) {
                    if(init_id == data[i].id){
                        str += '<option value="'+data[i].id+'" selected="selected">'+data[i].name+'</option>';
                    }else{
                        str += '<option value="'+data[i].id+'">'+data[i].name+'</option>';
                    }
                }
                $(dom).html(str);
                $(dom).attr('data-init', '-1');
                area_form.render("select");
            }
        }
    });
}

//初始化操作
function initArea(form){
    area_form = form;
    $("[data-flag='area']").each(function(){
        var province_dom = $(this).find("select[data-type='province']");
        //判断是否要初始化加载市列表
        var init_province_id = province_dom.attr('data-init');
		getProvince(province_dom);
        if(init_province_id){
            var city_dom = $(this).find("select[data-type='city']");
            //判断是否要初始化加载区列表
            var init_city_id = city_dom.attr('data-init');
			getCity(city_dom, init_province_id);
            if(init_city_id){
                var district_dom = $(this).find("select[data-type='district']");
                var init_district_id = district_dom.attr('data-init');
                getDistrict(district_dom, init_city_id);
                //判断是否要加载街道列表
                if(init_district_id){
                    var street_dom = $(this).find("select[data-type='street']");
                    var init_street_id = street_dom.attr('data-init');
                    getStreet(street_dom, init_street_id);
                }
            }
        }
    });

    //省改变以后触发的事件
    $("[data-flag='area'] select[data-type='province']").each(function(){
        var that = this;
        var filter = $(that).attr('lay-filter');
        form.on('select('+ filter +')', function(data){
            var area_dom = $(that).parents("[data-flag='area']");
            var province_id = data.value;
			$(that).val(province_id);
            var city_dom = area_dom.find("select[data-type='city']");
            getCity(city_dom, province_id);
            var district_dom = area_dom.find("select[data-type='district']");
            district_dom.html("<option value='-1'>请选择区</option>");
            form.render("select");
        })
    });

    //市改变以后触发的事件
    $("[data-flag='area'] select[data-type='city']").each(function(){
        var that = this;
        var filter = $(that).attr('lay-filter');
        form.on('select('+ filter +')', function(data){
            var area_dom = $(that).parents("[data-flag='area']");
            var city_id = data.value;
			$(that).val(city_id);
            var district_dom = area_dom.find("select[data-type='district']");
            getDistrict(district_dom, city_id);
        })
    });
	
	//区改变以后触发的事件
    $("[data-flag='area'] select[data-type='district']").each(function(){
        var that = this;
        var filter = $(that).attr('lay-filter');
        form.on('select('+ filter +')', function(data){
            var district_id = data.value;
            $(that).val(district_id);
        })
    });

    //区改变以后触发的事件
    $("[data-flag='area'] select[data-type='street']").each(function(){
        var that = this;
        var filter = $(that).attr('lay-filter');
        form.on('select('+ filter +')', function(data){
            var district_id = data.value;
            $(that).val(district_id);
        })
    })
}