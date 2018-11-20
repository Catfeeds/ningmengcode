<?php
use think\Controller;
use think\Request;
use think\Session;
use app\teacher\business\UploadFiles;
use app\admin\business\Docking;
use app\admin\business\KnowledgeSetupManage;
//该类用于所有文件上传
class Upload
{
	/**
	 * 文件上传调用，上传图片等，腾讯云
	 *
	 * @return \think\Response
	 *
	 */

	public function getUploadFiles($files,$filetype,$organid){
		if (empty($files['files'])) {
			return return_format([],29001,lang('29001'));
		}
		//将三维数组转换成一维数组
		$file = $files['files']['uploadFile'];
		$filename = $file['tmp_name'];//获取用户刚刚上传的文件
		//判断是否是一个上传文件
		if (!is_uploaded_file($filename)) {
			// 如果该文件不是一个上传的文件
			return return_format('',29002,lang('29002'));
		}

		if (empty($files)) {
			// 如果传入数据为空
			return return_format('',29003,lang('29003'));
		}

		// 允许上传的文件后缀
			$temp = explode(".", $file["name"]);
		if($filetype==1||$filetype==2 || $filetype == 4 || $filetype == 5||$filetype == 6){
			$allowedExts = array('xls','xlsx','ppt','pptx','doc', 'docx','txt','pdf','jpg','gif','jpeg','png','bmp', 'mp3','mp4','rmvb','avi','mov','zip','rar','mpeg','mpg','3gp','flv','swf','wmv','vob','mkv','webm','ogg');//rar和rmvb的mime类型相同
			$typearr = array('image/gif' ,'image/jpeg','image/jpg','image/pjpeg','image/x-png','image/png','image/bmp','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/vnd.ms-powerpoint','application/vnd.openxmlformats-officedocument.presentationml.presentation','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','text/plain','application/pdf','audio/mp3','video/mp4','video/vnd.rn-realvideo','video/avi','video/quicktime','application/x-zip-compressed','application/octet-stream',
                'video/mpeg','video/3gpp','application/x-shockwave-flash','video/x-ms-wmv','video/x-matroska','video/webm','audio/ogg');
		}else{
			$allowedExts = array('mp4','rmvb','avi','mov','mpeg','mpg','3gp','flv','swf','wmv','vob','mkv','webm','ogg'); //'mp3',
			$typearr = array('video/mp4','application/octet-stream','video/avi','video/quicktime','video/mpeg','video/3gpp','application/x-shockwave-flash','video/x-ms-wmv','video/x-matroska','video/webm','audio/ogg'); //'audio/mp3',
		}
		//echo $file['size'];
		$extension = end($temp);//获取文件后缀名
		//判断文件扩展名和上传文件的内容mime类型
		if (in_array($extension,$allowedExts) && in_array($file['type'],$typearr)) {
			//判断文件大小,是否大于100M
			$tempfilesize = $file['size']/1024;
			if ($tempfilesize<=102400) {
				//判断
				if ($file['error']>0) {
					return return_format([],29004,lang('29004'));
				}else{
					$all['type'] = $file['type'];//文件类型
					$all['size'] = $file['size']/1024;//单位kb
					$all['tmppath'] = $file['tmp_name'];//文件临时存储位置
					//如果没有upload目录，则你需要创建它，upload目录权限未777

					$addtime=date("Ymd",time());
					$testdir = "./upload/".$addtime.'/';
					//把文件名格式化，便于存储
					$arr = explode(".", $file["name"]);

					//不允许出现两个“.”
					//if(count($arr)>2) return return_format([],29005,lang('29005'));
					if($filetype==2 || $filetype==3){
						// 拓课文件
						$middlename = $file["name"];
					}else{
						// 腾讯云oss 文件名生成
						$counts = count($arr);
						$imgname = date("Y").date("m").date("d").date("H").date("i").date("s").rand(1000, 9999).".".$arr[$counts-1];
						$middlename = $imgname;
					}

					if(!file_exists($testdir))  mkdir($testdir,0777);
					if(!file_exists($testdir.$middlename)) {
						//如果upload目录不存在该文件则将上传文件收到upload目录下
						move_uploaded_file($file['tmp_name'], $testdir.$middlename);
					}

					// 生成path路径
					$all['path'] = $testdir.$middlename;
					$all['name'] = $middlename;
					$file['name'] = $middlename;

					//判断$fieltype 如果$filetype==1,则上传u云
					//如果$filetype == 2，则上传拓客云
					if($filetype == 2){
						$files['allpathnode'] = [9,1,1];
					}else{
						$files['allpathnode'] = is_array($files['allpathnode'])?$files['allpathnode']:explode(',', $files['allpathnode']);
						$files['allpathnode'][1] = $organid;
					}

					$file['dst'] = $this->getFileUrl($files['allpathnode'],$file['name']);
					// 图片裁剪
					if(isset($files['tailoringData'])&&$files['tailoringData']){
						$this->tailoringImg($testdir,$middlename,$files['tailoringData'],$file['type'],$file['dst']);
					}
					//上传文件
					$teccent = self::uploaducloud($file['type'],$all['path'],$file['name'],'');
//					print_r($teccent);
//					exit();
					//判断文件是否上传U云是否成功
					if ($teccent['code'] == 0) {
						//$filetype 1普通图片文件上传 2 拓课普通文件上传 3 录制件上传
						if($filetype == 3 || $filetype==2){
							$dock = new Docking;
							$files['teacherid'] = isset($files['teacherid'])?$files['teacherid']:0;
							// 用途 1 录制件 2 普通课件
							$usetype = $filetype==2?2:1;
							$Docking = $dock->uploadFiles($all['path'],$all['name'],$files['fatherid'],$files['teacherid'],$usetype,$teccent['data']);
							if (!empty($Docking)) {
								is_file($all['path']) && unlink($all['path']);
								return return_format($Docking,0,lang('success'));
							}else{
								return return_format([],29008,lang('29008'));
							}
						}elseif($filetype == 4) { //上传知识配置背景图
							$knowledgeSetupManage = new KnowledgeSetupManage;
							$r = $knowledgeSetupManage->uploadToFiles($teccent['data']);
							if (!empty($r)) {
								is_file($all['path']) && unlink($all['path']);
								return return_format($teccent['data'],0,lang('success'));
							}else{
								return return_format([],29008,lang('29008'));
							}
						}elseif($filetype == 5) { //上传知识配置二维码
							$knowledgeSetupManage = new KnowledgeSetupManage;
							$r = $knowledgeSetupManage->updateToFiles($teccent['data']);
							if (!empty($r)) {
								is_file($all['path']) && unlink($all['path']);
								return return_format($teccent['data'],0,lang('success'));
							}else{
								return return_format([],29008,lang('29008'));
							}	
						}elseif($filetype == 6){
                            //上传成功则删除本地文件
                            is_file($all['path']) && unlink($all['path']);
                            $return = [];
                            $return['filename'] = $files['files']['uploadFile']['name'];
                            $return['imgurl'] = $teccent['data'];
                            return return_format($return,0,lang('success'));
                        }
                        else{
							//上传成功则删除本地文件
							is_file($all['path']) && unlink($all['path']);
							return return_format($teccent['data'],0,lang('success'));
						}
					}else{
						return $teccent;
					}
				}
			}else{
				return return_format([],29009,lang('29009'));
			}
		}else{
			return return_format([],29010,lang('29010'));
		}
	}


	function getFileUrl($files,$name){
		//判断当前文件路径
		$receive = self::checkpath($files);
		//合成最终文件所在路径
		$dstfolder = $receive['purposename'].'/'.$receive['plane'];
		return $dstfolder."/".$name;
	}


	//判断参数路径
	protected function checkpath($allpathnode){
		//plane平台 organid 机构id purposename 上传文件用途
		switch ($allpathnode[2]) {
			case 1:
				$plane = 'official';
				break;
			case 2:
				$plane = 'organ';
				break;
			case 3:
				$plane = 'teacher';
				break;
			case 4:
				$plane = 'student';
				break;
			case 5:
				$plane = 'appteacher';
				break;
			case 6:
				$plane = 'appstudent';
				break;
		}
		//purposename 文件夹
		switch ($allpathnode[0]) {
			case 1:
				$purposename = 'headimg';//头像
				break;
			case 2:
				$purposename = 'advertisement';//广告
				break;
			case 3:
				$purposename = 'logo';
				break;
			case 4:
				$purposename = 'frontphoto';
				break;
			case 5:
				$purposename = 'backphoto';
				break;
			case 6:
				$purposename = 'organphoto';
				break;
			case 7:
				$purposename = 'recommphoto';
				break;
            case 8:
                $purposename = 'recordingparts';
				break;
			case 9:
				$purposename = 'courseware';
				break;
			case 10:
				$purposename = 'qrcode';//二维码
				break;
			case 11:
				$purposename = 'exercisesubject';//题目图片
				break;
		}
		if (empty($plane)||empty($purposename)) {
			return return_format([],29011,lang('29011'));
		}else{
			return ['plane'=>$plane,'purposename'=>$purposename];
		}
	}

	//src 本地路径，dst 服务器路径
	protected function uploaducloud($contenttype,$src,$dst,$bucket=''){
		$ucloud = new \UcloudManage;
		$cos = $ucloud->uploadpost($src,$dst,$bucket);

		//$bizAttr = '';
		//$authority = 'eWPrivateRPublic';
		//$customerHeaders = array('x-cos-acl' => 'public-read','Content-Type'=>$contenttype);
		//$ucloud->updateFile($dst,$bizAttr,$authority,$customerHeaders);//更新文件控制
		return $cos;
	}




	// 上传腾讯云 裁剪


	/**
	 * @param $file				路径前缀
	 * @param $name				文件名称
	 * @param $tailoringData	对应的裁剪规则 'width,heigth|400,300|400,400'
	 * @param $filetype			文件类型
	 * @param $dstfolder		上传到腾讯云oss路径
	 */
	public function tailoringImg($file,$name,$tailoringData = '',$filetype,$dstfolder){

		$oldfile = $file.'/'.$name;
		$image = getimagesize($oldfile);
		$lodwidth = intval($image[0]);
		$lodheight = intval($image[1]);

		// '3000,2000|400,300|400,400'
//		$tailoringData = '3000,2000|400,300|400,400';

		list($imgname,$imgtype) = explode('.',$name);

		require_once('../Vendor/phpthumb/PhpThumbFactory.class.php');

		if($tailoringData){
			// 裁剪尺寸
			$indata = explode('|',$tailoringData);
			foreach ($indata as $k => $v){
				$imgArr = explode(',',$v);
				list($width,$height) = $imgArr;

				// 等比例中心裁剪
				if($lodwidth>=$width&&$lodheight>=$height){
					//第一种情况 剪切宽高都小于原图
					//直接剪切
				}elseif($lodwidth>=$width&&$lodheight<=$height){
					//第二种情况
					$width = intval(($lodheight*$width)/$height);
					$height = $lodheight;
				}elseif($lodwidth<=$width&&$lodheight>=$height){
					//第三种情况
					$height = intval(($lodwidth*$height)/$width);
					$width = $lodwidth;
				}else{
					//宽和高都大于原图
					//获取缩放比例
					$widthone = intval(($lodheight*$width)/$height);
					$widthtwo = $widthone>$lodwidth?$lodwidth:$widthone;
					$heightone = intval(($lodwidth*$height)/$width);
					$height = $heightone>$lodheight?$lodheight:$heightone;
					$width = $widthtwo;
				}

				// 裁剪 生成文件名
				$inImgName = $imgname.'_'.$imgArr[0].'_'.$imgArr[1].'.'.$imgtype;
				$newpath = $file.'/'.$inImgName;

				$thumb = PhpThumbFactory::create($oldfile);
				$thumb->adaptiveResize($width, $height);
				$thumb->save($newpath);
				if(file_exists($file.'/'.$inImgName)){
					// 裁剪出来的图片存在去上传
					$uplodeStatus = self::uploaducloud($filetype,$newpath,$dstfolder,'');
					if (!empty($uplodeStatus['data'])) is_file($newpath) && unlink($newpath);
				}
			}
		}
	}
    /**
     * 保存提交过来的图片
     *@param $savepath  string  保存图片的路径 不是全路径
     *@param $img       stiring base64图片实体，含base64图片头
     *@return           array
     *@author jorsh20151106
     **/
    public function SaveFormUpload($savepath, $img, $types=array()){
        $addtime = date('Ymd',time());
        $basedir = '/upload/'.'base64'.$addtime.'/'.$savepath;
        //$fullpath = dirname(THINK_PATH).$basedir;
        $fullpath = '.'.$basedir;
        if(!is_dir($fullpath)){
            //判断当前路径是否为空，否则创建一个文件夹
            mkdir($fullpath,0777,true);
        }
        $types = empty($types)? array('jpg', 'gif', 'png', 'jpeg'):$types;
        $img = str_replace(array('_','-'), array('/','+'), $img);
        $b64img = substr($img, 0,14000000);//按10MB大小
        if(preg_match('/^(data:\s*image\/(\w+);base64,)/', $b64img, $matches)){
            $type = $matches[2];
            //判断图片类型
            if(!in_array($type, $types)){
                return return_format([],29010,lang('29010'));
            }
            $imglen = strlen($b64img);
            $filesize = ($imglen-($imglen/8)*2);
            //判断文件大小
            if(number_format(($filesize/1024),2)>10*1024){
                return return_format('',2,'文件过大');
            }
            //echo '原图大小'.number_format(($filesize/1024),2).'KB';
            $img = str_replace($matches[1], '', $img);
            $img = base64_decode($img);
            //$photo = '/'.md5(date('YmdHis').rand(1000, 9999)).'.'.$type;//照片全路径
            $photo = '/'.date("Y").date("m").date("d").date("H").date("i").date("s").rand(1000, 9999).".".$type;//照片全路径
            file_put_contents($fullpath.$photo, $img);//字符串写入文件中
            if(file_exists($fullpath.$photo)){
                //如果base64生成的图片存储成功，则上传u云
                $cos = self::uploaducloud($type,$fullpath.$photo,$photo,'');
                //上传成功则删除本地文件
                is_file($fullpath.$photo) && unlink($fullpath.$photo);
                return return_format($cos['data'],0,lang('success'));
            }
            return return_format('',0,lang('success'));
        }
        return return_format('',20516,'请选择要上传的图片');
        //return array('error'=>2,'msg'=>'请选择要上传的图片','url'=>'');
    }
}
