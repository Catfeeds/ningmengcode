<?php
$ZhaoZQ = [];
/**************************机构后台*******************************/
//机构后台-》注册机构超级管理员(个体老师|企业)
$ZhaoZQ[] = array(
    'url'=>'/admin/Index/registerUser',
    'name'=>'机构后台-》注册机构超级管理员(个体老师|企业)',
    'type'=>'post',
    'data'=>"{'useraccount':'userzzq','password':'123456','mobile':'18739798667','mobileCode':'743470','imageCode':'2p43i','sessionId':'7e0471d7ee9ebdd168082bd40fe88088','restype':'1','vip':'1'}",
    'tip'=>"{'useraccount':'用户名','password':'密码','mobile':'手机号','mobileCode':'验证码','imageCode':'图形验证码','sessionId':'sessionId的值','restype':'注册类型 1表示个体老师,2表示企业','vip':'0表示免费机构,1表示vip机构'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据,包括以下字段',
                                'organid':'新机构的机构id',
                                'info': '此次请求返回数据描述'
                           }",
);

//机构后台-》获取手机号验证码
$ZhaoZQ[] = array(
    'url'=>'/admin/Index/sendOrganWebUserCode',
    'name'=>'机构后台-》获取手机号验证码',
    'type'=>'post',
    'data'=>"{'mobile':'15236963264','type':'0','imageCode':'XDFV','sessionId':'4565ertert4356wre','vip':'0'}",
    'tip'=>"{'mobile':'手机号码','type':'0表示注册时发|1表示找回密码的时候发送','imageCode':'图形验证码','sessionId':'sessionId的值','vip':'注册的免费机构时候填0,vip机构填1|找密码免费机构填0，VIP机构填1'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述'
                           }",
);
//机构后台-》获取图形验证码
$ZhaoZQ[] = array(
    'url'=>'/admin/Index/showOrganWebVerify',
    'name'=>'机构后台-》获取图形验证码',
    'type'=>'post',
    'data'=>"{}",
    'tip'=>"{}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述'
                           }",
);

//机构后台-》找回密码第一步(填写手机号，检测图形验证码)
$ZhaoZQ[] = array(
    'url'=>'/admin/Index/findPassOne',
    'name'=>'机构后台-》找回密码第一步(填写手机号，检测图形验证码)',
    'type'=>'post',
    'data'=>"{'mobile':'15236963264','imageCode':'XDFV','sessionId':'4565ertert4356wre','isVip':'0'}",
    'tip'=>"{'mobile':'手机号码','imageCode':'图形验证码','sessionId':'sessionId的值','isVip':'0表示免费机构,1表示付费机构'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述'
                           }",
);

//机构后台-》找回密码第二步(填写手机号验证码，新密码)
$ZhaoZQ[] = array(
    'url'=>'/admin/Index/findPassTwo',
    'name'=>'机构后台-》找回密码第二步(填写手机号验证码，新密码)',
    'type'=>'post',
    'data'=>"{'mobile':'15236963264','mobileCode':'114563','imageCode':'XDFV','sessionId':'4565ertert4356wre','newPassword':'654321','isVip':'0'}",
    'tip'=>"{'mobile':'手机号码','mobileCode':'短信验证码','imageCode':'图形验证码','sessionId':'sessionId的值','newPassword':'新密码','isVip':'0表示免费机构,1表示付费机构'}",
    'returns'=>"{
                                'code': '返回的查询标识，0为正常返回，其他为异常',
                                'data': '最外层data为此次请求的返回数据',
                                'info': '此次请求返回数据描述'
                           }",
);
//机构后台-》提交基本资料-》填写机构基本信息
$ZhaoZQ[] = array(
    'url'=>'/admin/Organ/setOrganBaseInfo',
    'name'=>'机构后台-》提交基本资料-》填写机构基本信息',
    'type'=>'post',
    'data'=>"{	'organname':'柠檬教育',
				'summary':'柠檬教育',
				'imageurl':'www.baidu.com',
				'hotline':'046-213123','email':'13456781122',
				'email':'34545@qq.com',
				'contactname':'李先生',
				'contactphone':'18610374671',
				'contactemail':'li@qq.com'
			}",
	'tip'=>"{
				'organname':'学堂名称',
				'summary':'学堂概述',
				'imageurl':'Logo',
				'hotline':'客服热线',
				'email':'客服邮箱',
				'contactname':'联系人姓名',
				'contactphone':'电话号码',
				'contactemail':'联系邮箱',
		   }",
	'returns'=>"{
				'code': '返回的查询标识，0为正常返回，其他为异常',
				'data': '最外层data为此次请求的返回数据',
				'info': '此次请求返回数据描述'
		   }",
);

//机构后台-》提交基本资料-》获取机构基本信息
$ZhaoZQ[] = array(
    'url'=>'/admin/Organ/getOrganBaseInfo',
    'name'=>'机构后台-》提交基本资料-》获取机构基本信息',
    'type'=>'post',
    'data'=>"",
    'tip'=>"",
    'returns'=>"{
					'organname':'学堂名称',
					'summary':'学堂概述',
					'imageurl':'Logo',
					'hotline':'客服热线',
					'email':'客服邮箱',
					'contactname':'联系人姓名',
					'contactphone':'电话号码',
					'contactemail':'联系邮箱',
			   }",
);
////机构后台-》提交基本资料-》填写机构认证信息
//$ZhaoZQ[] = array(
//    'url'=>'/admin/Organ/setOrganConfirmInfo',
//    'name'=>'机构后台-》提交基本资料-》填写机构认证信息',
//    'type'=>'post',
//    'data'=>"{'organid':'4','idname':'zhangsan','idnum':'411234198906070098','frontphoto':'1.jpg','backphoto':'2.jpg','organname':'校学习','organnum':'123123123123345','organphoto':'56.jpg','confirmtype':'1'}",
//    'tip'=>"{'organid':'机构id(必传)','idname':'个人身份证名称,当企业认证时可传可不传','idnum':'个人身份证号码,,当企业认证时可传可不传','frontphoto':'个人或者法人正面照','backphoto':'个人或者法人背面照','organname':'企业名称','organnum':'营业执照号码','organphoto':'营业执照照片(个人认证的时候可不传)','confirmtype':'1表示个人2表示企业'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
////机构后台-》提交基本资料-》获取机构认证信息
//$ZhaoZQ[] = array(
//    'url'=>'/admin/Organ/getOrganConfirmInfo',
//    'name'=>'机构后台-》提交基本资料-》获取机构认证信息',
//    'type'=>'post',
//    'data'=>"{'organid':'4'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);

//机构后台-》入住审核-》机构审核中,展示机构的介绍信息
//$ZhaoZQ[] = array(
//    'url'=>'/admin/Organ/getOrganIntroduceInfo',
//    'name'=>'机构后台-》入住审核-》机构审核中,展示机构的介绍信息',
//    'type'=>'post',
//    'data'=>"{'organid':'4'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据,有以下字段',
//                                'organname':'机构名',
//                                'imageurl':'机构logo',
//                                'summary':'机构介绍',
//                                'info': '此次请求返回数据描述'
//                           }",
//);

////机构后台-》审核通过-》展示机构的审核结果信息
//
//$ZhaoZQ[] = array(
//    'url'=>'/admin/Organ/getAuditResByOrganId',
//    'name'=>'机构后台-》审核通过-》展示机构的审核结果信息',
//    'type'=>'post',
//    'data'=>"{'organid':'4'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//机构后台-》提交基本资料-》展示机构的最新的被拒绝信息
//$ZhaoZQ[] = array(
//    'url'=>'/admin/Organ/getLatestResuseInfoByOrganId',
//    'name'=>'机构后台-》提交基本资料-》展示机构的最新的被拒绝信息',
//    'type'=>'post',
//    'data'=>"{'organid':'4'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
// 机构后台-》机构审核被拒绝后返回未认证的状态
//$ZhaoZQ[] = array(
//    'url'=>'/admin/Organ/FromRefusedToUnAudited',
//    'name'=>'机构后台-》机构审核被拒绝后返回未认证的状态',
//    'type'=>'post',
//    'data'=>"{'organid':'4'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                              'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                }",
//);


//$ZhaoZQ[] = array(
//    'url'=>'/admin/Index/applyVipOrgan',
//    'name'=>'机构后台-》免费机构申请vip机构',
//    'type'=>'post',
//    'data'=>"{'organid':'4'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                              'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                }",
//);
//




/**************************机构后台*******************************/

/**************************官方后台*******************************/
//官方后台-》登录-》后台用户登录
//$ZhaoZQ[] = array(
//    'url'=>'/official/Login/login',
//    'name'=>'官方后台-》登录-》后台用户登录',
//    'type'=>'post',
//    'data'=>"{'username':'user1','password':'123456'}",
//    'tip'=>"{'username':'用户名','password':'密码'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
////官方后台-》登录-》后台用户退出
//$ZhaoZQ[] = array(
//    'url'=>'/official/Login/logout',
//    'name'=>'官方后台-》登录-》后台用户退出',
//    'type'=>'post',
//    'data'=>"{}",
//    'tip'=>"{}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
////官方后台-》登录-》获取后台登录管理员的信息
//$ZhaoZQ[] = array(
//    'url'=>'/official/User/getOfficialUserLoginInfo',
//    'name'=>'官方后台-》登录-》获取后台登录管理员的信息',
//    'type'=>'post',
//    'data'=>"{'id':'1'}",
//    'tip'=>"{'id':'管理员id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'realname': '真实姓名',
//                                'logintime': '本次登录时间',
//                                'lastlogintime': '上次登录时间',
//                                'ip': 'ip地址',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//// 官方后台-》机构-》机构管理-》获取机构列表
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/getOrganList',
//    'name'=>'官方后台-》机构-》机构管理-》获取机构列表',
//    'type'=>'post',
//    'data'=>"{'auditstatus':'0','organname':'','orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'auditstatus':'机构的审核状态:0代表未认证,1代表待审核,2代表已拒绝,3代表已通过 ,为空的时候后台默认为0','organname':'机构名:搜索关键字,可为空','orderbys':'排序方式:id desc表示按最新的在前面,id asc 最新的排在后边 为空时默认按时间最新排在前面','pagenum':'页码数 为空时默认为1','pernum':'每页的数目 为空时默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '包含lists与count字段 lists为数组',
//                                'pagenum':'搜索出来的结果的总的页码数',
//                                'count':'机构数目',
//                                'lists':'包含机构信息字段',
//                                'id': 'id值',
//                                'organname': '机构名称',
//                                'imageurl': 'logo地址',
//                                'domain': '域名',
//                                'auditstatus': '机构的审核状态:0代表未认证,1代表待审核,2代表已拒绝,3代表已通过 为空的时候默认为0',
//                                'addtime': '2018-05-08 09:56:06',
//                                'passtime': '通过时间',
//                                'useraccount': '注册人姓名',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
////官方后台-》机构-》机构管理-》获取机构列表数目
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/getAllOrganListCount',
//    'name'=>'官方后台-》机构-》机构管理-》获取机构列表数目',
//    'type'=>'post',
//    'data'=>"{}",
//    'tip'=>"{}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'unAuditedCount': '未认证的机构数目',
//                                'inAuditedCount': '待审的机构数目',
//                                'refusedCount': '被拒绝的机构数目',
//                                'passCount': '通过的机构数目',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//// 官方后台-》机构-》机构管理-》设置机构启用或者禁用
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/setOrganOnOrOff',
//    'name'=>'官方后台-》机构-》机构管理-》设置机构启用或者禁用',
//    'type'=>'post',
//    'data'=>"{'organid':'1',auditstatus:'4'}",
//    'tip'=>"{'organid':'机构id','auditstatus':'3表示当前启用，改为禁用，4表示当前禁用，改为启用'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                              'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                }",
//);
//
//// 官方后台-》机构-》机构详情-》获取某机构的注册信息
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/getOrganRegisterInfo',
//    'name'=>'官方后台-》机构-》机构详情-》获取某机构的注册信息',
//    'type'=>'post',
//    'data'=>"{'organid':'1'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                  'data': '最外层data为此次请求的返回数据',
//                                    'username': '注册人姓名',
//                                    'mobile': '联系电话',
//                                    'addtime': '注册时间',
//                                    'domain': '域名',
//                                    'auditInfo': '分为未认证 ，待审核，已拒绝，通过审核且已启用，通过审核且已禁用',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//// 官方后台-》机构-》机构详情-》获取某机构的基本信息
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/getOrganBaseInfo',
//    'name'=>'官方后台-》机构-》机构详情-》获取某机构的基本信息',
//    'type'=>'post',
//    'data'=>"{'organid':'1'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'organname': '机构id',
//                                'imageurl': 'logo地址',
//                                'contactname': '联系人姓名',
//                                'contactphone': '联系电话',
//                                'contactemail': '联系邮箱',
//                                'summary': '概述',
//                                'phone': '客服电话',
//                                'email': '客服邮箱',
//                                'organid': '机构id',
//                                'baseinfoid': '机构信息表的id',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//// 官方后台-》机构-》机构详情-》获取某机构的认证信息
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/getOrganConfirmInfo',
//    'name'=>'官方后台-》机构-》机构详情-》获取某机构的认证信息',
//    'type'=>'post',
//    'data'=>"{'organid':'1'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                  'data': '最外层data为此次请求的返回数据',
//                                    'id': '主键值',
//                                    'idname': '个人身份证名字 当confirmtype=1返回',
//                                    'idnum': '个人身份证号码 当confirmtype=1返回',
//                                    'frontphoto': '个人或者法人正面照',
//                                    'backphoto': '个人或者法人背面照',
//                                    'organname': '企业名称',
//                                    'organnum': '营业执照号码',
//                                    'organphoto': '营业执照照片',
//                                    'confirmtype': '1表示个人2表示企业',
//                                    'organid': '机构id',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
// 官方后台-》机构-》机构详情-》获取某机构的审核结果
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/getOrganAuditResById',
//    'name'=>'官方后台-》机构-》机构详情-》获取某机构的审核结果',
//    'type'=>'post',
//    'data'=>"{'organid':'1'}",
//    'tip'=>"{'organid':'机构id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                              'data': {
//                                'auditinfo': '未通过或者通过',
//                                'refuseinfo': '通过的时候为空'
//                              }',
//                                'info': '此次请求返回数据描述'
//                }",
//);
// 官方后台-》机构-》机构详情-》审核某机构
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/doAudit',
//    'name'=>'官方后台-》机构-》机构详情-》审核某机构',
//    'type'=>'post',
//    'data'=>"{'auditstatus':'2','organid':'1','refuseinfo':'模糊不清'}",
//    'tip'=>"{'auditstatus':'2代表拒绝3代表通过','organid':'机构id','refuseinfo':'当auditstatus为2时必须填写，为3的时候为空'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);

// 官方后台-》机构-》机构管理-》获取申请vip的机构列表
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/getApplyVipOrganList',
//    'name'=>'官方后台-》机构-》机构管理-》获取申请vip的机构列表',
//    'type'=>'post',
//    'data'=>"{'organname':'','orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'organname':'机构名:搜索关键字,可为空','orderbys':'排序方式:id desc表示按最新的在前面,id asc 最新的排在后边 为空时默认按时间最新排在前面','pagenum':'页码数 为空时默认为1','pernum':'每页的数目 为空时默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'info': '此次请求返回数据描述',
//                                'data': '包含lists与count字段 lists为数组',
//                                'pagenum':'搜索出来的结果的总的页码数',
//                                'count':'机构数目',
//                                'lists':'包含机构信息字段',
//                                'id': 'id值',
//                                'organname': '机构名称',
//                                'contactname':'联系人姓名',
//                                'contactphone':'联系人电话',
//                                'contactemail':'联系邮箱',
//                                'passtime': '申请时间'
//
//                           }",
//);
// 官方后台-》机构-》机构管理-》复制免费机构的信息到vip机构
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/copyFromOldOrganToNewOrgan',
//    'name'=>'官方后台-》机构-》机构管理-》复制免费机构的信息到vip机构',
//    'type'=>'post',
//    'data'=>"{'oldOrganid':'1'}",
//    'tip'=>"{'oldOrganid':'免费机构的organid'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据,有以下字段',
//                                'newOrganid':'新的organid',
//                                'info': '此次请求返回数据描述'
//                           }",
//);

// 官方后台-》设置-》进行课堂配置
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/setOrganClassConfig',
//    'name'=>'官方后台-》设置-》进行课堂配置',
//    'type'=>'post',
//    'data'=>"{'toonetime':'20','smallclasstime':'25','bigclasstime':'30','maxclass':'100','minclass':'50'}",
//    'tip'=>"{'toonetime':'一对一时间','smallclasstime':'小班课时间','bigclasstime':'大班课时间','maxclass':'大班课人数上限','minclass':'小班课人数上限'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);

// 官方后台-》设置-》获取课堂配置
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/getOrganClassConfig',
//    'name'=>'官方后台-》设置-》获取课堂配置',
//    'type'=>'post',
//    'data'=>"{}",
//    'tip'=>"{}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
////官方后台-》设置-》添加广告
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/addOrganSlideImg',
//    'name'=>'官方后台-》设置-》添加广告',
//    'type'=>'post',
//    'data'=>"{'ramark':'轮播图1','imagepath':'1.jpg'}",
//    'tip'=>"{'ramark':'广告图描述','imagepath':'广告图路径'}",
//    'returns'=>"{
//                                'code': '  请求返回数据描述'
//                           }",
//);
////官方后台-》设置-》获取广告列表
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/getOrganSlideImgList',
//    'name'=>'官方后台-》设置-》获取广告列表',
//    'type'=>'post',
//    'data'=>"{}",
//    'tip'=>"{}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': 'data为数组,数组每一项',
//                                 'id': '主键值',
//                                  'remark': '广告图描述',
//                                  'imagepath': '广告图路径',
//                                  'sortid': '排序id',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//官方后台-》设置-》查看广告详情
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/getOrganSlideImgById',
//    'name'=>'官方后台-》设置-》查看广告详情',
//    'type'=>'post',
//    'data'=>"{'id':'1'}",
//    'tip'=>"{'id':'广告id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'id': '图片id',
//                                'remark': '图片描述',
//                                'imagepath': '图片地址',
//                                'sortid': '排序值',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//官方后台-》设置-》编辑广告
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/editOrganSlideImg',
//    'name'=>'官方后台-》设置-》编辑广告',
//    'type'=>'post',
//    'data'=>"{'id':'1',ramark':'轮播图1','imagepath':'1.jpg'}",
//    'tip'=>"{'id':'广告id','ramark':'广告图描述','imagepath':'广告图路径'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//官方后台-》设置-》删除广告
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/delOrganSlideImg',
//    'name'=>'官方后台-》设置-》删除广告',
//    'type'=>'post',
//    'data'=>"{'id':'1'}",
//    'tip'=>"{'id':'广告id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//官方后台-》权限-》成员管理-》添加管理员
//$ZhaoZQ[] = array(
//    'url'=>'/official/User/addOfficialUser',
//    'name'=>'官方后台-》权限-》成员管理-》添加管理员',
//    'type'=>'post',
//    'data'=>"{'username':'user1','realname':'张三','mobile':'18878786665','password':'123456','repassword':'123456','info':'备注'}",
//    'tip'=>"{'username':'用户名','realname':'真实姓名','mobile':'手机号','password':'密码可为空','repassword':'重复密码','info':'备注,可为空'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);



//官方后台-》权限-》成员管理-》获取管理员列表
//$ZhaoZQ[] = array(
//    'url'=>'/official/User/getOfficialUserList',
//    'name'=>'官方后台-》权限-》成员管理-》获取管理员列表',
//    'type'=>'post',
//    'data'=>"{'username':'','orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'username':'管理员名称:搜索关键字,可为空','orderbys':'排序方式:id desc表示按最新的在前面,id asc 最新的排在后边 为空时默认按时间最新排在前面','pagenum':'页码数 为空时默认为1','pernum':'每页的数目 为空时默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '包含lists,count,pagenum字段 lists为数组',
//                                'count':'总数量',
//                                'pagenum':'总页数',
//                                'lists':'包含以下管理员信息字段',
//                                'id': '管理员id',
//                                'username': '用户名',
//                                'realname': '真实姓名',
//                                'mobile': '手机号',
//                                'addtime': '添加时间',
//                                'logintime': '登录时间',
//                                'status': '1表示启用 0表示禁用',
//                                'info': '此次请求返回数据描述'
//                           }",
//);


//官方后台-》权限-》成员管理-》获取某个管理员详情
//$ZhaoZQ[] = array(
//    'url'=>'/official/User/getOfficialUserById',
//    'name'=>'官方后台-》权限-》成员管理-》获取某个管理员详情',
//    'type'=>'post',
//    'data'=>"{'id':'1'}",
//    'tip'=>"{'id':'管理员id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'id': '图片id',
//                                'username': '用户名',
//                                'realname': '真实姓名',
//                                'mobile': '手机号',
//                                'info': '备注',
//                                'status': '1表示启用0表示禁用',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//
////官方后台-》权限-》成员管理-》编辑管理员
//$ZhaoZQ[] = array(
//    'url'=>'/official/User/editOfficialUser',
//    'name'=>'官方后台-》权限-》成员管理-》编辑管理员',
//    'type'=>'post',
//    'data'=>"{'id':'1','username':'user1','realname':'张三','mobile':'18878786665','password','123456','repassword':'123456','info':'备注'}",
//    'tip'=>"{'id':'1','username':'用户名不能改，可设置表单只读','realname':'真实姓名','mobile':'手机号','password','密码为空的时候，表示不修改密码，否则修改密码','repassword':'密码为空的时候，表示不修改密码，否则修改密码','info':'备注,可为空'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//
//
////官方后台-》权限-》成员管理-》删除后台管理员
//$ZhaoZQ[] = array(
//    'url'=>'/official/User/delOfficialUser',
//    'name'=>'官方后台-》权限-》成员管理-》删除后台管理员',
//    'type'=>'post',
//    'data'=>"{'id':'1'}",
//    'tip'=>"{'id':'管理员id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//
//
////官方后台-》权限-》成员管理-》设置后台管理员启用或者禁用
//$ZhaoZQ[] = array(
//    'url'=>'/official/User/setOfficialUserOnOrOff',
//    'name'=>'官方后台-》权限-》成员管理-》设置后台管理员启用或者禁用',
//    'type'=>'post',
//    'data'=>"{'id':'1','status':'1'}",
//    'tip'=>"{'id':'管理员id','status':'当前管理员的状态值1或者0 1表示当前启用，0表示当前禁用'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
////官方后台-》权限-》成员管理-》获取管理员操作日志列表
//$ZhaoZQ[] = array(
//    'url'=>'/official/User/getUserOperateRecordList',
//    'name'=>'官方后台-》权限-》成员管理-》获取管理员操作日志列表',
//    'type'=>'post',
//    'data'=>"{'username':'user1',date:'20180511','orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'username':'管理员名称:搜索关键字,可为空',date:'搜索日期','orderbys':'排序方式:id desc表示按最新的在前面,id asc 最新的排在后边 为空时默认按时间最新排在前面','pagenum':'页码数 为空时默认为1','pernum':'每页的数目 为空时默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '包含lists,count,pagenum字段 lists为数组',
//                                'count':'总数量',
//                                'pagenum':'总页数',
//                                'lists':'包含以下管理员操作信息字段',
//                                'id': '管理员id',
//                                'username': '用户名',
//                                'addtime': '添加时间',
//                                'ip': 'IP地址',
//                                'operateinfo': '操作信息',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
////官方后台-》权限-》成员管理-》批量删除管理员操作日志
//$ZhaoZQ[] = array(
//    'url'=>'/official/User/delUserOperateRecord',
//    'name'=>'官方后台-》权限-》成员管理-》批量删除管理员操作日志',
//    'type'=>'post',
//    'data'=>"{'ids':'7,8'}",
//    'tip'=>"{'ids':'主键id的字符串集合'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '返回信息',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//// 官方后台-》财务-》订单统计-》订单列表
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/getOrderList',
//    'name'=>'官方后台-》财务-》订单统计-》订单列表',
//    'type'=>'post',
//    'data'=>"{'fromdate':'2018-05-01','enddate':'2018-05-18','domain':'','orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'fromdate':'开始日期','enddate':'结束日期','domain':'域名 搜索关键字','orderbys':'排序方式 id desc 最新的在前面 id asc 最旧的在前面','pagenum':'页码数 默认为1','pernum':'每页的数量 默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '包含lists,count,pagenum字段 lists为数组',
//                                'count':'总数量',
//                                'pagenum':'总页数',
//                                'lists':'包含以下管理员操作信息字段',
//                                'id': '主键值',
//                                'ordernum': '订单编号',
//                                'ordertime': '下单时间',
//                                'amount': '下单金额',
//                                'domain': '域名',
//                                'organname': '机构名称',
//                                'username': '学生姓名',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//// 官方后台-》财务-》订单统计-》累计交易余额
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/getTradeTotalSum',
//    'name'=>'官方后台-》财务-》订单统计-》累计交易余额',
//    'type'=>'post',
//    'data'=>"{}",
//    'tip'=>"{}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据,有以下字段',
//                                'totalSum': '累计交易余额(单位:元)',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//// 官方后台-》财务-》订单统计-》订单详情
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/getOrderDetail',
//    'name'=>'官方后台-》财务-》订单统计-》订单详情',
//    'type'=>'post',
//    'data'=>"{'id':'1'}",
//    'tip'=>"{'id':'订单id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'id': '订单id',
//                                'ordernum': '订单编号',
//                                'orderstaus': '订单状态',
//                                'ordertime': '下单时间',
//                                'username': '学生姓名',
//                                'ordersource': '订单来源,PC或者手机',
//                                'paytype': '订单状态 默认已支付，其他的有 已下单，已取消，已支付，申请退款，已退款  退款驳回',
//                                'originprice': '课程原价',
//                                'discount': '优惠金额',
//                                'amount': '实际金额',
//                                'balance': '余额划扣',
//                                'inMoney': '入账金额',
//                                'coursename': '课程名称',
//                                'classname': '班级名称',
//                                'teachername': '教师姓名',
//                                'organname': '机构名称',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//// 官方后台-》财务-》账户明细-》账户余额
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/getRemainingSum',
//    'name'=>'官方后台-》财务-》账户明细-》账户余额',
//    'type'=>'post',
//    'data'=>"{}",
//    'tip'=>"{}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据,有以下字段',
//                                'remainingSum': '账户余额(单位:元)',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//// 官方后台-》财务-》账户明细-》收入列表（学生第三方账号充值|买课列表）
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/getAccountDetailInList',
//    'name'=>'官方后台-》财务-》账户明细-》收入列表（学生第三方账号充值|买课列表）',
//    'type'=>'post',
//    'data'=>"{'orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'orderbys':'排序方式 id desc 最新的在前面 id asc 最旧的在前面','pagenum':'页码数 默认为1','pernum':'每页的数量 默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '包含lists,count,pagenum字段 lists为数组',
//                                'count':'总数量',
//                                'pagenum':'总页数',
//                                'lists':'包含以下管理员操作信息字段',
//                                'organname': '机构名',
//                                'domain': '域名',
//                                'username': '学生姓名',
//                                'paynum': '充值或者下单支付的金额',
//                                'paytime': '支付时间',
//                                'paytype': '支付方式:支付宝|微信|银联',
//                                'paystatus': '类型:下单|充值',
//                                'out_trade_no': '订单号|充值号',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//// 官方后台-》财务-》账户明细-》充值|买课详情
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/getAccountInDetail',
//    'name'=>'官方后台-》财务-》账户明细-》充值|买课详情',
//    'type'=>'post',
//    'data'=>"{'paystatus':'1','out_trade_no':'order1'}",
//    'tip'=>"{'paystatus':'1表示下单2表示充值','out_trade_no':'订单号|充值号'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'price': '充值金额',
//                                'rechargenum': '充值流水号',
//                                'addtime': '充值时间',
//                                'organname': '机构名称',
//                                'domain': '域名',
//                                'username': '学生姓名',
//                                'source': '充值来源:PC|手机',
//                                'paytype': '充值方式:微信|支付宝|银联',
//                                'info': '此次请求返回数据描述',
//                                '-----':'---------线上表示充值的详情，线下表示下单的详情----------',
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'id': '订单id',
//                                'ordernum': '订单编号',
//                                'orderstaus': '订单状态',
//                                'ordertime': '下单时间',
//                                'username': '学生姓名',
//                                'ordersource': '订单来源,PC或者手机',
//                                'paytype': '订单状态 默认已支付，其他的有 已下单，已取消，已支付，申请退款，已退款  退款驳回',
//                                'originprice': '课程原价',
//                                'discount': '优惠金额',
//                                'amount': '实际金额',
//                                'balance': '余额划扣',
//                                'inMoney': '入账金额',
//                                'coursename': '课程名称',
//                                'classname': '班级名称',
//                                'teachername': '教师姓名',
//                                'organname': '机构名称',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//// 官方后台-》财务-》账户明细-》支出列表（机构提现成功列表）
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/getWithDrawByOrganList',
//    'name'=>'官方后台-》财务-》账户明细-》支出列表（暂时指机构提现成功列表）',
//    'type'=>'post',
//    'data'=>"{'paystatus':'1','domain':'','orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'paystatus':'1表示成功提现','domain':'域名 搜索关键字，本接口为空','orderbys':'排序方式 id desc 最新的在前面 id asc 最旧的在前面','pagenum':'页码数 默认为1','pernum':'每页的数量 默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '包含lists,count,pagenum字段 lists为数组',
//                                'count':'总数量',
//                                'pagenum':'总页数',
//                                'lists':'包含以下管理员操作信息字段',
//                                'id': '提现表的主键id',
//                                'price': '提现金额',
//                                'addtime': '申请时间',
//                                'endtime': '处理时间',
//                                'organname': '机构名称',
//                                'domain': '域名',
//                                'paytype': '提现方式:支付宝|微信|银联',
//                                'paystatus': '提现状态:默认成功， 提现中|提现成功|提现失败|处理中',
//                                'cashaccount': '提现账号',
//                                'type': '机构退款',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//// 官方后台-》财务-》账户明细-》机构提现详情
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/getSumOutDetailByOrgan',
//    'name'=>'官方后台-》财务-》账户明细-》机构提现详情',
//    'type'=>'post',
//    'data'=>"{'id':'1'}",
//    'tip'=>"{'id':'提现表的主键id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'id': '提现表的主键id',
//                                'price': '提现金额',
//                                'addtime': '申请时间',
//                                'endtime': '处理时间',
//                                'organname': '机构名称',
//                                'domain': '域名',
//                                'paytype': '提现方式:支付宝|微信|银联',
//                                'paystatus': '提现状态:默认成功， 提现中|提现成功|提现失败|处理中',
//                                'cashaccount': '提现账号',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//
//
//
//// 官方后台-》财务-》提现申请-》机构提现申请列表(待审|处理中|成功|失败)
//
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/getWithDrawByOrganList',
//    'name'=>'官方后台-》财务-》提现申请-》机构提现申请列表(待审|处理中|成功|失败)',
//    'type'=>'post',
//    'data'=>"{'paystatus':'0','domain':'','orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'paystatus':'0表示待处理，1表示成功提现,2表示提现失败,3表示处理中','domain':'域名 搜索关键字','orderbys':'排序方式 id desc 最新的在前面 id asc 最旧的在前面','pagenum':'页码数 默认为1','pernum':'每页的数量 默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '包含lists,count,pagenum字段 lists为数组',
//                                'count':'总数量',
//                                'pagenum':'总页数',
//                                'lists':'包含以下管理员操作信息字段',
//                                'id': '提现表的主键id',
//                                'price': '提现金额',
//                                'addtime': '申请时间',
//                                'endtime': '处理时间',
//                                'organname': '机构名称',
//                                'domain': '域名',
//                                'paytype': '提现方式:支付宝|微信|银联',
//                                'paystatus': '提现状态:默认成功， 提现中|提现成功|提现失败|处理中',
//                                'cashaccount': '提现账号',
//                                'type': '机构退款',
//                                'info': '此次请求返回数据描述'
//                           }",
//);


//
//// 官方后台-》财务-》提现申请-》批量处理提现申请
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/manageWithDraw',
//    'name'=>'官方后台-》财务-》提现申请-》批量处理提现申请',
//    'type'=>'post',
//    'data'=>"{'ids':'13,14'}",
//    'tip'=>"{'ids':'提现表主键id的字符串,例如只有13 或者 13,14'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据,有以下字段data',
//                                'data':'显示的支付宝提交页面的表单',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//// 官方后台-》财务-》提现申请-》异步更改提现状态(支付宝测试)
//$ZhaoZQ[] = array(
//    'url'=>'/official/Notify/manageWithDrawResAsync',
//    'name'=>'官方后台-》财务-》提现申请-》异步更改提现状态(支付宝测试)',
//    'type'=>'post',
//    'data'=>"{}",
//    'tip'=>"{}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据,有以下字段data',
//                                'data':'显示的支付宝提交页面的表单',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//$ZhaoZQ[] = array(
//                'url'=>'/official/Course/addCategory',
//                'name'=>'官方后台-》课程-》分类-》添加分类 包括2级分类',
//                'type'=>'POST',
//                'data'=>"{'categoryname':'水果','fatherid':0}",
//                'tip'=>"{'categoryname':'分类名称','fatherid':'一级分类传0 二级分类传1级分类id'}",
//                'returns'=>"",
//                );
//
//
//$ZhaoZQ[] = array(
//                'url'=>'/official/Course/getCategoryIdList',
//                'name'=>'官方后台-》课程-》分类-》后台分类列表 和查询后台子类 带分页',
//                'type'=>'POST',
//                'data'=>"{'fatherid':0,'pagenum':1}",
//                'tip'=>"{'fatherid':'一级分类传0 二级分类传1级分类id','pagenum':'第几页'}",
//                'returns'=>"{'categoryname':'分类名',
//                            'rankstr':'级别',
//                            'juniorcount':'子类数量',
//                            'status':'状态码 0不显示 1 显示'}",
//                );
//
//
//
//$ZhaoZQ[] = array(
//                'url'=>'/official/Course/getCurricukumCategoryList',
//                'name'=>'官方后台-》课程-》分类-》后台添加课程模块 分类联动',
//                'type'=>'POST',
//                'data'=>"{'fatherid':0}",
//                'tip'=>"{'fatherid':'一级分类传0 二级分类传1级分类id','pagenum':'第几页'}",
//                'returns'=>"{'categoryname':'分类名',
//                            'rankstr':'级别',
//                            'juniorcount':'子类数量',
//                            'status':'状态码 0不显示 1 显示'}",
//                );
//
//
//$ZhaoZQ[] = array(
//                'url'=>'/official/Course/editCategoryId',
//                'name'=>'官方后台-》课程-》分类-》编辑分类 是否启用 名称编辑',
//                'type'=>'POST',
//                'data'=>"{'categoryname':'名称','status':1,'id':1}",
//                'tip'=>"{'categoryname':'分类名称 编辑时带','status':'是否启用 1显示 0不显示   启用时带','id':'分类id 必填'}",
//                'returns'=>"",
//                );
//
//
//$ZhaoZQ[] = array(
//                'url'=>'/official/Course/deleteCategory',
//                'name'=>'官方后台-》课程-》分类-》分类删除',
//                'type'=>'POST',
//                'data'=>"{'id':1}",
//                'tip'=>"",
//                'returns'=>"",
//                );
//
//
//$ZhaoZQ[] = array(
//                'url'=>'/official/Course/shiftCategory',
//                'name'=>'官方后台-》课程-》分类-》后台分类列表 上下移动',
//                'type'=>'POST',
//                'data'=>"{'id':7,'sort':7,'operate':0,'rank':1}",
//                'tip'=>"{'id':'分类id','sort':'当前排序值','operate':'分类操作 0上移 1下移','rank':'分类操作 级别'}",
//                'returns'=>"",
//                );
//
//// 官方后台-》课程-》课程-》获取课程列表
//$ZhaoZQ[] = array(
//    'url'=>'/official/Course/getClasseslist',
//    'name'=>'官方后台-》课程-》课程-》获取课程列表',
//    'type'=>'post',
//    'data'=>"{'coursename':'','orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'coursename':'课程名:搜索关键字,可为空','orderbys':'排序方式:id desc表示按最新的在前面,id asc 最新的排在后边 为空时默认按时间最新排在前面','pagenum':'页码数 为空时默认为1','pernum':'每页的数目 为空时默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'info': '此次请求返回数据描述',
//                                'data': '包含lists与count,pagenum字段 lists为数组',
//                                'pagenum':'搜索出来的结果的总的页码数',
//                                'count':'机构数目',
//                                'lists':'包含以下的字段',
//                                'id': '开课id',
//                                'curriculumid': '对应课程id',
//                                'status': '招生状态0表示停止招生,1表示招生',
//                                'type': '开课类型 1表示一对一,2表示小班了',
//                                'coursename': '课程名',
//                                'imageurl': '封面logo',
//                                'totalprice': '总价',
//                                'categorystr': '分类id',
//                                'organname': '所属机构名',
//                                'categoryname': '分类名',
//                                'classtype': '一对一'
//
//                           }",
//);
//
//// 官方后台-》课程-》课程-》获取课程总数
//$ZhaoZQ[] = array(
//    'url'=>'/official/Course/getClasseslistTotalCount',
//    'name'=>'官方后台-》课程-》课程-》获取课程总数',
//    'type'=>'post',
//    'data'=>"{}",
//    'tip'=>"{}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'count': '课程总数',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//// 官方后台-》课程-》课程-》设置课程上下架
//$ZhaoZQ[] = array(
//    'url'=>'/official/Course/doOnOrOffClass',
//    'name'=>'官方后台-》课程-》课程-》设置课程上下架',
//    'type'=>'post',
//    'data'=>"{'id':'1',status:'0'}",
//    'tip'=>"{'id':'开课id','status':'0表示下架，1表示上架'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                              'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                }",
//);
//
//
//
//
//
////官方后台-》设置-》添加套餐配置
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/addOrganAuditBill',
//    'name'=>'官方后台-》设置-》添加套餐配置',
//    'type'=>'post',
//    'data'=>"{'name':'基础套餐','logo':'1.jpg','info':'30点','indate':'1','price':'0.01','ontrial':'1'}",
//    'tip'=>"{'name':'套餐名称','logo':'logo地址','info':'描述','indate':'1表示一年','price':'价格','ontrial':'0表示试用版 1表示正式版'}",
//    'returns'=>"{
//                                'code': '  请求返回数据描述'
//                           }",
//);
////官方后台-》设置-》获取套餐列表
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/getOrganAuditBillList',
//    'name'=>'官方后台-》设置-》获取套餐列表',
//    'type'=>'post',
//    'data'=>"{'status':'2'}",
//    'tip'=>"{'status':'1表示有效的 2表示所有'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': 'data为数组,数组每一项',
//                                 'id': '主键值',
//                                  'name': '套餐名称',
//                                  'logo': '套餐图地址',
//                                  'info': '套餐描述',
//                                  'indate': '有效期(年)',
//                                  'price': '价格',
//                                  'ontrial':'1',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
////官方后台-》设置-》查看套餐详情
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/getOrganAuditBillById',
//    'name'=>'官方后台-》设置-》查看套餐详情',
//    'type'=>'post',
//    'data'=>"{'id':'1'}",
//    'tip'=>"{'id':'套餐id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                 'id': '主键值',
//                                  'name': '套餐名称',
//                                  'logo': '套餐图地址',
//                                  'info': '套餐描述',
//                                  'indate': '有效期(年)',
//                                  'price': '价格',
//                                  'ontrial':'1',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
////官方后台-》设置-》编辑套餐
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/editOrganAuditBill',
//    'name'=>'官方后台-》设置-》编辑套餐',
//    'type'=>'post',
//    'data'=>"{'id':'1','name':'基础套餐','logo':'1.jpg','info':'30点','indate':'1','price':'0.01','ontrial':'1'}",
//    'tip'=>"{'id':'套餐id','name':'套餐名称','logo':'logo地址','info':'描述','indate':'1表示一年','price':'价格','ontrial':'0表示试用版,1表示正式版'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
////官方后台-》设置-》删除套餐
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/delOrganAuditBill',
//    'name'=>'官方后台-》设置-》删除套餐',
//    'type'=>'post',
//    'data'=>"{'id':'1'}",
//    'tip'=>"{'id':'套餐id'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
////官方后台-》设置-》更改套餐状态
//$ZhaoZQ[] = array(
//    'url'=>'/official/Config/updateOrganAuditBillStatusById',
//    'name'=>'官方后台-》设置-》更改套餐状态',
//    'type'=>'post',
//    'data'=>"{'id':'1','status':'0'}",
//    'tip'=>"{'id':'套餐id','status':'套餐状态'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
//// 官方后台-》机构-》机构管理-》机构付款明细-》机构付款总额
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/getOrganPayAuditBillTotalSum',
//    'name'=>'官方后台-》机构-》机构管理-》机构付款明细-》机构付款总额',
//    'type'=>'post',
//    'data'=>"{}",
//    'tip'=>"{}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据,有以下字段',
//                                'totalSum': '机构付款总额(单位:元)',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//// 官方后台-》机构-》机构管理-》机构付款明细-》机构付款列表
//$ZhaoZQ[] = array(
//    'url'=>'/official/Organ/getOrganPayAuditBillList',
//    'name'=>'官方后台-》机构-》机构管理-》机构付款明细-》机构付款列表',
//    'type'=>'post',
//    'data'=>"{'fromdate':'2018-06-01','enddate':'2018-06-30','domain':'','orderbys':'id desc','pagenum':'1','pernum':'10'}",
//    'tip'=>"{'fromdate':'开始日期','enddate':'结束日期','domain':'域名 搜索关键字','orderbys':'排序方式 id desc 最新的在前面 id asc 最旧的在前面','pagenum':'页码数 默认为1','pernum':'每页的数量 默认为10'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '包含lists,count,pagenum字段 lists为数组',
//                                'count':'总数量',
//                                'pagenum':'总页数',
//                                'lists':'包含以下管理员操作信息字段',
//
//                                'id': '主键值',
//                                'domain': '机构域名',
//                                'organname': '机构名称',
//                                'billname': '套餐名称',
//                                'billinfo': '域名',
//                                'orderprice': '订单支付金额',
//                                'paytime': '支付时间（起始时间）',
//                                'usetime': '有效天数(天)',
//
//                                'info': '此次请求返回数据描述'
//                           }",
//);
//
////官方后台-》财务-》手动更改转账状态
//$ZhaoZQ[] = array(
//    'url'=>'/official/Finance/manualChangeWithDrawPayStatus',
//    'name'=>'官方后台-》设置-》手动更改转账状态',
//    'type'=>'post',
//    'data'=>"{'id':'1','paystatus':'1','reasons':'','price':'0.01','type':'2'}",
//    'tip'=>"{'id':'提现表id','paystatus':'1表示成功2表示失败','reasons':'paystatus为2的时候必填','price':'提现金额','type':'传2,此时表示手动注意固定值'}",
//    'returns'=>"{
//                                'code': '返回的查询标识，0为正常返回，其他为异常',
//                                'data': '最外层data为此次请求的返回数据',
//                                'info': '此次请求返回数据描述'
//                           }",
//);
/**************************官方后台*******************************/

