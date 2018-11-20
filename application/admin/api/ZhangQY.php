<?php
$ZhangQY = [];
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/curriculumCategoryList',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-分类列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'categoryname':'哈哈','pagenum':1}",
    //提交参数
    'tip'=>"{'categoryname':'课程名称','pagenum':'1'}",
    //返回实例
    'returns'=>"{
        'code': '状态码 0:成功 其他:异常',
        'data': {
                'curriculumlist': [
                        {
                                'id': '分类编号',
                                'coursename': '分类名称名称'
                                'categorysort': '排序名称'
                        }
                ],
                'pageinfo': {
                        'pagesize': '每页显示条数',
                        'pagenum': '当前页',
                        'total': '当前页显示条数'
                }
        },
        'info': 'OK'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/categoryAdd',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-分类列表-添加分类',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'categoryname':'我爱加班'}",
    //提交参数
    'tip'=>"{'categoryname':'课程分类名称'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/categorydel',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-分类列表-删除分类',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'categoryid':'1'}",
    //提交参数
    'tip'=>"{'categoryid':'分类id'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/courseAdd',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-分类列表-添加课程',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'categoryid':'2','courseids':'1,5,10'}",
    //提交参数
    'tip'=>"{'categoryid':'分类id','courseids':'添加课程ids'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/courseDel',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-分类列表-删除课程',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'categoryid':'2','courseids':'25'}",
    //提交参数
    'tip'=>"{'categoryid':'分类id','courseids':'删除的课程id'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/CategoryRecoSort',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-分类列表-上移、下移',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'categoryid1':'1','categoryid2':'2'}",
    //提交参数
    'tip'=>"{'categoryid1':'交换id1','categoryid1':'交换id2'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/CategoryRecoSee',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-分类列表-查看',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'categoryid':'1'}",
    //提交参数
    'tip'=>"{'categoryid':'分类id'}",
    //返回实例
    'returns'=>"{
        'code': 0,
        'data': [
                {
                        'id': '课程编号',
                        'coursename': '课程名称',
                }
        ],
        'info': '操作成功'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/curriculumRecoSort',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-分类列表-查看-上移、下移',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'curriculumid1':'2','curriculumid2':'5'}",
    //提交参数
    'tip'=>"{'curriculumid1':'交换id1','curriculumid2':'交换id2'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/curriculumRecoList',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-课程展示',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'coursename':'哈哈','pagenum':1,'recommend':0}",
    //提交参数
    'tip'=>"{'coursename':'课程名称','pagenum':'请求页数','recommend':'默认值为0，0:获取未推荐数据 1:获取推荐数据'}",
        //返回实例
        'returns'=>"{
        'code': '状态码 0:成功 其他:异常',
        'data': {
                'curriculumlist': [
                        {
                                'id': '课程编号',
                                'coursename': '课程名称'
                        }
                ],
                'pageinfo': {
                        'pagesize': '每页显示条数',
                        'pagenum': '当前页',
                        'total': '当前页显示条数'
                }
        },
        'info': 'OK'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/curriculumRecoAdd',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-课程添加',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'curriculumid': '3,41,39'}",
    //提交参数
    'tip'=>"{'curriculumid':[课程id,课程id,课程id']s",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/teacherRecoList',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-老师展示',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'teachername':'王大锤','pagenum':1,'recommend':0}",
    //提交参数
    'tip'=>"{'teachername':'老师名称','pagenum':'请求页数','recommend':'默认值为0，0:获取未推荐数据 1:获取推荐数据'}",
    //返回实例
    'returns'=>"{
        'code': '状态码 0:成功 其他:异常',
        'data': {
                'curriculumlist': [
                        {
                                'id': '课程编号',
                                'coursename': '课程名称'
                        }
                ],
                'pageinfo': {
                        'pagesize': '每页显示条数',
                        'pagenum': '当前页',
                        'total': '当前页显示条数'
                }
        },
        'info': 'OK'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/teacherRecoDel',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-老师删除',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'teacherid':'1,2,3'}",
    //提交参数
    'tip'=>"{'teacherid':'老师id,老师id,老师id'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/teacherRecoAdd',
    //接口名称
    'name'=>'机构后台-》促销-》课程推荐-老师添加',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'teacherid':'1,2,3'}",
    //提交参数
    'tip'=>"{'teacherid':'老师id,老师id,老师id'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/teacherRecoSort',
    //接口名称
    'name'=>'机构后台-》促销-》老师推荐-老师上移、下移',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'teacherid1':'2','teacherid2':'98'}",
    //提交参数
    'tip'=>"{'teacherid1':'交换id1','teacherid2':'交换id2'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/HomepageAdsDel',
    //接口名称
    'name'=>'机构后台-》促销-》首页广告删除',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'adsid':'11'}",
    //提交参数
    'tip'=>"{'ads':'广告id'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/HomepageAdsList',
    //接口名称
    'name'=>'机构后台-》促销-》首页广告列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'wu':'无请求参数'}",
    //提交参数
    'tip'=>"{'wu':'无请求参数'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': [
                {
                        'id': '编号',
                        'remark': '广告名称',
                        'urltype': '跳转链接类型（1：课程名称 2：老师名称 3：url）',
                        'url': 'url地址',
                        'teachername': '老师名称',
                        'coursename': '课程名称'
                }
        ],
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/HomepageAdsUpload',
    //接口名称
    'name'=>'机构后台-》促销-》首页广告添加',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'adsid':'1','remark':'全场9块9','imagepath':'www.baidu.com','urltype':'1','teacherid':'1','courseid':'1','url':'www.baiud.com/img.jpg'}",
    //提交参数
    'tip'=>"{'adsid':'广告id','remark':'图片名称','imagepath':'上传图片地址','urltype':'跳转类型','teacherid':'老师id','courseid':'课程id','url':'跳转地址'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/HomeCourseList',
    //接口名称
    'name'=>'机构后台-》促销-》首页广告添加-课程、老师列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'listtype':'1','name':'王大锤','page_num':'1'}",
    //提交参数
    'tip'=>"{'listtype':'请求列表类型（1：课程列表 2：老师列表）','name':'老师或者课程名称','page_num':'请求页数，默认请求页数为0'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/HomepageAdsEdit',
    //接口名称
    'name'=>'机构后台-》促销-》首页广告编辑',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{
                'adsid': 1,
                'remark': '困',
                'imagepath': 'www.baidu.com',
                'urltype': 1,
                'teacherid': 1,
                'courseid': 1,
                'url': 'www.sina.com'
        }",
    //提交参数
    'tip'=>"{
                'adsid': '广告id',
                'remark': '备注',
                'imagepath': '图片地址',
                'urltype': '跳转 类型（1：课程id 2：老师id 3：其他）',
                'teacherid': '老师id',
                'courseid': '课程id',
                'url': '跳转url'
        }",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/GetHomepageAds',
    //接口名称
    'name'=>'机构后台-》促销-》首页广告编辑-数据获取',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'adsid':'6'}",
    //提交参数
    'tip'=>"{'adsid':'编辑的数据id'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/HomeAdsExpress',
    //接口名称
    'name'=>'机构后台-》促销-》首页广告编辑-课程、老师标识',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'expresstype':'1','express':'6','pagenum':'2','name':'名称'}",
    //提交参数
    'tip'=>"{'expresstype':'标识类型（1：课程标识 2：老师标识）','express':'标识id','pagenum':'请求页','name':'搜索名称'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/TypeProList',
    //接口名称
    'name'=>'机构后台-》促销-》分类推荐列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'recommend':'0'}",
    //提交参数
    'tip'=>"{'recommend':'0:获取分类未推荐数据 1:获取分类推荐数据 '}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/TypeProAdd',
    //接口名称
    'name'=>'机构后台-》促销-》分类推荐添加',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'typeid':'1,5,9'}",
    //提交参数
    'tip'=>"{'typeid':'分类id,分类id,分类id'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/TypeProDel',
    //接口名称
    'name'=>'机构后台-》促销-》分类推荐删除',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'typeid':'1'}",
    //提交参数
    'tip'=>"{'typeid':'分类id'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/FreeCollectionList',
    //接口名称
    'name'=>'机构后台-》促销-》免费领取',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'start_time':'1536211539','end_time':'1536319539','page_num':'1'}",
    //提交参数
    'tip'=>"{'start_time':'开始时间','end_time':'结束时间','page_num':'请求页'}",
    //返回实例
    'returns'=>"{
        'id': '序号',
        'mobile': '手机号',
        'receivetime': '领取时间',
        'prphone': '区号',
        'name':'昵称'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetProList',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'setname':'学生优享套餐A','pagenum':1}",
    //提交参数
    'tip'=>"{'setname':'学生优享套餐A','pagenum':1}",
    //返回实例
    'returns'=>"{
        'code': 0,
        'data': {
                'setlist': [
                        {
                                'id': '序号',
                                'setmeal': '套餐名称',
                                'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                                'threshold': '使用门槛 0 无限制 其他代表满够金额',
                                'setprice': '价格',
                                'efftype': 1,
                                'effstarttime': '开始时间',
                                'effendtime': '结束时间',
                                'efftime': 0,
                                'shelf': '0:下架 1:上架',
                                'efftype': '有效期',
                                'expiry': '状态'
                        }
                ],
                'pageinfo': {
                        'pagesize': '20',
                        'pagenum': '1',
                        'total': 3
                }
        },
        'info': 'OK'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetListShelf',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表-上下架修改',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'setid':'1','shelf':0}",
    //提交参数
    'tip'=>"{'setid':'套餐id','shelf':'0:下架 1:上架'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetDetailedList',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表-查看-套餐明细',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'setid':'3'}",
    //提交参数
    'tip'=>"{'setid':'套餐id'}",
    //返回实例
    'returns'=>"{
        'code': 0,
        'data': {
                'bug': [
                        {
                                'threshold': '使用门槛',
                                'trialtype': '可使用课程',
                                'overdue': '状态',
                                'bughour': '课时',
                                'bug': '已购买',
                                'suse': '已使用',
                                'nouse': '未使用'
                        }
                ],
                'give': [
                        {
                                'threshold': '使用门槛',
                                'trialtype': '可使用课程',
                                'overdue': '状态',
                                'bughour': '课时',
                                'bug': '已购买',
                                'suse': '已使用',
                                'nouse': '未使用'
                        }
                ]
        },
        'info': '操作成功'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetDataList',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表-查看-数据列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'usestatus':'0','pagenum':'1','setid':'206'}",
    //提交参数
    'tip'=>"{'usestatus':' 0:未使用 1:使用 2:已过期','pagenum':'请求页数','setid':'套餐id'}",
    //返回实例
    'returns'=>"{
        'code': 0,
        'data': {
                'setdata': [
                        {
                                'id': '序号',
                                'nickname': '名称',
                                'mobile': '手机号',
                                'bugtime': '购买时间',
                                'usetime': '使用时间',
                                'ifuse': '是否使用(0:未使用 1:使用 2:已过期)'
                        }
                ],
                'pageinfo': {
                        'pagesize': '20',
                        'pagenum': '1',
                        'total': 3
                }
        },
        'info': 'OK'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetProDel',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表-删除',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'setid':'1'}",
    //提交参数
    'tip'=>"{'setid':'套餐id'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetAddCurriculum',
    //接口名称
    'name'=>'机构后台-》促销-》添加套餐-指定课程列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'coursename':'语文','classtypes':'1','pagenum':'3'}",
    //提交参数
    'tip'=>"{'coursename':'课程名称','classtypes':'1 录播课 2直播课','pagenum':'请求页数'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetCategory',
    //接口名称
    'name'=>'机构后台-》促销-》添加套餐-指定分类列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"",
    //提交参数
    'tip'=>"",
    //返回实例
    'returns'=>"",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetInsert',
    //接口名称
    'name'=>'机构后台-》促销-》添加套餐',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{
            'bughour': '50',
            'setmeal': '优惠套餐A',
            'setprice': '1200',
            'limitbuy': '0',
            'threshold': '0',
            'efftype': {
                 'status':'1',
                 'effstarttime': '1536310173',
                 'effendtime': '1536310173',
                 'efftime': '5',    
             },
            'trialtype':{
                  'status':'1',
                  'categoryids': '1,5,9',
                   'curriculumids': '12,46',
             },
            'content': '使用说明',
            'setimgpath': 'http://51menke-1253417915.cosgz.myqcloud.com//advertisement//1//official//201809071426486066.jpg',
            'givestatus': '0',
            'sendvideo': '2',
            'sendlive': '3',
            'giftthreshold': '0',
            'giftefftype': {
                   'status':'1',
                   'gifteffstarttime': '1536310173',
                   'gifteffendtime': '1536310173',
                   'giftefftime': '5',
            },
            'gifttrialtype': {
                   'status':'1',
                   'giftcategoryids': '12,46',
                   'giftcurriculumids': '12,45'
            }
            
    }",
    //提交参数
    'tip'=>"{
            'bughour': '课时',
            'setmeal': '套餐名称',
            'setprice': '套餐价格',
            'limitbuy': '限购次数(0:无限制 其他:限制次数)',
            'threshold': '使用门槛(0:无限制 其他:满金额使用)',
            'efftype': {
                 'status':'有效期状态（1：时间范围 2：固定天数）',
                 'effstarttime': '开始时间(时间戳)',
                 'effendtime': '结束时间(时间戳)',
                 'efftime': '固定天数',    
             },
            'trialtype': {
                  'status':'可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                  'categoryids': '课程分类ids',
                   'curriculumids': '指定课程ids',
             },
            'content': '使用说明',
            'setimgpath': '图片地址',
            'givestatus': '是否赠送课时（0：不赠送 1：赠送）',
            'sendvideo': '赠送课时（录播课）',
            'sendlive': '增送课时（直播课）',
            'giftthreshold': '使用门槛(0:无限制 其他:满金额使用)',
            'giftefftype': {
                   'status':'1',
                   'gifteffstarttime': '开始时间(时间戳)',
                   'gifteffendtime': '结束时间(时间戳)',
                   'giftefftime': '固定天数',
            },
            'gifttrialtype': {
                   'status':'可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                   'giftcategoryids': '课程分类ids',
                   'giftcurriculumids': '指定课程ids'
            }
            
    }",
    //返回实例
    'returns'=>" 'returns'=>'{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetUpdate',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表-编辑-获取编辑数据',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'setid':'1'}",
    //提交参数
    'tip'=>"{'setid':'套餐id'}",
    //返回实例
    'returns'=>"{
        'code': 0,
        'data': {
                '购买套餐': [
                        {
                                'bughour': '购买课时',
                                'setmeal': '套餐价格',
                                'setimgpath': '图片路径',
                                'limitbuy': '限购次数',
                                'setprice': '套餐价格',
                                'threshold': '使用门槛 0 无限制 其他代表满够金额',
                                'efftype': '有效期状态（1：时间范围 2：固定天数）',
                                'effendtime': '开始时间',
                                'effstarttime': '结束时间',
                                'efftime': '购买后到期天数',
                                'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                                'categoryids': '课程分类ids',
                                'curriculumids': '指定课程ids',
                                'content': '使用内容',
                                'givestatus': '是否赠送课时（0：不赠送 1：赠送）'
                        }
                ],
                '赠送套餐': [
                        {
                                'sendvideo': '赠送录播课（0：未赠送 其他：赠送录播s课时间',
                                'sendlive': '赠送直播课（0：未赠送 其他：赠送直播s课时间）',
                                'giftthreshold': '使用门槛 0 无限制 其他代表满够金额',
                                'giftefftype': '有效期状态（1：时间范围 2：固定天数）',
                                'gifteffstarttime': '有效期开始时间',
                                'gifteffendtime': '有效期结束时间',
                                'giftefftime': '购买后到期天数',
                                'gifttrialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                                'giftcategoryids': '课程分类ids',
                                'giftcurriculumids': '指定课程ids'
                        }
                ]
        },
        'info': '操作成功'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetUpdateCategory',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表-编辑-指定分类',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'categoryids':'1,5,7'}",
    //提交参数
    'tip'=>"{'categoryids':'1,5,7'}",
    //返回实例
    'returns'=>" 'returns'=>'{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetUpdateCurriculum',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表-编辑-指定课程',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'curriculumids':'1,5','coursename':'语文','classtypes':'1','pagenum':'3'}",
    //提交参数
    'tip'=>"{'curriculumids':'课程ids','coursename':'课程名称','classtypes':'1 录播课 2直播课','pagenum':'请求页数'}",
    //返回实例
    'returns'=>" 'returns'=>'{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/SetModify',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表-编辑',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{
            'id': '164',
            'bughour': '50',
            'setmeal': '优惠套餐A',
            'setprice': '1200',
            'limitbuy': '0',
            'threshold': '0',
            'efftype': {
                 'status':'1',
                 'effstarttime': '1536310173',
                 'effendtime': '1536310173',
                 'efftime': '5',    
             },
            'trialtype':{
                  'status':'1',
                  'categoryids': '1,5,9',
                   'curriculumids': '12,46',
             },
            'content': '使用说明',
            'setimgpath': 'http://51menke-1253417915.cosgz.myqcloud.com//advertisement//1//official//201809071426486066.jpg',
            'givestatus': '0',
            'sendvideo': '2',
            'sendlive': '3',
            'giftthreshold': '0',
            'giftefftype': {
                   'status':'1',
                   'gifteffstarttime': '1536310173',
                   'gifteffendtime': '1536310173',
                   'giftefftime': '5',
            },
            'gifttrialtype': {
                   'status':'1',
                   'giftcategoryids': '12,46',
                   'giftcurriculumids': '12,45'
            }
            
    }",
    //提交参数
    'tip'=>"",
    //返回实例
    'returns'=>"",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Promotion/CategoryTransformation',
    //接口名称
    'name'=>'机构后台-》促销-》套餐列表-分类转换',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'categoryids':'1,5'}",
    //提交参数
    'tip'=>"{'categoryids':'课程ids'}",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Educational/TransferClassList',
    //接口名称
    'name'=>'机构后台-》教务-》调班列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'status':'0','studentname':'王小锤','pagenum':'1'}",
    //提交参数
    'tip'=>"{'status':'0:获取未处理 1:获取已处理','studentname':'学生名称','pagenum':'请求页'}",
    //返回实例
    'returns'=>"{
        'code': 0,
        'data': {
                'classlist': [
                        {
                                'id': '序号',
                                'studentname': '学生名称',
                                'coursename': '课程',
                                'oldteacher': '原老师',
                                'newteacher': '新老师',
                                'oldclass': '原班级',
                                'newclass': '新班级',
                                'status':'1:已批准 2:已拒绝'
                        }
                ],
                'pageinfo': {
                        'pagesize': '20',
                        'pagenum': '1',
                        'total': 1
                }
        },
        'info': '操作成功'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Educational/TransferClassApply',
    //接口名称
    'name'=>'机构后台-》教务-》调班列表->同意、拒绝',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'status':'1','tranid':'47'}",
    //提交参数
    'tip'=>"{'status':'1同意 2:拒绝','tranid':'调班id'}",
    //返回实例
    'returns'=>"",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Educational/TransferLessonList',
    //接口名称
    'name'=>'机构后台-》教务-》调课列表',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'status':'0','studentname':'王小锤','pagenum':'1'}",
    //提交参数
    'tip'=>"{'status':'0:获取未处理 1:获取已处理','studentname':'学生名称','pagenum':'请求页'}",
    //返回实例
    'returns'=>"{
        'code': 0,
        'data': {
                'lesslist': [
                        {
                                'id': '序号',
                                'student_name': '学生名称',
                                'coursename': '课程',
                                'old_teacher': '原老师',
                                'new_teacher': '新老师',
                                'oldsort': '原课时排序',
                                'ondlesson': '原课时名称',
                                'newsort': '新课时排序',
                                'newlesson': '新课时名称',
                                'updatetime': '修改时间',
                                'applytime': '申请时间',                         
                                'old_class': '原班级',
                                'new_class': '新班级'
                        }
                ],
                'pageinfo': {
                        'pagesize': '20',
                        'pagenum': '1',
                        'total': 4
                }
        },
        'info': '操作成功'
}",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Educational/influenceClass',
    //接口名称
    'name'=>'机构后台-》教务-》调班列表->检测调班是否会对学生造成影响',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'tranid':'1'}",
    //提交参数
    'tip'=>"{'tranid':'调班id'}",
    //返回实例
    'returns'=>"",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Educational/TransferLessApply',
    //接口名称
    'name'=>'机构后台-》教务-》调课列表->同意、拒绝',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'status':'1','tranid':'1'}",
    //提交参数
    'tip'=>"{'status':'1同意 2:拒绝','tranid':'调课id'}",
    //返回实例
    'returns'=>"",
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/admin/Educational/testingLessApply',
    //接口名称
    'name'=>'机构后台-》教务-》调课列表->同意、拒绝-检测此课时是否已经开班',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"{'tranid':'1'}",
    //提交参数
    'tip'=>"{'tranid':'调课id'}",
    //返回实例
    'returns'=>"" ,
);
$ZhangQY[] = array(
    //接口地址
    'url'=>'/appstudent/Homepage/getSlideList',
    //接口名称
    'name'=>'APP学生端-》官网首页 -》轮播图',
    //提交类型
    'type'=>'POST',
    //提交实例
    'data'=>"",
    //提交参数
    'tip'=>"",
    //返回实例
    'returns'=>"{
        'code': '返回的查询标识，0为正常返回，其他为异常',
        'data': '最外层data为此次请求的返回数据',
        'info': '此次请求返回数据描述',
        'id': '轮播图片id',
        'remark': '图片备注',
        'imagepath': 'imagepath',
        'sortid': '顺序',
        'urltype': '跳转 类型（1：课程id 2：老师id 3：其他）',
        'teacherid': '老师id',
        'courseid': '课程id',
        'coursetype':'课程类型1录播2直播'
        'url': '其他'
}",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/HomePage/getRecommendCourser',
    'name'=>'APP学生端-官网首页-热门推荐',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'subhead': '课程副标题',
                            'imageurl': '课程图片',
                            'coursename': '课程名称',
                            'courseid': '课程id 用于跳转到课程详情',
                            'price': '课程最低价格',
                            'maxprice': '课程最大价格',
                            'giftdescribe': '赠品描述',
                            'classtypes': '1 录播课 2直播课',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/HomePage/getThreeCategroyList',
    'name'=>'APP学生端-官网首页-查询所有的三级分类',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
                    'code': '返回的查询标识，0为正常返回，其他为异常',
                    'data': '最外层data为此次请求的返回数据',
                    'info': '此次请求返回数据描述',
                    'category_id': '分类id',
                    'categoryname': '分类名称',
                    'rank': '等级',
                    'fatherid': '父级id'
                   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/HomePage/getThreeCourseList',
    'name'=>'APP学生端-官网首页-查询三级分类下的课程',
    'type'=>'post',
    'data'=>"{'categoryid':'8'}",
    'tip'=>"{'categoryid':'年级对应的分类集合'}",
    'returns'=>"{
                    'code': '返回的查询标识，0为正常返回，其他为异常',
                    'data': '最外层data为此次请求的返回数据',
                    'info': '此次请求返回数据描述',
                    'category_id': '分类id',
                    'categoryname': '分类名称',
                    'rank': '等级',
                    'fatherid': '父级id'
                   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/HomePage/getCategoryList',
    'name'=>'APP学生端-官网首页-查询中部分类及其课程',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'category_id': '分类id',
                            'categoryname': '分类名称',
                            'rank': '分类等级',
                            'fatherid': '父级id',
                            'sort': '排序',
                            'courselist': '分类下的课程list'
                            'courseid': '课程id',
                            'coursename': '课程名称',
                            'subhead': '课程副标题',
                            'imageurl': '课程图片',
                            'price': '课程价格',
                            'maxprice': '课程最大价格',
                            'giftdescribe': '课程描述',
                            'classtypes': '课程类型 1录播课2直播课',
                            'classnum': '课程数量'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/searchCourseOrTeacher',
    'name'=>'APP学生端-》官网首页 -》学生端-课程-课程或老师搜索',
    'type'=>'post',
    'data'=>"{'search':'关键字','searchtype':1,'pagenum':1}",
    'tip'=>"{'search':'需要搜索的关键字','searchtype':'1课程搜索2老师搜索','pagenum':'当前页数'}",
    'returns'=>"{
        'code': 0,
        'data': {
                'arr': [
                        {
                                'courseid': '课程id',
                                'coursename': '课程标题',
                                'subhead': '课程副标题',
                                'imageurl': 'http://talkcloud002.cn-gd.ufileos.com/talkcloud002_201809202056324513.jpg',
                                'price': '价格',
                                'classtypes': '开班类型 1 录播课 2直播课'
                        }
                ],
                'pageinfo': {
                        'pagesize': 20,
                        'pagenum': '1',
                        'total': 24
                }
        },
        'info': '操作成功'
}",
);
//$ZhangQY[] = array(
//    'url'=>'/appstudent/Homepage/getRecommendCourser',
//    'name'=>'APP学生端-》官网首页 -》热门推荐',
//    'type'=>'post',
//    'data'=>"",
//    'tip'=>"",
//    'returns'=>"{
//                    'code': '返回的查询标识，0为正常返回，其他为异常',
//                    'data': '最外层data为此次请求的返回数据',
//                    'info': '此次请求返回数据描述',
//                    'subhead': '课程副标题',
//                    'imageurl': '课程图片',
//                    'coursename': '课程名称',
//                    'courseid': '课程id 用于跳转到课程详情',
//                    'price': '课程最低价格',
//                    'maxprice': '课程最大价格',
//                    'giftdescribe': '赠品描述',
//                    'classtypes': '1 录播课 2直播课',
//}",
//);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/getRecommendTeacher',
    'name'=>'APP学生端-》机构首页-》名师推荐',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
                        'code': '返回的查询标识，0为正常返回，其他为异常',
                        'data': '最外层data为此次请求的返回数据',
                        'info': '此次请求返回数据描述',
                        'teachername': '老师名称',
                        'profile': '老师简介 暂不用此字段',
                        'classesnum': '暂不用此字段',
                        'imageurl': '暂不用此字段',
                        'recommend': '暂不用此字段',
                        'teacherid': '老师id',
                        'identphoto': '证件照片',
                        'slogan': '老师介绍语',
                        'student_num': '暂不用此字段'
}",
);
//$ZhangQY[] = array(
//    'url'=>'/appstudent/Teacherdetail/getTeacherCurriculum',
//    'name'=>'APP学生端-》官网首页-老师个人信息',
//    'type'=>'post',
//    'data'=>"{'teacherid':1}",
//    'tip'=>"{'teacherid':'老师id'}",
//    'returns'=>"{
//                            'code': '返回的查询标识，0为正常返回，其他为异常',
//                            'data': '最外层data为此次请求的返回数据',
//                            'info': '此次请求返回数据描述',
//                            'teacherid': '老师id',
//                            'imageurl': '老师头像',
//                            'prphone': '手机号前缀',
//                            'mobile': '手机号',
//                            'teachername': '真实姓名',
//                            'nickname': '昵称',
//                            'accountstatus': '暂不用',
//                            'addtime': '暂不用',
//                            'sex': '性别 0保密 1男 2女',
//                            'country': '国家',
//                            'province': '省',
//                            'city': '城市',
//                            'profile': '简介',
//                            'birth': '暂不用',
//                            'age': '年龄',
//                            'classnum': '开班数量',
//                            'student': '学生数量',
//                            'score': '评分',
//                            'lable': '老师标签List',
//                            'tagname': '老师名称',
//                            'is_collection':'如果是官方首页,需要此字段;是否收藏过该老师的标识',
//                            'organinfo':'如果是官方首页,需要此字段;机构详情list',
//                            'imageurl': '机构Logo',
//                            'organid': '机构id，用于查看机构详情',
//                            'organname': '机构名称',
//                            'summary': '机构简介',
//                            'phone': '机构电话',
//                            'email': '机构邮箱',
//                            'vip': '暂不用此字段'
//                           }",
//);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/addUserFavorCategory',
    'name'=>'APP学生端-》官网首页 -》选择分类',
    'type'=>'post',
    'data'=>"{'categoryid':1}",
    'tip'=>"{'categoryid':'分类id'}",
    'returns'=>"{
                'code': '返回的查询标识，0为正常返回，其他为异常',
                'data': '最外层data为此次请求的返回数据',
                'info': '此次请求返回数据描述',
           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Curriculumdetail/chooseAllList',
    'name'=>'APP学生端-课程详情-课程选择',
    'type'=>'post',
    'data'=>"{'courseid':186}",
    'tip'=>"{'courseid':1}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'addtime': 1538218638,
                            'curriculumid': '课程id',
                            'scheduid': '班级id',
                            'fullpeople': '人数',
                            'starttime': '日期',
                            'gradename': '班级名称',
							   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Curriculumdetail/getCurriculumDateList',
    'name'=>'APP学生端-首页-课程详情返回可选日期',
    'type'=>'post',
    'data'=>"{'courseid':1}",
    'tip'=>"{'courseid':1}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/getCurriculumInfo',
    'name'=>'APP学生端-》官网首页 -》课程详情',
    'type'=>'post',
    'data'=>"{'courseid':2,'teacherid':1,'date':'2018-07-06','fullpeople':4}",
    'tip'=>"{'courseid':'课程id','teacherid':'老师id 可选参数 默认不传','date':'日期','fullpeople':'可选默认0 4或者6'}",
    'returns'=>"{
                    'code': '返回的查询标识，0为正常返回，其他为异常',
                    'data': '最外层data为此次请求的返回数据',
                    'info': '此次请求返回数据描述',
                }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/getAllTeacherList',
    'name'=>'APP学生端-官网首页-名师堂',
    'type'=>'post',
    'data'=>"{'pagenum':1}",
    'tip'=>"{'pagenum':'分页页数'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'teacherid': '老师id',
                            'imageurl': '老师头像',
                            'prphone': '手机号前缀',
                            'mobile': '手机号',
                            'teachername': '真实姓名',
                            'nickname': '昵称',
                            'accountstatus': '暂不用',
                            'addtime': '暂不用',
                            'sex': '性别 0保密 1男 2女',
                            'country': '国家',
                            'province': '省',
                            'city': '城市',
                            'profile': '简介',
                            'birth': '暂不用',
                            'age': '年龄',
                            'classnum': '开班数量',
                            'student': '学生数量',
                            'score': '评分',
                            'lable': '老师标签List',
                            'tagname': '老师名称',
                            'is_collection':'如果是官方首页,需要此字段;是否收藏过该老师的标识',
                            'organinfo':'如果是官方首页,需要此字段;机构详情list',
                            'imageurl': '机构Logo',
                            'organid': '机构id，用于查看机构详情',
                            'organname': '机构名称',
                            'summary': '机构简介',
                            'phone': '机构电话',
                            'email': '机构邮箱',
                            'vip': '暂不用此字段'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Teacherdetail/getTeacherCurriculum',
    'name'=>'APP学生端-》官网首页 -》老师详情',
    'type'=>'post',
    'data'=>"{'teacherid':1}",
    'tip'=>"{'teacherid':'老师id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'teacherid': '老师id',
                            'imageurl': '老师头像',
                            'prphone': '手机号前缀',
                            'mobile': '手机号',
                            'teachername': '真实姓名',
                            'nickname': '昵称',
                            'accountstatus': '暂不用',
                            'addtime': '暂不用',
                            'sex': '性别 0保密 1男 2女',
                            'country': '国家',
                            'province': '省',
                            'city': '城市',
                            'profile': '简介',
                            'birth': '暂不用',
                            'age': '年龄',
                            'classnum': '开班数量',
                            'student': '学生数量',
                            'score': '评分',
                            'lable': '老师标签List',
                            'tagname': '老师名称',
                            'is_collection':'如果是官方首页,需要此字段;是否收藏过该老师的标识',
                            'organinfo':'如果是官方首页,需要此字段;机构详情list',
                            'imageurl': '机构Logo',
                            'organid': '机构id，用于查看机构详情',
                            'organname': '机构名称',
                            'summary': '机构简介',
                            'phone': '机构电话',
                            'email': '机构邮箱',
                            'vip': '暂不用此字段'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Teacherdetail/getTeacherClass',
    'name'=>'APP学生端-》官网首页 -》老师详情-Ta的班级',
    'type'=>'post',
    'data'=>"{'teacherid':1,'classtype':0}",
    'tip'=>"{'teacherid':'老师id','classtype':'课程类型  0免费课程1在售课程'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'subhead': '课程副标题',
								'imageurl': '课程图片',
								'coursename': '课程名称',
								'courseid': '课程id 用于跳转到课程详情',
								'price': '课程最低价格',
								'maxprice': '课程最大价格',
								'giftdescribe': '赠品描述',
								'classtypes': '1 录播课 2直播课',
							   }",
);
//我的-个人信息
$ZhangQY[] = array(
    'url'=>'/appstudent/User/getStudentInfo',
    'name'=>'APP学生端-》我的 - 个人信息',
    'type'=>'post',
    'data'=>"{'studentid':'1'}",
    'tip'=>"{'studentid':'学生id'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'usablemoney': '账户余额',
								'birth': '出生年月日',
								'id': '学生id',
								'username': '真实姓名',
								'prphone': '手机前缀',
								'mobile': '18235102743',
								'nickname': '昵称',
								'sex': '性别 0保密 1男 2女',
								'country': '国家',
								'province': '省',
								'city': '城市',
								'profile': '学生简介',
								'year': '出生年',
								'month': '出生月',
								'date': '出生日'
						   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/getStudentPaylog',
    'name'=>'APP学生端-》我的 - 账户余额-明细',
    'type'=>'post',
    'data'=>"{'studentid':'1','pagenum':1,'limit':1}",
    'tip'=>"{'studentid':'学生id','pagenum':'可选分页页数','limit':'每页页数'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'pageinfo': '分页list', 
								'pagesize':'每页多少条记录',
								'pagenum':'当前页码',
								'total':'总记录数',
								'coursename': '课程名称',
								'studentid': 1,
								'paynum': '金额',
								'paytype': '支付类型1余额支付2微信支付3支付宝支付4银联支付',
								'paystatus': '流水类型1：购买课程2：充值3:购买套餐',
								'paytime': '操作时间'
						   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/teacherCollectList',
    'name'=>'APP学生端-》我的 - 我的收藏-老师列表',
    'type'=>'post',
    'data'=>"{'studentid':5,'pagenum':1,'limit':10}",
    'tip'=>"{'studentid':'学生id','pagenum':'分页页数','limit':'每页记录数'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'imageurl': '老师照片',
								'profile': '老师简介',
								'teachername': '老师姓名',
								'teacherid': '老师id'
							   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/classCollectList',
    'name'=>'APP学生端-》我的 - 我的收藏-课程列表',
    'type'=>'post',
    'data'=>"{'studentid':5,'pagenum':1,'limit':10}",
    'tip'=>"{'studentid':'学生id','pagenum':'分页页数','limit':'每页记录数'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'curriculumid': '课程id',
								'organid': '机构id',
								'scheduid': '班级id',
								'teacherid': '老师id',
								'type': '班级类型',
								'totalprice': '课时总价',
								'gradename': '班级名称',
								'imageurl': '课程图片',
								'teachername': '老师名称'
							   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/teacherCollect',
    'name'=>'APP学生端-》我的 - 收藏老师',
    'type'=>'post',
    'data'=>"{'teacherid':1}",
    'tip'=>"{'teacherid':'老师id'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/cancelTeacherCollect',
    'name'=>'APP学生端-》我的 - 取消收藏老师',
    'type'=>'post',
    'data'=>"{'teacherid':1}",
    'tip'=>"{'teacherid':'老师id'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/classCollect',
    'name'=>'APP学生端-》我的 - 收藏课程班级',
    'type'=>'post',
    'data'=>"{'courseid':1}",
    'tip'=>"{'courseid':'课程id'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/cancelClassCollect',
    'name'=>'APP学生端-》我的 - 取消收藏课程班级',
    'type'=>'post',
    'data'=>"{'courseid':1}",
    'tip'=>"{'courseid':'课程id'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
);

$ZhangQY[] = array(
    'url'=>'/appstudent/User/getAddressList',
    'name'=>'APP学生端-》我的 - 查询收货地址',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'studentid': '学生id',
                            'pid': '省id',
                            'cityid': '城市id',
                            'areaid': '区域id',
                            'address': '详细地址',
                            'zipcode': '邮编',
                            'linkman': '联系人',
                            'mobile': '手机号',
                            'isdefault': '1:默认收货地址0否',
                            'pname': '省名称',
                            'cname': '城市名称',
                            'aname': '区域名称'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/addOrUpdateAddress',
    'name'=>'APP学生端-》我的 - 添加、修改收货地址',
    'type'=>'post',
    'data'=>"{'pid':7,'cityid':'1001','areaid':1002,'address':'红军营南路','linkman':'测试人','mobile':'18235102743','zipcode':'030024','isdefault':0,'id':1}",
    'tip'=>"{'pid':'省id','cityid':'城市id','areaid':'区域id','address':'地址','linkman':'联系人','mobile':'手机号','zipcode':'邮政编码','isdefault':'是否默认地址 1默认0否','id':'可选 修改信息是必选'}",
    'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/deleteAddress',
    'name'=>'App学生端-》个人信息-删除收货地址',
    'type'=>'post',
    'data'=>"{'id':1}",
    'tip'=>"{'id':'收货地址id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/packageUseList',
    'name'=>'APP学生端-》我的 - 我的套餐',
    'type'=>'post',
    'data'=>"{'status':'1','pagenum':1}",
    'tip'=>"{'status':'0待使用 1已使用 2已过期','pagenum':'分页页数'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'packageid': '套餐id',
                            'bughour': '购买课时数量',
                            'setmeal': '套餐名称',
                            'setimgpath': '套餐封面url',
                            'setprice': '套餐价格',
                            'limitbuy': '限购次数（0：无限购，其他限购次数）',
                            'threshold': '使用门槛 0 无限制 其他代表满够金额',
                            'efftype': '有效期状态（1：时间范围 2：固定天数）',
                            'effendtime': '时间范围维度下的开始时间',
                            'effstarttime': '时间范围维度下的结束时间',
                            'efftime': '固定天数 维度下的时间',
                            'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'content': '使用内容',
                            'givestatus': '是否赠送课时（0：不赠送 1：赠送) 如果为1 赠送套餐的详情需要展示',
                            'sendvideo': '赠送录播课（0：未赠送 其他：赠送录播s课时间',
                            'sendlive': '赠送直播课（0：未赠送 其他：赠送直播s课时间）',
                            'giftthreshold': '使用门槛 0 无限制 其他代表满够金额',
                            'giftefftype': '赠送课时的有效期状态（1：时间范围 2：固定天数）',
                            'gifteffstarttime': '时间范围维度下的开始时间',
                            'gifteffendtime':'时间范围维度下的结束时间',
                            'giftefftime': '购买后到期天数',
                            'gifttrialtype': '可使用课程（0：全部课程，1：指定分类，2：指定课程）',
                            'packagegiftid': '赠送课时的id',
                            'amount': '套餐金额',
                            'orderstatus': '套餐状态',
                            'studentid': '学生id',
                            'ordernum': '订单金额',
                            'type': '套餐类型 1套餐 2套餐赠送课时',
                            'surplus': '套餐剩余课时数量',
                            'total': '套餐总课时数量',
                           }",
);
//我的-签到首页
$ZhangQY[] = array(
    'url'=>'/appstudent/User/signinHome',
    'name'=>'APP学生端-》我的 - 签到首页',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
								'signinimage': '背景图',
								'signdata.total': '总签到次数',
								'signdata.consecutive': '连续签到天数',
								'knowledge': '知识数组',
								'qrcode': '二维码url',
                            }",
);
//签到
$ZhangQY[] = array(
    'url'=>'/appstudent/User/signin',
    'name'=>'APP学生端-》我的 - 签到',
    'type'=>'post',
    'data'=>"{'knowledgeid':1}",
    'tip'=>"{'knowledgeid':'知识id'}",
    'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//历史签到
$ZhangQY[] = array(
    'url'=>'/appstudent/User/mySigninList',
    'name'=>'APP学生端-》我的 - 历史签到',
    'type'=>'post',
    'data'=>"{'pagenum':1,'limit':10}",
    'tip'=>"{'pagenum':'分页页数','limit':'每页记录数'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',	
								'signdate': '签到时间',
								'content': '内容',
							   }",
);
//更换签到背景图列表
$ZhangQY[] = array(
    'url'=>'/appstudent/User/getSigninbgiList',
    'name'=>'APP学生端-》我的 - 更换背景-背景图列表',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/changeSigninImage',
    'name'=>'APP学生端-》我的 - 更换背景',
    'type'=>'post',
    'data'=>"{'signinimageid':1}",
    'tip'=>"{'signinimageid':'图片id'}",
    'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
);
//我的点评
$ZhangQY[] = array(
    'url'=>'/appstudent/User/myCommentList',
    'name'=>'APP学生端-》我的 - 课堂反馈-点评列表',
    'type'=>'post',
    'data'=>"{'studentid':5,'pagenum':1,'limit':10}",
    'tip'=>"{'studentid':'学生id','pagenum':'分页页数','limit':'每页记录数'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								
							   }",
);
//点评详情
$ZhangQY[] = array(
    'url'=>'/appstudent/User/myCommentMsg',
    'name'=>'APP学生端-》我的 - 课堂反馈-点评详情',
    'type'=>'post',
    'data'=>"{'id':5}",
    'tip'=>"{'id':'主键id'}",
    'returns'=>"{
                   	'code': '返回的查询标识，0为正常返回，其他为异常',
                     'data': '最外层data为此次请求的返回数据',
                     'info': '此次请求返回数据描述',
                     'periodname': '课时名称',
                     'nickname': '老师昵称',
                     'addtime': '点评时间',
                     'score': '评分',
                     'comment': '评论内容',
                   }",
);
//随机图形验证码
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/randomCode',
    'name'=>'APP学生端-》找回密码、注册-随机图形验证码',
    'type'=>'post',
    'data'=>"",
    'tip'=>"{}",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'codeimg': 'data:image/png;base64流文件',
                             'sessionid': '验证码的标识'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/sendMobileMsg',
    'name'=>'APP学生端-》找回密码、注册-发送短信',
    'type'=>'post',
    'data'=>"{'mobile':18235102743,'type':1,'prphone':'86'}",
    'tip'=>"{'mobile':'手机号','type':'业务类型 1找回密码 2 注册','prphone':'默认不传'}",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/register',
    'name'=>'APP学生端-首页-提交注册',
    'type'=>'post',
    'data'=>"{'mobile':18235102743,'code':'1234','password':'1234567','prphone':'86','key':'dsdsd'}",
    'tip'=>"{'mobile':'手机号','code':'短信验证码','password':'密码','prphone':'手机国家区号','key':'签名用的key'}",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/getUserFavorCategory',
    'name'=>'APP学生端-注册-查询感兴趣的分类',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'categoryid': '分类id',
	    						'name': '分类名称',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/addUserFavorCategory',
    'name'=>'APP学生端-注册-添加学生的分类',
    'type'=>'post',
    'data'=>"{'categoryid':1}",
    'tip'=>"{'categoryid':'分类id'}",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/getUserTag',
    'name'=>'APP学生端-注册-查询学生标签',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'fathername': '主标签',
	    						'childname': '子标签',
	    						'tagid': '主标签id',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/addUserTag',
    'name'=>'APP学生端-注册-添加学生标签',
    'type'=>'post',
    'data'=>"{'tagid':1,'childtags':'1,2'}",
    'tip'=>"{'tagid':'父标签id','childtags':'子标签id集合,逗号隔开'}",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/updatePass',
    'name'=>'APP学生端-》-找回密码-找回密码',
    'type'=>'post',
    'data'=>"{'mobile':18235102743,'code':'1234','newpass':'1234567'}",
    'tip'=>"{'mobile':'手机号','code':'短信验证码','newpass':'新密码'}",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/updateUserPass',
    'name'=>'APP学生端-》-个人资料-修改密码',
    'type'=>'post',
    'data'=>"{'mobile':18235102743,'code':'1234','newpass':'1234567'}",
    'tip'=>"{'mobile':'手机号','code':'短信验证码','newpass':'新密码'}",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/sendUpdateMobileMsg',
    'name'=>'APP学生端-个人资料-修改手机号,修改密码发送验证码',
    'type'=>'post',
    'data'=>"{'mobile':'18235102745','prphone':'86'}",
    'tip'=>"{'mobile':'手机号','prphone':'手机号国家号'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                       }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/updateMobile',
    'name'=>'APP学生端-个人资料-修改手机号',
    'type'=>'post',
    'data'=>"{'oldmobile':'18235102745','prphone':'86','newmobile':'18235102742','studentid':1,'code':'123456'}",
    'tip'=>"{'oldmobile':'原来手机号','prphone':'手机号国家区号','newmobile':'新手机号','studentid':'学生id','code':'验证码'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                       }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/updateStudentInfo',
    'name'=>'APP学生端-》-个人资料-编辑资料',
    'type'=>'post',
    'data'=>"{'imageurl':'url','nickname':'12','sex':0,'country':23,'province':13,'city':56,'birth':'2018-3-5','profile':'简介','studentid':'1'}",
    'tip'=>"{'imageurl':'学生头像链接',
				    			'nickname':'昵称',
				    			'sex':'性别id',
				    			'country':'国家id',
				    			'province':'省份id',
				    			'city':'城市id',
				    			'birth':'2018-3-5',
				    			'profile':'简介',
				    			'studentid':'学生用户id'
				    		}",
    'returns'=>"{
			    				        'code': '返回的查询标识,0为正常返回,其他为异常',
			    						'data': '最外层data为此次请求的返回数据',
			    						'info': '此次请求返回数据描述',
		                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/getHomeworkList',
    'name'=>'App学生端-我的作业-我的作业列表',
    'type'=>'post',
    'data'=>"{'pagenum':1,'status':0}",
    'tip'=>"{'pagenum':'分页页数','status':'0未完成 1已完成 2.已批阅'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'homeworkid': '作业编号',
                            'courseid': '课程Id',
                            'classid': '班级id',
                            'lessonid': '课节id',
                            'studentid': '学生id',
                            'submittime': '提交时间',
                            'score': '分数',
                            'coursename': '课程名称',
                            'gradename': '班级名称',
                            'periodname': '课节名称',
                            'teacherid': '老师id',
                            'endtime': '截止时间',
                            'teachername': '老师名称'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/searchHomework',
    'name'=>'APP学生端-我的作业-搜索',
    'type'=>'post',
    'data'=>"{'pagenum':1,'status':0,'search':'测试'}",
    'tip'=>"{'pagenum':'分页页数','status':'0未完成 1已完成 2.已批阅','search':'搜索名称'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'homeworkid': '作业编号',
                            'courseid': '课程Id',
                            'classid': '班级id',
                            'lessonid': '课节id',
                            'studentid': '学生id',
                            'submittime': '提交时间',
                            'score': '分数',
                            'coursename': '课程名称',
                            'gradename': '班级名称',
                            'periodname': '课节名称',
                            'teacherid': '老师id',
                            'endtime': '截止时间',
                            'teachername': '老师名称'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/getQuestionList',
    'name'=>'APP学生端-我的作业-写作业查询我的题库',
    'type'=>'post',
    'data'=>"{'lessonid':1095,'classid':1}",
    'tip'=>"{'lessonid':'班级的课时id','classid':'班级id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'subjectid': '对应题库id',
                            'type': '题型(1选择题，2多选题，3填空题，4作文)',
                            'options': '选择题的选项 当type等于1或2的时候用此字段',
                            'courseid': '课程id',
                            'periodid': '课程对应的课时id',
                            'name': '题目名称',
                            'imageurl': '图片url',
                            'analysis': '作文题的解析 用于type为2的',
                            'score': '分值',
                            'periodname': '课时名称',
                            'subject_count': '题目总数',
                            'periodname': '课程的课时名称',
                            'lessonsid': '班级对应的课时id',
                            'lessonsname': '班级对应的课时名称 试卷名称用这个',
                            'totalscore': 1
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/submitQuestions',
    'name'=>'APP学生端-我的作业-作业提交',
    'type'=>'post',
    'data'=>"{
'answers': [{'subjectid':1,'answer':'测试1'},{'subjectid':2,'answer':'测试2'}],'classid':1,'lessonid':1095,'homeworkid':1

}",
    'tip'=>"{
'answers': [{'subjectid':'题目id','answer':'答案'},{'subjectid':2,'answer':'测试2'}],'classid':'班级id','lessonid':'班级课时id','homeworkid':'作业id'

}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/showUpdateQuestions',
    'name'=>'APP学生端-我的作业-修改查询作业信息',
    'type'=>'post',
    'data'=>"{'lessonid':1095,'classid':1}",
    'tip'=>"{'lessonid':'班级的课时id','classid':'班级id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'subjectid': '对应题库id',
                            'type': '题型(1选择题，2多选题，3填空题，4作文)',
                            'options': '选择题的选项 当type等于1或2的时候用此字段',
                            'courseid': '课程id',
                            'periodid': '课程对应的课时id',
                            'name': '题目名称',
                            'imageurl': '图片url',
                            'analysis': '作文题的解析 用于type为2的',
                            'score': '分值',
                            'answers':'答案',
                            'periodname': '课时名称',
                            'subject_count': '题目总数',
                            'periodname': '课程的课时名称',
                            'lessonsid': '班级对应的课时id',
                            'lessonsname': '班级对应的课时名称 试卷名称用这个',
                            'totalscore': 1
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/updateQuestions',
    'name'=>'APP学生端-我的作业-作业修改提交',
    'type'=>'post',
    'data'=>"{
'answers': [{'subjectid':1,'answer':'测试1'},{'subjectid':2,'answer':'测试2'}],'classid':1,'lessonid':1095,'homeworkid':1

}",
    'tip'=>"{
'answers': [{'subjectid':'题目id','answer':'答案'},{'subjectid':2,'answer':'测试2'}],'classid':'班级id','lessonid':'班级课时id','homeworkid':'作业id'

}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/getCompleteQuestionList',
    'name'=>'APP学生端-我的作业-查询已完成的作业信息',
    'type'=>'post',
    'data'=>"{'lessonid':1095,'classid':1}",
    'tip'=>"{'lessonid':'班级的课时id','classid':'班级id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'subjectid': '对应题库id',
                            'type': '题型(1选择题，2多选题，3填空题，4作文)',
                            'options': '选择题的选项 当type等于1或2的时候用此字段',
                            'courseid': '课程id',
                            'periodid': '课程对应的课时id',
                            'name': '题目名称',
                            'imageurl': '图片url',
                            'analysis': '作文题的解析 用于type为2的',
                            'score': '分值',
                            'periodname': '课时名称',
                            'subject_count': '题目总数',
                            'periodname': '课程的课时名称',
                            'lessonsid': '班级对应的课时id',
                            'lessonsname': '班级对应的课时名称 试卷名称用这个',
                            'totalscore': 1
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/sendHomeworkMessage',
    'name'=>'APP学生端-作业-已完成作业发送消息提醒',
    'type'=>'post',
    'data'=>"{'homeworkid':1}",
    'tip'=>"{'homeworkid':'学生作业id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/getTopCategory',
    'name'=>'APP学生端-课程-展示一级分类',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
        'code': 0,
        'data': [
                {
                        'category_id': '分类id',
                        'categoryname': '分类名称',
                        'rank': '等级',
                        'fatherid': '父级id',
                        'sort': '排序',
                        'imgs': '分类图片',
                        'describe': '分类描述',
                        'icos': '分类图标'
                },
        ],
        'info': '操作成功'
}",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/getTopCategoryChild',
    'name'=>'APP学生端-课程-筛选',
    'type'=>'post',
    'data'=>"{'categoryid':1}",
    'tip'=>"{'categoryid':'一级分类id'}",
    'returns'=>"{
    'code': 0,
    'data': [
        {
            'category_id': '分类id',
            'categoryname': '分类名称',
            'rank': '分类等级',
            'fatherid': '父级id',
            'sort': '排序',
            'child': [
                {
                    'category_id': '分类id',
                    'categoryname': '分类名称',
                    'rank': '分类等级',
                    'fatherid': '父级id',
                    'sort': '排序',
                }
            ]
        },
    ],
    'info': '操作成功'
}",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/getFilterCourserList',
    'name'=>'APP学生端-课程-筛选分类下的课程',
    'type'=>'post',
    'data'=>"{'is_free':0,'category_id':'8','pagenum':'1'}",
    'tip'=>"{'is_free':'是否查询免费学。0:全部.1:免费','category_id':'可选默认不填类型:数字','pagenum':'分页页码'}",
    'returns'=>"{
        'code': 0,
        'data': {
                'schedulist': [
                        {
                                'courseid': '课程id',
                                'coursename': '课程名称',
                                'subhead': '课程副标题',
                                'imageurl': '课程图片',
                                'price': '课程最小价格',
                                'maxprice': '课程最大价格',
                                'giftdescribe': '课程描述',
                                'classtypes': '课程类型',
                                'classnum': '课程数量'
                        },
                ],
                'pageinfo': {
                        'pagesize': '每页条目数',
                        'pagenum': '当前页数',
                        'total': '共计条数'
                }
        },
        'info': '查询成功'
}",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/appstudentRecharge',
    'name'=>'App学生端-个人资料-充值',
    'type'=>'post',
    'data'=>"{'amount':'1.00','paytype':'2','source':1}",
    'tip'=>"{'amount':'订单金额','paytype':'支付类型 2:微信支付3支付宝4银联','source':'充值渠道1pc 2手机'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            '如果是支付宝支付': '跳转到支付宝页面',
                            '如果是微信支付': '会返回codeurl字段 用于支付扫码的url',
                          
                       }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Homepage/teacherRecommend',
    'name'=>'APP学生端-首页-名师堂',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"",
);
//我的课表
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/studentLessonsList',
    'name'=>'APP学生端-》课表 - 我的课表',
    'type'=>'post',
    'data'=>"{'status':0,'pagenum':1,'limit':20}",
    'tip'=>"{'status':'状态：默认0未上课，1已结束','pagenum':'页码','limit':'每页多少条记录'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'schedulingid': '排课id',
                                'intime': '2018-05-09',
                                'coursename': '课程名称',
                                'type': '班型',
                                'teacherid': '老师id',
                                'toteachid': '课节id',
                                'periodname': '课时名称',
                                'periodsort': '课节排序id',
                                'curriculumid': '课程id',
                                'classhour': '课节时长',
                                'lessonsid': '排班课时id',
                                'teachername': '老师名称',
                                'buttonstatus': '定义按钮状态  0 未开始 1进教室 2 去评价 3回放',
                                'starttime': '开始时间',
                                'endtime': '结束时间',
                                'imageurl': '课程封面图',
                                'pageinfo': '分页信息'
						   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/gotoComment',
    'name'=>'APP学生端-》课表 - 评价',
    'type'=>'post',
    'data'=>"{'curriculumid':1,'classtype':1,'studentid':1,'teacherid':1,'score':1,'schedulingid':1,'content':'评论呢','lessonsid':1,'toteachid':'课时id'}",
    'tip'=>"{'curriculumid':1,'classtype':'课程类型1录播课2直播课','studentid':'学生id','teacherid':'老师id','score':1,'schedulingid':'排课id 可选','content':'评论呢','lessonsid':'课节id','toteachid':'课时id,可选，直播课用'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                            }",
);
//已结束课时查看点评
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/getFeedback',
    'name'=>'APP学生端-》课表 - 查看点评',
    'type'=>'post',
    'data'=>"{'lessonsid':'','schedulingid':''}",
    'tip'=>"{'lessonsid':'课时id','schedulingid':'排班id'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'score': '评分',
                                'comment': '点评详情',
						   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/getPackageList',
    'name'=>'APP学生端-首页-查询我的套餐列表',
    'type'=>'post',
    'data'=>"{'pagenum':1}",
    'tip'=>"{'pagenum':'分页页数'}",
    'returns'=>"{
                             'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'packageid': '套餐id',
                            'bughour': '购买课时数量',
                            'setmeal': '套餐名称',
                            'setimgpath': '套餐封面url',
                            'setprice': '套餐价格',
                            'limitbuy': '限购次数（0：无限购，其他限购次数）',
                            'threshold': '使用门槛 0 无限制 其他代表满够金额',
                            'efftype': '有效期状态（1：时间范围 2：固定天数）',
                            'effendtime': '时间范围维度下的开始时间',
                            'effstarttime': '时间范围维度下的结束时间',
                            'efftime': '固定天数 维度下的时间',
                            'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'content': '使用内容',
                            'givestatus': '是否赠送课时（0：不赠送 1：赠送) 如果为1 赠送套餐的详情需要展示',
                            'sendvideo': '赠送录播课（0：未赠送 其他：赠送录播s课时间',
                            'sendlive': '赠送直播课（0：未赠送 其他：赠送直播s课时间）',
                            'giftthreshold': '使用门槛 0 无限制 其他代表满够金额',
                            'giftefftype': '赠送课时的有效期状态（1：时间范围 2：固定天数）',
                            'gifteffstarttime': '时间范围维度下的开始时间',
                            'gifteffendtime':'时间范围维度下的结束时间',
                            'giftefftime': '购买后到期天数',
                            'gifttrialtype': '可使用课程（0：全部课程，1：指定分类，2：指定课程）',
                            'packagegiftid': '赠送课时的id'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/getPackageDetail',
    'name'=>'APP学生端-首页-查询我的套餐详情',
    'type'=>'post',
    'data'=>"{'packageid':1}",
    'tip'=>"{'packageid':'套餐id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'packageid': '套餐id',
                            'bughour': '购买课时数量',
                            'setmeal': '套餐名称',
                            'setimgpath': '套餐封面url',
                            'setprice': '套餐价格',
                            'limitbuy': '限购次数（0：无限购，其他限购次数）',
                            'threshold': '使用门槛 0 无限制 其他代表满够金额',
                            'efftype': '有效期状态（1：时间范围 2：固定天数）',
                            'effendtime': '时间范围维度下的开始时间',
                            'effstarttime': '时间范围维度下的结束时间',
                            'efftime': '固定天数 维度下的时间',
                            'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'content': '使用内容',
                            'givestatus': '是否赠送课时（0：不赠送 1：赠送) 如果为1 赠送套餐的详情需要展示',
                            'sendvideo': '赠送录播课（0：未赠送 其他：赠送录播s课时间',
                            'sendlive': '赠送直播课（0：未赠送 其他：赠送直播s课时间）',
                            'giftthreshold': '使用门槛 0 无限制 其他代表满够金额',
                            'giftefftype': '赠送课时的有效期状态（1：时间范围 2：固定天数）',
                            'gifteffstarttime': '时间范围维度下的开始时间',
                            'gifteffendtime':'时间范围维度下的结束时间',
                            'giftefftime': '购买后到期天数',
                            'gifttrialtype': '可使用课程（0：全部课程，1：指定分类，2：指定课程）',
                            'packagegiftid': '赠送课时的id'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/gotoOrder',
    'name'=>'APP学生端-首页-套餐下单',
    'type'=>'post',
    'data'=>"{'packageid':1,'ordersource':'1'}",
    'tip'=>"{'packageid':1,'ordersource':'下单渠道 1web 2app'}",
    'returns'=>"{
                             'code': '返回的查询标识，0为正常返回，其他为异常',
                             'data': '最外层data为此次请求的返回数据',
                             'info': '此次请求返回数据描述',
                             'ordernum': '201809071531153651275622'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/showOrderDetail',
    'name'=>'APP学生端-首页-显示套餐订单详情',
    'type'=>'post',
    'data'=>"{'ordernum':'201809071531153651275622'}",
    'tip'=>"{}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'packageid': '套餐id',
                            'bughour': '购买课时数量',
                            'setmeal': '套餐名称',
                            'setimgpath': '套餐封面url',
                            'setprice': '套餐价格',
                            'limitbuy': '限购次数（0：无限购，其他限购次数）',
                            'threshold': '使用门槛 0 无限制 其他代表满够金额',
                            'efftype': '有效期状态（1：时间范围 2：固定天数）',
                            'effendtime': '时间范围维度下的开始时间',
                            'effstarttime': '时间范围维度下的结束时间',
                            'efftime': '固定天数 维度下的时间',
                            'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'content': '使用内容',
                            'givestatus': '是否赠送课时（0：不赠送 1：赠送) 如果为1 赠送套餐的详情需要展示',
                            'sendvideo': '赠送录播课（0：未赠送 其他：赠送录播s课时间',
                            'sendlive': '赠送直播课（0：未赠送 其他：赠送直播s课时间）',
                            'giftthreshold': '使用门槛 0 无限制 其他代表满够金额',
                            'giftefftype': '赠送课时的有效期状态（1：时间范围 2：固定天数）',
                            'gifteffstarttime': '时间范围维度下的开始时间',
                            'gifteffendtime':'时间范围维度下的结束时间',
                            'giftefftime': '购买后到期天数',
                            'gifttrialtype': '可使用课程（0：全部课程，1：指定分类，2：指定课程）',
                            'packagegiftid': '赠送课时的id'
                            'amount': '套餐金额',
                            'orderstatus': '套餐状态',
                            'studentid': '学生id',
                            'ordernum': '订单金额'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/gotoPay',
    'name'=>'APP学生端-首页-套餐支付',
    'type'=>'post',
    'data'=>"{'ordernum':1,'paytype':'1'}",
    'tip'=>"{'ordernum':1,'paytype':'支付方式0其他，1余额，2微信，3支付宝，4银联5贝宝paypal'}",
    'returns'=>"{
                             'code': '返回的查询标识，0为正常返回，其他为异常',
                             'data': '最外层data为此次请求的返回数据',
                             'info': '此次请求返回数据描述',
                             'ordernum': '201809071531153651275622',
                             '如果是支付宝支付': '跳转到支付宝页面',
                             '如果是微信支付': '会返回codeurl字段 用于支付扫码的url',
                             '如果是余额支付': '会返回成功失败的消息',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/getPackageOrderList',
    'name'=>'APP学生端-个人中心-查询套餐订单列表',
    'type'=>'post',
    'data'=>"{'pagenum':1}",
    'tip'=>"{'pagenum':1}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'packageid': '套餐id',
                            'bughour': '购买课时数量',
                            'setmeal': '套餐名称',
                            'setimgpath': '套餐封面url',
                            'setprice': '套餐价格',
                            'limitbuy': '限购次数（0：无限购，其他限购次数）',
                            'threshold': '使用门槛 0 无限制 其他代表满够金额',
                            'efftype': '有效期状态（1：时间范围 2：固定天数）',
                            'effendtime': '时间范围维度下的开始时间',
                            'effstarttime': '时间范围维度下的结束时间',
                            'efftime': '固定天数 维度下的时间',
                            'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'content': '使用内容',
                            'givestatus': '是否赠送课时（0：不赠送 1：赠送) 如果为1 赠送套餐的详情需要展示',
                            'sendvideo': '赠送录播课（0：未赠送 其他：赠送录播s课时间',
                            'sendlive': '赠送直播课（0：未赠送 其他：赠送直播s课时间）',
                            'giftthreshold': '使用门槛 0 无限制 其他代表满够金额',
                            'giftefftype': '赠送课时的有效期状态（1：时间范围 2：固定天数）',
                            'gifteffstarttime': '时间范围维度下的开始时间',
                            'gifteffendtime':'时间范围维度下的结束时间',
                            'giftefftime': '购买后到期天数',
                            'gifttrialtype': '可使用课程（0：全部课程，1：指定分类，2：指定课程）',
                            'packagegiftid': '赠送课时的id'
                            'amount': '套餐金额',
                            'orderstatus': '套餐状态',
                            'studentid': '学生id',
                            'ordernum': '订单金额'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/cancelOrder',
    'name'=>'APP学生端-我的套餐订单-取消订单',
    'type'=>'post',
    'data'=>"{'ordernum':'201804281944329376183807'}",
    'tip'=>"{'ordernum':'订单号'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/orderSuccess',
    'name'=>'APP学生端-套餐订单-订单成功详情',
    'type'=>'post',
    'data'=>"{'ordernum':'201804281811553803628717'}",
    'tip'=>"{'ordernum':'订单号'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'packageid': '套餐id',
                            'bughour': '购买课时数量',
                            'setmeal': '套餐名称',
                            'setimgpath': '套餐封面url',
                            'setprice': '套餐价格',
                            'limitbuy': '限购次数（0：无限购，其他限购次数）',
                            'threshold': '使用门槛 0 无限制 其他代表满够金额',
                            'efftype': '有效期状态（1：时间范围 2：固定天数）',
                            'effendtime': '时间范围维度下的开始时间',
                            'effstarttime': '时间范围维度下的结束时间',
                            'efftime': '固定天数 维度下的时间',
                            'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'content': '使用内容',
                            'givestatus': '是否赠送课时（0：不赠送 1：赠送) 如果为1 赠送套餐的详情需要展示',
                            'sendvideo': '赠送录播课（0：未赠送 其他：赠送录播s课时间',
                            'sendlive': '赠送直播课（0：未赠送 其他：赠送直播s课时间）',
                            'giftthreshold': '使用门槛 0 无限制 其他代表满够金额',
                            'giftefftype': '赠送课时的有效期状态（1：时间范围 2：固定天数）',
                            'gifteffstarttime': '时间范围维度下的开始时间',
                            'gifteffendtime':'时间范围维度下的结束时间',
                            'giftefftime': '购买后到期天数',
                            'gifttrialtype': '可使用课程（0：全部课程，1：指定分类，2：指定课程）',
                            'packagegiftid': '赠送课时的id'
                            'amount': '套餐金额',
                            'orderstatus': '套餐状态',
                            'studentid': '学生id',
                            'ordernum': '订单金额'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/packageUseList',
    'name'=>'APP学生端-我的套餐-查询套餐使用状态',
    'type'=>'post',
    'data'=>"{'status':'1','pagenum':1}",
    'tip'=>"{'status':'0待使用 1已使用 2已过期','pagenum':'分页页数'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'packageid': '套餐id',
                            'bughour': '购买课时数量',
                            'setmeal': '套餐名称',
                            'setimgpath': '套餐封面url',
                            'setprice': '套餐价格',
                            'limitbuy': '限购次数（0：无限购，其他限购次数）',
                            'threshold': '使用门槛 0 无限制 其他代表满够金额',
                            'efftype': '有效期状态（1：时间范围 2：固定天数）',
                            'effendtime': '时间范围维度下的开始时间',
                            'effstarttime': '时间范围维度下的结束时间',
                            'efftime': '固定天数 维度下的时间',
                            'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'content': '使用内容',
                            'givestatus': '是否赠送课时（0：不赠送 1：赠送) 如果为1 赠送套餐的详情需要展示',
                            'sendvideo': '赠送录播课（0：未赠送 其他：赠送录播s课时间',
                            'sendlive': '赠送直播课（0：未赠送 其他：赠送直播s课时间）',
                            'giftthreshold': '使用门槛 0 无限制 其他代表满够金额',
                            'giftefftype': '赠送课时的有效期状态（1：时间范围 2：固定天数）',
                            'gifteffstarttime': '时间范围维度下的开始时间',
                            'gifteffendtime':'时间范围维度下的结束时间',
                            'giftefftime': '购买后到期天数',
                            'gifttrialtype': '可使用课程（0：全部课程，1：指定分类，2：指定课程）',
                            'packagegiftid': '赠送课时的id',
                            'amount': '套餐金额',
                            'orderstatus': '套餐状态',
                            'studentid': '学生id',
                            'ordernum': '订单金额',
                            'type': '套餐类型 1套餐 2套餐赠送课时',
                            'surplus': '套餐剩余课时数量',
                            'total': '套餐总课时数量',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Package/deletePackageUse',
    'name'=>'APP学生端-我的套餐订单-删除学生套餐的使用',
    'type'=>'post',
    'data'=>"{'packageuseid':1}",
    'tip'=>"{'packageuseid':'套餐使用的id'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Myorder/gotoOrder',
    'name'=>'APP学生端-订单-提交订单',
    'type'=>'post',
    'data'=>"{'courseid':5,'studentid':1,'schedulingid':'1','amount':'1.00','ordersource':'1','organid':1,'originprice':'1234567','addressid':1,'usestatus':1,'type':1,'packageid':1,'packagegiftid':1,'packageuseid':1}",
    'tip'=>"{'courseid':'课程id','studentid':'学生登录用户名id','schedulingid':'班级id','amount':'实付金额','ordersource':'下单渠道 1web 2app','organid':'机构id','originprice':'课程原价','addressid':'地址id','usestatus':'是否使用优惠券0未使用1使用','type':'类型','packageid':'套餐id','packagegiftid':'赠送套餐id','packageuseid':'套餐的使用id'}",
    'returns'=>"{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'discount': '优惠金额',
	    						'originprice': '课程总价',
	    						'amount': '实付金额',
	    						'ordernum': '订单号',
	    						'usablemoney': '账户余额',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Myorder/getUserPackage',
    'name'=>'APP学生端-订单详情-查询学生用户可用的优惠券',
    'type'=>'post',
    'data'=>"{'curriculumid':1,'amount':'1.00'}",
    'tip'=>"{'curriculumid':'课程id','amount':'课程金额或者班级金额'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '',
                            'packageid': '套餐id',
                            'packagegiftid': '赠送套餐优惠券id',
                            'type': '优惠券类型1优惠券2赠送的优惠券',
                            'surplus': '套餐剩余课时数量',
                            'total': '套餐总课时数量',
                            'endtime': '套餐到期时间',
                            'setmeal': '套餐名称',
                            'threshold': '套餐使用门槛',
                            'trialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'categoryids': '分类id集合',
                            'curriculumids': '课程id集合',
                            'gifttrialtype': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'giftcategoryids': '分类集合',
                            'giftcurriculumids': '可使用课程（1：全部课程，2：指定分类，3：指定课程）',
                            'giftthreshold': '赠送套餐套餐使用门槛'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Myorder/showOrderDetail',
    'name'=>'APP学生端-订单-支付中心',
    'type'=>'post',
    'data'=>"{'ordernum':'201804281811553803628717'}",
    'tip'=>"{'ordernum':'订单号'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'orderid': '订单id',
                            'studentid': '学生id',
                            'curriculumid': '课程id',
                            'ordernum': '订单号',
                            'classname': '班级名称',
                            'coursename': '课程名称',
                            'originprice': '课程原价',
                            'discount': '优惠金额',
                            'ordertime': '下单时间',
                            'schedulingid': '班级排课id',
                            'amount': '订单金额',
                            'type': '班级类型 1是一对一 2是小班 3是大班',
                            'organid': '机构id',
                            'orderstatus': '订单状态',
                            'teachername': '老师姓名',
                            'imageurl': '课程图片',
                            'usablemoney': '学生可用余额'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Myorder/queryOrderStatus',
    'name'=>'APP学生端-订单-查询订单状态',
    'type'=>'post',
    'data'=>"{'ordernum':'201804281811553803628717','type':1}",
    'tip'=>"{'ordernum':'订单号','type':'1购买课程2购买套餐'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Myorder/showOrderDetail',
    'name'=>'APP学生端-订单-支付中心',
    'type'=>'post',
    'data'=>"{'ordernum':'201804281811553803628717'}",
    'tip'=>"{'ordernum':'订单号'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'orderid': '订单id',
                            'studentid': '学生id',
                            'curriculumid': '课程id',
                            'ordernum': '订单号',
                            'classname': '班级名称',
                            'coursename': '课程名称',
                            'originprice': '课程原价',
                            'discount': '优惠金额',
                            'ordertime': '下单时间',
                            'schedulingid': '班级排课id',
                            'amount': '订单金额',
                            'type': '班级类型 1是一对一 2是小班 3是大班',
                            'organid': '机构id',
                            'orderstatus': '订单状态',
                            'teachername': '老师姓名',
                            'imageurl': '课程图片',
                            'usablemoney': '学生可用余额'
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Myorder/getMyOrderList',
    'name'=>'APP学生端-我的订单-我的课程订单列表',
    'type'=>'post',
    'data'=>"{'pagenum':1,'limit':1,'status':1}",
    'tip'=>"{'pagenum':'分页页数','limit':'每页记录数','status':'1代表未支付订单 2代表已支付订单 3代表全部订单'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            'pageinfo':'分页list',
                            'pagesize': '每页记录数',
                            'pagenum': '当前页数',
                            'total': '总记录数',
                            'data': '数据list',
                            'schedulingid': '排课id',
                            'teacherid': '老师id',
                            'organid': '机构id',
                            'curriculumid': '课程Id',
                            'orderid': '订单id',
                            'ordernum': '订单号',
                            'classname': '班级名称',
                            'coursename': '课程名称',
                            'ordertime': '下单时间',
                            'amount': '订单金额',
                            'originprice': '课程原价',
                            'type': '班级类型',
                            'orderstatus': '订单状态0已下单，1已取消，2已支付，3申请退款',
                            'teachername': '老师名称',
                            'imageurl': '课程图片',
                            'subhead': '课程简介'
                       }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Myorder/cancelOrder',
    'name'=>'APP学生端-我的订单-取消订单',
    'type'=>'post',
    'data'=>"{'ordernum':'201804281944329376183807'}",
    'tip'=>"{'ordernum':'订单号'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                                'data': '05:30老师可预约时间值'
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Myorder/gotoPay',
    'name'=>'APP学生端-课程订单-立即支付',
    'type'=>'post',
    'data'=>"{'ordernum':'201808272126369803894011','studentid':1,'paytype':'2'}",
    'tip'=>"{'ordernum':'订单号','studentid':'学生id','paytype':'如果是单一支付方式：0其他，1余额，2微信，3支付宝，4银联'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '此次请求返回数据描述',
                            '如果是支付宝支付': '跳转到支付宝页面',
                            '如果是微信支付': '会返回codeurl字段 用于支付扫码的url',
                            '如果是余额支付': '会返回成功失败的消息',
                       }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/updateMsgStatus',
    'name'=>'App学生端-我的消息-修改消息状态为已查看',
    'type'=>'post',
    'data'=>"{'latelytime':1478524444}",
    'tip'=>"{'latelytime':'最新一条消息的添加时间'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '操作成功',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/messageList',
    'name'=>'APP学生端-我的消息-消息列表',
    'type'=>'post',
    'data'=>"{'pagenum':1,'limit':10, 'type':1}",
    'tip'=>"{'pagenum':'分页页数','limit':'每页记录数', 'type':'消息类型: 0全部消息 1系统消息 2推送消息'}",
    'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'id': '消息id',
								'type': '消息类型 1订单提醒 2上课提醒 3评论提醒 4预约提醒 5课程提醒 6推荐消息 7购买提醒 8收藏提醒 9机构邀请 10作业提醒 11集体推送 12课堂反馈',
								'title': '消息标题',
								'content': '消息内容',
								'addtime': '发送时间',
								'istoview': '是否被查看 0未查看 1已查看',
							   }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/getNewMsg',
    'name'=>'App学生端-我的消息-是否有新消息以及数据',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '操作成功',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/User/deleteMsg',
    'name'=>'App学生端-我的消息-删除消息和批量删除消息',
    'type'=>'post',
    'data'=>"{'messageids':5}",
    'tip'=>"{'messageids':'消息id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '操作成功',
                           }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/getBuyCurriculum',
    'name'=>'App学生端-我的课程-我的课程列表',
    'type'=>'post',
    'data'=>"{'pagenum':1,'limit':1,'coursetype':1}",
    'tip'=>"{'pagenum':'分页页数','limit':'每页记录数','coursetype':'课程类型1：录播课2直播课'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                                'pageinfo':'分页list',
                                'pagesize': '每页记录数',
                                'pagenum': '当前页数',
                                'total': '总条数',
                                'schedulingid': '排课id',
                                'teacherid': '老师id',
                                'organid': '机构id',
                                'curriculumid': '课程id',
                                'orderid': '订单id暂且不用',
                                'ordernum': '订单号暂且不用',
                                'classname': '班级名称',
                                'coursename': '课程名称',
                                'ordertime': '下单时间 暂不用',
                                'amount': '暂不用订单金额',
                                'originprice': '课程原价',
                                'type': '班级类型',
                                'orderstatus': '暂不用',
                                'teachername': '老师名称',
                                'imageurl': '课程图片',
                                'subhead': '课程副标题',
                                'reserve': '如果是一对一课程，查看是否预约完毕；1为预约完毕,查看详情0为去约课',
                                'learned': '已学课时100%',
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/getClassSchedule',
    'name'=>'App学生端-我的课程-课时安排或查看详情',
    'type'=>'post',
    'data'=>"{'coursetype':1,'schedulingid':'1','courseid':2}",
    'tip'=>"{'coursetype':'1:录播课查看详情2:直播课课时安排','ordernum':'订单号','courseid':'课程id'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                                'curriculumid': '课程id',
                                'organid': '机构id',
                                'scheduid': '排课id',
                                'teacherid': '老师id',
                                'type': '班级类型 1是一对一 2是小课班 3是大课班',
                                'totalprice': '课时总价',
                                'gradename': '班级名称',
                                'teachername': '老师名称',
                                'teacherimg': '老师图片,暂且不用',
                                'imageurl': '课程图片',
                                'coursename': '课程图片',
                                'subhead': '课程副标题',
                                'describe': '适用人群描述',
                                'studypeople': '学生数量 暂且不用',
                                'periodnum': '暂且不用',
                                'categoryid': '暂且不用',
                                'generalize': '课程概述',
                                'unit': '单元List',
                                'unitid': '单元id',
                                'unitname': '单元名称',
                                'unitsort': '单元排序',
                                'period': '课节List',
                                'periodname': '课节名称',
                                'periodsort': '课节排序',
                                'intime': '上课时间年月日',
                                'time': '上课时间时分秒',
                                'lessonsid': '课节id',
                                'toteachid': '预约时间表id'
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/getRecordComment',
    'name'=>'APP学生端-我的课程-查看录播课课时评论',
    'type'=>'post',
    'data'=>"{'lessonsid':1,'pagenum':1}",
    'tip'=>"{'lessonsid':'课时id','pagenum':'分页页数'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                                'imageurl': '学生邮箱',
                                'nickname': '学生昵称',
                                'addtime': '评论时间',
                                'score': '评分',
                                'content': '评论内容',
                                'commentid': '评论id'
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/watchPlayback',
    'name'=>'APP学生端-我的课程-直播课查看课时的视频',
    'type'=>'post',
    'data'=>"{'lessonsid':1,'courseid':1,'studentid':1}",
    'tip'=>"{'lessonsid':'课时id','courseid':'课程id','studentid':'学生id'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                                'fileurl': '文件url',
                                'iscomment': '学生评论标识1已经评论0未评论'
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/getAllClassList',
    'name'=>'学生端-调班-查询可调的班级信息和目标班级信息',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '',
                            'schedulingid': '原班级id',
                            'gradename': '原班级名称',
                            'periodnum': '原班级课时数量',
                            'curriculumid': '原班级课程id',
                            'num': '原班级已上课的课时数量',
                            'surplusnum': '原班级剩余课时数量',
                            'classlist': '目标班级list',
                            'schedulingid': '目标班级id',
                            'gradename': '目标班级名称',
                            'teachername': '目标班级老师名称',
                            'starttime': '目标班级开始上课时间',
                            'endtime': '目标班级课程结束时间'
                                           }"
);
$ZhangQY[] = array(
    'url'=>'student/Mycourse/submitApplyClasss',
    'name'=>'学生端-调班-调班提交',
    'type'=>'post',
    'data'=>"{'curriculumid':1,'oldschedulingid':1,'newschedulingid':2}",
    'tip'=>"{'curriculumid':'课程id','原班级id':1,'newschedulingid':'新班级id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '',
                           }",
);
$ZhangQY[] = array(
    'url'=>'student/Mycourse/getBuyCourseList',
    'name'=>'学生端-调课-查询课程和原班级信息',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '',
                            'coursename': '课程名称',
                            'curriculumid': '课程id',
                            'classinfo': 
                            'schedulingid': '班级id',
                            'gradename': '班级名称',
                            'periodnum': '课时数量',
                            'curriculumid': '课程id',
                            'lessons': '原班级课时list'
                            'teacherid': '老师id',
                            'teachername': '老师名称',
                            'lessonsid': '班级对应课节id',
                            'periodname': '课时名称',
                            'periodid': '课程对应的课时id'
                            }",
);
$ZhangQY[] = array(
    'url'=>'student/Mycourse/getSelectableLessons',
    'name'=>'学生端-调课-获取可选择的课时名称',
    'type'=>'post',
    'data'=>"{'lessonsid':1254,'schedulingid':168,'curriculumid':146,'periodid':166}",
    'tip'=>"{'lessonsid':'课时id','schedulingid':'原班级id','curriculumid':'课程id','periodid':'课程对应的课时id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '',
                            'schedulingid': '班级id',
                            'gradename': '班级名称',
                            'teachername': '老师姓名',
                            'starttime': '开始时间',
                            'endtime': '结束时间',
                            'newlessonsid': '目标课时的id',
                            'newteachername': '目标班级的老师姓名'
                            }",
);
$ZhangQY[] = array(
    'url'=>'student/Mycourse/submitApplylession',
    'name'=>'学生端-调课-调课提交',
    'type'=>'post',
    'data'=>"{'newlessonsid':1254,'oldlessonsid':168,'curriculumid':146}",
    'tip'=>"{'newlessonsid':'新课节id','oldlessonsid':'旧的课节信息','curriculumid':'课程id'}",
    'returns'=>"{
                            'code': '返回的查询标识，0为正常返回，其他为异常',
                            'data': '最外层data为此次请求的返回数据',
                            'info': '',
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/intoClassroom',
    'name'=>'App学生端-我的课表-进教室',
    'type'=>'post',
    'data'=>"{'toteachid':1}",
    'tip'=>"{'toteachid':'预约时间id'}",
    'returns'=>"{
                              'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                                'addtime': '开启时间',
                                'shuttime': '结束时间',
                                'classroomno': '教室号',
                                'confuserpwd': '学生密码',
                                'passwordrequired': '学生进去教室是否需要密码 0否 1是',
                                'nickname': '学生昵称',
                            }",
);
$ZhangQY[] = array(
    'url'=>'/appstudent/Mycourse/getLessonsPlayback',
    'name'=>'APP学生端-我的课表-回放',
    'type'=>'post',
    'data'=>"{'toteachid':1}",
    'tip'=>"{'toteachid':'预约时间id'}",
    'returns'=>"{
                              'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述',
                                'teachername': '老师名称',
                                'starttime': '上课时间',
                                'lessonsname': '课节名称',
                                'video':'视频片段list' ,
                                'playpath': '视频片段url',
                                'https_playpath': '视频https片段',
                                'duration': '时长时分秒',
                                'part': '片段编号1,2'
                            }",
);