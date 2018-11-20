<?php
/**
 * 
 * 题库管理业务逻辑
 * 
 */
namespace app\admin\business;
use app\admin\model\Category;
use app\admin\model\Schedulinglessoninfo;
use app\admin\model\Studenthomework;
use app\admin\model\Studenthomeworkanswer;
use app\admin\model\Exercisesubject;
use app\admin\model\Exercisesubjectoptions;
use app\admin\model\Lessons;
use app\admin\model\Studentinfo;
class HomeworkManage{
	
	function __construct() {
	}
	
	/**
	 * 通过lessonid获取习题详细信息
	 * @Author lc
	 * @param $id id
	 * @return object
	 *
	 */
	public function getcourseCategoryListOne(){
		$category = new Category();
		$where['fatherid'] = 0;
		$baseinfo = $category->getAllCategoryList($where);

		if( empty($baseinfo) ){
			return return_format([],90007);
		}else{

			return return_format($baseinfo,0);
		}

	}
	
	/**
	 * 通过lessonid获取习题详细信息
	 * @Author lc
	 * @param $id id
	 * @return object
	 *
	 */
	public function getcourseCategoryListTwo($data){
		if(empty($data['category_level_one'])){
			return return_format('',90008);
		}
		$category = new Category();
		$where['fatherid'] = $data['category_level_one'];
		$baseinfo = $category->getAllCategoryList($where);

		if( empty($baseinfo) ){
			return return_format([],90007);
		}else{

			return return_format($baseinfo,0);
		}

	}

	/**
	 * @课程列表
	 * @Author lc
	 * @param $where 查询条件
	 * @param $pagenum 每页显示行数
	 * @param $limit 查询页数
	 **/
	public function getSchedulinglessonList($data, $limit) {
		$where = [] ;;
		isset($data['reviewstatus']) && $where['sl.reviewstatus'] = $data['reviewstatus'];
		$ca_one_findin = !empty($data['category_level_one'])?' FIND_IN_SET('.$data['category_level_one'].', replace(cu.categorystr, "-", ",")) ':'';
		$ca_two_findin = !empty($data['category_level_two'])?' FIND_IN_SET('.$data['category_level_two'].', replace(cu.categorystr, "-", ",")) ':'';
		
		if($data['pagenum'] > 0){
			$start = ($data['pagenum'] - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		//$where['e.delflag'] = 0;
		
		$SchedulingLessonmodel = new Schedulinglessoninfo;
		$field = 'sl.classid,sl.lessonid,s.curriculumname,cu.categorystr,s.gradename,le.periodname,te.nickname as teachername,s.realnum';
		$return = $SchedulingLessonmodel->getSchedulingLessonList($where, $field, $limitstr, $ca_one_findin, $ca_two_findin);
		$total  = $SchedulingLessonmodel->getSchedulingLessonListCount($where, $ca_one_findin, $ca_two_findin);

		if( empty($return) ){
			return return_format([],90007) ;
		}else{
			$category = new Category();
			$studenthomework = new Studenthomework();
			foreach($return as $k => $v){
				$return[$k]['categoryname'] = $category->getCategoryName(explode('-', $v['categorystr']));
				$return[$k]['submitedcount'] = $studenthomework->getSubmitedStudentCount($v['classid'], $v['lessonid']);
				$return[$k]['notreviewcount'] = $studenthomework->getNotReviewStudentCount($v['classid'], $v['lessonid']);
			}
			$result = [
				'data'=>$return,// 内容结果集
				'pageinfo'=>[
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$data['pagenum'] ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;
			return return_format($result,0);
		}
	}
	
	/**
	 * 获取列表
	 * @Author lc
	 * @param $name根据昵称查询    可选
	 * @param $pagenum 分页页码        可选
	 * @param $limit   取出多少条记录  必填
	 * @return array
	 */
	public function getStudentHomeworkList($classid,$lessonid,$nickname,$pagenum,$limit){
		if(empty($classid) || empty($lessonid)){
			return return_format('',90001);
		}
		$where = [];
		$where['sh.classid'] = $classid;
		$where['sh.lessonid'] = $lessonid;
		!empty($nickname) && $where['s.nickname'] = ['like','%'.$nickname.'%'];
		if($pagenum>0){
			$start = ($pagenum - 1 ) * $limit ;
			$limitstr = $start.','.$limit ;
		}else{
			$start = 0 ;
			$limitstr = $start.','.$limit ;
		}
		
		$SchedulingLessonmodel = new Schedulinglessoninfo;
		$Studenthomeworkmodel = new Studenthomework;
		$answeramodel = new Studenthomeworkanswer;
		$sl_reviewstatus = $SchedulingLessonmodel->getReviewStatus($classid, $lessonid);
		
		$field = 'sh.id,sh.classid,sh.lessonid,sh.studentid,s.nickname,sh.submittime,sh.issubmited,sh.reviewstatus';

		$return = $Studenthomeworkmodel->getStudentHomeworkList($where,$field,$limitstr);
		foreach($return as $k => $v){
			if(!empty($v['submittime'])){
				$return[$k]['submittime'] = date("Y-m-d H:i:s", $v['submittime']);
				if($v['reviewstatus'] == 1){
					$return[$k]['sumcore'] = $answeramodel->getStudentSumCore($v['classid'], $v['lessonid'], $v['studentid']);
				}
			}
		}
		
		$total  = $Studenthomeworkmodel->getStudentHomeworkListCount($where);

		if( empty($return) ){
			return return_format([],90006) ;
		}else{
			$result = [
				'lessionstatus' => $sl_reviewstatus['reviewstatus'],
				'data' => $return,
				'pageinfo' => [
					'pagesize'=>$limit ,// 每页多少条记录
					'pagenum' =>$pagenum ,//当前页码
					'total'   => $total // 符合条件总的记录数
				]
			] ;
			return return_format($result,0);
		}
	}
	
	/**
	 * 通过lessonid获取习题详细信息
	 * @Author lc
	 * @param $id id
	 * @return object
	 *
	 */
	public function previewExercise($classid, $lessonid){
		if(!is_intnum($classid)){
			return return_format('',90002,lang('90002'));
        }
		if(!is_intnum($lessonid)){
			return return_format('',90003,lang('90003'));
        }
        
        $data = $this->queryQuestions($classid,$lessonid);
		
		if( empty($data) ){
			return return_format([],70403);
		}else{
			return return_format($data,0,lang('success'));
		}	
	}
	
	/**
	 *  查看作业详情
	 * @Author lc
	 * @param $id id
	 * @return object
	 *
	 */
	public function viewHomeworkinfo($classid, $lessonid, $studentid, $status){
		if(!is_intnum($classid)){
            return return_format('',90002,lang('90002'));
        }
		
		if(!is_intnum($lessonid)){
            return return_format('',90003,lang('90003'));
        }
		
		if(!is_intnum($studentid)){
            return return_format('',90004,lang('90004'));
        }
        
        $data = $this->queryQuestions($classid,$lessonid,$studentid,$status);
		if( empty($data) ){
			return return_format([],70403);
		}else{
			return return_format($data,0,lang('success'));
		}
	}
	
	/**
     * 统一查询题库 或者答案
     * @Author lc
	 * @param  $classid
	 * @param  $lessonid  课时id
     * @param  $studentid  学生id
     * @param  $status 作业状态 0未提交1已完成2.已批阅
     * @return array
     *
     */
    public function queryQuestions($classid,$lessonid,$studentid=0,$status=0){

        //查询题库 用课程的课时id
		$lessonsmodel = new Lessons;
		$subjectmodel = new Exercisesubject;
        $field = 'periodid';
        $periodid = $lessonsmodel->getFieldName($lessonid,$field)['periodid'];
        $where = [
            'periodid' => $periodid,
			'delflag' => 0,
			'status' => 1
        ];
		$field = 'id as subjectid,type,courseid,periodid,name,imageurl,analysis,correctanswer,score';
        $list  = $subjectmodel->getSubjectList($where, $field);
		if(empty($list)) return '';
		
        //根据题型型分组
        $grouped = [];
        foreach ($list as $value) {
            $grouped[$value['type']][] = $value;
        }
       for($i=1;$i<5;$i++){
            if(empty($grouped[$i])){
                $grouped[$i] = [];
            }
       }
        $optionmodel  = new Exercisesubjectoptions;
        $answermodel = new Studenthomeworkanswer;

        foreach($grouped as $k=>$v){
            if(!empty($grouped[$k])){
                foreach($grouped[$k] as $key=>$value){
                    //获取选择题选项
                    if($k == 1 || $k==2){
                        $where = [
                            'subjectid' => $value['subjectid']
                        ];
                        $grouped[$k][$key]['options'] = $optionmodel->getSubjectOptions($where);
                    }
                    //如果已提交
                    if($status != 0){
						$where = [
							'classid' => $classid,
							'lessonid' => $lessonid,
							'subjectid' => $value['subjectid'],
							'studentid' => $studentid
						];
                        $grouped[$k][$key]['answers'] = $answermodel->getAnswers($where)['answer'];
                    
						//已批阅的需要加正确答案和评分
						if($status == 2){
							$grouped[$k][$key]['teacherscore'] = $answermodel->getAnswers($where)['score'];
							$grouped[$k][$key]['comment'] = $answermodel->getAnswers($where)['comment'];
						}
					}
                }
            }
        }
        
        $result['subject'] = $grouped;
        $result['classid'] = $classid;
		$result['lessonid'] = $lessonid;
		$result['periodname'] = $lessonsmodel->getFieldName($lessonid, 'periodname')['periodname'];
		if($studentid != 0){
			$studentmodel = new Studentinfo;
			$studentarr = $studentmodel->getStudentId($studentid, 'nickname');
			$result['studentname'] = $studentarr['nickname'];
		}
        return $result;
    }
}
	