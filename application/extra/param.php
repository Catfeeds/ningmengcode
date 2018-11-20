<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/21 0021
 * Time: 14:36
 */
return [
	//短信验证码允许错误次数
	'sms_maxallowed' => 5,
	//定义官方机构id
	'affical_organid' => 1,
	//定义服务器域名
	'server_url' => 'http://ningmeng.talk-cloud.cn',
	'http_name' => 'http://',
	// 配置服务器 主域名 用于 控制注册机构的访问控制 ，在扩展 extend/domain/ 下
	'maindomain' => 'menke.com',
	// 套餐过期前多少天可以 续费
	'mealneardays' => 30,
	'pagesize' => [
		'adminorder_orderlist' => 20, // /admin/order/getOrderList
		'adminorder_adminlist' => 20, // /admin/organ/getAdminList
		'adminorder_lessonlist' => 20, // /admin/organ/getLessonsByDate
		'adminrecomm_courselist' => 20, // /admin/recommend/getCourseList
		'adminrecomm_teacherlist' => 20, // /admin/recommend/getTeacherList
		'adminstu_userlist' => 20, // /admin/student/getUserList
		'adminteach_teachlist' => 20, // /admin/teacher/getTeachList
		'adminteach_lablelist' => 20, // /admin/teacher/teachLableList
		'adminteach_valuelist' => 20, // /admin/teacher/getValueList
		'officialrecomm_freeorgan' => 20, // /official/recommend/getFreeOrgan
		'officialstu_userlist' => 20, // /official/student/getUserList
		//student/Homepage/getFilterCourserList
		'student_courserlist' => 20,
		//student/Mycourse/searchCourseByCname
		'student_searchcourse' => 20,
		//student/Homepage/searchOrgainOrCourse
		'student_searchall' => 20,
		//student/Homepage/getOrganCourseList
		'student_getorgancourse' => 20,
		//student/Homepage/getOrganTeacherList
		'student_getteacherlist' => 20,
		//student/Mycourse/getBuyCurriculum
		'student_curriculum' => 20,
		//student/Myorder/getMyOrderList
		'student_orderlist' => 20,
		//student/Teacherdetail/getCommentList
		'student_commentlist' => 20,
		//student/User/teacherCollectList
		'student_teachercollect' => 20,
		//student/User/organCollectList
		'student_organcollect' => 20,
		//student/User/classCollectList
		'student_classcollect' => 20,
		//student/User/getStudentPaylog
		'student_studentpaylog' => 20,
		//student/User/getHomeworkList
		'student_homework_list' => 20,
        //student/User/messageList
        'student_messagelist' => 20,
        //student/Package/getPackageList
        'student_packagelist' => 20,
        //student/Package/packageUseList
        'student_packageuselist' => 20,

		'official_category_list' => 20,

		'official_class_list' => 20,

		'official_order_list' => 20,

		'official_account_detail_in' => 20,

		'official_withdraw_by_organ_list' => 20,

		'official_organ_list' => 20,

		'official_organ_pay_audit_bill_list' => 20,

		'official_apply_vip_organ_list' => 20,

		'official_user_list' => 20,

		'official_user_operate_list' => 20,
		//teacher/Course/getSchedulingList
		'teacher_schedulinglist' => 20,
		//teacher/Course/getCurricukum
		'teacher_sche_curriculumlist' => 20,
		//teacher/student/getUserList
		'teacher_stu_list' => 20,
		//appteacher/Order/getOrderList
		'teacher_oderlist' => 20,
		//teacher/Teacher/getPersoninfo
		'person_info_listA' => 5,
		'person_info_listB' => 5,
		'person_info_listC' => 5,
		//teacher/Teacher/getAllComment
		'teacher_comment_list' => 20,
		'adminStudent_Categorylist' => 20,
		'adminStudent_Taglist' => 20,
		'admin_KnowledgeTypelist' => 20,
		'admin_Knowledgelist' => 20,
		'admin_Rewardlist' => 20,
        'admin_curriculumpromotion_list'=>10, // /admin/Proadmin/curriculumReco
        'admin_teacherpromotion_list'=>20, // /admin/Proadmin/teacherReco
        'admin_setpro_list' => '20', // /admin/Proadmin/setProList
        'admin_curriculum_list'=>'10',// /admin/Promotion/SetAddCurriculum
        'admin_transferclass_list'=>'20',// /admin/Promotion/SetAddCurriculum
        'admin_setdata_list' => '20',
        'admin_freeclass_list' => '20',
        'admin__list' => '20',
        'Teacher_composition_list' => '20',
        'app_composition_list' => '6'

	],
	/*每分钟*/
	/*每小时 某分*/
	/*每天 某时:某分*/
	/*每周-某天 某时:某分  0=周日*/
	/*每月-某天 某时:某分*/
	/*某月-某日 某时-某分*/
	/*某年-某月-某日 某时-某分*/
	'sys_crond_timer' => array('*', 'i', '*:i', 'H:i', '@-w H:i', '*-d H:i', 'm-d H:i', 'Y-m-d H:i'),

	// caond 定时任务配置列表
	'crond_list' => [
		'*' => [
			// 每分钟
			/*'app\admin\business\TimingTask::savelog'*/
		],
		'5' => [
			//每 5分钟
			'app\admin\business\TimingTask::makeRoom',
			'app\admin\business\TimingTask::cancelOrder',
			'app\admin\business\TimingTask::getTimeStatus',
			//'app\admin\business\TimingTask::updateFile',
			'app\admin\business\TimingTask::cancelPackageOrder',
			'app\admin\business\TimingTask::cancelPackageStatus',
		],
		'10' => [
			'app\admin\business\TimingTask::RemindMessage'
		],
        '*:05'=>[
            //每隔十分钟跑一次
            'app\admin\business\TimingTask::rcMobile'
        ],
        '*:15'=>[
            //每隔十分钟跑一次
            'app\admin\business\TimingTask::rcMobile'
        ],
        '*:25'=>[
            //每隔十分钟跑一次
            'app\admin\business\TimingTask::rcMobile'
        ],
        '*:35'=>[
            //每隔十分钟跑一次
            'app\admin\business\TimingTask::rcMobile'
        ],
        '*:45'=>[
            //每隔十分钟跑一次
            'app\admin\business\TimingTask::rcMobile'
        ],
        '*:55'=>[
            //每隔十分钟跑一次
            'app\admin\business\TimingTask::rcMobile'
        ],


//		'*:30' => [
//			// 1小时跑一次
//			'app\admin\business\TimingTask::rcMobile',
//		],
//		'*:14' => [
//			//每小时14分，44分跑一次
//			'app\admin\business\TimingTask::RemindMessage',
//		],
//		'*:44' => [
//			'app\admin\business\TimingTask::RemindMessage',
//		],
//		'18:42'=>[
		//			// 一天一次 每天0点跑
		//			 'app\admin\business\TimingTask::getTimeStatus'
		//		],
		//		'00:00'      => [],  //每周 ------------
		'*-01 00:00' => [], //每月--------
		'*:00' => [], //每小时---------
	],

	'ClassTime' => [
		'00:00', '00:10', '00:20', '00:30', '00:40', '00:50',

		'01:00', '01:10', '01:20', '01:30', '01:40', '01:50',

		'02:00', '02:10', '02:20', '02:30', '02:40', '02:50',

		'03:00', '03:10', '03:20', '03:30', '03:40', '03:50',

		'04:00', '04:10', '04:20', '04:30', '04:40', '04:50',

		'05:00', '05:10', '05:20', '05:30', '05:40', '05:50',

		'06:00', '06:10', '06:20', '06:30', '06:40', '06:50',

		'07:00', '07:10', '07:20', '07:30', '07:40', '07:50',

		'08:00', '08:10', '08:20', '08:30', '08:40', '08:50',

		'09:00', '09:10', '09:20', '09:30', '09:40', '09:50',

		'10:00', '10:10', '10:20', '10:30', '10:40', '10:50',

		'11:00', '11:10', '11:20', '11:30', '11:40', '11:50',

		'12:00', '12:10', '12:20', '12:30', '12:40', '12:50',

		'13:00', '13:10', '13:20', '13:30', '13:40', '13:50',

		'14:00', '14:10', '14:20', '14:30', '14:40', '14:50',

		'15:00', '15:10', '15:20', '15:30', '15:40', '15:50',

		'16:00', '16:10', '16:20', '16:30', '16:40', '16:50',

		'17:00', '17:10', '17:20', '17:30', '17:40', '17:50',

		'18:00', '18:10', '18:20', '18:30', '18:40', '18:50',

		'19:00', '19:10', '19:20', '19:30', '19:40', '19:50',

		'20:00', '20:10', '20:20', '20:30', '20:40', '20:50',

		'21:00', '21:10', '21:20', '21:30', '21:40', '21:50',

		'22:00', '22:10', '22:20', '22:30', '22:40', '22:50',

		'23:00', '23:10', '23:20', '23:30', '23:40', '23:50',
	],
	
	'teacherTypeArr' => [
		'2' => 1,
		'5' => 2,
	],
	'JPProduction' => true,
];