<?php
/**
 * KirySaaS--------||bai T o o Y ||
 * =========================================================
 * ----------------------------------------------
 * User Mack Qin
 * Copy right 2019-2029 kiry 保留所有权利。
 * ----------------------------------------------
 * =========================================================
 */

return [
    'template'       => [
        [
            'name'  => 'DIYVIEW_INDEX',
            'title' => '网站主页',
            'value' => '',
            'type'  => 'ADMIN',
            'icon'  => ''
        ],
        [
            'name'  => 'DIYVIEW_SHOP_INDEX',
            'title' => '店铺主页',
            'value' => '',
            'type'  => 'SHOP',
            'icon'  => ''
        ],
    ],
    'util'           => [
        [
            'name'             => 'TEXT',
            'title'            => '文本',
            'type'             => 'SYSTEM',
            'controller'       => 'Text',
            'value'            => '{ title : "『文本』", subTitle : "", textAlign : "left", backgroundColor : "",textColor:"#333",padding:0, "link" : {},"fontSize" : 16,style:1 }',
            'sort'             => '10000',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'TEXT_NAV',
            'title'            => '文本导航',
            'type'             => 'SYSTEM',
            'controller'       => 'TextNav',
            'value'            => '{ fontSize : 14, textColor : "#333333", textAlign : "left", backgroundColor : "", arrangement : "vertical", list : [{ text : "『文本导航』","link" : {}}] }',
            'sort'             => '10001',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'NOTICE',
            'title'            => '公告',
            'type'             => 'SYSTEM',
            'controller'       => 'Notice',
            'value'            => '{ "backgroundColor": "","textColor": "#333333","fontSize": 14,"padding":0,"marginLeftRight":0,"list": [{"title": "公告","link": {}},{"title": "公告","link": {}}]}',
            'sort'             => '10002',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'GRAPHIC_NAV',
            'title'            => '图文导航',
            'type'             => 'SYSTEM',
            'controller'       => 'GraphicNav',
            'value'            => '{ "textColor": "#666666","defaultTextColor": "#666666","backgroundColor": "","selectedTemplate": "imageNavigation","scrollSetting": "fixed","imageScale": 100,padding : 0, "paddingTopBottom": 0, "marginLeftRight": 0,"radius":0,"list": [{"imageUrl": "","title": "","link": {}},{"imageUrl": "","title": "","link": {}},{"imageUrl": "","title": "","link": {}},{"imageUrl": "","title": "","link": {}}], "borderTopLeftRadius": 0, "borderTopRightRadius": 0, "borderBottomLeftRadius": 0, "borderBottomRightRadius": 0}',
            'sort'             => '10003',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'IMAGE_ADS',
            'title'            => '图片广告',
            'type'             => 'SYSTEM',
            'controller'       => 'ImageAds',
            'value'            => '{ selectedTemplate : "carousel-posters", imageClearance : 0, padding : 0, height : 0, list : [ { imageUrl : "", title : "", "link" : {}} ] }',
            'sort'             => '10004',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'SEARCH',
            'title'            => '商品搜索',
            'type'             => 'SYSTEM',
            'controller'       => 'Search',
            'value'            => '{textAlign:"left",bgColor:"",borderType:2,textColor:"",backgroundColor: ""}',
            'sort'             => '10005',
            'support_diy_view' => '',
            'max_count'        => 1
        ],
        [
            'name'             => 'TITLE',
            'title'            => '顶部标题',
            'type'             => 'SYSTEM',
            'controller'       => 'Title',
            'value'            => '{ "title": "『顶部标题』","backgroundColor": "","textColor": "#000000","isOpenOperation" : false,"leftLink" : {},"rightLink" : {},"operation_name" : "操作","fontSize" : 16}',
            'sort'             => '10006',
            'support_diy_view' => '',
            'max_count'        => 1
        ],
        [
            'name'             => 'RICH_TEXT',
            'title'            => '富文本',
            'type'             => 'SYSTEM',
            'controller'       => 'RichText',
            'value'            => '{backgroundColor: "",padding : 0 ,"html" : "" }',
            'sort'             => '10007',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'RUBIK_CUBE',
            'title'            => '魔方',
            'type'             => 'SYSTEM',
            'controller'       => 'RubikCube',
            'value'            => '{"backgroundColor":"", "selectedTemplate": "row1-of2","list": [{ imageUrl : "", link : {} },{ imageUrl : "", link : {} }], "selectedRubikCubeArray" : [] ,"diyHtml": "","customRubikCube": 4,"heightArray": ["74.25px","59px","48.83px","41.56px"],"imageGap": 0}',
            'sort'             => '10008',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'CUSTOM_MODULE',
            'title'            => '自定义模块',
            'type'             => 'SYSTEM',
            'controller'       => '',
            'value'            => '',
            'sort'             => '10009',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'POP_WINDOW',
            'title'            => '弹窗广告',
            'type'             => 'SYSTEM',
            'controller'       => 'PopWindow',
            'value'            => '{ "image_url":"","link":{}}',
            'sort'             => '10010',
            'support_diy_view' => '',
            'max_count'        => 1
        ],
        [
            'name'             => 'HORZ_LINE',
            'title'            => '辅助线',
            'type'             => 'SYSTEM',
            'controller'       => 'HorzLine',
            'value'            => '{paddingTop:0, color : "#e5e5e5", padding : "no-padding", borderStyle : "solid" }',
            'sort'             => '10011',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'HORZ_BLANK',
            'title'            => '辅助空白',
            'type'             => 'SYSTEM',
            'controller'       => 'HorzBlank',
            'value'            => '{ height : 10, backgroundColor : "" }',
            'sort'             => '10012',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'VIDEO',
            'title'            => '视频',
            'type'             => 'SYSTEM',
            'controller'       => '',
            'value'            => '',
            'sort'             => '10013',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'VOICE',
            'title'            => '语音',
            'type'             => 'SYSTEM',
            'controller'       => '',
            'value'            => '',
            'sort'             => '10014',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'GOODS_LIST',
            'title'            => '商品列表',
            'type'             => 'SYSTEM',
            'controller'       => 'GoodsList',
            'value'            => '{ "sources" : "default", "categoryId" : 0, "goodsCount" : "6", "goodsId": [], "style": 1, "backgroundColor": "", "paddingUpDown": 0, "paddingLeftRight": 0, "isShowCart": 0, "cartStyle": 1, "isShowGoodName": 1, "isShowMarketPrice": 1, "isShowGoodSaleNum": 1, "isShowGoodSubTitle": 0 }',
            'sort'             => '10016',
            'support_diy_view' => '',
            'max_count'        => 0
        ],
        [
            'name'             => 'SHOP_INFO',
            'title'            => '店铺信息',
            'type'             => 'SYSTEM',
            'controller'       => 'ShopInfo',
            'value'            => '{ "color" : "#333333" }',
            'sort'             => '10017',
            'support_diy_view' => 'DIY_VIEW_SHOP',
            'max_count'        => 1
        ],
        [
            'name'             => 'RANK_LIST',
            'title'            => '排行榜',
            'type'             => 'SYSTEM',
            'controller'       => 'RankList',
            'value'            => '{ "sources" : "category", goodsId : "", "categoryId" : 0, "categoryLevel" : 1, "goodsCount" : "6" }',
            'sort'             => '10018',
            'support_diy_view' => 'DIY_VIEW_SHOP',
            'max_count'        => 1
        ],
        [
            'name'             => 'SHOP_SEARCH',
            'title'            => '店内搜索',
            'type'             => 'SYSTEM',
            'controller'       => 'ShopSearch',
            'value'            => '{}',
            'sort'             => '10019',
            'support_diy_view' => 'DIY_VIEW_SHOP',
            'max_count'        => 1
        ],
        [
            'name'             => 'SHOP_STORE',
            'title'            => '门店',
            'type'             => 'SYSTEM',
            'controller'       => 'ShopStore',
            'value'            => '{}',
            'sort'             => '10020',
            'support_diy_view' => 'DIY_VIEW_SHOP',
            'max_count'        => 1
        ],
        [
            'name'             => 'GOODS_CATEGORY',
            'title'            => '商品分类',
            'type'             => 'SYSTEM',
            'controller'       => 'GoodsCategory',
            'value'            => '{"level":"1","template":"1"}',
            'sort'             => '10021',
            'support_diy_view' => '',
            'max_count'        => 1
        ],
        [
            'name'             => 'TOP_CATEGORY',
            'title'            => '顶部分类条',
            'type'             => 'SYSTEM',
            'controller'       => 'TopCategory',
            'value'            => '{"title":"首页","selectColor":"#FF4544","nsSelectColor":"#333333",backgroundColor : "",}',
            'sort'             => '10022',
            'support_diy_view' => 'DIYVIEW_INDEX',
            'max_count'        => 1
        ],
        [
            'name' => 'FLOAT_BTN',
            'title' => '浮动按钮',
            'type' => 'SYSTEM',
            'controller' => 'FloatBtn',
            'value' => '{ "textColor": "#ffffff", "backgroundColor": "", subTitle : "", "list": [{"imageUrl": "","title": "","link": {}}]}',
            'sort' => '10022',
            'support_diy_view' => '',
            'max_count' => 1
        ]
    ],
    'link'           => [
        [
            'name'    => 'INDEX',
            'title'   => '主页',
            'wap_url' => '/pages/index/index/index',
            'web_url' => ''
        ],
        [
            'name'    => 'GOODS_CATEGORY',
            'title'   => '商品分类',
            'wap_url' => '/pages/goods/category/category',
            'web_url' => '',
            'support_diy_view' => 'DIY_VIEW_INDEX',
        ],
        [
            'name'    => 'GOODS_CART',
            'title'   => '购物车',
            'wap_url' => '/pages/goods/cart/cart',
            'web_url' => '',
        ],
        [
            'name'    => 'LOGIN',
            'title'   => '登录',
            'wap_url' => '/pages/login/login/login',
            'web_url' => ''
        ],
        [
            'name'    => 'REGISTER',
            'title'   => '注册',
            'wap_url' => '/pages/login/register/register',
            'web_url' => ''
        ],
        [
            'name'    => 'MEMBER_INDEX',
            'title'   => '会员中心',
            'wap_url' => '/pages/member/index/index',
            'web_url' => '',
        ],
        [
            'name'    => 'NOTICE_LIST',
            'title'   => '公告列表',
            'wap_url' => '/otherpages/notice/list/list',
            'web_url' => '',
            'support_diy_view' => 'DIY_VIEW_INDEX',
        ],
        [
            'name'    => 'HELP_LIST',
            'title'   => '帮助中心',
            'wap_url' => '/otherpages/help/list/list',
            'web_url' => '',
            'support_diy_view' => 'DIY_VIEW_INDEX',
        ],
        [
            'name'    => 'BRAND_LIST',
            'title'   => '品牌专区',
            'wap_url' => '/otherpages/goods/brand/brand',
            'web_url' => '',
            'support_diy_view' => 'DIY_VIEW_INDEX',
        ],
        [
            'name'    => 'POINT_INDEX',
            'title'   => '积分商城',
            'wap_url' => '/promotionpages/point/list/list',
            'web_url' => '',
            'support_diy_view' => 'DIY_VIEW_INDEX',
        ],
        [
            'name'    => 'COUPON_LIST',
            'title'   => '领券中心',
            'wap_url' => '/otherpages/goods/coupon/coupon',
            'web_url' => '',
            'support_diy_view' => 'DIY_VIEW_INDEX',
        ],
        [
            'name'    => 'TOPIC_LIST',
            'title'   => '专题活动',
            'wap_url' => '/promotionpages/topics/list/list',
            'web_url' => '',
            'support_diy_view' => 'DIY_VIEW_INDEX',
        ],
        [
            'name'             => 'SHOP_INDEX',
            'title'            => '店铺首页',
            'wap_url'          => '/otherpages/shop/index/index',
            'web_url'          => '',
            'support_diy_view' => 'DIY_VIEW_SHOP',
        ],
        [
            'name'             => 'SHOP_INTRODUCE',
            'title'            => '店铺介绍',
            'wap_url'          => '/otherpages/shop/introduce/introduce',
            'web_url'          => '',
            'support_diy_view' => 'DIY_VIEW_SHOP',
        ],
        [
            'name'             => 'SHOP_LIST',
            'title'            => '店铺商品列表',
            'wap_url'          => '/otherpages/shop/list/list',
            'web_url'          => '',
            'support_diy_view' => 'DIY_VIEW_SHOP',
        ],
        [
            'name'             => 'SHOP_CATEGORY',
            'title'            => '店铺商品分类',
            'wap_url'          => '/otherpages/shop/category/category',
            'web_url'          => '',
            'support_diy_view' => 'DIY_VIEW_SHOP',
        ],
        [
            'name'             => 'SHOP_LIST',
            'title'            => '店铺街',
            'wap_url'          => '/otherpages/shop/street/street',
            'web_url'          => '',
            'support_diy_view' => '',
        ],

    ],
    'link_construct' => [
        [
            'name'       => 'MALL_PAGE',
            'title'      => '商城链接',
            'parent'     => '',
            'wap_url'    => '',
            'web_url'    => '',
            'sort'       => 1,
            'child_list' => [
                ['name' => 'MALL_PAGE', 'title' => '商城页面', 'parent' => '', 'wap_url' => '', 'web_url' => '', 'sort' => 0, 'child_list' => []],
                ['name' => 'MIC_PAGE', 'title' => '微页面', 'parent' => '', 'wap_url' => '/pages/index/index/index', 'web_url' => '', 'sort' => 0, 'child_list' => []],
                ['name' => 'ADDON_PAGE', 'title' => '插件页面', 'parent' => '', 'wap_url' => '/pages/index/index/index', 'web_url' => '', 'sort' => 0, 'child_list' => []],
            ]
        ],
        [
            'name'       => 'COMMODITY',
            'title'      => '商品链接',
            'parent'     => '',
            'wap_url'    => '',
            'web_url'    => '',
            'sort'       => 2,
            'child_list' => [
                ['name' => 'ALL_GOODS', 'title' => '全部商品', 'parent' => '', 'wap_url' => '', 'web_url' => '', 'child_list' => []],
                ['name' => 'PINTUAN_GOODS', 'title' => '拼团商品', 'parent' => '', 'wap_url' => '', 'web_url' => '', 'child_list' => []],
                ['name' => 'DISTRIBUTION_GOODS', 'title' => '分销商品', 'parent' => '', 'wap_url' => '', 'web_url' => '', 'child_list' => []],
                ['name' => 'GROUPBUY_GOODS', 'title' => '团购商品', 'parent' => '', 'wap_url' => '', 'web_url' => '', 'child_list' => []],
                ['name' => 'BARGAIN_GOODS', 'title' => '砍价商品', 'parent' => '', 'wap_url' => '', 'web_url' => '', 'child_list' => []],
            ]
        ],
        [
            'name'       => 'GAME_LINK',
            'title'      => '游戏链接',
            'parent'     => '',
            'wap_url'    => '',
            'web_url'    => '',
            'sort'       => 2,
            'child_list' => []
        ],
        [
            'name'       => 'CUSTOM_LINK',
            'title'      => '自定义链接',
            'parent'     => '',
            'wap_url'    => '',
            'web_url'    => '',
            'sort'       => 3,
            'child_list' => []
        ],
    ]
];