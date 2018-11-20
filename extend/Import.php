<?php
use think\Controller;
use think\Request;
use think\Session;
use app\admin\business\SubjectManage;
use app\admin\business\KnowledgeManage;
use app\admin\business\TeacherManage;
use app\admin\business\ExampleManage;
//该类用于所有数据导入
class Import
{
	/**
	 * 文件导入调用
	 *
	 * @return \think\Response
	 *
	 */

	public function importDatas($files,$importtype){
		require 'spreadsheet/vendor/autoload.php';
		if (empty($files['files'])) {
			return return_format([],29001,lang('29001'));
		}
		$file = $files['files']['uploadFile'];
		$filename = $file['tmp_name'];
		if (!is_uploaded_file($filename)) {
			return return_format('',29002,lang('29002'));
		}

		if (empty($files)) {
			return return_format('',29003,lang('29003'));
		}
		
		if (empty($importtype)) {
			return return_format('',29003,lang('29003'));
		}

		$temp = explode(".", $file["name"]);

		$allowedExts = array('xls','xlsx');
		
		
		$extension = end($temp);
		if (in_array($extension,$allowedExts)) {
			$tempfilesize = $file['size']/1024;
			if ($tempfilesize<=102400) {  //文件不能大于100M
				if ($file['error']>0) {
					return return_format([],29004,lang('29004'));
				}else{
					
					$addtime = date("Ymd", time());
					$testdir = "./upload/" . $addtime . '/';
					//$testdir = dirname(__DIR__) . '/public/upload/' . $addtime . '/';
					if(!file_exists($testdir))  mkdir($testdir,0777);
					
					$imgname = date("Y").date("m").date("d").date("H").date("i").date("s").rand(1000, 9999).".".$extension;
					
					$destfile = $testdir . $imgname;
					if(!file_exists($destfile)) {
						$info = move_uploaded_file($file['tmp_name'], $destfile);
					}
					if(!$info){
						return return_format([],70410,lang('70410'));
					}
					$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($destfile);
					//read excel data and store it into an array
					$xls_data = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
					unset($xls_data[1]);
					$xls_data = array_values($xls_data);
					is_file($destfile) && unlink($destfile);
					if(empty($xls_data)){
						return return_format([],70413,lang('70413'));
					}
					
					if($importtype == 1) { //导入题库
						$objManage = new SubjectManage;
						$r = $objManage->ImportSubjects($xls_data);
						if ($r) {
							return return_format(['num'=>$r],0,lang('success'));
						}else{
							return return_format([],70411,lang('70411'));
						}
					}elseif($importtype == 2) { //导入知识
						$objManage = new KnowledgeManage;
						$r = $objManage->ImportKnowledges($xls_data);
						if ($r) {
							return return_format(['num'=>$r],0,lang('success'));
						}else{
							return return_format([],70210,lang('70210'));
						}
					}elseif($importtype == 3) { //导入老师
						$objManage = new TeacherManage;
						$r = $objManage->ImportTeachers($xls_data);
						if ($r) {
							return return_format(['num'=>$r],0,lang('success'));
						}else{
							return return_format([],40502,lang('40502'));
						}
					}elseif($importtype == 4) { //导入例句
						$objManage = new ExampleManage;
						$r = $objManage->ImportExamples($xls_data);
						if ($r) {
							return return_format(['num'=>$r],0,lang('success'));
						}else{
							return return_format([],90027,lang('90027'));
						}
					}
				}
			}else{
				return return_format([],29009,lang('29009'));
			}
		}else{
			return return_format([],29010,lang('29010'));
		}
	}
}
