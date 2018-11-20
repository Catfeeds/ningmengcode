<?php
	$JiCR = [];
	$JiCR[] = array(
	    			'url'=>'/admin/Course/getCurricukumList',
	    			'name'=>'课程=>课程列表接口',
	    			'type'=>'POST',
	    			'data'=>"{'coursename':'','pagenum':1}",
	    			'tip'=>"{'coursename':'课程名称','pagenum':'第几页','classtypes':'开班类型 1 录播课 2直播课'}",
	    			'returns'=>"{'id': '课程id',
	    						'imageurl': '课程图片',
                                'coursename': '课程名称',
                                'price': '基础价',
                                'status': '状态 0下架 1上架',
                                'categoryid': 6,
                                'categoryname': '分类名称',
                                'addtime':'添加时间',
                                'classtypesstr':'类型'}",
	    			);

	$JiCR[] = array(
	    			'url'=>'/admin/Course/getCurricukumCounts',
	    			'name'=>'课程=>课程列表上方 统计各状态的课程数量',
	    			'type'=>'POST',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{'soldoutnum': '下架数据',
	    						'putawaynum': '上架数据',
                                'allsum': '全部数据'}",
	    			);



	$JiCR[] = array(
		'url'=>'/admin/Course/getListGfit',
		'name'=>'课程=>获取赠品列表',
		'type'=>'POST',
		'data'=>"{'id':3}",
		'tip'=>"{'id':'课程id 后续编辑必填'}",
		'returns'=>"{
				'id': '赠品id',
				'name': '赠品名称',
				'num':'赠送数量',
				'selected':'0选中 1未选中'
			}",
	);

	$JiCR[] = array(
	    			'url'=>'/admin/Course/addOneCurricukum',
	    			'name'=>'课程=>课程模块的添加/编辑 模块 第一步',
	    			'type'=>'POST',
	    			'data'=>"{'classtypes':1,'coursename':'哈哈哈','subhead':'土话一百讲 - 20 节 陕西话','imageurl':'https://www.baidu.com','categorystr':'1-3-6',categoryid:6,'generalize':'课程概述'}",
	    				'tip'=>"{
							'id':'带id编辑 不带id添加',
							'classtypes':'开班类型 1 录播课 2直播课 此必传',
	    					'coursename':'课程名称',
	    					'subhead':'课程副标题',
	    					'imageurl':'课程封面图',
	    					'categorystr':'分类id集',
	    					'categoryid':'第三级分类 或者说最后一级分类',
	    					'labellist':'课程标签 二级标签',
	    					'generalize':'课程概述',
	    					'teacherid':'老师ID 录播课必填'}",
	    				'returns'=>"{'id':'添加id回显'}",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/addTwoCurricukum',
	    			'name'=>'课程=>课程模块的添加编辑 第二步',
	    			'type'=>'POST',
	    			'data'=>"{'id':'2','inperiod':[{'unitname':'第一单元 土话44444','unitsort':1,'id':'','list':[{'id':'','periodname':'土话呵呵44444','periodsort':1,'courseware':[{'id':1,'name':'hehe'},{'id':2,'name':'hehe'}]}],},{'unitname':'第一单元 土话999','unitsort':2,'id':'','list':[{'id':'','periodname':'土话呵呵999','periodsort':1,'courseware':[{'id':1,'name':'hehe'},{'id':2,'name':'hehe'}]},{'id':'','periodname':'土话哈哈999','periodsort':2,'courseware':[{'id':1,'name':'hehe'},{'id':2,'name':'hehe'}]}],}],}",
	    			'tip'=>"{'id':'课程id ',
								'inperiod':[{
							            'unitname':'课程单元',
							            'unitsort':'课程单元排序 1开始',
							            'id':'课程单元id',
							            'list':[{
						                	'id':'课时id',
						                    'periodname':'课时名称',
						                    'periodsort':'课时排序 1开始',
						                    'courseware':'对应的课件',
					                	}],
							       	}],
							    }",
	    			'returns'=>"",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/addTriCurricukum',
	    			'name'=>'课程=>课程模块的添加编辑 第三步',
	    			'type'=>'POST',
	    			'data'=>"{'id':32,'price':11,'status':1,'giftstatus':0,'gift':[{'id':1,'num':3},{'id':3,'num':2}]}",
	    			'tip'=>"{
	    				'id':'课程id',
	    				'price':'课程基础价（最低价）',
	    				'status':'状态 0下架 1上架',
	    				'giftstatus':'赠品状态 0有赠品 1无赠品',
	    				'gift':[{
	    					'id':'赠品ID',
	    					'num':'赠品数量'
	    				}]}",
	    			'returns'=>"",
	    			);

	$JiCR[] = array(
	    			'url'=>'/admin/Course/editCurricukum',
	    			'name'=>'课程=>课程模块 上下架  删除',
	    			'type'=>'POST',
	    			'data'=>"{'id':78,'status':0,'delflag':0}",
	    			'tip'=>"{
	    				'id':'课程id',
	    				'delflag':'删除标识 0 删除 1未删除 传他删除',
	    				'status':'状态 0下架 1上架 传他上下架'}",
	    			'returns'=>"",
	    			);

	$JiCR[] = array(
					'url'=>'/admin/Course/getCurricuClass',
					'name'=>'课程=>课程模块 获取课程对应的班级列表',
					'type'=>'POST',
					'data'=>"{'id':32,'limit':1}",
					'tip'=>"{
							'id':'课程id',
							'limit':'当前页数',
					}",
					'returns'=>"
						{'id':'班级ID','gradename':'班级名称'}
					",
	);

	$JiCR[] = array(
	    			'url'=>'/admin/Course/getCurricukumEditId',
	    			'name'=>'课程=>获取 编辑课程详情数据回显（编辑必用）',
	    			'type'=>'POST',
	    			'data'=>"{'id':32}",
	    			'tip'=>"{'id':'课程id'}",
	    			'returns'=>"{
						'coursename': '课程标题',
		                'subhead': '副标题',
		                'imageurl': '课程图像',
		                'price': '课程基础价（最低价）',
		                'status': '状态 0下架 1上架',
		                'generalize': '课程概述',
		                'categoryid': '课程分类',
		                'classtypes': '开班类型 1 录播课 2直播课',
		                'categorystr': '课程分类 集',
		                'periodnum': '课时数量',
		                'categorystrname':'分类处理集',
		                'labellist':'课程标签',
		                'inperiod':'课程单元和课时集合',
		                'unitname':'课程单元名称',
		                'unitsort':'单元排序值 从1开始',
		                'periodname':'课时名称',
		                'periodsort':'课时排序值 1开始',
		                'courseware':'课件id集',

		            }",
	    			);

	$JiCR[] = array(
	    			'url'=>'/admin/Course/getCurricukumId',
	    			'name'=>'课程=>获取 课程详情（不支持编辑详情回显）',
	    			'type'=>'POST',
	    			'data'=>"{'id':32}",
	    			'tip'=>"{
	    				'id':'课程id'}",
	    			'returns'=>"{
						'coursename': '课程标题',
		                'subhead': '副标题',
		                'imageurl': '课程图像',
		                'price': '课程基础价（最低价）',
		                'status': '状态 0下架 1上架',
		                'statusstr':'转义后的状态',
		                'generalize': '课程概述',
		                'categoryid': '课程分类',
		                'classtypes': '开班类型 1 录播课 2直播课',
		                'categorystr': '课程分类 集',
		                'periodnum': '课时数量',
		                'categorystrname':'分类处理集',
		                'labellist':'课程标签',
		                'inperiod':'课程单元和课时集合',
		                'unitname':'课程单元名称',
		                'unitsort':'单元排序值 从1开始',
		                'periodname':'课时名称',
		                'periodsort':'课时排序值 1开始',
		                'courseware':'课件id集',
		                'unitnumstr':'已转义 几个单元',
		                'recruitnum':'获取课程 招生班级数',
		                'payordernum':'查询已购买次数',
		                'addtimestr':'添加时间',
	    			}",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/getComment',
	    			'name'=>'课程=>获取课程对应的评论列表',
	    			'type'=>'POST',
	    			'data'=>"{'id':32,'pagenum':1}",
	    			'tip'=>"{'id':'课程id','pagenum':'第几页','lessonsid':'课时ID  课时详情页传此参数'}",
	    			'returns'=>"{
						'content': '评论内容',
		                'addtimestr': '评论时间',
		                'studentinfo': '评论人信息',
		                'teacherinfo': '被评论老师信息',
		                'classtypestr': '班型',
		                'gradename':'班级名称',
		                'score': '评分'
	    			}",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/addCategory',
	    			'name'=>'分类=>添加分类 包括2级分类',
	    			'type'=>'POST',
	    			'data'=>"{'categoryname':'水果','fatherid':0,'imgs':'www.baidu.com','describe':'分类描述','icos':'小图标','icostwo':'小图标2 未选中'}",
	    			'tip'=>"{'categoryname':'分类名称','fatherid':'一级分类传0 二级分类传1级分类id','imgs':'分类log','icos':'小图标','icostwo':'小图标2 未选中','describe':'分类描述'}",
	    			'returns'=>"",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/getCategoryIdList',
	    			'name'=>'分类=>后台分类列表 和查询后台子类 带分页',
	    			'type'=>'POST',
	    			'data'=>"{'fatherid':0,'pagenum':1}",
	    			'tip'=>"{'fatherid':'一级分类传0 二级分类传1级分类id','pagenum':'第几页'}",
	    			'returns'=>"{'categoryname':'分类名',
	    						'rankstr':'级别',
	    						'juniorcount':'子类数量',
	    						'status':'状态码 0不显示 1 显示',
	    						'imgs':'分类log',
	    						'describe':'分类描述'
	    					}",
	    			);



	$JiCR[] = array(
	    			'url'=>'/admin/Course/getCurricukumCategoryList',
	    			'name'=>'分类=>后台添加课程模块 分类联动',
	    			'type'=>'POST',
	    			'data'=>"{'fatherid':0}",
	    			'tip'=>"{'fatherid':'一级分类传0 二级分类传1级分类id','pagenum':'第几页'}",
	    			'returns'=>"{'categoryname':'分类名',
	    						'rankstr':'级别',
	    						'juniorcount':'子类数量',
	    						'status':'状态码 0不显示 1 显示',
	    						'imgs':'分类log',
	    						'describe':'分类描述'
	    					}",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/editCategoryId',
	    			'name'=>'分类=>编辑分类 是否启用 名称编辑',
	    			'type'=>'POST',
	    			'data'=>"{'categoryname':'名称','status':1,'id':1,'imgs':'分类log','describe':'分类描述','icos':'小图标','icostwo':'小图标2 未选中'}",
	    			'tip'=>"{'categoryname':'分类名称 编辑时带','status':'是否启用 1显示 0不显示   启用时带','icos':'小图标','icostwo':'小图标2 未选中','id':'分类id 必填'}",
	    			'returns'=>"",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/deleteCategory',
	    			'name'=>'分类=>分类删除',
	    			'type'=>'POST',
	    			'data'=>"{'id':1}",
	    			'tip'=>"",
	    			'returns'=>"",
	    			);

	$JiCR[] = array(
		'url'=>'/admin/Course/getCatergoryCurricu',
		'name'=>'分类=>获取要删除的分类对应的课程列表',
		'type'=>'POST',
		'data'=>"{'id':0,'limit':1}",
		'tip'=>"{'id':'分类ID','limit':'第几页'}",
		'returns'=>"{
			'id':'课程ID',
			'coursename':'课程名称',
		}",
	);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/shiftCategory',
	    			'name'=>'分类=>后台分类列表 上下移动',
	    			'type'=>'POST',
	    			'data'=>"{'id':7,'sort':7,'operate':0,'rank':1}",
	    			'tip'=>"{'id':'分类id','sort':'当前排序值','operate':'分类操作 0上移 1下移','rank':'分类操作 级别'}",
	    			'returns'=>"",
	    			);


	// 课程标签模块接口

	$JiCR[] = array(
	    			'url'=>'/admin/Course/addCoursetags',
	    			'name'=>'标签=>添加课程标签',
	    			'type'=>'POST',
	    			'data'=>"{'tagname':'标签1','fatherid':0}",
	    			'tip'=>"{'tagname':'标签名','fatherid':'0表示1级标签 下级标签此字段传上级标签id'}",
	    			'returns'=>"",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/getCoursetagsIdList',
	    			'name'=>'标签=>后台课程标签列表 和查询后台子课程标签',
	    			'type'=>'POST',
	    			'data'=>"{'fatherid':0,'pagenum':1}",
	    			'tip'=>"{'fatherid':'父级id 初始传0 查询下级传上级id','pagenum':'第几页'}",
	    			'returns'=>"{'id':'标签id',
	    						'tagname':'标签名称',
	    						'sort':'排序值',
	    						'fatherid':'父子级关系字段 默认0',
	    						'status':'标签状态 0禁用 1 启用',
	    						'addtimestr':'添加时间',
	    						'fathertagname':'上级标签名 初始默认为空 二级标签才有数据',
	    						'juniorcount':'获取下级子集数量'}",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/editCoursetagsId',
	    			'name'=>'标签=>编辑课程标签 是否启用 名称编辑',
	    			'type'=>'POST',
	    			'data'=>"{'id':1,'tagname':'标签1','status':1,'delflag':1}",
	    			'tip'=>"{'id':'编辑id必带','tagname':'标签名 编辑就带参数','status':'标签状态 0禁用 1 启用 启用禁用带此参数'}",
	    			'returns'=>"",
	    			);



	$JiCR[] = array(
	    			'url'=>'/admin/Course/deleteCoursetags',
	    			'name'=>'标签=>课程标签删除',
	    			'type'=>'POST',
	    			'data'=>"{'id':1}",
	    			'tip'=>"{'id':'标签id'}",
	    			'returns'=>"",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/shiftCoursetags',
	    			'name'=>'标签=>后台标签列表 上下移动',
	    			'type'=>'POST',
	    			'data'=>"{'id':1,'operate':1,'fatherid':1,'sort':1}",
	    			'tip'=>"{'id':'标签id','operate':'标签操作 0上移 1下移','fatherid':'上下级 1级0 下级为上级id','sort':'标签操作 当前排序值'}",
	    			'returns'=>"",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/getCoursetagsTree',
	    			'name'=>'标签=>后台课程添加 标签树',
	    			'type'=>'POST',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{'id':'标签id',
	    						'tagname':'标签名称',
	    						'sort':'排序值',
	    						'fatherid':'父子级关系字段 默认0'}",
	    			);




	$JiCR[] = array(
	    			'url'=>'/admin/Course/getSchedulingList',
	    			'name'=>'开课=>后台开课列表',
	    			'type'=>'POST',
	    			'data'=>"{'name':'','pagenum':1,'instatus':''}",
	    			'tip'=>"{'type':'开班类型 1 录播课 2直播课','name':'课程名称','pagenum':'第几页','instatus':'传空字符串全部  0 未招生 1已招生  3已满员 4授课中 5已结束(已取消)  6 已超时'}",
	    			'returns'=>"{'id':'标签id',
	    						'gradename':'班级名称',
	    						'coursename':'课程名称',
	    						'categoryname':'分类名称集',
	    						'imageurl':'课程图片',
	    						'periodnum':'课时数',
	    						'teachername':'老师名称',
	    						'totalprice':'课程总价',
	    						'payordernum':'报名总数',
	    						'juniorcount':'获取下级子集数量',
	    						'status':'是否暂停招生 0 下架，1 上架',
	    						'classstatusStr':'班级状态 0 未招生 1已招生 2已成班 3已满员 4授课中 5已结束(已取消)'}",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/getCurricukum',
	    			'name'=>'开课=>开课选择 课程列表接口',
	    			'type'=>'POST',
	    			'data'=>"{'coursename':'','pagenum':1,'classtypes':1}",
	    			'tip'=>"{'coursename':'课程名称','pagenum':'第几页','classtypes':'开班类型 1 录播课 2直播课'}",
	    			'returns'=>"{'id': '课程id',
	    						'imageurl': '课程图片',
                                'coursename': '课程名称',
                                'price': '基础价',
                                'status': '状态 0下架 1上架',
                                'categoryid': 6,
                                'categoryname': '分类名称',
                            	'periodnum':'课时数量'}",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/getTeacherList',
	    			'name'=>'开课=>查询授课老师',
	    			'type'=>'POST',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{'teacherid': '老师id',
	    						'teachername': '教师名称',
                                'nickname': '教师昵称'}",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/getSchedulingInfo',
	    			'name'=>'开课=>开班详情返回页',
	    			'type'=>'POST',
	    			'data'=>"{'type':2,'curriculumid':32,'id':30}",
	    			'tip'=>"{'type':'开班类型 1 录播课 2直播课','curriculumid':'课程id','id':'排课id   编辑时必带'}",
	    			'returns'=>"{
					                'id': 41,
					                'totalprice': '课时总价',
					                'gradename': '班级名称',
					                'classnum': '成班人数',
					                'teacherid': '老师id',
					                'curriculumname': '课程名称',
					                'curriculumid': '课程id',
					                'periodnum': '课时数量',
					                'classhour':'课时时间',
					                'list': [
					                        {
				                                'id': '课程单元id',
				                                'unitname': '单元名称',
				                                'curriculumid': '课程id',
				                                'unitsort': '课程单元排序',
				                                'list': [
				                                        {
			                                                'id': '课时id',
			                                                'periodname': '课时名称',
			                                                'periodsort': '课时排序',
			                                                'courseware': '对应的课件集',
			                                                'unitid': '课时单元id',
			                                                'intime': '预约日期',
			                                                'timekey': '预约时间区间',
			                                                'teacherid': '老师id',
			                                                'timestr': '前台时间显示集',
			                                                'teachername': '老师名称'
				                                        }
				                                ]
					                        },
					                ],
					                'teachername': '老师名称'}",
	    			);


	$JiCR[] = array(
		'url'=>'/admin/Course/addOneSchedu',
		'name'=>'开课=>后台开班/编辑开班第一步',
		'type'=>'POST',
		'data'=>"{'curriculumid':64,'price':82,'gradename':'班级名称','fullpeople':20}",
		'tip'=>"{'id':'开课id 填写为编辑 不填为添加',
				'curriculumid':'开课选择的课程id',
				'price':'课程单价',
				'gradename':'班级名称',
				'fullpeople':'班级人数上限',
				}",
		'returns'=>"{'data':'添加成功返回为ID  '}",
	);

	$JiCR[] = array(
			'url'=>'/admin/Course/addEditScheduling',
			'name'=>'开课=>后台开班/编辑开班第二步',
			'type'=>'POST',
			'data'=>"{'id':1,'teacherid':82,'list':[{'intime':'2018-09-07','teacherid':82,'timekey':'12:10','classhour':' 00:40','id':'4','unitsort':1,'periodsort':1},{'intime':'2018-09-07','teacherid':82,'timekey':'14:20','classhour':' 00:40','id':'5','unitsort':1,'periodsort':2},{'intime':'2018-09-07','teacherid':82,'timekey':'16:10','classhour':' 00:40','id':'6','unitsort':2,'periodsort':3},{'intime':'2018-09-07','teacherid':82,'timekey':'17:00','classhour':' 00:40','id':'147','unitsort':2,'periodsort':4}]}",
			'tip'=>"{'id':'开课id 必填',
					'teacherid':'老师id',
					'list':[{
							'intime':'课时预订年月日 格式如 2018-05-05',
							'teacherid':'课时绑定老师',
							'periodsort':'课时排序值',
							'timekey':'传开始时间 例 01:10',
							'classhour':'课时时长 例 00:40',
							'id':'此为对应的课时id',
							'unitsort':'此为课时单元排序值 从1开始 '
						}]
					}",
			'returns'=>"",
			);

	$JiCR[] = array(
	    			'url'=>'/admin/Course/getTimeOccupy',
	    			'name'=>'开课=>获取对应老师的占用时间',
	    			'type'=>'POST',
	    			'data'=>"{'teacherid':42,'intime':'2018-05-11'}",
	    			'tip'=>"{'teacherid':'老师id','intime':'传输日期 格式必须严格要求'}",
	    			'returns'=>"",
	    			);

	$JiCR[] = array(
	    			'url'=>'/admin/Course/enrollStudent',
	    			'name'=>'开课=>上下架班级',
	    			'type'=>'POST',
	    			'data'=>"{'id':42,'status':0}",
	    			'tip'=>"{'id':'开课id','status':'0是下架，1是上架'}",
	    			'returns'=>"",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/deleteScheduling',
	    			'name'=>'开课=>删除开课信息',
	    			'type'=>'POST',
	    			'data'=>"{'id':42}",
	    			'tip'=>"{'id':'开课id'}",
	    			'returns'=>"",
	    			);

	$JiCR[] = array(
		'url'=>'/admin/Course/getSchedulingOrder',
		'name'=>'开课=>获取班级对应的订单列表',
		'type'=>'POST',
		'data'=>"{'id':233,'limit':1}",
		'tip'=>"{'id':'班级id','limit':'第几页'}",
		'returns'=>"{
				'orderid':'订单ID',
				'studentname':'学生姓名'
			}",
	);

	$JiCR[] = array(
		'url'=>'/admin/Course/getStudentList',
		'name'=>'开课=>课程对应的学生列表',
		'type'=>'POST',
		'data'=>"{'id':3,'pagenum':1}",
		'tip'=>"{'id':'开课id','pagenum':'第几页'}",
		'returns'=>"{
			'studentid':'学生ID',
			'studentname':'学生姓名',
			'prphone':'手机号前缀',
			'mobile':'手机号',
		}",
	);

	$JiCR[] = array(
		'url'=>'/admin/Course/sendMessage',
		'name'=>'开课=>学生消息发送',
		'type'=>'POST',
		'data'=>"{'id':1,'type':1,'ids':'143,144,145','title':'加班通知','content':'不解释，今晚通宵加班！'}",
		'tip'=>"{'id':'班级id','type':'1班级 2课程','ids':'学生id集','title':'标题','content':'内容'}",
		'returns'=>"",
	);


	$JiCR[] = array(
					'url'=>'/admin/Course/checkClass',
					'name'=>'机构=>检查开课分类 和 添加班级课程',
					'type'=>'POST',
					'data'=>"{'type':1}",
					'tip'=>"{
												'type':'1 添加课程检查分类 2 开课检查课程'
											}",
					'returns'=>"",
				);

	$JiCR[] = array(
	    			'url'=>'/admin/Course/fileAdd',
	    			'name'=>'资源=>添加文件夹',
	    			'type'=>'POST',
	    			'data'=>"{'showname':'超级文件夹','filetype':0}",
	    			'tip'=>"{'showname':'文件夹名称','filetype':'文件夹/文件类型 0 公有的 1 私有的'}",
	    			'returns'=>"",
	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Course/getFileList',
	    			'name'=>'资源=>文件夹列表和 资源列表',
	    			'type'=>'POST',
	    			'data'=>"{'showname':'','pagenum':1,'fatherid':0,'filetype':0,'teacherid':''}",
	    			'tip'=>"{'showname':'文件夹名称 搜索字段不传为空字符串','pagenum':'第几页',
	    					'fatherid':'初始默认0 查询文件夹下级资源传文件夹id',
	    					'filetype':'文件夹/文件类型 0 公有的 1 私有的',
	    					'teacherid':'指定老师的',
	    					'usetype':'用途 1 录制件 2 普通课件',
	    					}",

	    			'returns'=>"{'fileid':'id',
	    						'fileurl':'文件地址',
	    						'showname':'显示名称',
	    						'sizes':'文件大小',
	    						'addtimestr':'添加时间',
	    						'juniorcount':'文件夹下资源个数',
	    						'fatherid':'父级id 对应上级 fileid',
	    						'usetype':'用途'
	    					}",
	    			);

	$JiCR[] = array(
		'url'=>'/admin/Course/getFileTeacher',
		'name'=>'资源=> 老师列表',
		'type'=>'POST',
		'data'=>"{'teachername':'','pagenum':1}",
		'tip'=>"{'teachername':'老师名称','pagenum':'第几页'}",
		'returns'=>"{'teacherid':'id',
					'teachername':'老师名称'
					}",
	);

	$JiCR[] = array(
	    			'url'=>'/admin/Course/deleteFile',
	    			'name'=>'资源=>删除课件',
	    			'type'=>'POST',
	    			'data'=>"{'fileid':1}",
	    			'tip'=>"{'fileid':'对应文件夹和 资源id','teacherid':'课件对应的教师id'}",
	    			'returns'=>"",
	    			);

//	$JiCR[] = array(
//	    			'url'=>'/admin/Course/editOrgan',
//	    			'name'=>'设置=>编辑机构基本设置',
//	    			'type'=>'POST',
//	    			'data'=>"{toonetime:30}",
//	    			'tip'=>"{'toonetime':'一对一时间',
//	    					'smallclasstime':'小课班时间',
//	    					'bigclasstime':'大课班时间',
//	    					'regionprefix':'域名前缀',
//	    					'maxclass':'大班课人数',
//	    					'minclass':'小班课人数',
//	    					'roomkey':'机构房间key'}",
//	    			'returns'=>"",
//	    			);


	$JiCR[] = array(
	    			'url'=>'/admin/Finance/getAccount',
	    			'name'=>'财务=>账单明细列表',
	    			'type'=>'POST',
	    			'data'=>"{'pagenum':1,'studentname':'','coursename':'','intime':''}",
	    			'tip'=>"{
	    					'studentname':'学生姓名',
	    					'coursename':'课程名称',
	    					'intime':'时间区间',
	    					'pagenum':'第几页 默认1'
	    					}",
	    			'returns'=>"{
	    				'studentname':'学生',
	    				'coursename':'课程名称',
	    				'amount':'订单金额',
	    				'teachername':'授课老师',
	    				'ordernum':'订单编号',
	    				'paytime':'支付时间 ',
	    				'classname':'班级名称',
	    				'address':'收获地址'
	    			}",
	    		);

	$JiCR[] = array(
		'url'=>'/admin/Finance/getAccountInfo',
		'name'=>'财务=>账单明细 详情',
		'type'=>'POST',
		'data'=>"{'id':1}",
		'tip'=>"{'id':'订单id'}",
		'returns'=>"{
				'studentname':'学生',
				'ordersource':'下单渠道',
				'teachername':'老师名称',
				'gradename':'课程名称',
				'paytime':'支付时间',
				'orderstatus':'订单状态',
				'paytype':'支付类型',
				'coursename':'课程名称',
				'originprice':'课程原价',
				'discount':'优惠金额',
				'amount':'实付金额'
			}",
	);



	$JiCR[] = array(
	    			'url'=>'/admin/Finance/getAccountCount',
	    			'name'=>'财务=>账户明细统计',
	    			'type'=>'POST',
	    			'data'=>"{'type':1}",
	    			'tip'=>"{'type':'1已结算 0待结算'}",
	    			'returns'=>"{
	    				'usablemoney':'机构余额',
	    				
	    				'name':''
	    			}",  //'forthemoney':'待结算金额',
	    			);


	$JiCR[] = array(
		'url'=>'/admin/Finance/reconciliation',
		'name'=>'财务=>对账中心列表',
		'type'=>'POST',
		'data'=>"{'pagenum':1,'teachername':'','curriculumname':'','gradename':''}",
		'tip'=>"{
				'teachername':'老师姓名',
				'curriculumname':'课程名称',
				'gradename':'班级名称',
				'pagenum':'第几页 默认1'
				}",
		'returns'=>"{
				'id':'班级ID',
				'teacherid':'老师ID',
				'teachername':'老师姓名',
				'curriculumname':'课程名称',
				'gradename':'班级名称',
				'periodnum':'课时总数量',
				'completenum':'完成数量',
			}",
	);


	$JiCR[] = array(
		'url'=>'/admin/Finance/teacherReconciliation',
		'name'=>'财务=>对账中心老师对账详情',
		'type'=>'POST',
		'data'=>"{'id':1,'pagenum':1}",
		'tip'=>"{'id':'班级ID','pagenum':'第几页 默认1'}",
		'returns'=>"{
				'id':'课时ID',
				'teacherid':'老师ID',
				'periodname':'课时名称',
				'starttime':'开始时间',
				'countOrder':'应到人数',
				'realnumber':'实到人数',
			}",
	);


	$JiCR[] = array(
		'url'=>'/admin/Finance/haveClassInfo',
		'name'=>'财务=>对账中心 上课明细',
		'type'=>'POST',
		'data'=>"{'id':1,'pagenum':1}",
		'tip'=>"{'id':'课时ID','pagenum':'第几页 默认1'}",
		'returns'=>"{
					'id':'课时ID',
					'studentname':'学生姓名',
					'attendancestatus':'出勤状态 0出勤，1出勤',
					'attendancestatusStr':'出勤状态转码',
					'studentid':'学生ID',
				}",
	);




//	$JiCR[] = array(
//	    			'url'=>'/admin/Finance/addWithdraw',
//	    			'name'=>'财务=>提现申请',
//	    			'type'=>'POST',
//	    			'data'=>"{'price':11,'paytype':2,'cashaccount':15665885171}",
//	    			'tip'=>"{'price':'提现金额',
//	    					'paytype':'提现类型 2:微信支付3支付宝4银联',
//	    					'cashaccount':'提现账号'}",
//	    			'returns'=>"{}",
//	    			);

//	$JiCR[] = array(
//	    			'url'=>'/admin/Finance/getWithdraw',
//	    			'name'=>'财务=>提现明细列表',
//	    			'type'=>'POST',
//	    			'data'=>"{'pagenum':1,'paystatus':0}",
//	    			'tip'=>"{'pagenum':'第几页 默认1',
//	    					 'paystatus':'0提现中 1提现成功 2提现失败'}",
//	    			'returns'=>"{'addtimestr':'申请时间',
//	    						 'endtimestr':'处理时间',
//	    						 'price':'金额',
//	    						 'paytypestr':'收款平台',
//	    						 'cashaccount':'收款账号',
//	    						 'paystatusstr':'状态',
//	    						 'withsn'=>'编号'}",
//	    			);


//	$JiCR[] = array(
//	    			'url'=>'/admin/Finance/getTeacherSum',
//	    			'name'=>'财务=>获取对应老师的对账统计',
//	    			'type'=>'POST',
//	    			'data'=>"{'pagenum':1,'intime':'2018-04-05 ~ 2018-06-06','teachername':''}",
//	    			'tip'=>"{'pagenum':'第几页 默认1',
//	    					 'intime':'时间区间',
//	    					 'teachername':'老师姓名'}",
//	    			'returns'=>"{'teacherid':'老师id',
//	    						 'nickname':'老师昵称',
//	    						 'mobile':'老师手机号',
//	    						 'price':'收入',
//	    						 'teachername':'老师名称',
//	    						 'ordersum':'订单数'}",
//	    			);

//	$JiCR[] = array(
//	    			'url'=>'/admin/Finance/getTeacherPaylog',
//	    			'name'=>'财务=>获取指定老师指定时间的盈利明细',
//	    			'type'=>'POST',
//	    			'data'=>"{'pagenum':1,'intime':'2018-04-05 ~ 2018-06-06','teacherid':40}",
//	    			'tip'=>"{'pagenum':'第几页 默认1',
//	    					 'intime':'时间区间',
//	    					 'teacherid':'老师id'}",
//	    			'returns'=>"{'studentname':'学生',
//			    				'coursename':'课程名称',
//			    				'teachername':'授课老师',
//			    				'out_trade_no':'订单编号',
//			    				'paynum':'订单金额',
//			    				'addtimestr':'结算时间  (待结算里下单时间)',
//			    				'orderstatus':'订单状态 (只有已结算有)'}",
//	    			);


	$JiCR[] = array(
	    			'url'=>'/index/Login/getPublicKey',
	    			'name'=>'登录=>获取公钥',
	    			'type'=>'POST',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{'key':'公钥 获取后存储到本地'}",
	    			);


	$JiCR[] = array(
	    			'url'=>'/index/Login/login',
	    			'name'=>'登录=>全部角色登录',
	    			'type'=>'POST',
	    			'data'=>"{'key':'asaskdas123jhad1j3nj',
	    			'username':'admin','password':'123456','type':'2','source':'web','organid':1,'prephone':86,'code':'123456'}",
	    			'tip'=>"{
	    				'key':'前端加密的标识 前端生成后存储本地 发送给后端',
	    				'username':'登录 老师的用户名登录 学生手机号登录 机构手机号登录',
	    				'password':'密码',
	    				'type':'1老师 2机构 3学生 4好迹星app学生',
	    				'source':'web/app/microsite',
	    				'organid':'普通机构老师登录必传',
	    				'organtype':'学生登陆使用 官方可不传 收费机构传',
	    				'prephone':'手机号前缀',
	    				'code':'好迹星app学生登陆验证码',
	    			}",
	    			'returns'=>"{
	    				'token':'token   后期请求带上 header',
	    				'imageurl':'头像 通用于学生和老师',
	    				'organid':'所属机构id',
	    				'organname':'机构名称',
	    				'restype':'机构 注册类型，默认是1表示个体老师,2表示企业',
	    				'logintime':'机构上次登录时间',
	    				'userimage':'机构管理员 头像',
						'uid':'学生id 老师id 机构uid',
						'prphone':'手机号前缀 ',
						'mobile':'手机号',
						'nickname':'学生昵称 老师昵称',
						'teacherid':'老师id',
						'teachertype':'老师类型: 1课程 2作文',
						'teachername':'老师名称',
	    				}",
	    			);


	$JiCR[] = array(
					'url'=>'/index/Login/exitLogin',
					'name'=>'登录=>全部角色退出',
					'type'=>'POST',
					'data'=>"{token:'---'}",
					'tip'=>"{
								'token':'token',
							}",
					'returns'=>"",
				);

	$JiCR[] = array(
					'url'=>'/admin/Organ/getUserMeader',
					'name'=>'管理员=>获取管理员信息',
					'type'=>'POST',
					'data'=>"",
					'tip'=>"{
								'useraccount':'用户名',
								'adminname':'姓名',
								'mobile':'手机号',
								'userimage':'头像',
							}",
					'returns'=>"",
				);

	$JiCR[] = array(
					'url'=>'/admin/Teacher/editTeachPass',
					'name'=>'机构=>机构直接修改老师密码',
					'type'=>'POST',
					'data'=>"{'teachid':1,'password':123456,'reppassword':123456}",
					'tip'=>"{
								'teachid':'老师ID',
								'password':'密码',
								'reppassword':'重复密码'
							}",
					'returns'=>"",
				);

	$JiCR[] = array(
		'url'=>'/admin/Organ/setOrganAboutus',
		'name'=>'机构=>修改 关于我们',
		'type'=>'POST',
		'data'=>"{'aboutus':'关于我们'}",
		'tip'=>"",
		'returns'=>"",
	);
	$JiCR[] = array(
		'url'=>'/admin/Organ/getOrganAboutus',
		'name'=>'机构=>获取关于我们',
		'type'=>'POST',
		'data'=>"",
		'tip'=>"",
		'returns'=>"{'aboutus':'关于我们'}",
	);
	
	//修改下载配置json
	$JiCR[] = array(
		'url'=>'/admin/Organ/setOrganDownloadJson',
		'name'=>'机构=>修改下载配置',
		'type'=>'POST',
		'data'=>"{
			'qrcodeandroid':'',
			'qrcodeios':'',
			'cloudclasswindows':'',
			'cloudclassmac':'',
			'teamviewwindows':'',
			'teamviewmac':'',
			'chromewindows':'',
			'chromemac':'',
			'browser360xp':'',
		}",
		'tip'=>"{
			'qrcodeandroid':'安卓二维码',
			'qrcodeios':'IOS二维码',
			'cloudclasswindows':'Windows系统',
			'cloudclassmac':'Mac系统',
			'teamviewwindows':'Windows系统',
			'teamviewmac':'Mac系统',
			'chromewindows':'Windows系统',
			'chromemac':'Mac系统',
			'browser360xp':'XP系统',
		}",
		'returns'=>"",
	);
	
	//获取下载配置json
	$JiCR[] = array(
		'url'=>'/admin/Organ/getOrganDownloadJson',
		'name'=>'机构=>获取下载配置',
		'type'=>'POST',
		'data'=>"",
		'tip'=>"",
		'returns'=>"{
			'qrcodeandroid':'安卓二维码',
			'qrcodeios':'IOS二维码',
			'cloudclasswindows':'Windows系统',
			'cloudclassmac':'Mac系统',
			'teamviewwindows':'Windows系统',
			'teamviewmac':'Mac系统',
			'chromewindows':'Windows系统',
			'chromemac':'Mac系统',
			'browser360xp':'XP系统',
		}",
	);
	
	$JiCR[] = array(
		'url'=>'/admin/Authoritys/getUserGroup',
		'name'=>'权限=>部门列表',
		'type'=>'POST',
		'data'=>"{'pagenum':1}",
		'tip'=>"{'pagenum':'第几页'}",
		'returns'=>"{
				'id':'部门id',
				'name':'部门名称',
				'status':'0 启用 1禁用',
				'addtime':'添加时间',
				'titleinfo':'菜单名称集'
			}",
	);

	$JiCR[] = array(
		'url'=>'/admin/Authoritys/getAllUserGroup',
		'name'=>'权限=>全部 部门列表 编辑管理员使用',
		'type'=>'POST',
		'data'=>"",
		'tip'=>"",
		'returns'=>"{
				'id':'部门id',
				'name':'部门名称'
			}",
	);


	$JiCR[] = array(
		'url'=>'/admin/Authoritys/addGroup',
		'name'=>'权限=>添加编辑部门',
		'type'=>'POST',
		'data'=>"{'name':'开发部','treepath':'1,2,3'}",
		'tip'=>"{'name':'部门名称','treepath':'菜单ID','id':'部门id 带id为编辑',}",
		'returns'=>"{
				'id':'部门id 带id为编辑',
			}",
	);

	$JiCR[] = array(
		'url'=>'/admin/Authoritys/deleteGroup',
		'name'=>'权限=>删除部门',
		'type'=>'POST',
		'data'=>"{'id':1}",
		'tip'=>"{'id':'部门id'}",
		'returns'=>"{
					'id':'部门id 带id为编辑',
				}",
	);


	$JiCR[] = array(
		'url'=>'/admin/Authoritys/menuListTree',
		'name'=>'权限=>左侧菜单列表树 和编辑回显',
		'type'=>'POST',
		'data'=>"{'id':''}",
		'tip'=>"{
					'id':'部门id',
				}",
		'returns'=>"{
			'instatus':'0选择状态 1未选中状态',
			'path':'树上下级 链',
			'name':'菜单名字',
			'url':'菜单链接',
			'fatherid':'父级ID',
			'list':'下级'
		}",
	);

	$JiCR[] = array(
		'url'=>'/admin/Authoritys/getMenuTree',
		'name'=>'权限=>登陆成功获取左侧菜单',
		'type'=>'POST',
		'data'=>"{'id':''}",
		'tip'=>"{
					'id':'部门id',
				}",
		'returns'=>"{
				'name':'菜单名字',
				'url':'菜单链接',
				'fatherid':'父级ID',
				'list':'下级'
			}",
	);

	$JiCR[] = array(
		'url'=>'/admin/Course/getClassStudent',
		'name'=>'课时=>课时详情 上课信息列表',
		'type'=>'POST',
		'data'=>"{'id':'','pagenum':1}",
		'tip'=>"{'id':'课时id','pagenum':'第几页'}",
		'returns'=>"{
					'id':'老师 学生id',
					'teacherType':'1老师 3学生',
					'nickname':'老师学生姓名',
					'attendancestatus':'0未出勤，1已出勤'
				}",
	);

	$JiCR[] = array(
		'url'=>'/appstudent/User/bindingUser',
		'name'=>'APP 登陆=>绑定对应的极光别名',
		'type'=>'POST',
		'data'=>"{'registrationid':'机构返回唯一表registrationid'}",
		'tip'=>"",
		'returns'=>"",
	);


	$JiCR[] = array(
		'url'=>'/admin/Course/getLessonsStudent',
		'name'=>'课时学生=>课时对应的学生列表',
		'type'=>'POST',
		'data'=>"{'pagenum':1,'id':1323}",
		'tip'=>"{'pagenum':'第几页','id':'课时ID'}",
		'returns'=>"{
			'id': '老师ID',
			'nickname': '老师昵称',
			'mobile': '手机号'
		}",
	);



//
//	$JiCR[] = array(
//					'url'=>'/admin/Classroom/oneClassEdit',
//					'name'=>'开课2.0=>添加课程第一步',
//					'type'=f>'POST',
//					'data'=>"{'classtypes':'2','teacherid':'140','coursename':'哈哈哈','subhead':'土话一百讲 - 20 节 陕西话','imageurl':'https://www.baidu.com','categorystr':'1-3-6',categoryid:6,'labellist':'10-11','generalize':'课程概述'}",
//					'tip'=>"{
//								'id': '带id编辑 不带id添加',
//								'classtypes':'开班类型 1是一对一 2是小课班 3是大课班',
//								'teacherid':'老师ID',
//								'coursename': '课程名称',
//								'subhead': '课程副标题',
//								'imageurl': '课程封面图',
//								'categorystr': '分类id集',
//								'categoryid': '第三级分类 或者说最后一级分类',
//								'labellist': '课程标签 二级标签',
//								'generalize': '课程概述'
//							}",
//					'returns'=>"",
//				);









