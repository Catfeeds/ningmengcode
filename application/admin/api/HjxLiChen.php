<?php								
	 //管理后台-用户-学生管理-学生列表
	$HjxLiChen[] = array(
	    			'url'=>'/admin/student/getHjxUserList',
	    			'name'=>'管理后台-用户-学生管理-好迹星学生列表',
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
	    						'id': '学生id',
	    						'mobile': '学生账号',
	    						'nickname': '学生昵称',
								'sex': '性别 0保密 1男 2女',
								'school': '学校',
								'grade': '年级',
								'class': '班级',
								'logintime': '最近一次登录时间',
	    						'status': '账号状态,默认0开启，1关闭',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	
	//管理后台-用户-学生管理-更改学生状态
	$HjxLiChen[] = array(
	    			'url'=>'/admin/student/changeHjxUserStatus',
	    			'name'=>'管理后台-用户-学生管理-更改好迹星学生状态',
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
	$HjxLiChen[] = array(
	    			'url'=>'/admin/composition/getCompositionStatistics',
	    			'name'=>'管理后台-作文管理-统计',
	    			'type'=>'post',
	    			'data'=>"",
	    			'tip'=>"",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'total': '提交总数',
	    						'reviewed': '已批阅数',
                            }",
	    			);				
	$HjxLiChen[] = array(
	    			'url'=>'/admin/composition/getCompositionList',
	    			'name'=>'管理后台-作文管理-作文列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'status': '0',
	    				'nickname': '',
	    				'studentreviewscore': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'status': '批阅状态(0未批阅, 1已批阅)',
	    				'nickname': '用户昵称',
	    				'studentreviewscore': '学生评分',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': '作文id',
	    						'type': '类型: 1作文, 2日记',
	    						'school': '学校',
	    						'grade': '年级',
	    						'class': '班级',
	    						'nickname': '学生姓名',
	    						'title': '作文标题',
	    						'addtime': '添加时间',
	    						'pretechername': '上次批改人',
                                'prereviewtime': '上次批阅时间',
								'techername': '批改人',
                                'reviewtime': '批阅时间',
                                'studentreviewscore': '学生评分',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	$HjxLiChen[] = array(
	    			'url'=>'/admin/composition/getCompositioninfo',
	    			'name'=>'管理后台-作文管理-作文详情',
	    			'type'=>'post',
	    			'data'=>"{
	    				'compositionid': 1,
	    			}",
	    			'tip'=>"{
	    				'compositionid': '作文id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'studentname': '学生姓名',
	    						'title': '作文标题',
	    						'content': '作文内容',
	    						'imgurl': '作文图片',
	    						'videourl': '录音url',
	    						'addtime': '添加时间',
	    						'teachercomment': '老师批阅',
	    						'studentcomment': '学生回复',
                            }",
	    			);
	//删除作文
	$HjxLiChen[] = array(
	    			'url'=>'/admin/composition/deleteComposition',
	    			'name'=>'管理后台-作文管理-删除作文',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':5,
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
	
	$HjxLiChen[] = array(
	    			'url'=>'/admin/review/getReviewList',
	    			'name'=>'管理后台-作文-批阅管理-老师批阅列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'teachername': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'teachername': '老师名称',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'teacherid': '老师id',
	    						'teachername': '老师名称',
	    						'reviewcount': '批阅数',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	$HjxLiChen[] = array(
	    			'url'=>'/admin/review/getTeacherReviewList',
	    			'name'=>'管理后台-作文-批阅管理-老师批阅详情列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'teacherid': 1,
	    				'nickname': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'teacherid': '老师id',
	    				'nickname': '学生姓名',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'compositionid': '作文id',
	    						'studentname': '学生姓名',
	    						'title': '作文标题',
	    						'reviewtime': '批阅时间',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
	
/**********************      好迹星1.1.0 接口开始       ************/
	//标签管理
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Commentlabel/getCommentlabelList',
	    			'name'=>'管理后台-作文-标签管理-标签列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'star': '1',
	    				'content': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'star': '星级',
	    				'content': '标签内容',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'star': '星级',
								'content': '标签内容',
								'addtime': '添加时间',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
				
	//管理后台-作文-标签管理-获取标签详细信息
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Commentlabel/getCommentlabelmsg',
	    			'name'=>'管理后台-作文-标签管理-编辑时获取标签详细信息',
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
	    						'type': '标签类型',
								'content': '标签内容',
                            }",
	    			);
	
	//管理后台-作文-标签管理-添加标签
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Commentlabel/addCommentlabel',
	    			'name'=>'管理后台-作文-标签管理-添加标签',
	    			'type'=>'post',
	    			'data'=>"{
	    				'star': '',
	    				'content': '',
	    			}",
	    			'tip'=>"{
	    				'star': '星级',
	    				'content': '标签内容',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);

	//管理后台-作文-标签管理-更新标签
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Commentlabel/updateCommentlabel',
	    			'name'=>'管理后台-作文-标签管理-更新标签',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'star': '1',
	    				'content': '',
	    			}",
	    			'tip'=>"{
	    				'id': '标签id',
	    				'star': '星级',
	    				'content': '标签内容',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//删除标签
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Commentlabel/deleteCommentlabel',
	    			'name'=>'管理后台-作文-标签管理-删除标签',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':5,
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
	
	//例句类型
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentencetype/getExampleTypeList',
	    			'name'=>'管理后台-作文-例句类型-例句类型列表',
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
	
    	
	//管理后台-作文-例句类型-获取例句类型详细信息
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentencetype/getExampleTypeMsg',
	    			'name'=>'管理后台-作文-例句类型-例句类型详细信息',
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
	
	//管理后台-作文-例句类型-添加例句类型
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentencetype/addExampleType',
	    			'name'=>'管理后台-作文-例句类型-添加例句类型',
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

	//管理后台-作文-例句类型-更新分类
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentencetype/updateExampleType',
	    			'name'=>'管理后台-作文-例句类型-更新例句类型',
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
	//删除例句类型
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentencetype/deleteExampleType',
	    			'name'=>'管理后台-作文-例句类型-删除例句类型',
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
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentencetype/getAllExampleTypeList',
	    			'name'=>'管理后台-作文-例句管理-获取所有例句类型',
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
	
	//例句管理
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentence/getExampleList',
	    			'name'=>'管理后台-作文-例句管理-例句列表',
	    			'type'=>'post',
	    			'data'=>"{
	    				'type': '',
	    				'content': '',
	    				'pagenum': 1,
	    			}",
	    			'tip'=>"{
	    				'type': '例句类型id',
	    				'content': '例句内容',
	    				'pagenum': '页码',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'typename': '例句类型',
								'content': '例句内容',
								'addtime': '添加时间',
	    						'pageinfo': '分页信息' ,
                                'pagesize': '每页最多条数' ,
                                'pagenum': '当前页码' ,
                                'total': '符合条件的总记录数目' ,
                            }",
	    			);
				
	//管理后台-作文-例句管理-获取例句详细信息
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentence/getExamplemsg',
	    			'name'=>'管理后台-作文-例句管理-编辑时获取例句详细信息',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': '1',
	    			}",
	    			'tip'=>"{
	    				'id': '例句id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
	    						'id': 'id',
	    						'type': '例句类型',
								'content': '例句内容',
                            }",
	    			);
	
	//管理后台-作文-例句管理-添加例句
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentence/addExample',
	    			'name'=>'管理后台-作文-例句管理-添加例句',
	    			'type'=>'post',
	    			'data'=>"{
	    				'type': '',
	    				'content': '',
	    			}",
	    			'tip'=>"{
	    				'type': '例句类型id',
	    				'content': '例句内容',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);

	//管理后台-作文-例句管理-更新例句
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentence/updateExample',
	    			'name'=>'管理后台-作文-例句管理-更新例句',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id': 6,
	    				'type': '',
	    				'content': '',
	    			}",
	    			'tip'=>"{
	    				'id': '例句id',
	    				'type': '例句类型id',
	    				'content': '例句内容',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);
	//删除例句
	$HjxLiChen[] = array(
	    			'url'=>'/admin/Examplesentence/deleteExample',
	    			'name'=>'管理后台-作文-例句管理-删除例句',
	    			'type'=>'post',
	    			'data'=>"{
	    				'id':5,
	    			}",
	    			'tip'=>"{
	    				'id':'要删除的例句id',
		    		}",
	    			'returns'=>"{
	    				        'code': '返回的查询标识，0为正常返回，其他为异常',
	    						'data': '最外层data为此次请求的返回数据',
	    						'info': '此次请求返回数据描述',
                            }",
	    			);