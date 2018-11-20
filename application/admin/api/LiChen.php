<?php
	$LiChen = [];
	//题库管理-课程列表
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/getCurricukumList',
	    			'name'=>'管理后台-教务-题库管理-课程列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'coursename': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'coursename': '课程名称',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '课程id',
	    						'coursename': '课程名称',
	    						'categoryname': '分类名称',
	    						'exercise_count': '试题数',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	//课程下习题列表				
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/getExerciseList',
	    			'name'=>'管理后台-教务-题库管理-习题列表',
	    			'type'=>'post',
	    			'data'=>"{
						'courseid': '',
	    				'periodname': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
						'courseid': '课程id',
	    				'periodname': '习题名称',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	//管理后台-教务-题库配置-查看习题
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/getExerciseinfo',
	    			'name'=>'管理后台-教务-题库配置-查看习题',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': '',
	    			}",
	    			'tip'=>"{
	    				'id': '课时习题id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
					}",			
	    			);
	
	//管理后台-教务-题库管理-录入习题
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/addExercise',
	    			'name'=>'管理后台-教务-题库管理-录入习题',
	    			'type'=>'post',
	    			'data'=>"{
	    				'courseid'     :'',
						'periodid'      :'',
						'subject': {
								'1': [
										{
												'type': 1,
												'name': 'test name',
												'imageurl': null,
												'options': [
														'A...',
														'B...',
														'C...',
														'D...'
												],
												'analysis': '',
												'correctanswer': 'test answer',
												'score': 10
										},
										{
												'type': 1,
												'name': '你喜欢NBA哪个球员',
												'imageurl': null,
												'options': [
														'A. jordan',
														'B. kobe',
														'C. carter',
														'D. wade'
												],
												'analysis': '',
												'correctanswer': 'c. carter',
												'score': 10
										}
								],
								'2': [],
								'3': [
										{
												'type': 3,
												'name': '乔丹的英文全名是什么？',
												'imageurl': null,
												'options': '',
												'analysis': '',
												'correctanswer': 'michael jordan',
												'score': 10
										}
								],
								'4': []
						}
	    			}",
	    			'tip'=>"{
	    				'courseid'     :'课程id(必须)',
						'periodid'      :'课时id(必须)',
						'subject': {
								'1': [
										{
												'type': '题目类型',
												'name': '题干名称',
												'imageurl': '题目图片',
												'options': '选择题选项数组(非选择题留空)',
												'analysis': '解析',
												'correctanswer': '正确答案',
												'score': '分值'
										} 
								],
								'2': [],
								'3': [],
								'4': []
						}
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	
	//检查课时是否已录入习题				
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/checkHaveExercise',
	    			'name'=>'管理后台-教务-题库管理-检查课时是否已录入习题',
	    			'type'=>'post',
	    			'data'=>"{
	    				'courseid' : '',
						'periodid' : '',
	    			}",
	    			'tip'=>"{
	    				'courseid' :'课程id',
						'periodid' :'课时id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	
	//获取所有课程
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/getAllCurriculum',
	    			'name'=>'管理后台-教务-题库管理-录入题目时获取所有课程',
	    			'type'=>'post',
	    			'data'=>"{
	    				'coursename': '',
	    			}",
	    			'tip'=>"{
	    				'coursename': '课程名称',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
					}",			
	    			);
	
    //获取课程下的课时列表
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/getPeriodList',
	    			'name'=>'管理后台-教务-题库管理-录入题目时获取课程下的课时列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'curriculumid': '',
	    				'periodname': '',
	    			}",
	    			'tip'=>"{
	    				'curriculumid': '课程id',
	    				'periodname': '课时名称',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
					}",			
	    			);
		
	//管理后台-教务-题库管理-编辑习题
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/updateExercise',
	    			'name'=>'管理后台-教务-题库管理-编辑习题',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id'     :'',
						'subject': {
								'1': [
										{
												'type': 1,
												'name': 'test name',
												'imageurl': null,
												'options': [
														'A...',
														'B...',
														'C...',
														'D...'
												],
												'analysis': '',
												'correctanswer': 'test answer',
												'score': 10
										},
										{
												'type': 1,
												'name': '你喜欢NBA哪个球员',
												'imageurl': null,
												'options': [
														'A. jordan',
														'B. kobe',
														'C. carter',
														'D. wade'
												],
												'analysis': '',
												'correctanswer': 'c. carter',
												'score': 10
										}
								],
								'2': [],
								'3': [
										{
												'type': 3,
												'name': '乔丹的英文全名是什么？',
												'imageurl': null,
												'options': '',
												'analysis': '',
												'correctanswer': 'michael jordan',
												'score': 10
										}
								],
								'4': []
						}
	    			}",
	    			'tip'=>"{
	    				'id'     :'课时习题id(必须)',
						'subject': {
								'1': [
										{
												'type': '题目类型',
												'name': '题干名称',
												'imageurl': '题目图片',
												'options': '选择题选项数组',
												'analysis': '解析',
												'correctanswer': '正确答案',
												'score': '分值'
										} 
								],
								'2': [],
								'3': [],
								'4': []
						}
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	
	//判断习题是否有班级正在使用
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/checkHaveSchedulingLesson',
	    			'name'=>'管理后台-教务-题库管理-判断习题是否有班级正在使用',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':'',
	    			}",
	    			'tip'=>"{
	    				'id':'习题id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
					
	//删除习题
	$LiChen[] = array(
	    			'url'=>'/admin/Subject/deleteExercise',
	    			'name'=>'管理后台-教务-题库管理-删除习题',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':'',
	    			}",
	    			'tip'=>"{
	    				'id':'要删除的习题id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
					
	//作业管理 - 课程一级分类
	$LiChen[] = array(
	    			'url'=>'/admin/Homework/courseCategoryListOne',
	    			'name'=>'管理后台-教务-作业管理-课程一级分类',
	    			'type'=>'post',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//作业管理 - 课程二级分类
	$LiChen[] = array(
	    			'url'=>'/admin/Homework/courseCategoryListTwo',
	    			'name'=>'管理后台-教务-作业管理-课程二级分类',
	    			'type'=>'post',
	    			'data'=>"{'category_level_one': '',}",
	    			'tip'=>"{'category_level_one': '一级分类id',}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	
	//作业管理 - 班级作业详情
	$LiChen[] = array(
	    			'url'=>'/admin/Homework/getSchedulinglessonList',
	    			'name'=>'管理后台-教务-作业管理-班级作业详情',
	    			'type'=>'post',
	    			'data'=>"{
						'reviewstatus': '0',
						'category_level_one': '',
						'category_level_two': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
						'reviewstatus': '批阅状态(0未批阅,1已批阅)',
						'category_level_one': '一级分类',
						'category_level_two': '二级分类',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
								'classid': '班级id',
                                'lessonid': '课时id',
                                'curriculumname': '课程名称',
                                'gradename': '班级名称',
                                'periodname': '习题名称',
                                'teachername': '教师名称',
                                'realnum': '班级总人数',
                                'categoryname': '课程分类',
                                'submitedcount': '提交人数',
                                'notreviewcount': '未批阅人数',
	    						'pageinfo': '分页信息',
                                'pagesize': '每页最多条数',
                                'pagenum': '当前页码',
                                'total': '符合条件的总记录数目',
                            }",
	    			);
	//预览习题			
	$LiChen[] = array(
	    			'url'=>'/admin/Homework/PreviewExercise',
	    			'name'=>'管理后台-教务-作业管理-预览习题',
	    			'type'=>'post',
	    			'data'=>"{
						'classid': '',
						'lessonid': '',
	    			}",
	    			'tip'=>"{
						'classid': '班级id',
						'lessonid': '课时id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);	
	
	//学员提交作业明细				
	$LiChen[] = array(
	    			'url'=>'/admin/Homework/getStudentHomeworkList',
	    			'name'=>'管理后台-教务-作业管理-学员提交作业明细',
	    			'type'=>'post',
	    			'data'=>"{
						'classid': '',
						'lessonid': '',
						'nickname': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
						'classid': '班级id',
						'lessonid': '课时id',
						'nickname': '学员姓名',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'lessionstatus': '班级习题批阅状态，0未完成，1已批阅',
	    						'id': '编号id',
								'classid': '班级id',
                                'lessonid': '课时id',
                                'studentid': '学生id',
                                'nickname': '学生姓名',
                                'submittime': '提交时间',
                                'issubmited': '是否提交，0未提交，1已提交',
                                'reviewstatus': '批阅状态，0未，1已批阅',
                                'sumcore': '成绩',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);				
	//查看作业			
	$LiChen[] = array(
	    			'url'=>'/admin/Homework/viewHomework',
	    			'name'=>'管理后台-教务-作业管理-查看作业',
	    			'type'=>'post',
	    			'data'=>"{
						'classid': '',
						'lessonid': '',
						'studentid': '',
						'status': '0',
	    			}",
	    			'tip'=>"{
						'classid': '班级id',
						'lessonid': '课时id',
						'studentid': '学生id',
						'status': '作业状态(0未提交,1已提交未批阅,2已批阅)',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	
	//签到背景图列表
	$LiChen[] = array(
	    			'url'=>'/admin/knowledgeSetup/getSigninbgiList',
	    			'name'=>'管理后台-教务-知识配置-签到背景图列表',
	    			'type'=>'post',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',

                            }",
	    			);
					
	//删除签到背景图
	$LiChen[] = array(
	    			'url'=>'/admin/knowledgeSetup/deleteSignInBgi',
	    			'name'=>'管理后台-教务-知识配置-删除签到背景图',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':'',
	    			}",
	    			'tip'=>"{
	    				'id':'要删除的id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	
	//二维码列表
	$LiChen[] = array(
	    			'url'=>'/admin/knowledgeSetup/getQrList',
	    			'name'=>'管理后台-教务-知识配置-二维码列表',
	    			'type'=>'post',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
								'imageurl': '二维码url',
                            }",
	    			);
	
	//知识配置
	$LiChen[] = array(
	    			'url'=>'/admin/knowledgeSetup/getKnowledgeTypeList',
	    			'name'=>'管理后台-教务-知识配置-知识类型列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'name': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'name': '类型名称',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '类型名称',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	
    //获取所有知识类型
	$LiChen[] = array(
	    			'url'=>'/admin/KnowledgeSetup/getAllKnowledgeTypeList',
	    			'name'=>'管理后台-教务-知识管理-获取所有知识类型',
	    			'type'=>'post',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '类型名称',
                            }",
	    			);
    	
	//管理后台-教务-知识配置-知识配置列表-获取知识类型详细信息
	$LiChen[] = array(
	    			'url'=>'/admin/knowledgeSetup/getKnowledgeTypeinfo',
	    			'name'=>'管理后台-教务-知识配置-知识类型详细信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 1,
	    			}",
	    			'tip'=>"{
	    				'id': '类型id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '类型名称',
					}",			
	    			);
	
	//管理后台-教务-知识配置-添加知识类型
	$LiChen[] = array(
	    			'url'=>'/admin/knowledgeSetup/addKnowledgeType',
	    			'name'=>'管理后台-教务-知识配置-添加知识类型',
	    			'type'=>'post',
	    			'data'=>"{
	    				'name': '类型名称',
	    			}",
	    			'tip'=>"{
	    				'name': '类型名称',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);

	//管理后台-教务-知识配置-更新知识分类
	$LiChen[] = array(
	    			'url'=>'/admin/knowledgeSetup/updateKnowledgeType',
	    			'name'=>'管理后台-教务-知识配置-更新知识类型',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'name': '类型名称',
	    			}",
	    			'tip'=>"{
	    				'id': 'id',
	    				'name': '类型名称',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//删除知识类型
	$LiChen[] = array(
	    			'url'=>'/admin/knowledgeSetup/deleteKnowledgeType',
	    			'name'=>'管理后台-教务-知识配置-删除知识类型',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':5,
	    			}",
	    			'tip'=>"{
	    				'id':'要删除的类型id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);				

	
	//知识管理
	$LiChen[] = array(
	    			'url'=>'/admin/Knowledge/getKnowledgeList',
	    			'name'=>'管理后台-教务-知识管理-知识列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'content': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'content': '知识内容',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'typename': '知识类型',
								'content': '知识内容',
								'answer': '谜底',
								'forstudenttypename': '适用对象',
								'addtime': '添加时间',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	//管理后台-教务-知识管理-获取知识详细信息
	$LiChen[] = array(
	    			'url'=>'/admin/Knowledge/getKnowledgemsg',
	    			'name'=>'管理后台-教务-知识管理-编辑时获取知识详细信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': '',
	    			}",
	    			'tip'=>"{
	    				'id': '知识id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'typeid': '知识类型',
								'content': '知识内容',
								'answer': '谜底',
								'forstudenttype': '适用对象',
                            }",
	    			);
	
	//管理后台-教务-知识管理-添加知识
	$LiChen[] = array(
	    			'url'=>'/admin/Knowledge/addKnowledge',
	    			'name'=>'管理后台-教务-知识管理-添加知识',
	    			'type'=>'post',
	    			'data'=>"{
	    				'typeid': '',
	    				'content': '',
	    				'answer': '',
	    				'forstudenttype': '',
	    			}",
	    			'tip'=>"{
	    				'typeid': '知识类型id',
	    				'content': '知识内容',
	    				'answer': '谜底',
						'forstudenttype': '适用对象id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//批量导入数据				
	$LiChen[] = array(
	    			'url'=>'/admin/Importdata/Import',
	    			'name'=>'管理后台-教务-批量导入数据',
	    			'type'=>'post',
	    			'data'=>"{'importtype':1}",
	    			'tip'=>"{'importtype':'导入类型(1=导入试题, 2=导入知识, 3=导入老师, 4=导入例句)'}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//管理后台-教务-知识管理-更新知识
	$LiChen[] = array(
	    			'url'=>'/admin/Knowledge/updateKnowledge',
	    			'name'=>'管理后台-教务-知识管理-更新知识',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'typeid': '',
	    				'content': '',
	    				'answer': '',
						'forstudenttype': '',
	    			}",
	    			'tip'=>"{
	    				'id': '知识id',
	    				'typeid': '知识类型id',
	    				'content': '知识内容',
	    				'answer': '谜底',
						'forstudenttype': '适用对象id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//删除知识
	$LiChen[] = array(
	    			'url'=>'/admin/Knowledge/deleteKnowledge',
	    			'name'=>'管理后台-教务-知识管理-删除知识',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':5,
	    			}",
	    			'tip'=>"{
	    				'id':'要删除的知识id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	
	//教务-奖励设置
	$LiChen[] = array(
	    			'url'=>'/admin/reward/getRewardList',
	    			'name'=>'管理后台-教务-奖励设置-奖品列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'name': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'name': '奖品名称',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '奖品名称',
	    						'type': '奖品类型(1折扣券,2现金券)',
	    						'condition1': '连续签到',
	    						'condition2': '总签到',
	    						'status': '账号状态,默认1开启，0关闭',
	    						'addtime': '添加时间',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	//管理后台-教务-奖励设置-获取奖品详细信息
	$LiChen[] = array(
	    			'url'=>'/admin/reward/getRewardinfo',
	    			'name'=>'管理后台-教务-奖励设置-奖品详细信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 1,
	    			}",
	    			'tip'=>"{
	    				'id': '奖品id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '奖品名称',
	    						'condition1': '连续签到',
	    						'condition2': '总签到',
								'type': '优惠券类型(1折扣券,2现金券)',
	    						'value': '面额',
	    						'mixamount': '使用门槛',
	    						'expiretype': '有效期类型：1日期范围, 2固定天数',
	    						'expirevalue': '有效期值',
	    						'forcoursetype'    : '可使用课程类型(0所有课程,1指定分类,2指定课程)',
								'forcoursevalue'    : '可使用课程值(多个值以逗号分隔)',
	    						'note': '使用说明',
                            }",
	    			);
	
	//管理后台-教务-奖励设置-编辑时获取奖品详细信息
	$LiChen[] = array(
	    			'url'=>'/admin/reward/getRewardMsg',
	    			'name'=>'管理后台-教务-奖励设置-编辑时获取奖品详细信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 1,
	    			}",
	    			'tip'=>"{
	    				'id': '奖品id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '奖品名称',
	    						'condition1': '连续签到',
	    						'condition2': '总签到',
								'type': '优惠券类型(1折扣券,2现金券)',
	    						'value': '面额',
	    						'mixamount': '使用门槛',
	    						'expiretype': '有效期类型：1日期范围, 2固定天数',
	    						'expirevalue': '有效期值',
	    						'forcoursetype'    : '可使用课程类型(0所有课程,1指定分类,2指定课程)',
								'forcoursevalue'    : '可使用课程值(多个值以逗号分隔)',
	    						'note': '使用说明',
                            }",
	    			);
	
	//管理后台-教务-奖励设置-添加奖品
	$LiChen[] = array(
	    			'url'=>'/admin/reward/addReward',
	    			'name'=>'管理后台-教务-奖励设置-添加奖品',
	    			'type'=>'post',
	    			'data'=>"{
	    				'name'          : '',
						'condition1'    : '',
						'condition2'    : '',
						'type'          : '',
						'value'    : '',
						'mixamount'    : '',
						'expiretype'   : '',
						'expirevalue'  : '',
						'forcoursetype'    : '',
						'forcoursevalue'    : '',
						'note'      : '使用说明',
	    			}",
	    			'tip'=>"{
	    				'name'          : '奖品名字',
						'condition1'    : '连续签到',
						'condition2'    : '总签到',
						'type'          : '优惠券类型(1折扣券,2现金券)',
						'value'         : '面额',
						'mixamount'    : '使用门槛',
						'expiretype'   : '有效期类型：1日期范围, 2固定天数',
						'expirevalue'  : '有效期值',
						'forcoursetype'    : '可使用课程类型(0所有课程,1指定分类,2指定课程)',
						'forcoursevalue'    : '可使用课程值(多个值以逗号分隔)',
						'note'      : '使用说明',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);

	//管理后台-教务-奖励设置-更新奖品数据
	$LiChen[] = array(
	    			'url'=>'/admin/reward/updateReward',
	    			'name'=>'管理后台-教务-奖励设置-更新奖品',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'name'          : '',
						'condition1'    : '',
						'condition2'    : '',
						'type'          : '',
						'value'    : '',
						'mixamount'    : '',
						'expiretype'   : '',
						'expirevalue'  : '',
						'forcoursetype'    : '',
						'forcoursevalue'    : '',
						'note'      : '',
	    			}",
	    			'tip'=>"{
	    				'id': 'id',
	    				'name'          : '奖品名字',
						'condition1'    : '连续签到',
						'condition2'    : '总签到',
						'type'          : '优惠券类型(1折扣券,2现金券)',
						'value'    :      '面额',
						'mixamount'    : '使用门槛',
						'expiretype'   : '有效期类型：1日期范围, 2固定天数',
						'expirevalue'  : '有效期值',
						'forcoursetype'    : '可使用课程类型(0所有课程,1指定分类,2指定课程)',
						'forcoursevalue'    : '可使用课程值(多个值以逗号分隔)',
						'note'      : '使用说明',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//管理后台-教务-奖励设置-更改奖品状态
	$LiChen[] = array(
	    			'url'=>'/admin/reward/SwitchRewardStatus',
	    			'name'=>'管理后台-教务-奖励设置-更改奖品状态',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'status': 0,
	    			}",
	    			'tip'=>"{
	    				'id': 'id',
	    				'status': '账号状态,默认1开启，0关闭',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//删除奖品
	$LiChen[] = array(
	    			'url'=>'/admin/reward/deleteReward',
	    			'name'=>'管理后台-教务-奖励设置-删除奖品',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':5,
	    			}",
	    			'tip'=>"{
	    				'id':'要删除的奖品id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//签到统计
	$LiChen[] = array(
	    			'url'=>'/admin/signin/getSigninList',
	    			'name'=>'管理后台-教务-签到统计-签到列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'nickname': '',
						'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'nickname': '学生姓名',
						'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '编号',
	    						'nickname': '学生姓名',
	    						'addtime': '注册时间',
	    						'totalsignin': '签到总天数',
	    						'consignin': '连续签到天数',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	);
	
	 //管理后台-用户-老师管理-老师列表
	$LiChen[] = array(
	    			'url'=>'/admin/teacher/getTeachList',
	    			'name'=>'管理后台-用户-老师管理-老师列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'nickname': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'nickname': '老师姓名',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '老师 id',
	    						'sex': '性别 0保密 1男 2女',
	    						'prphone': '手机号国别',
	    						'mobile': '老师手机号',
	    						'nickname': '老师昵称',
	    						'status': '账号状态,默认0开启，1关闭',
	    						'logintime': '最近一次登录时间',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	$LiChen[] = array(
	    			'url'=>'/admin/teacher/addTeacherMsg',
	    			'name'=>'管理后台-用户-老师管理-添加教师信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'roleid':'1',
	    				'nickname':'老师姓名',
	    				'prphone':'手机前缀',
	    				'mobile':'手机号',
						'school':'所在学校',
	    				'grade':'所在年级',
	    				'class':'所在班级',
	    				'password':'213213213213',
	    				'repassword':'213213213213',
	    			}",
	    			'tip'=>"{
	    				'roleid':'权限:1课程 ,2作文',
	    				'nickname':'老师姓名',
	    				'prphone':'手机前缀',
	    				'mobile':'手机号',
	    				'school':'所在学校',
	    				'grade':'所在年级',
	    				'class':'所在班级',
		    			'password':'用户密码',
		    			'repassword':'重复密码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	
	$LiChen[] = array(
	    			'url'=>'/admin/teacher/teachInfo',
	    			'name'=>'管理后台-用户-老师管理-查看教师信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'teachid':1,
	    			}",
	    			'tip'=>"{
						'teachid':'老师id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	$LiChen[] = array(
	    			'url'=>'/admin/teacher/getTeachMsg',
	    			'name'=>'管理后台-用户-老师管理-编辑时获取教师详细信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'teachid':1,
	    			}",
	    			'tip'=>"{
						'teachid':'老师id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	$LiChen[] = array(
	    			'url'=>'/admin/teacher/updateTeacherMsg',
	    			'name'=>'管理后台-用户-老师管理-修改教师信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'teacherid':'1',
	    				'roleid':'1',
	    				'nickname':'老师姓名',
	    				'prphone':'86',
	    				'mobile':'12345678900',
						'school':'所在学校',
	    				'grade':'所在年级',
	    				'class':'所在班级',
	    			}",
	    			'tip'=>"{
						'teacherid':'老师id',
	    				'roleid':'权限:1课程 ,2作文',
	    				'nickname':'老师姓名',
						'prphone':'手机号前缀',
	    				'mobile':'手机号',
	    				'school':'所在学校',
	    				'grade':'所在年级',
	    				'class':'所在班级',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);				
	//删除前判断教师是否有课
	$LiChen[] = array(
	    			'url'=>'/admin/teacher/checkTeacherHaveCourse',
	    			'name'=>'管理后台-用户-老师管理-判断教师是否有课',
	    			'type'=>'post',
	    			'data'=>"{
	    				'teacherid' : '',
	    			}",
	    			'tip'=>"{
	    				'teacherid' : '要判断的教师id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);				

	//删除教师
	$LiChen[] = array(
	    			'url'=>'/admin/teacher/deleteTeacher',
	    			'name'=>'管理后台-用户-老师管理-删除教师',
	    			'type'=>'post',
	    			'data'=>"{
	    				'teacherid':5,
	    			}",
	    			'tip'=>"{
	    				'teacherid':'要删除的教师id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	

    //修改账户启用状态
	$LiChen[] = array(
	    			'url'=>'/admin/teacher/switchTeachStatus',
	    			'name'=>'管理后台-用户-老师管理-修改账户启用状态',
	    			'type'=>'post',
	    			'data'=>"{
	    				'teacherid':'',
	    				'dataflag':1,
	    			}",
	    			'tip'=>"{
	    				'teacherid':'教师id',
	    				'dataflag':'要修改的状态值(0启用,1禁用)',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//重置密码
	$LiChen[] = array(
				'url'=>'/admin/teacher/editTeachPass',
				'name'=>'管理后台-用户-老师管理-重置密码',
				'type'=>'POST',
				'data'=>"{
					'teachid':'1',
					'password':'123456',
					'reppassword':'123456'}",
				'tip'=>"{
				  'teachid ':'老师id',
				  'password':'登录密码',
				  'reppassword':'重复密码'}",
				'returns'=>"{
					'code': '返回的查询标识,0为正常返回,其他为异常',
					'data': '最外层data为此次请求的返回数据',
					'info': '此次请求返回数据描述',}"
				);				
					
	 //管理后台-用户-学生管理-学生列表
	$LiChen[] = array(
	    			'url'=>'/admin/student/getUserList',
	    			'name'=>'管理后台-用户-学生管理-学生列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'mobile': '',
	    				'nickname': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'mobile': '手机号',
	    				'nickname': '用户昵称',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '学生 id',
	    						'sex': '性别 0保密 1男 2女',
	    						'prphone': '手机号国别',
	    						'mobile': '学生手机号',
	    						'nickname': '学生昵称',
	    						'status': '账号状态,默认0开启，1关闭',
	    						'logintime': '最近一次登录时间',

	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	//管理后台-用户-学生管理-获取学生详细信息
	$LiChen[] = array(
	    			'url'=>'/admin/student/getUserinfo',
	    			'name'=>'管理后台-用户-学生管理-学生详细信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'userid': 1,
	    			}",
	    			'tip'=>"{
	    				'userid': '学生id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '学生 id',
	    						'imageurl': '学生头像',
	    						'prphone': '手机号国别',
	    						'mobile': '学生手机号',
	    						'birth': '学生生日时间戳形式',
	    						'sex': '性别 0保密 1男 2女',
	    						'logintime': '学生最近登陆时间',
	    						'country': '国家编号',
	    						'province': '省编号',
	    						'city': '城市编号',
	    						'profile': '学生简介',
	    						'username': '学生名字',
	    						'nickname': '学生昵称',
	    						'status': '账号状态,默认0开启，1关闭',
	    						'addtime': '账号添加时间',
	    						'birthday': '学生生日',
	    						'courselist': '购买的课程列表',
	    						'id': '购买的课程id',
	    						'coursename': '课程名称',
	    						'classname': '班级名称',
	    						'amount': '课程价格',
	    						'type': '班级类型 1是一对一 2是小课班 3是大课班',
                            }",
	    			);
	/* //管理后台-用户-学生管理-添加学生
	$LiChen[] = array(
	    			'url'=>'/admin/student/addUser',
	    			'name'=>'管理后台-用户-学生管理-添加学生',
	    			'type'=>'post',
	    			'data'=>"{
	    				'imageurl': 'studentheadimage.png',
	    				'prphone': '+86',
	    				'mobile': '18888888888',
	    				'nickname': '大鹏',
	    				'username': '董成鹏',
	    				'sex': 1,
	    				'country': 12,
	    				'province': 22,
	    				'city': 33,
	    				'birth': '2018-03-30',
	    				'profile': '这个是一个学生的自我告白',
	    				'status': 0,
	    			}",
	    			'tip'=>"{
	    				'imageurl': '头像图片',
	    				'prphone': '国别前缀',
	    				'mobile': '手机号码',
	    				'nickname': '昵称',
	    				'username': '学生名字',
	    				'sex': '学生性别 0保密 1男 2女',
	    				'country': '国家id',
	    				'province': '省份id',
	    				'city': '城市id',
	    				'birth': '生日 1889-3-4 格式',
	    				'profile': '学生简介',
	    				'status': '学生账号状态默认0开启，1关闭',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);

	//管理后台-用户-学生管理-更新学生数据
	$LiChen[] = array(
	    			'url'=>'/admin/student/updateUser',
	    			'name'=>'管理后台-用户-学生管理-更新学生数据',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'imageurl': 'studentheadimage.png',
	    				'prphone': '+86',
	    				'mobile': '18888888888',
	    				'nickname': '大鹏',
	    				'username': '董成鹏update',
	    				'sex': 0,
	    				'country': 12,
	    				'province': 22,
	    				'city': 33,
	    				'birth': '2018-03-30',
	    				'profile': '这个是一个学生的自我告白',
	    				'status': 0,
	    			}",
	    			'tip'=>"{
	    				'id': '需要更新数据的学生的id',
	    				'imageurl': '头像图片',
	    				'prphone': '国别前缀',
	    				'mobile': '手机号码',
	    				'nickname': '昵称',
	    				'username': '学生名字',
	    				'sex': '学生性别 0保密 1男 2女',
	    				'country': '国家id',
	    				'province': '省份id',
	    				'city': '城市id',
	    				'birth': '生日 1889-3-4 格式',
	    				'profile': '学生简介',
	    				'status': '学生账号状态默认0开启，1关闭',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			); */
	//管理后台-用户-学生管理-更改学生状态
	$LiChen[] = array(
	    			'url'=>'/admin/student/changeUserStatus',
	    			'name'=>'管理后台-用户-学生管理-更改学生状态',
	    			'type'=>'post',
	    			'data'=>"{
	    				'userid': 6,
	    				'flag': 1,
	    			}",
	    			'tip'=>"{
	    				'userid': '需要修改的学生id',
	    				'flag': '0开启，1关闭',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	
	//管理后台-用户-学生管理-推送消息
	$LiChen[] = array(
		'url'=>'/admin/student/sendStudentMessage',
		'name'=>'管理后台-用户-学生管理-推送消息',
		'type'=>'post',
		'data'=>"{'studentids':[1,2,3],'title':'','content':''}",
		'tip'=>"{'studentids':'学生id数组','title':'推送标题','content':'推送内容'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
	);
	
	 //管理后台-用户-学生分类-学生分类列表
	$LiChen[] = array(
	    			'url'=>'/admin/Studentcategory/getCategoryList',
	    			'name'=>'管理后台-用户-学生分类-学生分类列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'name': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'name': '分类名称',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '分类名称',
	    						'status': '账号状态,默认1开启，0关闭',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	
	//获取所有学生分类			
	$LiChen[] = array(
	    			'url'=>'/admin/Studentcategory/getAllCategoryList',
	    			'name'=>'管理后台-教务-学生分类-获取所有学生分类',
	    			'type'=>'post',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '分类名称',
                            }",
	    			);
					
	//管理后台-用户-学生分类-获取分类详细信息
	$LiChen[] = array(
	    			'url'=>'/admin/Studentcategory/getCategoryMsg',
	    			'name'=>'管理后台-用户-学生分类-分类详细信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 1,
	    			}",
	    			'tip'=>"{
	    				'id': '学生分类id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '分类名称',
                            }",
	    			);
	//管理后台-用户-学生分类-添加学生分类
	$LiChen[] = array(
	    			'url'=>'/admin/Studentcategory/addCategory',
	    			'name'=>'管理后台-用户-学生分类-添加分类',
	    			'type'=>'post',
	    			'data'=>"{
	    				'name': '分类名称',
	    			}",
	    			'tip'=>"{
	    				'name': '分类名称',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);

	//管理后台-用户-学生分类-更新学生数据
	$LiChen[] = array(
	    			'url'=>'/admin/Studentcategory/updateCategory',
	    			'name'=>'管理后台-用户-学生分类-更新学生分类',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'name': '分类名称',
	    			}",
	    			'tip'=>"{
	    				'id': 'id',
	    				'name': '分类名称',
	    				//'status': '账号状态,默认1开启，0关闭',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//管理后台-用户-学生分类-更改分类状态
	$LiChen[] = array(
	    			'url'=>'/admin/Studentcategory/SwitchCategoryStatus',
	    			'name'=>'管理后台-用户-学生分类-更改分类状态',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'status': 0,
	    			}",
	    			'tip'=>"{
	    				'id': 'id',
	    				'status': '账号状态,默认1开启，0关闭',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//删除学生分类
	$LiChen[] = array(
	    			'url'=>'/admin/Studentcategory/deleteCategory',
	    			'name'=>'管理后台-用户-学生分类-删除分类',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':'',
	    			}",
	    			'tip'=>"{
	    				'id':'要删除的分类id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);				

     //管理后台-用户-学生标签-学生标签列表
	$LiChen[] = array(
	    			'url'=>'/admin/studentTag/getTagList',
	    			'name'=>'管理后台-用户-学生标签-标签列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'name': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'name': '标签名称',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '标签名称',
	    						'childname': '子标签名称',
	    						'status': '账号状态,默认1开启，0关闭',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	//管理后台-用户-学生标签-获取标签详细信息
	$LiChen[] = array(
	    			'url'=>'/admin/studentTag/getTaginfo',
	    			'name'=>'管理后台-用户-学生标签-标签详细信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 1,
	    			}",
	    			'tip'=>"{
	    				'id': '标签id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'name': '标签名称',
	    						'childname': '子标签名称',
	    						'status': '账号状态,默认1开启，0关闭',
                            }",
	    			);
	//管理后台-用户-学生标签-添加学生标签
	$LiChen[] = array(
	    			'url'=>'/admin/studentTag/addTag',
	    			'name'=>'管理后台-用户-学生标签-添加标签',
	    			'type'=>'post',
	    			'data'=>"{
	    				'name': '',
	    				'childname': '',
	    			}",
	    			'tip'=>"{
	    				'name': '标签名称',
						'childname': '子标签名称',
	    				//'status': '账号状态,默认1开启，0关闭',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);

	//管理后台-用户-学生标签-更新标签数据
	$LiChen[] = array(
	    			'url'=>'/admin/studentTag/updateTag',
	    			'name'=>'管理后台-用户-学生标签-更新标签',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': '',
	    				'name': '',
						'childname': '',
	    			}",
	    			'tip'=>"{
	    				'id': 'id',
	    				'name': '标签名称',
						'childname': '子标签名称',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//管理后台-用户-学生标签-更改标签状态
	$LiChen[] = array(
	    			'url'=>'/admin/studentTag/SwitchTagStatus',
	    			'name'=>'管理后台-用户-学生标签-更改标签状态',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'status': 0,
	    			}",
	    			'tip'=>"{
	    				'id': 'id',
	    				'status': '状态：默认0不显示，1显示',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	
	//删除学生标签
	$LiChen[] = array(
	    			'url'=>'/admin/studentTag/deleteTag',
	    			'name'=>'管理后台-用户-学生标签-删除标签',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':'',
	    			}",
	    			'tip'=>"{
	    				'id':'要删除的标签id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
					
/*************************************      微网站接口 Start           **********************/
    //首页轮播图
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/getSlideList',
		'name'=>'微网站-首页-轮播图',
		'type'=>'post',
		'data'=>"",
		'tip'=>"",
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
								'courseid':'课程id',
								'url': '其他',
							   }",
	);
	
	//首页-查询所有的三级分类
	$LiChen[] = array(
		'url'=>'/microsite/HomePage/getThreeCategroyList',
		'name'=>'微网站-首页-查询所有的三级分类',
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
	
	//查询三级分类下的课程
	$LiChen[] = array(
		'url'=>'/microsite/HomePage/getThreeCourseList',
		'name'=>'微网站-首页-查询三级分类下的课程',
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
	
	// 首页->热门推荐
	$LiChen[] = array(
		'url'=>'/microsite/HomePage/getRecommendCourser',
		'name'=>'微网站-首页-热门推荐',
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
	
	// 首页->各一级分类下最新5个课程
	$LiChen[] = array(
		'url'=>'/microsite/HomePage/getTopCategoryCourser',
		'name'=>'微网站-首页-各一级分类最新5个课程',
		'type'=>'post',
		'data'=>"",
		'tip'=>"",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'categoryname': '分类名称',
								'category_id': '分类ID',
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
     
	/************套餐相关 strart  *****/
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/getHomePackageList',
		'name'=>'微网站-首页-获取最新三条学生套餐',
		'type'=>'post',
		'data'=>"",
		'tip'=>"",
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
	
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/getPackageList',
		'name'=>'微网站-首页-学生套餐列表',
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
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/getPackageDetail',
		'name'=>'微网站-首页-套餐详情',
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
	$LiChen[] = array(
		'url'=>'/microsite/Package/gotoOrder',
		'name'=>'微网站-首页-套餐下单',
		'type'=>'post',
		'data'=>"{'packageid':1,'ordersource':'1'}",
		'tip'=>"{'packageid':1,'ordersource':'下单渠道 1web 2app 3microsite'}",
		'returns'=>"{
								 'code': '返回的查询标识，0为正常返回，其他为异常',
								 'data': '最外层data为此次请求的返回数据',
								 'info': '此次请求返回数据描述',
								 'ordernum': '201809071531153651275622'
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/Package/showOrderDetail',
		'name'=>'微网站-首页-显示套餐详情',
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
	$LiChen[] = array(
		'url'=>'/microsite/Package/gotoPay',
		'name'=>'微网站-首页-套餐支付',
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
	$LiChen[] = array(
		'url'=>'/microsite/Package/getPackageOrderList',
		'name'=>'微网站-个人中心-查询套餐订单列表',
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
	$LiChen[] = array(
		'url'=>'/microsite/Package/cancelOrder',
		'name'=>'微网站-我的套餐订单-取消订单',
		'type'=>'post',
		'data'=>"{'ordernum':'201804281944329376183807'}",
		'tip'=>"{'ordernum':'订单号'}",
		'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
								}",
	);
	$LiChen[] = array(
		'url'=>'/microsite/Package/orderSuccess',
		'name'=>'微网站-套餐订单-订单成功详情',
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
	$LiChen[] = array(
		'url'=>'/microsite/Package/packageUseList',
		'name'=>'微网站-我的-我的套餐列表',
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
	
	//删除套餐
	$LiChen[] = array(
		'url'=>'/microsite/Package/deletePackageUse',
		'name'=>'微网站-我的套餐-删除学生套餐',
		'type'=>'post',
		'data'=>"{'packageuseid':'1'}",
		'tip'=>"{'packageuseid': '套餐使用的id'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
	);

/**************************     套餐 End  ***************************/

/*********************   课程订单逻辑 Start  ****************************/
	$LiChen[] = array(
		'url'=>'/microsite/Myorder/getMyOrderList',
		'name'=>'微网站-课程订单-我的订单列表',
		'type'=>'post',
		'data'=>"{'pagenum':1,'limit':1}",
		'tip'=>"{'pagenum':'分页页数','limit':'每页记录数'}",
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
								'orderstatus': '订单状态0已下单，1超时未支付已取消状态，2已支付，3申请退款',
								'teachername': '老师名称',
								'imageurl': '课程图片',
								'subhead': '课程简介'
						   }",
	);
	
	$LiChen[] = array(
		'url'=>'/microsite/Myorder/gotoOrder',
		'name'=>'微网站-课程订单-提交订单',
		'type'=>'post',
		'data'=>"{'courseid':5,'studentid':1,'schedulingid':'1','amount':'1.00','ordersource':'1','organid':1,'originprice':'1234567','addressid':1,'usestatus':1,'type':1,'packageid':1,'packagegiftid':1,'packageuseid':1}",
		'tip'=>"{'courseid':'课程id','studentid':'学生登录用户名id','schedulingid':'班级id','amount':'实付金额','ordersource':'下单渠道 1web 2app 3microsite','organid':'机构id','originprice':'课程原价','addressid':'地址id','usestatus':'是否使用优惠券0未使用1使用','type':'类型','packageid':'套餐id','packagegiftid':'赠送套餐id','packageuseid':'套餐的使用id'}",
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
	$LiChen[] = array(
		'url'=>'/microsite/Myorder/getUserPackage',
		'name'=>'微网站-课程订单-查询学生用户可用的优惠券',
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
	$LiChen[] = array(
		'url'=>'/microsite/Myorder/showOrderDetail',
		'name'=>'微网站-课程订单-支付中心',
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
	$LiChen[] = array(
		'url'=>'/microsite/Myorder/queryOrderStatus',
		'name'=>'微网站-课程订单-查询订单状态',
		'type'=>'post',
		'data'=>"{'ordernum':'201804281811553803628717','type':1}",
		'tip'=>"{'ordernum':'订单号','type':'1购买课程2购买套餐'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
	);

	$LiChen[] = array(
		'url'=>'/microsite/Myorder/cancelOrder',
		'name'=>'微网站-课程订单-取消订单',
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
	$LiChen[] = array(
		'url'=>'/microsite/Myorder/gotoPay',
		'name'=>'微网站-课程订单-立即支付',
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
/*********************   课程订单逻辑 End  ****************************/
	// 首页->名师推荐
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/getRecommendTeacher',
		'name'=>'微网站-首页-名师推荐',
		'type'=>'post',
		'data'=>"",
		'tip'=>"",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'teachername': '老师名称',
								'imageurl': '教师头像',
								'teacherid': '老师id',
								'identphoto': '证件照片',
							   }",
	);

	$LiChen[] = array(
    'url'=>'/microsite/Homepage/getAllTeacherList',
    'name'=>'微网站-首页-名师堂',
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
	
	$LiChen[] = array(
    'url'=>'/microsite/Teacherdetail/getTeacherCurriculum',
    'name'=>'微网站-首页-老师详情',
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
	$LiChen[] = array(
		'url'=>'/microsite/Teacherdetail/getTeacherClass',
		'name'=>'微网站-首页-Ta的班级',
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
	//我的课表
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/studentLessonsList',
		'name'=>'微网站-我的-我的课表',
		'type'=>'post',
		'data'=>"{'status':0,'pagenum':1,'limit':20}",
		'tip'=>"{'status':'状态：默认0未上课，1已结束','pagenum':'页码','limit':'每页多少条记录'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'time': '上课时间',
								'schedulingid': '排课id',
                                'intime': '2018-05-09',
                                'coursename': '课程名称',
                                'gradename': '班级名称',
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
	//已结束课时查看点评
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/getFeedback',
		'name'=>'微网站-我的-我的课表-查看点评',
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
	//我的课程
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/getBuyCurriculum',
		'name'=>'微网站-我的课程-我的课程列表',
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
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/getClassSchedule',
		'name'=>'微网站-我的课程-课时安排或查看详情',
		'type'=>'post',
		'data'=>"{'coursetype':1,'schedulingid':'1','courseid':2}",
		'tip'=>"{'coursetype':'1:录播课查看详情2:直播课课时安排','schedulingid':'班级id','courseid':'课程id'}",
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
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/watchPlayback',
		'name'=>'微网站-我的课程-录播课查看课时的视频',
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
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/getRecordComment',
		'name'=>'微网站-我的课程-查看录播课课时评论',
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
	
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/getTopCategory',
		'name'=>'微网站-课程分类-展示一级分类',
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
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/getTopCategoryChild',
		'name'=>'微网站-课程分类-展示一级分类下所有分类',
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
	
	// 首页->分类查询
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/getFilterCourserList',
		'name'=>'微网站-首页-查询分类下的课程',
		'type'=>'post',
		'data'=>"{'is_free':0,'category_id':'1','pagenum':'1','coursetype':0}",
		'tip'=>"{'is_free':'是否查询免费学。0:全部.1:免费','category_id':'可选默认不填类型:数字','pagenum':'分页页码 ','coursetype':'可选 0全部 1录播课 2直播课'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'pageinfo':'分页list',
								'pagesize':'每页多少条记录',
								'pagenum':'当前页码',
								'total':'总记录数',
								'categorylist': '分类List',
								'category_id': '分类id' ,
								'categoryname': '分类名称',
								'rank': '分类等级',
								'fatherid': '父类id',
								'sort': '排序id',
								'subhead': '课程副标题',
								'imageurl': '课程图片',
								'coursename': '课程名称',
								'courseid': '课程id 用于跳转到课程详情',
								'price': '课程最低价格',
								'maxprice': '课程最大价格',
								'classtypes': '课程类型 1 录播课 2直播课'
							   }",
	);
	/* $LiChen[] = array(
		'url'=>'/microsite/Homepage/getCateLeader',
		'name'=>'微网站-首页-分类搜索导航和面包屑',
		'type'=>'post',
		'data'=>"{'category_id':1,'organid':1}",
		'tip'=>"{'category_id':'必选 分类id','organid':'1'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'id': '分类id',
								'title': '分类名称',
								'rank': '分类等级',
								'fid': '父级id',
								'children':'下集分类',
								'selected':'是否选中 true:选中; false:未选中',
								'leader': '面包屑'
							   }",
	); */
	$LiChen[] = array(
		'url'=>'/microsite/Curriculumdetail/chooseAllList',
		'name'=>'微网站-课程详情-课程选择',
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
								   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/Curriculumdetail/getCurriculumDateList',
		'name'=>'微网站-首页-课程详情返回可选日期',
		'type'=>'post',
		'data'=>"{'courseid':1}",
		'tip'=>"{'courseid':1}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/Curriculumdetail/getCurriculumInfo',
		'name'=>'微网站-首页-课程详情列表',
		'type'=>'post',
		'data'=>"{'courseid':2,'classtypes':2,'teacherid':1,'date':'2018-07-06','fullpeople':4}",
		'tip'=>"{'courseid':'课程id','classtypes':'1 录播课 2直播课','teacherid':'老师id 可选参数 默认不传','date':'日期','fullpeople':'可选默认0 4或者6'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'category': '分类List',
								'tags': '课程标签 官方的没有此List',
								'applystatus':'报名状态0不可报名1可报名',
								'timestr': '日期',
								'courser': '课程相关信息list',
								'curriculumid': '课程id',
								'organid': '机构id',
								'scheduid': '排课班级id',                
								'teacherid': '老师id',
								'type': '班级类型 1:一对一2：小班课3：大班课',
								'totalprice': '课时总价',
								'gradename': '班级名称',
								'teachername': '老师姓名',
								'imageurl': '课程封面图',
								'coursename': '课程名称',
								'subhead': '课程副标题',
								'describe': '适用人群描述',
								'studypeople': '学习人数',
								'periodnum': '课时总数',
								'generalize': '课程概述',
								'teacher': '授课老师信息list',
								'imageurl': '老师头像',
								'classesnum': '开课数量',
								'studentnum': '学生数量',
								'teachername': '老师姓名',
								'unit': '课时安排list',
								'unitid': '单元id',
								'curriculumid': 2,
								'unitname': '单元名称',
								'unitsort': '单元排序',
								'period': '课节list',
								'periodname': '课节名称',
								'periodsort': 1,
								'intime': '上课时间年月日',
								'time': '上课时间时分秒',
								'recommend': '老师推荐课程list',
								'curriculumid': '课程id',
								'type': '班级类型1：一对一2：小班课 3：大班课',
								'totalprice': '课时总价',
								'teachername': '老师姓名',
								'imageurl': '课程封面图',
								'coursename': '课程名称',
								'scheduid': '班级id',
								'organinfo': '机构list 官方网站有次list',
								'imageurl': '机构图片',
								'organid': '机构id',
								'organname': '机构名称',
								'summary': '机构简介',
								'phone': '机构电话',
								'email': '机构email',
								'realnum': '报名人数',
								'vip': '0'
							}",
	);
	
	//调班调课
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/getAllClassList',
		'name'=>'微网站-调班-查询可调的班级信息和目标班级信息',
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
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/submitApplyClasss',
		'name'=>'微网站-调班-调班提交',
		'type'=>'post',
		'data'=>"{'curriculumid':1,'oldschedulingid':1,'newschedulingid':2}",
		'tip'=>"{'curriculumid':'课程id','原班级id':1,'newschedulingid':'新班级id'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/getBuyCourseList',
		'name'=>'微网站-调课-查询课程和原班级信息',
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
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/getSelectableLessons',
		'name'=>'微网站-调课-获取可选择的课时名称',
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
	$LiChen[] = array(
		'url'=>'/microsite/Mycourse/submitApplylession',
		'name'=>'微网站-调课-调课提交',
		'type'=>'post',
		'data'=>"{'newlessonsid':1254,'oldlessonsid':168,'curriculumid':146}",
		'tip'=>"{'newlessonsid':'新课节id','oldlessonsid':'旧的课节信息','curriculumid':'课程id'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '',
								}",
	);
	
	//我的作业
	$LiChen[] = array(
		'url'=>'/microsite/User/getHomeworkList',
		'name'=>'微网站-首页-我的作业',
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
								'teachername': '老师名称',
								'subjectcount': '题目总数'
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/getQuestionList',
		'name'=>'微网站-我的作业-写作业查询我的题库',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/submitQuestions',
		'name'=>'微网站-我的作业-作业提交',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/showUpdateQuestions',
		'name'=>'微网站-我的作业-修改查询作业信息',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/updateQuestions',
		'name'=>'微网站-我的作业-作业修改提交',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/getCompleteQuestionList',
		'name'=>'微网站-我的作业-查询已完成的作业信息',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/updateMicroStudentInfo',
		'name'=>'微网站-首页-编辑资料',
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
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/sendMobileMsg',
		'name'=>'微网站-首页-找回密码、注册-发送短信',
		'type'=>'post',
		'data'=>"{'mobile':18235102743,'code':'1234','organid':1,'sessionid':'54444444444','type':1,'prphone':'86'}",
		'tip'=>"{'mobile':'手机号','code':'图形验证码','organid':'机构id','sessionid':'随机验证码返回的验证码标识','type':'业务类型 1找回密码 2 注册','prphone':'默认不传'}",
		'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/updatePass',
		'name'=>'微网站-首页-找回密码-找回密码',
		'type'=>'post',
		'data'=>"{'mobile':18235102743,'code':'1234','organid':1,'newpass':'1234567'}",
		'tip'=>"{'mobile':'手机号','code':'短信验证码','organid':'机构id','newpass':'新密码'}",
		'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/Homepage/register',
		'name'=>'微网站-首页-提交注册',
		'type'=>'post',
		'data'=>"{'mobile':18235102743,'code':'1234','organid':1,'password':'1234567','prphone':'86'}",
		'tip'=>"{'mobile':'手机号','code':'短信验证码','organid':'机构id;官方首页默认1','password':'密码','prphone':'手机国家区号'}",
		'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
	);
	
    $LiChen[] = array(
		'url'=>'/microsite/User/getUserFavorCategory',
		'name'=>'微网站-首页-学生分类列表',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/addUserFavorCategory',
		'name'=>'微网站-首页-选择分类',
		'type'=>'post',
		'data'=>"{'categoryid':1}",
		'tip'=>"{'categoryid':'分类id'}",
		'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/getUserTag',
		'name'=>'微网站-首页-学生标签列表',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/addUserTag',
		'name'=>'微网站-首页-选择标签',
		'type'=>'post',
		'data'=>"{'tagid':1,'childtags':'1,2'}",
		'tip'=>"{'tagid':'父标签id','childtags':'子标签id集合,逗号隔开'}",
		'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
	);
	
	//我的-个人信息
	$LiChen[] = array(
		'url'=>'/microsite/User/getStudentInfo',
		'name'=>'微网站-我的-个人信息',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/getStudentPaylog',
		'name'=>'微网站-我的-账户余额-明细',
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
	//我的-签到首页
	$LiChen[] = array(
	    			'url'=>'/microsite/User/signinHome',
	    			'name'=>'微网站-我的-签到首页',
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
	$LiChen[] = array(
	    			'url'=>'/microsite/User/signin',
	    			'name'=>'微网站-我的-签到',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/mySigninList',
		'name'=>'微网站-我的-历史签到',
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
	$LiChen[] = array(
	    			'url'=>'/microsite/User/getSigninbgiList',
	    			'name'=>'微网站-我的-更换背景-背景图列表',
	    			'type'=>'post',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	$LiChen[] = array(
		'url'=>'/microsite/User/changeSigninImage',
		'name'=>'微网站-我的-更换背景',
		'type'=>'post',
		'data'=>"{'signinimageid':1}",
		'tip'=>"{'signinimageid':'图片id'}",
		'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/teacherCollect',
		'name'=>'微网站-首页-收藏老师',
		'type'=>'post',
		'data'=>"{'teacherid':1}",
		'tip'=>"{'teacherid':'老师id'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/cancelTeacherCollect',
		'name'=>'微网站-首页-取消收藏老师',
		'type'=>'post',
		'data'=>"{'teacherid':1}",
		'tip'=>"{'teacherid':'老师id'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/classCollect',
		'name'=>'微网站-首页-收藏课程班级',
		'type'=>'post',
		'data'=>"{'courseid':1}",
		'tip'=>"{'courseid':'课程id'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/cancelClassCollect',
		'name'=>'微网站-首页-取消收藏课程班级',
		'type'=>'post',
		'data'=>"{'courseid':1}",
		'tip'=>"{'courseid':'课程id'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/teacherCollectList',
		'name'=>'微网站-我的-我的收藏-老师',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/classCollectList',
		'name'=>'微网站-我的-我的收藏-课程',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/messageList',
		'name'=>'微网站-我的-我的消息',
		'type'=>'post',
		'data'=>"{'pagenum':1,'limit':10,'type':1}",
		'tip'=>"{'pagenum':'分页页数','limit':'每页记录数','type':'消息类型: 0全部消息 1系统消息 2推送消息'}",
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
	$LiChen[] = array(
		'url'=>'/microsite/User/getNewMsg',
		'name'=>'微网站-我的消息-是否有新消息以及数据',
		'type'=>'post',
		'data'=>"",
		'tip'=>"",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '操作成功',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/deleteMsg',
		'name'=>'微网站-我的消息-删除消息和批量删除消息',
		'type'=>'post',
		'data'=>"{'messageids':5}",
		'tip'=>"{'messageids':'消息id'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '操作成功',
							   }",
	);

	//我的点评	
	$LiChen[] = array(
		'url'=>'/microsite/User/myCommentList',
		'name'=>'微网站-我的-课堂反馈-点评列表',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/myCommentMsg',
		'name'=>'微网站-我的-课堂反馈-点评详情',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/studentRecharge',
		'name'=>'微网站-我的-账户余额-充值',
		'type'=>'post',
		'data'=>"{'studentid':1,'amount':'1.00','paytype':'2','organid':1,'source':1}",
		'tip'=>"{'studentid':'学生id','amount':'订单金额','paytype':'支付类型 2:微信支付3支付宝4银联','organid':'机构id','source':'充值渠道1pc 2手机'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
								'如果是支付宝支付': '跳转到支付宝页面',
								'如果是微信支付': '会返回codeurl字段 用于支付扫码的url',
							  
						   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/addOrUpdateAddress',
		'name'=>'微网站-我的-添加或者修改收货地址',
		'type'=>'post',
		'data'=>"{'pid':7,'cityid':'1001','areaid':1002,'address':'红军营南路','linkman':'测试人','mobile':'18235102743','zipcode':'030024','isdefault':0,'id':1}",
		'tip'=>"{'pid':'省id','cityid':'城市id','areaid':'区域id','address':'地址','linkman':'联系人','mobile':'手机号','zipcode':'邮政编码','isdefault':'是否默认地址 1默认0否','id':'可选 修改信息是必选'}",
		'returns'=>"{
									'code': '返回的查询标识，0为正常返回，其他为异常',
									'data': '最外层data为此次请求的返回数据',
									'info': '此次请求返回数据描述',
							   }",
	);
	$LiChen[] = array(
		'url'=>'/microsite/User/getAddressList',
		'name'=>'微网站-我的-查询收货地址',
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
	$LiChen[] = array(
		'url'=>'/microsite/User/deleteAddress',
		'name'=>'微网站-我的-删除收货地址',
		'type'=>'post',
		'data'=>"{'id':1}",
		'tip'=>"{'id':'收货地址id'}",
		'returns'=>"{
								'code': '返回的查询标识，0为正常返回，其他为异常',
								'data': '最外层data为此次请求的返回数据',
								'info': '此次请求返回数据描述',
							   }",
	);
	/***************      微网站接口 End           **********************/		