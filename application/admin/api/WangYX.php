<?php
$WangYX = [];
// 首页-》登陆认证
// $WangYX[] = array(
//     			'url'=>'/admin/index/login',
//     			'name'=>'首页-登陆认证',
//     			'type'=>'post',
//     			'data'=>"{
//     				'username':'admin6' ,
//     				'passwd':'admin6' ,
//     			}",
//     			'tip'=>"{
//     				'username':'用户名' ,
//     				'passwd':'密码' ,
//     			}",
//     			'returns'=>"{
//     						'code': '返回的查询标识，0为正常返回，其他为异常',
//     						'data': '最外层data为此次请求的返回数据',
//     						'info': '此次请求返回数据描述',
//                            }",
//     			);
// 首页-》推出登陆
// $WangYX[] = array(
//     			'url'=>'/admin/index/logout',
//     			'name'=>'首页-退出登陆',
//     			'type'=>'post',
//     			'data'=>"",
//     			'tip'=>"",
//     			'returns'=>"{
//     						'code': '返回的查询标识，0为正常返回，其他为异常',
//     						'data': '最外层data为此次请求的返回数据',
//     						'info': '此次请求返回数据描述',
//                            }",
//     			);
// 首页-》购买套餐
//	$WangYX[] = array(
//	    			'url'=>'/admin/organ/orderQuery',
//	    			'name'=>'机构-购买套餐订单状态查询',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'ordernum': 1 ,
//	    			}",
//	    			'tip'=>"{
//	    				'ordernum': '订单号' ,
//	    			}",
//	    			'returns'=>"{
//	    						'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }",
//	    			);

// 首页-》机构课表
$WangYX[] = array(
	'url' => '/admin/organ/organCourseList',
	'name' => '首页-机构课表',
	'type' => 'post',
	'data' => "{'date':'2018-04-28'}",
	'tip' => "{'date':'需要获取那个月的数据，日可以随便但是必须有'}",
	'returns' => "{
	    						'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'timestr': '日期',
	    						'year': '年',
                                'month': '月',
                                'day': '日',
                                'num': '这个日期有多少节课',
                                '':'第一层数组键代表返回数据的周，内层数据的键代表周几，比如周一就是1'
                            }",
);

// 首页-》机构课表
$WangYX[] = array(
	'url' => '/admin/organ/getLessonsByDate',
	'name' => '首页-机构课表-单日课程列表',
	'type' => 'post',
	'data' => "{'date':'2018-03-26'}",
	'tip' => "{'date':'需要获取课程列表对应的日期'}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                                'coursename': '课程名称',
                                'type': '班型' ,
                                'periodname':'课节名称' ,
                                'periodsort':'课节排序id' ,
                                'teachername': '教师名称' ,
                                'starttime': '课程开始时间' ,
                                'endtime': '课程结束时间' ,
                                'forclassurl':'巡课链接',

                                'pageinfo':'分页信息' ,
	    						'pagesize':'每页最多条数' ,
	    						'pagenum':'当前页码' ,
	    						'total':'总记录数' ,
                            }",
);
//首页-账号设置 修改当前登录用户的手机号和密码
// $WangYX[] = array(
//     			'url'=>'/admin/organ/getLessonsByDate',
//     			'name'=>'首页-账号设置-更新机构的管理员的信息',
//     			'type'=>'post',
//     			'data'=>"{'date':'2018-03-26'}",
//     			'tip'=>"{'date':'需要获取课程列表对应的日期'}",
//     			'returns'=>"{'code': '返回的查询标识，0为正常返回，其他为异常',
//     						'data': '最外层data为此次请求的返回数据',
//     						'info': '此次请求返回数据描述',
//     						'intime': '日期',
//     						'timekey': '年',
//                                'coursename': '月',
//                                'type': '日',
//                                'teacherid': '这个日期有多少节课',
//                                'periodname':'第一层数组键代表返回数据的周，内层数据的键代表周几，比如周一就是1'
//                                'periodsort':'第一层数组键代表返回数据的周，内层数据的键代表周几，比如周一就是1'
//                            }",
//     			);
// 首页-》修改用户头像和用户名字
$WangYX[] = array(
	'url' => '/admin/organ/updateUserMsg',
	'name' => '首页-修改用户头像和用户名字',
	'type' => 'post',
	'data' => "{'userimg':'lilisang','username':'Longwang'}",
	'tip' => "{'userimg':'头像url','username':'用户名或昵称'}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
// 首页-》管理员修改密码发送短信
$WangYX[] = array(
	'url' => '/admin/organ/sendMessage',
	'name' => '首页-管理员修改密码发送短信',
	'type' => 'post',
	'data' => "{'prephone':'86','mobile':'18801347168'}",
	'tip' => "{'prephone':'手机号前缀','username':'手机号码'}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
// 首页-》发送更新密码请求
$WangYX[] = array(
	'url' => '/admin/organ/updatePass',
	'name' => '首页-发送更新密码请求',
	'type' => 'post',
	'data' => "{'mark':'获取到的验证码','pass':'123456','repass':'123456'}",
	'tip' => "{'mark':'先通过获取短信接口来得到短信验证码','pass':'新密码','repass':'重复新密码'}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
// 首页-》更换手机号
$WangYX[] = array(
	'url' => '/admin/organ/changeMobile',
	'name' => '首页-更换手机号',
	'type' => 'post',
	'data' => "{'mark':'获取到的验证码','oldphone':'18801347166','newphone':'18801347168'}",
	'tip' => "{'mark':'先通过获取短信接口来得到短信验证码','oldphone':'旧手机号','newphone':'新手机号'}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
// 首页-》机构数据统计
$WangYX[] = array(
	'url' => '/admin/organ/getOrganAnalysis',
	'name' => '首页-机构数据统计',
	'type' => 'post',
	'data' => "{
	    				'courseline':'day',
	    				'flowline':'week',
	    			}",
	'tip' => "{
	    				'courseline':'课程统计按天 周 月，可传 day  week  month',
	    				'flowline':'可传 week month',
	    			}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',

                                'totalflowcash': '总流水额',
                                'totalorder': '总订单数',
                                'studenttotal':'总学生数' ,
                                'teachtotal': '教师总数' ,

                                'classarr': '班级统计' ,

                                'classingnum': '招生中的班级数' ,
                                'classtodaynum': '今日开班' ,
                                'classyesnum': '昨日开班' ,
                                'monthclassnum': '本月开班' ,

                                'studentarr':'学生统计' ,
                                'stutodaynum':'今日新增' ,
                                'stuyesnum':'昨日新增' ,
                                'monthnum':'本月新增' ,
                                'studenttotal':'总学生数' ,

                            }",
);
// 首页-》机构数据统计 课程按时间选择返回
$WangYX[] = array(
	'url' => '/admin/organ/getOrganAnaCourse',
	'name' => '首页-机构数据统计-课程按时间选择返回',
	'type' => 'post',
	'data' => "{
	    				'courseline':'day',
	    			}",
	'tip' => "{
	    				'courseline':'课程统计按天 周 月，可传 day  week  month',
	    			}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',

                                'coursearr': '课程 时间数组下标：学生总数' ,
                                'realnum': '学生数量' ,
                                'num': '课节数目' ,
                                'intime': '日期' ,
                            }",
);
// 首页-》机构数据统计-交易流水统计
$WangYX[] = array(
	'url' => '/admin/organ/getOrganAnaFlow',
	'name' => '首页-机构数据统计-交易流水统计',
	'type' => 'post',
	'data' => "{
	    				'flowline':'week',
	    			}",
	'tip' => "{
	    				'flowline':'可传 week month',
	    			}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',

                                'coursearr': '课程 时间数组下标：学生总数' ,
                                'flowarr': '流水统计' ,
                                'datestr': '日期' ,
                                'num': '订单数' ,
                                'totalpay': '金额' ,
                            }"
	    			);


	// 首页-》机构数据统计-交易流水统计
	$WangYX[] = array(
		'url' => '/admin/organ/getOrganPayAnaFlow',
		'name' => '首页-机构数据统计-各端流水统计',
		'type' => 'post',
		'data' => "{
					'flowline':'week',
				}",
		'tip' => "{
					'flowline':'可传 week month',
				}",
		'returns' => "{
					'code': '返回的查询标识，0为正常返回，其他为异常',
					'data': '最外层data为此次请求的返回数据',
					'info': '此次请求返回数据描述',
	
					'datestr': '日期' ,
					'pcprice': 'pc流水金额' ,
					'wxprice': '微信端流水金额' ,
					'appprice': 'app端流水金额' ,
				}"
	);
/* 	// 用户-教师管理-教师列表
	$WangYX[] = array(
	    			'url'=>'/admin/teacher/getTeachList',
	    			'name'=>'用户-教师管理-教师列表、昵称及手机号检索分页',
	    			'type'=>'post',
	    			'data'=>"{'pagenum':1 }",
	    			'tip'=>"{'mobile':'18888888888','nickname':'昵称' ,'pagenum':'页码'}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                                'teacherid': '教师id，用户id同一个',
                                'prphone':'手机号前缀' ,
                                'mobile':'手机号' ,
                                'teachername': '教师名称' ,
                                'nickname': '教师昵称' ,
                                'accountstatus': '账号状态，0可使用 1禁用' ,
                                'lastlogin': '最后登陆时间' ,
                                'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
);
// 查看教师详情信息
$WangYX[] = array(
	'url' => '/admin/teacher/teachInfo',
	'name' => '用户-教师管理-教师详情页面',
	'type' => 'post',
	'data' => "{'teachid':1 }",
	'tip' => "{'teachid':'教师id'}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'baseinfo' : '教师基本信息数组键' ,
	    						'teacherid' : '教师id' ,
	    						'imageurl' : '教师图片' ,
	    						'prphone' : '手机号前缀' ,
	    						'mobile' : '手机号' ,
	    						'teachername' : '教师名字' ,
	    						'nickname' : '教师昵称' ,
	    						'accountstatus' : ' 账号状态 0可使用 1禁用' ,
	    						'addtime' : ' 账号添加时间' ,
	    						'sex' : ' 0保密 1男 2女' ,
	    						'country' : '国家' ,
	    						'province' : '省' ,
	    						'city' : '市' ,
	    						'profile' : '个人简介' ,
	    						'birth' : '教师生日' ,
	    						'logintime' : '最后一次登录时间' ,

                                'teachlable': '教师已经选择的标签',
                                'name':'标签名字' ,
                                'list':'标签对应的所有的值数组' ,
                                'id':'标签值的id' ,
                                'tagname':'标签值名字' ,
                                'fatherid':'标签名字对应id，即上层名字对应的id' ,
                                'selectedid': '教师已经选中的标签值对应的id数组' ,

                                'timeavailable': '教师设置的空余时间数组' ,
                                'id': '主键目前不使用' ,
                                'week': '周几，1代表周一依次类推' ,
                                'mark': '这一天老师选中的可约时间数组键，参考配置数组' ,

                            }",
);
// 用户-教师管理-教师详情页面-编辑
$WangYX[] = array(
	'url' => '/admin/teacher/getTeachMsg',
	'name' => '用户-教师管理-教师详情页面-编辑',
	'type' => 'post',
	'data' => "{'teachid':1 }",
	'tip' => "{'teachid':'教师id'}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'baseinfo' : '教师基本信息数组键' ,
	    						'teacherid' : '教师id' ,
	    						'imageurl' : '教师图片' ,
	    						'prphone' : '手机号前缀' ,
	    						'mobile' : '手机号' ,
	    						'teachername' : '教师名字' ,
	    						'nickname' : '教师昵称' ,
	    						'accountstatus' : ' 账号状态 0可使用 1禁用' ,
	    						'sex' : ' 0保密 1男 2女' ,
	    						'profile' : '个人简介' ,
	    						'birth' : '教师生日' ,
	    						'country' : '国家id 根据地区json处理' ,
	    						'province' : '省份id 根据地区json处理' ,
	    						'city' : '城市id 根据地区json处理 ' ,

                            }",
);
//更新教师信息
$WangYX[] = array(
	'url' => '/admin/teacher/updateTeacherMsg',
	'name' => '用户-教师管理-教师详情页面-更新编辑',
	'type' => 'post',
	'data' => "{
	    				'imageurl':'uarl',
	    				'nickname':'12',
	    				'truename':'',
	    				'sex':0,
	    				'country':23,
	    				'province':13,
	    				'city':56,
	    				'birth':'2018-3-5',
	    				'profile':'简介',
	    				'status':2,
	    				'teacherid':5,
	    				'prphone':'+86'
	    			}",
	'tip' => "{'imageurl':'教师图片链接',
		    			'nickname':'昵称',
		    			'truename':'真实姓名',
		    			'sex':'性别id',
		    			'country':'国家id',
		    			'province':'省份id',
		    			'city':'城市id',
		    			'birth':'2018-3-5',
		    			'profile':'简介',
		    			'status':'是否启用状态',
		    			'teacherid':'需要更新数据的教师id',
		    			'prphone':'号码国别'
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
// 修改手机号
$WangYX[] = array(
	'url' => '/admin/teacher/updateMobile',
	'name' => '教师端-个人资料=>资料编辑-修改手机号',
	'type' => 'POST',
	'data' => "{
					'oldmobile':'18880915122',
					'newmobile':'18879215446',
		            'teacherid':'695640',
		            'code':'695640',
		            'prphone':'86'
	        	}",
	'tip' => "{'oldmobile':'修改前手机号',
					'newmobile':'新手机号',
				  	'teacherid' :'教师id',
				  	'code' :'验证码',
				  	'prphone' : '手机号前缀'
				}",
	'returns' => "{
					'code': '返回的查询标识,0为正常返回,其他为异常',
					'data': '最外层data为此次请求的返回数据',
					'info': '此次请求返回数据描述'}",
);

//修改密码
$WangYX[] = array(
	'url' => '/admin/teacher/updatePass',
	'name' => '教师端-个人资料=>资料编辑-修改密码',
	'type' => 'POST',
	'data' => "{

					'mobile':'18241893379',
					'code':'333',
					'newpass':'123456',
					'repass':'123456'}",
	'tip' => "{
				  'mobile ':'手机号',
				  'code':'验证码',
				  'organid':'机构编号',
				  'newpass':'新密码',
					'repass':'再次输入密码'}",
	'returns' => "{
					'code': '返回的查询标识,0为正常返回,其他为异常',
					'data': '最外层data为此次请求的返回数据',
					'info': '此次请求返回数据描述',}",
);
//教师修改手机号发短信
$WangYX[] = array(
	'url' => '/admin/teacher/sendUpdateMobileMsg',
	'name' => '教师端-个人资料=>资料编辑-修改手机号发短信',
	'type' => 'POST',
	'data' => "{
					'newmobile':'199999999',
					'prphone':'86'
				   }",
	'tip' => "{'newmobile':'新手机号',
				     'organid':'组织id',
				     'prphone':'手机区号'}",
	'returns' => "{
					'code': '返回的查询标识,0为正常返回,其他为异常',
					'data': '最外层data为此次请求的返回数据',
					'info': '此次请求返回数据描述',}",
);
//教师修改手机号发短信
$WangYX[] = array(
	'url' => '/admin/teacher/sendUpdatePassMsg',
	'name' => '教师端-个人资料=>资料编辑-修改当前密码发短信',
	'type' => 'POST',
	'data' => "{
					'mobile':'199999999',
					'prphone':'86'
				   }",
	'tip' => "{'newmobile':'新手机号',
				     'organid':'组织id',
				     'prphone':'手机区号'}",
	'returns' => "{
					'code': '返回的查询标识,0为正常返回,其他为异常',
					'data': '最外层data为此次请求的返回数据',
					'info': '此次请求返回数据描述',}",
);

//添加教师信息
// 'imageurl':'http://www.baidu.com/iamge/imge.png',
//     				'mobile':'123123123',
//     				'nickname':'匈牙利',
//     				'teachername':'名字不能重复',
//     				'sex':0,
//     				'country':23,
//     				'province':31,
//     				'city':123,
//     				'birth':'2018-5-2',
//     				'profile':'添加新老师简介',
//     				'password':'213213213213',
//     				'repassword':'213213213213',
//     				'status':2,
//     				'prphone':'+86'
$WangYX[] = array(
	'url' => '/admin/teacher/addTeacherMsg',
	'name' => '用户-教师管理-教师详情页面-添加教师信息',
	'type' => 'post',
	'data' => "{
	    				'nickname':'老师姓名',
	    				'teachername':'登录帐号',
	    				'prphone':'手机前缀',
	    				'mobile':'手机号',
	    				'password':'213213213213',
	    				'repassword':'213213213213',
	    				'status':0,
	    			}",
	'tip' => "{
	    				'nickname':'老师姓名',
	    				'teachername':'登录帐号',
	    				'prphone':'手机前缀',
	    				'mobile':'手机号',
		    			'password':'用户密码',
		    			'repassword':'重复密码',
		    			'status':'账户状态 0可使用 1禁用 默认0启用',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);

//删除教师
$WangYX[] = array(
	'url' => '/admin/teacher/deleteTeacher',
	'name' => '用户-教师管理-教师详情页面-删除教师',
	'type' => 'post',
	'data' => "{
	    				'teacherid':5,
	    			}",
	'tip' => "{
	    				'teacherid':'要删除的教师id',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//获取机构所有的教师标签和值
$WangYX[] = array(
	'url' => '/admin/teacher/getLabelAndValue',
	'name' => '用户-教师管理-教师详情页面-获取机构所有的教师标签和值',
	'type' => 'post',
	'data' => "{
	    				'teachid':1,
	    			}",
	'tip' => "{
	    				'teachid':'教师id',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '标签名字对应id，即上层名字对应的id',
	    						'tagname': '标签名',
	    						'vid': '标签值对应id',
	    						'vtagname': '标签值',
	    						'selected': 'true 代表教师已经有该标签，false表示没有',
                            }",
);
//更新教师拥有的标签和值
$WangYX[] = array(
	'url' => '/admin/teacher/updateBindLabel',
	'name' => '用户-教师管理-教师详情页面-更新教师拥有的标签值',
	'type' => 'post',
	'data' => "{
	    				'vids':[{id:4,vid:5},{id:4,vid:7}],
	    				'teachid':1,
	    			}",
	'tip' => "{
	    				'vids':'标签值vid，和父级id 的对象数组',
	    				'teachid':'教师id',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//设置教师空闲时间
$WangYX[] = array(
	'url' => '/admin/teacher/updateWeekIdle',
	'name' => '用户-教师管理-教师详情页面-设置教师空闲时间',
	'type' => 'post',
	'data' => "{
	    				'week':[{weekday:1,flag:'5,7'},{weekday:2,flag:'5,8,9'},{weekday:3,flag:'5'},{weekday:4,flag:'5'},{weekday:5,flag:'5,12,13'},{weekday:6,flag:'5'},{weekday:7,flag:'5,8,9'}],
	    				'teachid':1,
	    			}",
	'tip' => "{
	    				'week':'周几weekday，时间下标多个用逗号英文隔开 flag',
	    				'teachid':'教师id',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//修改账户启用状态
$WangYX[] = array(
	'url' => '/admin/teacher/switchTeachStatus',
	'name' => '用户-教师管理-教师详情页面-修改账户启用状态',
	'type' => 'post',
	'data' => "{
	    				'teacherid':5,
	    				'dataflag':1,
	    			}",
	'tip' => "{
	    				'teacherid':'要删除的教师id',
	    				'dataflag':'要修改的状态值',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			); */

/* 	//教师标签列表
	$WangYX[] = array(
	    			'url'=>'/admin/teacher/teachLableList',
	    			'name'=>'用户-教师标签-标签列表页',
	    			'type'=>'post',
	    			'data'=>"{
	    				'pagenum':1,
	    			}",
	'tip' => "{
	    				'pagenum':'当前页码',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '标签id',
	    						'tagname': '标签名字',
	    						'addtime': '添加标签时的时间戳',
	    						'fatherid': '父级标签id',
	    						'status': '标签的启用状态，0禁用，1启用',
	    						'num': '标签值的个数',
	    						'strdate': '创建标签的日期',

	    						'pagesize': '每页最多条数',
	    						'pagenum': '当前页码',
	    						'total': '总记录数',
                            }",
);

//教师标签列表 添加标签
$WangYX[] = array(
	'url' => '/admin/teacher/addTeachLable',
	'name' => '用户-教师标签-标签列表页-添加标签',
	'type' => 'post',
	'data' => "{
	    				'lablename':'tagname',
	    			}",
	'tip' => "{
	    				'lablename':'标签名称',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);

//教师标签列表 编辑标签
$WangYX[] = array(
	'url' => '/admin/teacher/saveTeachLable',
	'name' => '用户-教师标签-标签列表页-编辑标签',
	'type' => 'post',
	'data' => "{
	    				'lableid': 1,
	    				'lablename':'newtagname',
	    			}",
	'tip' => "{
	    				'lableid': '要更改的标签id',
	    				'lablename':'新标签名称',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//教师标签列表 切换标签启用状态
$WangYX[] = array(
	'url' => '/admin/teacher/switchLabelStatus',
	'name' => '用户-教师标签-标签列表页-切换标签启用状态',
	'type' => 'post',
	'data' => "{
	    				'lableid': 1,
	    				'dataflag':1,
	    			}",
	'tip' => "{
	    				'lableid': '要更改的标签id',
	    				'lablename':'0禁用，1启用',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//教师标签列表 删除标签
$WangYX[] = array(
	'url' => '/admin/teacher/delTeachLable',
	'name' => '用户-教师标签-标签列表页-删除标签',
	'type' => 'post',
	'data' => "{
	    				'lableid': 1,
	    				'delflag': 0,
	    			}",
	'tip' => "{
	    				'lableid': '要删除的标签id',
	    				'delflag': '0代表如果有老师在使用不删除，其他值代表强制删除不查询是否使用',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);

//教师 标签值 列表
$WangYX[] = array(
	'url' => '/admin/teacher/getValueList',
	'name' => '用户-教师标签-标签列表页-教师标签值列表',
	'type' => 'post',
	'data' => "{
	    				'lableid': 7,
	    				'pagenum': 1,
	    			}",
	'tip' => "{
	    				'lableid': '要获取那个标签的值，标签id',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '标签值的id',
	    						'tagname': '标签值的名字',
	    						'fatherid': '父级标签id',

	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
);
//教师 标签值 列表-上移下移
$WangYX[] = array(
	'url' => '/admin/teacher/exchangePos',
	'name' => '用户-教师标签-标签列表页-教师标签值列表-上移下移',
	'type' => 'post',
	'data' => "{
	    				'idx1': 5,
	    				'idx2': 6,
	    			}",
	'tip' => "{
	    				'idx1': '所要交换的标签纸的',
	    				'idx2': '',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '标签值的id',
	    						'tagname': '标签值的名字',
	    						'fatherid': '父级标签id',
                            }",
);
//教师标签值列表-修改值
$WangYX[] = array(
	'url' => '/admin/teacher/updateTagVal',
	'name' => '用户-教师标签-标签列表页-教师标签值列表-修改值',
	'type' => 'post',
	'data' => "{
	    				'lableid': 5,
	    				'lablename': '碧池',
	    			}",
	'tip' => "{
	    				'lableid': '需要修改的标签值的id' ,
	    				'lablename': '值要更新为' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//教师标签值列表-添加新的标签值
$WangYX[] = array(
	'url' => '/admin/teacher/addLableValue',
	'name' => '用户-教师标签-标签列表页-教师标签值列表-新增值',
	'type' => 'post',
	'data' => "{
	    				'parentid': 4,
	    				'tagname': '新增测试标签值',
	    			}",
	'tip' => "{
	    				'parentid': '父级标签id',
	    				'tagname': '新增值得名字' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//教师标签值列表-删除标签值
$WangYX[] = array(
	'url' => '/admin/teacher/removeVal',
	'name' => '用户-教师标签-标签列表页-教师标签值列表-删除标签值',
	'type' => 'post',
	'data' => "{
	    				'lableid': 6,
	    			}",
	'tip' => "{
	    				'lableid': '需要删除的标签值的id' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			); */

	//订单-订单列表
	$WangYX[] = array(
	    			'url'=>'/admin/order/getOrderList',
	    			'name'=>'订单-订单列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'pagenum': 1,
	    			}",
	'tip' => "{
	    				'ordernum': '订单编号',
	    				'ordertype': '订单类型0已下单，10超时未支付，20已支付，30申请退款，40已退款，为空获取所有类型',
	    				'pagenum': '页码',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'orderlist': '订单信息',
	    						'ordernum': '订单编号',
	    						'orderstatus': '0已下单，10超时未支付，20已支付，30申请退款，40已退款',
	    						'ordertime': '下单时间',
	    						'studentid': '学生id',
	    						'paytype': '支付类型 0其他，1余额，2微信，3支付宝，4银联',
	    						'amount': '订单金额',
	    						'coursename': '课程名称',
	    						'teacherid': '教师id',
	    						'teachername': '教师名称',
	    						'studentname': '学生名称',

	    						'statusnum':'订单状态及数量统计' ,
	    						'name':'订单类型' ,
	    						'num':'对应订单类型的数量' ,

	    						'pageinfo':'分页信息' ,
	    						'pagesize':'每页最多条数' ,
	    						'pagenum':'当前页码' ,
	    						'total':'总记录数' ,

                            }",
);

//订单-订单列表-订单详情
$WangYX[] = array(
	'url' => '/admin/order/orderInfo',
	'name' => '订单-订单列表-订单详情',
	'type' => 'post',
	'data' => "{
	    				'orderid': 1,
	    			}",
	'tip' => "{
	    				'orderid': '订单编号',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'orderlist': '订单信息',
	    						'ordernum': '订单编号',
	    						'orderstatus': '0已下单，1超时未支付，2已支付，3申请退款，4已退款',
	    						'ordertime': '下单时间',
	    						'studentid': '学生id',
	    						'ordersource': '订单来源 1pc 2手机',
	    						'paytype': '支付类型 0其他，1余额，2微信，3支付宝，4银联',
	    						'originprice': '课程原价',
	    						'discount': '折扣金额',
	    						'amount': '订单金额',
	    						'coursename': '课程名称',
	    						'classname': '班级名称',
	    						'teacherid': '订单id',
	    						'teachername': '教师名称',
	    						'studentname': '学生名称',
                            }",
);

//促销-课程推荐列表
$WangYX[] = array(
	'url' => '/admin/recommend/getCourseList',
	'name' => '促销-课程推荐列表',
	'type' => 'post',
	'data' => "{
	    				'pagenum': 1,
	    			}",
	'tip' => "{
	    				'coursename': '课程名字',
	    				'pagenum': '页码',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '班级id',
	    						'gradename': '班级名称',
	    						'price': '课时单价',
	    						'curriculumid': '课程id',
	    						'type': '班级类型 1是一对一 2是小课班 3是大课班',
	    						'recommend': '是否推荐0默认不推荐，1推荐',
	    						'coursename': '课程名称',
	    						'imageurl': '课程图片',

	    						'pageinfo':'分页信息' ,
	    						'pagesize':'每页最多条数' ,
	    						'pagenum':'当前页码' ,
	    						'total':'总记录数' ,
                            }",
);
//促销-课程推荐-上移下移
$WangYX[] = array(
	'url' => '/admin/recommend/exchangeCoursePos',
	'name' => '促销-课程推荐-上移下移',
	'type' => 'post',
	'data' => "{
	    				'courseid1': 1,
	    				'courseid2': 2,
	    			}",
	'tip' => "{
	    				'courseid1': '要交换位置的排课id',
	    				'courseid2': '要交换位置的排课id',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);

//促销-课程推荐-切换课程的推荐状态
$WangYX[] = array(
	'url' => '/admin/recommend/switchCourseStatus',
	'name' => '促销-课程推荐-切换课程的推荐状态',
	'type' => 'post',
	'data' => "{
	    				'courseid': 1,
	    				'status': 1,
	    			}",
	'tip' => "{
	    				'courseid': '排课id',
	    				'status': ' 0是暂停招生，1是未暂停招生',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//促销-老师推荐列表
$WangYX[] = array(
	'url' => '/admin/recommend/getTeacherList',
	'name' => '促销-老师推荐列表',
	'type' => 'post',
	'data' => "{
	    				'pagenum': 1,
	    			}",
	'tip' => "{
	    				'teachername': '需要检索的教师名称',
	    				'pagenum': '页码',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'teacherid': '教师id',
	    						'teachername': '教师名称',
	    						'recommend': '是否推荐0不推荐，1推荐',

	    						'pageinfo':'分页信息' ,
	    						'pagesize':'每页最多条数' ,
	    						'pagenum':'当前页码' ,
	    						'total':'总记录数' ,
                            }",
);

//促销-老师推荐列表
$WangYX[] = array(
	'url' => '/admin/recommend/exchangeTeacherPos',
	'name' => '促销-老师推荐列表-上移下移',
	'type' => 'post',
	'data' => "{
	    				'teacherid1': 1,
	    				'teacherid2': 2,
	    			}",
	'tip' => "{
	    				'teacherid1': '要交换位置的教师id',
	    				'teacherid2': '要交换位置的教师id',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//促销-老师推荐列表-切换老师的推荐状态
$WangYX[] = array(
	'url' => '/admin/recommend/switchTeacherStatus',
	'name' => '促销-老师推荐列表-切换老师的推荐状态',
	'type' => 'post',
	'data' => "{
	    				'teacherid': 1,
	    				'status': 1,
	    			}",
	'tip' => "{
	    				'teacherid': '需要设置的教师id',
	    				'status': '教师状态',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//促销-老师推荐列表-设置老师的推广图片
$WangYX[] = array(
	'url' => '/admin/recommend/addTeacherImage',
	'name' => '促销-老师推荐列表-设置老师的推广图片',
	'type' => 'post',
	'data' => "{
	    				'teacherid': 1,
	    				'image': 'https://gss3.bdstatic.com/-Po3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike92%2C5%2C5%2C92%2C30/sign=4954aad65b2c11dfcadcb771024e09b5/a6efce1b9d16fdfa84dcabdbb88f8c5494ee7b56.jpg',
	    				'profile': '景甜',
	    			}",
	'tip' => "{
	    				'teacherid': '需要设置的教师id',
	    				'image': '图片地址必填' ,
	    				'profile':'教师描述必填' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//促销-首页广告-图片列表
$WangYX[] = array(
	'url' => '/admin/recommend/getOrganSlide',
	'name' => '促销-首页广告-图片列表',
	'type' => 'post',
	'data' => "{

	    			}",
	'tip' => "{
	    				'无参':'无参' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '图片id',
	    						'remark': '图片备注',
	    						'imagepath': '图片链接地址',
	    						'sortid': '排序标识',
                            }",
);
//促销-首页广告-添加图片
$WangYX[] = array(
	'url' => '/admin/recommend/addSlideImage',
	'name' => '促销-首页广告-添加图片',
	'type' => 'post',
	'data' => "{
	    				'remark':'景甜' ,
	    				'image': 'https://gss3.bdstatic.com/-Po3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike92%2C5%2C5%2C92%2C30/sign=4954aad65b2c11dfcadcb771024e09b5/a6efce1b9d16fdfa84dcabdbb88f8c5494ee7b56.jpg' ,
	    			}",
	'tip' => "{
	    				'remark':'图片描述必填' ,
	    				'image': '图片地址必填' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//促销-首页广告-编辑图片
$WangYX[] = array(
	'url' => '/admin/recommend/editSlideImage',
	'name' => '促销-首页广告-编辑图片',
	'type' => 'post',
	'data' => "{
	    				'id': 3 ,
	    				'remark':'景甜' ,
	    				'image': 'https://gss3.bdstatic.com/-Po3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike92%2C5%2C5%2C92%2C30/sign=4954aad65b2c11dfcadcb771024e09b5/a6efce1b9d16fdfa84dcabdbb88f8c5494ee7b56.jpg' ,
	    			}",
	'tip' => "{
	    				'id': '要替换的记录id' ,
	    				'remark':'图片描述必填' ,
	    				'image': '图片地址必填' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//促销-首页广告-删除图片
$WangYX[] = array(
	'url' => '/admin/recommend/delSlideImage',
	'name' => '促销-首页广告-删除图片',
	'type' => 'post',
	'data' => "{
	    				'id': 7 ,
	    			}",
	'tip' => "{
	    				'id': '要删除的记录id' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//	//设置-收款设置
//	$WangYX[] = array(
//	    			'url'=>'/admin/organ/getPayMethod',
//	    			'name'=>'设置-获取收款设置',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    			}",
//	    			'tip'=>"{
//	    				'不需要参数': '不需要参数',
//		    		}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//	    						'id': '机构收款表id',
//	    						'bankname': '银行名称',
//	    						'branchname': '支行名称',
//	    						'cardid': '银行卡号',
//	    						'cardholder': '持卡人名字',
//	    						'wechatmark': '收款二维码',
//	    						'accountpayee': '支付宝账号',
//	    						'namepayee': '支付宝名称',
//                            }",
//	    			);
//设置-收款设置-修改收款设置
//	$WangYX[] = array(
//	    			'url'=>'/admin/organ/updatePayMethod',
//	    			'name'=>'设置-修改收款设置',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'bankname':'bankname' ,
//	    				'branchname':'分行名称' ,
//	    				'cardid':'6774567568456844' ,
//	    				'cardholder':'赵小竹' ,
//	    				'wechatmark':'winxin.png' ,
//	    				'accountpayee':'1888888888' ,
//	    				'namepayee':'赵小竹' ,
//	    			}",
//	    			'tip'=>"{
//	    				'bankname':'银行名称' ,
//	    				'branchname':'分行名称' ,
//	    				'cardid':'卡号' ,
//	    				'cardholder':'持卡人姓名' ,
//	    				'wechatmark':'付款二维码' ,
//	    				'accountpayee':'支付宝账号' ,
//	    				'namepayee':'支付宝名字' ,
//		    		}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }",
//	    			);
//设置-获取课堂配置
$WangYX[] = array(
	'url' => '/admin/organ/getOrganConfig',
	'name' => '机构后台-》设置-》获取企业设置配置',
	'type' => 'post',
	'data' => "",
	'tip' => "",
	'returns' => "{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'classhours':'课时时长',
								'organname':'机构名称',
								'profile':'机构简介',
								'summary':'机构概述',
								'imageurl':'机构logo',
								'hotline':'客服热线',
								'email':'客服邮箱',
								'contactname':'联系人姓名',
								'contactphone':'联系人电话号码',
								'contactemail':'联系人电话号码',

			   }",
);
//设置-进行课堂配置
$WangYX[] = array(
	'url' => '/admin/organ/setOrganBaseInfo',
	'name' => '机构后台-》设置-》企业设置编辑',
	'type' => 'post',
	'data' => "{'organname':'柠檬教育','summary':'柠檬教育','imageurl':'www.baidu.com','hotline':'34234233','email':'weqw@qq.com','contactname':'柠檬','contactphone':'18610374671','contactemail':'324@qq.com'}",
	'tip' => "{'classhours':'课时时长',
				'organname':'机构名称',
				'profile':'机构简介',
				'summary':'机构概述',
				'imageurl':'机构logo',
				'hotline':'客服热线',
				'email':'客服邮箱',
				'contactname':'联系人姓名',
				'contactphone':'联系人电话号码',
				'contactemail':'联系人电话号码',
				}",
	'returns' => "{
						'code': '返回的查询标识，0为正常返回，其他为异常',
						'data': '最外层data为此次请求的返回数据',
						'info': '此次请求返回数据描述'
				   }",
);
//权限-成员管理-成员列表
$WangYX[] = array(
	'url' => '/admin/organ/getAdminList',
	'name' => '权限-成员管理-成员列表',
	'type' => 'post',
	'data' => "{
	    				'username':'' ,
	    				'pagenum':1,
	    			}",
	'tip' => "{
	    				'username':'机构添加的管理员名字' ,
	    				'pagenum':'当前页码' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'uid': '管理员id',
	    						'username': '管理员名字',
	    						'mobile': '管理员手机号',
	    						'addtime': '管理员添加时间',
	    						'status': '0默认使用，1禁用',
	    						'useraccount': '用户账号名字',
	    						'logintime': '最近一次登录时间',
	    						'info': '备注说明',
	    						'groupstr':'分组',

	    						'pageinfo':'分页信息' ,
	    						'pagesize':'每页最多条数' ,
	    						'pagenum':'当前页码' ,
	    						'total':'总记录数' ,
                            }",
);

//权限-成员管理-添加管理员
$WangYX[] = array(
	'url' => '/admin/organ/addAdminUser',
	'name' => '权限-成员管理-添加管理员',
	'type' => 'post',
	'data' => "{
	    				'username':'auserarl' ,
	    				'mobile':'18807836753' ,
	    				'password':'add user uarl' ,
	    				'repassword':'add user uarl' ,
	    				'useraccount':'add user uarl',
	    				'info':'备注信息',
	    				'groupids':'1',
	    			}",
	'tip' => "{
	    				'username':'用户名' ,
	    				'mobile':'18807836753，手机号不能重复' ,
	    				'password':'密码' ,
	    				'repassword':'重复密码' ,
	    				'useraccount':'账号名',
	    				'info':'备注信息',
	    				'groupids':'部门ID',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//权限-成员管理-获取单个机构管理员的信息
$WangYX[] = array(
	'url' => '/admin/organ/getAdminUser',
	'name' => '权限-成员管理-获取单个机构管理员的信息',
	'type' => 'post',
	'data' => "{
	    				'adminid':1 ,
	    			}",
	'tip' => "{
	    				'adminid':'管理员id' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'uid': '管理员id',
	    						'username': '管理员名字',
	    						'mobile': '管理员手机号',
	    						'addtime': '添加时间',
	    						'status': '账号状态，0可使用1禁用',
	    						'useraccount': '账号名字',
	    						'logintime': '最后登陆时间',
                            }",
);
//权限-成员管理-更新管理员信息
$WangYX[] = array(
	'url' => '/admin/organ/updateAdminUser',
	'name' => '权限-成员管理-更新管理员信息',
	'type' => 'post',
	'data' => "{
	    				'adminid':1 ,
	    				'username':'auserarl' ,
	    				'mobile':'18807936753' ,
	    				'password':'add user uarl' ,
	    				'repassword':'add user uarl' ,
	    				'useraccount':'adduser uarl',
	    				'info':'备注信息',
	    			}",
	'tip' => "{
	    				'adminid':'管理员id' ,
	    				'username':'用户名' ,
	    				'mobile':'18807836753，手机号不能重复' ,
	    				'password':'密码' ,
	    				'repassword':'重复密码' ,
	    				'useraccount':'账号名',
	    				'info':'备注信息',
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '管理总表id，不是提交的adminid',
	    						'username': '管理员名字',
	    						'mobile': '管理员手机号',
	    						'addtime': '添加时间',
	    						'status': '账号状态，0可使用1禁用',
	    						'useraccount': '账号名字',
	    						'logintime': '最后登陆时间',
                            }",
);
//权限-成员管理-删除管理员
$WangYX[] = array(
	'url' => '/admin/organ/delAdminUser',
	'name' => '权限-成员管理-删除管理员',
	'type' => 'post',
	'data' => "{
	    				'adminid':21 ,
	    			}",
	'tip' => "{
	    				'adminid':'管理员id' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);
//权限-成员管理-切换管理员的启用状态
$WangYX[] = array(
	'url' => '/admin/organ/switchAdminFlag',
	'name' => '权限-成员管理-切换管理员的启用状态',
	'type' => 'post',
	'data' => "{
	    				'adminid':22 ,
	    				'flag': 1 ,
	    			}",
	'tip' => "{
	    				'adminid':'管理员id' ,
	    				'flag': '0默认使用，1禁用' ,
		    		}",
	'returns' => "{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
);

//	// 官方 数据统计
//	$WangYX[] = array(
//	    			'url'=>'/official/Index/index',
//	    			'name'=>'官方 数据统计',
//	    			'type'=>'post',
//	    			'data'=>"",
//	    			'tip'=>"",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//
//                                'totalflowcash': '总流水额',
//                                'organtotal': '总机构数',
//                                'studenttotal':'总学生数' ,
//                                'teachtotal': '教师总数' ,
//
//                                'organarr': '机构统计' ,
//
//                                'organtotal': '招生中的班级数' ,
//                                'organtodaynum': '今日新增机构' ,
//                                'organyesnum': '昨日新增机构' ,
//                                'monthorgannum': '本月新增机构' ,
//
//                                'studentarr':'学生统计' ,
//                                'stutodaynum':'今日新增' ,
//                                'stuyesnum':'昨日新增' ,
//                                'monthnum':'本月新增' ,
//                                'studenttotal':'总学生数' ,
//
//                            }"
//	    			);
//	// 官方 数据统计 课程按时间选择返回
//	$WangYX[] = array(
//	    			'url'=>'/official/Index/getOrganAnaCourse',
//	    			'name'=>'官方 数据统计-课程按时间选择返回',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'courseline':'day',
//	    			}",
//	    			'tip'=>"{
//	    				'courseline':'课程统计按天 周 月，可传 day  week  month',
//	    			}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//
//                                'coursearr': '课程 时间数组下标：学生总数' ,
//                                'realnum': '学生数量' ,
//                                'num': '课节数目' ,
//                                'intime': '日期' ,
//                            }"
//	    			);
//	// 官方 数据统计-交易流水统计
//	$WangYX[] = array(
//	    			'url'=>'/official/Index/getOrganAnaFlow',
//	    			'name'=>'官方 数据统计-交易流水统计',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'flowline':'week',
//	    			}",
//	    			'tip'=>"{
//	    				'flowline':'可传 week month',
//	    			}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//
//                                'coursearr': '课程 时间数组下标：学生总数' ,
//                                'flowarr': '流水统计' ,
//                                'datestr': '日期' ,
//                                'num': '订单数' ,
//                                'totalpay': '金额' ,
//                            }"
//	    			);
//
//
//	//官方后台-学生管理-学生列表
//	$WangYX[] = array(
//	    			'url'=>'/official/student/getUserList',
//	    			'name'=>'官方后台-学生管理-学生列表',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'mobile': '',
//	    				'nickname': '',
//	    				'pagenum': 1,
//	    			}",
//	    			'tip'=>"{
//	    				'mobile': '手机号',
//	    				'nickname': '用户昵称',
//	    				'pagenum': '页码',
//		    		}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//	    						'id': '学生 id',
//	    						'sex': '性别 0保密 1男 2女',
//	    						'prphone': '手机号国别',
//	    						'mobile': '学生手机号',
//	    						'nickname': '学生昵称',
//	    						'status': '账号状态,默认0开启，1关闭',
//	    						'logintime': '最近一次登录时间',
//
//	    						'pageinfo': '分页信息' ,
//                                'pagesize': '每页最多条数' ,
//                                'pagenum': '当前页码' ,
//                                'total': '符合条件的总记录数目' ,
//                            }",
//	    			);
//	//官方后台-学生管理-学生列表-获取学生详细信息
//	$WangYX[] = array(
//	    			'url'=>'/official/student/getUserinfo',
//	    			'name'=>'官方后台-用户-学生管理-学生列表-学生详细信息',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'userid': 1,
//	    			}",
//	    			'tip'=>"{
//	    				'userid': '学生id',
//		    		}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//	    						'id': '学生 id',
//	    						'imageurl': '学生头像',
//	    						'prphone': '手机号国别',
//	    						'mobile': '学生手机号',
//	    						'birth': '学生生日时间戳形式',
//	    						'sex': '性别 0保密 1男 2女',
//	    						'logintime': '学生最近登陆时间',
//	    						'country': '国家编号',
//	    						'province': '省编号',
//	    						'city': '城市编号',
//	    						'profile': '学生简介',
//	    						'username': '学生名字',
//	    						'nickname': '学生昵称',
//	    						'status': '账号状态,默认0开启，1关闭',
//	    						'addtime': '账号添加时间',
//	    						'birthday': '学生生日',
//
//
//	    						'courselist': '购买的课程列表',
//	    						'id': '购买的课程id',
//	    						'coursename': '课程名称',
//	    						'classname': '班级名称',
//	    						'amount': '课程价格',
//	    						'type': '班级类型 1是一对一 2是小课班 3是大课班',
//                            }",
//	    			);
//
//	//官方后台-学生管理-学生列表-更改学生状态
//	$WangYX[] = array(
//	    			'url'=>'/official/Student/changeUserStatus',
//	    			'name'=>'官方后台-学生管理-学生列表-更改学生状态',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'userid': 6,
//	    				'flag': 1,
//	    			}",
//	    			'tip'=>"{
//	    				'userid': '需要修改的学生id',
//	    				'flag': '0开启，1关闭',
//		    		}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }",
//	    			);
//	// 官方配置-推荐分类
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/getCategoryRecomm',
//	    			'name'=>'官方配置-推荐分类',
//	    			'type'=>'post',
//	    			'data'=>"",
//	    			'tip'=>"",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	// 官方配置-所有分类
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/getCategoryTree',
//	    			'name'=>'官方配置-所有分类',
//	    			'type'=>'post',
//	    			'data'=>"",
//	    			'tip'=>"",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	// 官方配置-增加推荐分类
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/updateCateRecomm',
//	    			'name'=>'官方配置-增加推荐分类',
//	    			'type'=>'post',
//	    			'data'=>"{'ids':'89^90'}",
//	    			'tip'=>"{'ids':'增加推荐的id'}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	// 官方配置-删除推荐分类
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/delRecomm',
//	    			'name'=>'官方配置-删除推荐分类',
//	    			'type'=>'post',
//	    			'data'=>"{'cateid':'90'}",
//	    			'tip'=>"{'cateid':'要删除推荐对应id'}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	// 官方配置-移动推荐分类
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/exchangeCatePos',
//	    			'name'=>'官方配置-移动推荐分类',
//	    			'type'=>'post',
//	    			'data'=>"{'cateid1':26,'cateid2':27}",
//	    			'tip'=>"{'cateid1':'要交换的id','cateid2':'要交换的id'}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	// 官方配置-推荐机构
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/getRecommOrgan',
//	    			'name'=>'官方配置-推荐机构',
//	    			'type'=>'post',
//	    			'data'=>"",
//	    			'tip'=>"",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	// 官方配置-获取免费机构
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/getFreeOrgan',
//	    			'name'=>'官方配置-获取免费机构',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				        'pagenum': 1,
//	    						'name': '',
//                            }",
//	    			'tip'=>"{
//	    				        'pagenum': '第几页',
//	    						'name': '搜索的名字或者id',
//                            }",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	// 官方配置-新增推荐免费机构
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/updateFreeOrgan',
//	    			'name'=>'官方配置-新增推荐免费机构',
//	    			'type'=>'post',
//	    			'data'=>"{'ids':'5^6'}",
//	    			'tip'=>"{'ids':'增加推荐的id，多个id之间 使用 ^ 符号 隔开'}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	// 官方配置-交换两个免费推荐的机构的位置
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/exchangeOrganPos',
//	    			'name'=>'官方配置-移动两个推荐的位置',
//	    			'type'=>'post',
//	    			'data'=>"{'organ1':5,'organ2':6}",
//	    			'tip'=>"{'organ1':'交换的推荐的id','organ2':'交换的推荐的id'}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	// 官方配置-交换两个免费推荐的机构的位置
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/delCommOrgan',
//	    			'name'=>'官方配置-移出推荐列表',
//	    			'type'=>'post',
//	    			'data'=>"{'organid':6}",
//	    			'tip'=>"{'organid':'要删除推荐的id'}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }"
//	    			);
//	//官方配置-banner配置
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/getOrganSlide',
//	    			'name'=>'官方配置-banner配置',
//	    			'type'=>'post',
//	    			'data'=>"",
//	    			'tip'=>"{
//	    				'无参':'无参' ,
//		    		}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//	    						'id': '图片id',
//	    						'remark': '图片备注',
//	    						'imagepath': '图片链接地址',
//	    						'sortid': '排序标识',
//                            }",
//	    			);
//	//官方配置-banner配置-添加广告
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/addSlideImage',
//	    			'name'=>'官方配置-banner配置-添加广告',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'remark':'景甜' ,
//	    				'image': 'https://gss3.bdstatic.com/-Po3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike92%2C5%2C5%2C92%2C30/sign=4954aad65b2c11dfcadcb771024e09b5/a6efce1b9d16fdfa84dcabdbb88f8c5494ee7b56.jpg' ,
//	    			}",
//	    			'tip'=>"{
//	    				'remark':'图片描述必填' ,
//	    				'image': '图片地址必填' ,
//		    		}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }",
//	    			);
//	//官方配置-banner配置-编辑图片
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/editSlideImage',
//	    			'name'=>'官方配置-banner配置-编辑图片',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'id': 3 ,
//	    				'remark':'景甜' ,
//	    				'image': 'https://gss3.bdstatic.com/-Po3dSag_xI4khGkpoWK1HF6hhy/baike/c0%3Dbaike92%2C5%2C5%2C92%2C30/sign=4954aad65b2c11dfcadcb771024e09b5/a6efce1b9d16fdfa84dcabdbb88f8c5494ee7b56.jpg' ,
//	    			}",
//	    			'tip'=>"{
//	    				'id': '要替换的记录id' ,
//	    				'remark':'图片描述必填' ,
//	    				'image': '图片地址必填' ,
//		    		}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }",
//	    			);
//	//官方配置-banner配置-删除图片
//	$WangYX[] = array(
//	    			'url'=>'/official/recommend/delSlideImage',
//	    			'name'=>'官方配置-banner配置-删除图片',
//	    			'type'=>'post',
//	    			'data'=>"{
//	    				'id': 14 ,
//	    			}",
//	    			'tip'=>"{
//	    				'id': '要删除的记录id' ,
//		    		}",
//	    			'returns'=>"{
//	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
//	    						'data': '最外层data为此次请求的返回数据',
//	    						'info': '此次请求返回数据描述',
//                            }",
//	    			);

$WangYX[] = array(
	'url' => '/admin/organ/getPeriodinfo',
	'name' => '机构教师-我的课表-课时详情',
	'type' => 'post',
	'data' => "{'toteachtimeid':1,
									'id':1,'starttime':'2018-5-8 6:30:23','endtime':'2018-5-8 19:30:23',
									'date':'2018-5-8'}",
	'tip' => "{'toteachtimeid':'toteachtime表的主键id',
									'teacherid':'教师id','id':'Lessons表的主键',
								  'starttime':'该课时开始时间',
									'date':'输入的日期 Y-m-d'}",
	'returns' => "{
			          					'code': '返回的查询标识,0为正常返回,其他为异常',
			          			    'data': '最外层data为此次请求的返回数据',
			          		    	'info':'此次请求返回数据描述',
													'listPeriodinfo':[{
														        'coursename': '课程名称',
													          'courseimage':'课程图片',
													          'periodname':'课节名称' ,
													          'type': '班型' ,
													          'gradename':'班级名',
													          'subhead':'课程副标题',
													          'generalize':'课程简介',
																		'sum':'学生数量'}],
													'studentlists':[{
													  0:[{'imageurl':'学生的头像','nickname':'学生昵称'}]
														}],
													'ware':[{
													        	'code': '返回的查询标识,0为正常返回,其他为异常',
													        	'data': '最外层data为此次请求的返回数据',
													        	'info':{'fileid':'该课件id',
																		    'filename':'该课件名字'}
														}],
													 'playback':[{'videourl':'视频地址','intime':'toteachtime时间','coursename':'课程名称'
														 ,'type':'班型','timekey':'时间键值','lessonsid':'课时副表id','teacherid':'教师id'}],
													 'coursecomments':[{'imageurl':'学生头像','studentid':'学生id','allaccountid':'教师id'
														 ,'content':'评价内容','score':'评分','addtime':'评论时间'}]

			          								}",
);
$WangYX[] = array(
	'url' => '/admin/organ/getperComment',
	'name' => '机构教师端-我的课表-课时详情-评论部分',
	'type' => 'post',
	'data' => "{'lessonsid':'1','date':'2018-5-8','pagenum':1,'pagesize':10}",
	'tip' => "{
									'teacherid':'教师id','lessonsid':'课时id','date':'输入的日期 Y-m-d','pagenum':'当前页码','pagesize':'每页行数',
								}",
	'returns' => "{
			          					'code': '返回的查询标识,0为正常返回,其他为异常',
			          			    'data': '最外层data为此次请求的返回数据',
			          		    	'info':'此次请求返回数据描述',
													 'coursecomments':[{'imageurl':'学生头像','studentid':'学生id','allaccountid':'教师id'
														 ,'content':'评价内容','score':'评分','addtime':'评论时间'}]
			          								}",
);
$WangYX[] = array(
	'url' => '/admin/organ/intoClassroom',
	'name' => '机构教师端-我的课表-进教室',
	'type' => 'post',
	'data' => "{'toteachid':1}",
	'tip' => "{'toteachid':'预约时间id'}",
	'returns' => "{
		                              'code': '返回的查询标识，0为正常返回，其他为异常',
		                                'data': '最外层data为此次请求的返回数据',
		                                'info': '此次请求返回数据描述',
		                                'teachername': '老师名称',
		                                'url': '进入教室的url'
		                            }",
);
$WangYX[] = array(
	'url' => '/admin/organ/getFileList',
	'name' => '机构教师端-资源=>文件夹列表和 资源列表',
	'type' => 'POST',
	'data' => "{'showname':'文件夹1','pagenum':1,'fatherid':0}",
	'tip' => "{'showname':'文件夹名称 搜索字段不传为空字符串','pagenum':'第几页',
							'fatherid':'初始默认0 查询文件夹下级资源传文件夹id','teahcerid':'教师id'}",
	'returns' => "{'fileid':'id',
				 					'showname':'显示名称',
				 					'sizes':'文件大小',
				 					'addtimestr':'添加时间',
				 					'juniorcount':'文件夹下资源个数',
				 					'fatherid':'父级id 对应上级 fileid'}",
);
$WangYX[] = array(
	'url' => '/admin/organ/addWarefile',
	'name' => '机构教师端-我的课表-课时详情-编辑未开始-添加课时相关资源列表的关联',
	'type' => 'POST',
	'data' => "{'id':'1','fileid':[1,2]}",
	'tip' => "{'id':'lessons表主键','fileid':'Filemanage表的主键'}",
	'returns' => "{'code': '返回的查询标识,0为正常返回,其他为异常',
							             'data': '最外层data为此次请求的返回数据',
							             'info': '此次请求返回数据描述',
												}",
);
$WangYX[] = array(
	'url' => '/admin/organ/delWarefile',
	'name' => '机构教师端-我的课表-课时详情-编辑未开始-删除课时相关资源列表的关联',
	'type' => 'POST',
	'data' => "{'id':'1','fileid':[1,2]}",
	'tip' => "{'id':'lessons表主键','fileid':'Filemanage表的主键'}",
	'returns' => "{'code': '返回的查询标识,0为正常返回,其他为异常',
							             'data': '最外层data为此次请求的返回数据',
							             'info': '此次请求返回数据描述'}",
);
