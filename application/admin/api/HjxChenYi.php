<?php
$HjxChenYi = [];
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/getStudentInfo',
    //接口名称
    'name'=>'好迹星App-》个人信息-》详情',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"",
    //提交参数
    'tip'=>"",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data': {
                'id':1,
                'imageurl':'',
                'usename':'大王',
                'nickname':'王大锤',
                'categoryid':'1',
                'school':'北大附中',
                'gradename':'测试分类',
                'school':'北大附中',
                'sex':0,
                'class':{
                    'code': '0',
                    'data':[
                        {
                            'id':5,
                            'name':'三年级'
                        },
                        {
                            'id':6,
                            'name':'四年级'
                        }
                        ], 
                        'info': '操作成功'
                    }
                },
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/updateAppuserInfo',
    //接口名称
    'name'=>'好迹星App-》个人信息-》修改',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'nickname':'小二郎'}",
    //提交参数
    'tip'=>"{'nickname':'昵称','imageurl':'头像地址','sex':'性别','shchool':'学校名','categoryid':'年级id','class':'班级名','equipment':'设备名'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data': '',
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/getClassInfo',
    //接口名称
    'name'=>'好迹星App-》班级信息-》列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"",
    //提交参数
    'tip'=>"",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data':[
            {
                'id':5,
                'name':'三年级'
            },
            {
                'id':6,
                'name':'四年级'
            }
        ], 
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/getLabelInfo',
    //接口名称
    'name'=>'好迹星App-》标签信息-》列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"",
    //提交参数
    'tip'=>"",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data':[
            {
                'id':1,
                'lablename':'风景'
            },
            {
                'id':2,
                'lablename':'爱情'
            }
        ], 
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/createLabelInfo',
    //接口名称
    'name'=>'好迹星App-》标签信息-》新增',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'label':'小说'}",
    //提交参数
    'tip'=>"{'label':'小说:标签名'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data':1,
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/deleteLabelInfo',
    //接口名称
    'name'=>'好迹星App-》标签信息-》删除',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'labelid':'4'}",
    //提交参数
    'tip'=>"{'labelid':'4:标签id'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data':1,
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/updateLabelInfo',
    //接口名称
    'name'=>'好迹星App-》标签信息-》修改',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'labelid':'4','label':'亲情'}",
    //提交参数
    'tip'=>"{'labelid':'4:标签id','label':'心情:标签名'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data':1,
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/searchInfo',
    //接口名称
    'name'=>'好迹星App-》首页搜索-》跳转',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"",
    //提交参数
    'tip'=>"",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data':[
            {
                'id':1,
                'lablename':'风景'
            },
            {
                'id':2,
                'lablename':'爱情'
            }
        ], 
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/searchLabelInfo',
    //接口名称
    'name'=>'好迹星App-》首页搜索-》便签搜索',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'labelid':1,'pagenum':'1'}",
    //提交参数
    'tip'=>"{'labelid:1标签id','pagenum':'1'}",
    //返回实例
    'returns'=>"{
        'code': '',
        'data': [
                {
                        'id': 1,
                        'title': '标题',
                        'content': '内容',
                        'addtime': '时间',
                        'lablename': [
                                {
                                        'lablename': '标签'
                                },
                                {
                                        'lablename': '标签'
                                },
                                {
                                        'lablename': '标签'
                                }
                        ],
                        'status': '状态（1：未提交 2：未批阅 3：已批阅）'
                }
        ],
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/searchArticleInfo',
    //接口名称
    'name'=>'好迹星App-》首页搜索-》文章标题关键字搜索',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'keywords':'北京','pagenum':'1'}",
    //提交参数
    'tip'=>"{'keywords:关键词','pagenum':'1'}",
    //返回实例
    'returns'=>"{
        'code': '',
        'data': [
                {
                        'id': 1,
                        'title': '标题',
                        'content': '内容',
                        'addtime': '时间',
                        'lablename': [
                                {
                                        'lablename': '标签'
                                },
                                {
                                        'lablename': '标签'
                                },
                                {
                                        'lablename': '标签'
                                }
                        ],
                        'status': '状态（1：未提交 2：未批阅 3：已批阅）'
                }
        ],
        'info': '操作成功'
}",
);
$HjxChenYi[] = array(
    //接口地址
    'url'=>'/apphjx/User/getEquipmentInfo',
    //接口名称
    'name'=>'好迹星App-》设置-》设备名称',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"",
    //提交参数
    'tip'=>"",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data':[
            {
                'equipment':'设备名称',
            }
        ], 
        'info': '操作成功'
}",
);