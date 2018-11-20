<?php
$HjxZhangQY = [];
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/teacher/Teacher/getCompositionList',
    //接口名称
    'name'=>'教师端-》作文批改-》列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'reviewstatus':'1','studentname':'张三','pagenum':'1'}",
    //提交参数
    'tip'=>"{'reviewstatus':'0:未批阅 1：我的批阅','studentname':'学生名称','pagenum':'1'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data': {
                'composition': [
                        {
                                'id': '编号',
                                'nickname': '学生名称',
                                'title': '标题',
                                'type': '作文类型(1作文, 2日记)',
                                'reviewscore': '评分'
                        }
                ],
                'pageinfo': {
                        'pagesize': '20',
                        'pagenum': '1',
                        'total': 3
                }
        },
        'info': '操作成功'
}",
);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/teacher/Teacher/getCompositionData',
    //接口名称
    'name'=>'教师端-》作文批改-》获取批阅数据',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1'}",
    //提交参数
    'tip'=>"{'compositionid':'作文id'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data': [
                {
                        'id':'id'
                        'title': '标题',
                        'addtime': '添加时间',
                        'nickname': '学生名称',
                        'content': '作文内容',
                        'imgurl': 'http://img.zcool.cn/community/0125fd5770dfa50000018c1b486f15.jpg@1280w_1l_2o_100sh.jpg'
                }
        ],
        'info': '操作成功'
}",
);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/teacher/Teacher/checksCompositionData',
    //接口名称
    'name'=>'教师端-》作文批改-》检测是否有人批阅作文',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1'}",
    //提交参数
    'tip'=>"{'compositionid':'作文id'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data': [],
        'info': '操作成功'
}",
);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/teacher/Teacher/reviewComposition',
    //接口名称
    'name'=>'教师端-》作文批改-》批阅',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1','reviewscore':'5','commentcontent':'文章开头简而得当，通过环境描写来衬托人物心情，十分艺术化。'}",
    //提交参数
    'tip'=>"{'compositionid':'作文id','reviewscore':'评分','commentcontent':'内容'}",
    //返回实例
    'returns'=>"",
);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/teacher/Teacher/UpdateReviewComposition',
    //接口名称
    'name'=>'教师端-》作文批改-》修改批阅',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1','reviewscore':'5','commentcontent':'文章开头简而得当，通过环境描写来衬托人物心情，十分艺术化。'}",
    //提交参数
    'tip'=>"{'compositionid':'作文id','reviewscore':'评分','commentcontent':'内容'}",
    //返回实例
    'returns'=>"",
);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/teacher/Teacher/seeReviewComposition',
    //接口名称
    'name'=>'教师端-》作文批改-》我的批阅 - 查看',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1'}",
    //提交参数
    'tip'=>"{'compositionid':'作文id'}",
    //返回实例
    'returns'=>"",
);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/teacher/Teacher/compositionRegresses',
    //接口名称
    'name'=>'教师端-》作文批改-》修改批阅状态',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1'}",
    //提交参数
    'tip'=>"{'compositionid':'作文id'}",
    //返回实例
    'returns'=>"",
);
$HjxZhangQY[] = array(
		'url'=>'/apphjx/Homepage/sendMobileMsg',
		'name'=>'好迹星app-登陆-发送短信',
		'type'=>'post',
		'data'=>"{'mobile':18235102743,'prphone':'86'}",
		'tip'=>"{'mobile':'手机号','prphone':'手机号前缀'}",
		'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
	);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/apphjx/Composition/homePageList',
    //接口名称
    'name'=>'好迹星APP-》首页-》作文列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionstatus':'1','pagenum':'1'}",
    //提交参数
    'tip'=>"{'compositionstatus':'作文类型(1作文, 2日记)','pagenum':'1'}",
    //返回实例
    'returns'=>"{
        'code': '',
        'data': [
                {
                        'id': 1,
                        'title': '标题',
                        'imgurl': '图片',
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
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/apphjx/Composition/compositionDetail',
    //接口名称
    'name'=>'好迹星APP-》作文 - 作文详情',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1'}",
    //提交参数
    'tip'=>"{'compositionid':'作文id'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data': {
                'composition': [
                        {
                                'title': '我的妈妈',
                                'addtime': 1539655823,
                                'nickname': '小二郎',
                                'content': '在一个阳光明媚的早上',
                                'imgurl': 'http://img.zcool.cn/community/0125fd5770dfa50000018c1b486f15.jpg@1280w_1l_2o_100sh.jpg',
                                'imgtrajectory':'图片轨迹'
                        }
                ],
                'teacher': [
                        {
                                'nickname': '老师姓名new new',
                                'reviewscore': '5',
                                'commentcontent': '真棒',
                                'reviewtime': 1539659633,
                                'imageurl': 'http://51menke-1253417915.cosgz.myqcloud.com/logo/1/official/201808281744444917.jpg'
                        }
                ],
                'status': '0：无评价 1：有评价'
        },
        'info': '操作成功'
}",
);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/apphjx/Composition/seeComment',
    //接口名称
    'name'=>'好迹星APP-》作文-查看评论',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1'}",
    //提交参数
    'tip'=>"{'compositionid':'作文id'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data': [
                {
                        'nickname': '学生名称',
                        'imageurl': '学生头像',
                        'reviewscore': '评分',
                        'commentcontent': '评论',
                        'reviewtime': '时间戳'
                        'commentlabel':{
                            '1':[
                                    {
                                        'id':'标签id',
                                        'star':'星级',
                                        'content':'评论的内容',
                                        'checked':'0-未选中，1-选中'
                                    }
                                ]
                        }
                }
        ],
        'info': '操作成功'
}",
);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/apphjx/Composition/modifyAddComposition',
    //接口名称
    'name'=>'好迹星APP-》作文-学生添加、修改评论',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1','reviewscore':'5','commentcontent':'感谢老师的评价'}",
    //提交参数
    'tip'=>"{'compositionid':'作文id','reviewscore':'评分','commentcontent':'内容'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data': [],
        'info': '操作成功'
}",
);
$HjxZhangQY[] = array(
    //接口地址
    'url'=>'/apphjx/Composition/compositionModifyAdd',
    //接口名称
    'name'=>'好迹星APP-》作文-添加、修改',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'compositionid':'1','type':'1','title':'我的太阳','content':'我的太阳内容','imgurl':'图片1~图片2','label':'1~5~6'}",
    //提交参数
    'tip'=>"{'composition':'作业id','type':'作文类型（1：作文 2：日记）','title':'标题','content':'我的太阳内容','imgurl':'图片地址','label':'标签id'}",
    //返回实例
    'returns'=>"{
        'code': '0',
        'data': [],
        'info': '操作成功'
}",
);

