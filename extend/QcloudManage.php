<?php
require('include.php');
use QCloud\Cos\Api;
date_default_timezone_set('PRC');
class QcloudManage{
	//bucketname
	private $bucket = 'cat';
	//uploadlocalpath
	private $src    = './hello.txt';
	//cospath
	private $dst    = '/testfolder/hello.txt';
	//downloadlocalpath
	private $dst2   = 'hello2.txt';
	//cosfolderpath
	private $folder = '/testfolder';
	
	//config your information
	private $config = [] ;
	// cos  object
	private $cosobj = '' ;
	/**
	 *	初始化数据
	 *
	 */
	public function __construct(){
        $temp = config('tcos.webconfig') ;
        $this->bucket = $temp['bucket'] ;
        $this->config['app_id']     = $temp['app_id'];
        $this->config['secret_id']  = $temp['secret_id'];
        $this->config['secret_key'] = $temp['secret_key'];
        $this->config['region']     = $temp['region'];
        $this->config['timeout']    = $temp['timeout'];

        $this->cosobj = new Api($this->config);
	}
	/**
	 *	创建文件夹
	 *	@param $folder 要创建的目录名字 dirname  创建子目录写法 dirname/childdirname
	 *
	 */
	public function createFolder( $folder,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Create folder in bucket.
		// var_dump($bucket);exit();
		$ret = $this->cosobj->createFolder($bucket, $folder);
		return $ret;
	}
	/**
	 *	上传文件
	 *	@param  $src 本地文件路径 eg: filename.txt   upload/image.png
	 *	@param  $dst cos 上保存位置及名称 eg: course/2/image.png
	 *
	 */
	public function upload($src, $dst,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Upload file into bucket.
		$ret = $this->cosobj->upload($bucket, $src, $dst);
		return $ret;
	}
	/**
	 *	下载文件
	 *	@param $dst  cos上的文件 eg: course/2/image.png
	 *	@param $dst2  下载到本地的路径 eg：upload/new.png 
	 *
	 */
	public function download($dst, $dst2,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Download file
		$ret = $this->cosobj->download($bucket, $dst, $dst2);
		return $ret;
	}
	/**
	 *	遍历指定的目录下文件
	 *	@param $folder  要遍历的目录 eg: '' 查看根目录  'course' 查看course 下的文件及目录，仅返回一层
	 *
	 */
	public function listFolder($folder,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// List folder.
		$ret = $this->cosobj->listFolder($bucket, $folder);
		return $ret;
	}
	/**
	 *	更新文件夹信息 设置访问权限
	 *	@param $folder  要更新的目录  eg: '' 查看根目录  'course' 查看course 
	 *	@param $bizAttr  要更新的属性
	 *	@param $bucket  
	 *
	 *
	 */
	public function updateFolder($folder, $bizAttr,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Update folder information.
		$bizAttr = "";
		$ret = $this->cosobj->updateFolder($bucket, $folder, $bizAttr);
		return $ret;
		
	}
	/**
	 *	更新文件的信息 
	 *	@param $bizAttr
	 *	@param $authority
	 *	@param $customerHeaders  设置可读属性
	 *
	 *
	 *
	 */
	public function updateFile($dst, $bizAttr,$authority, $customerHeaders,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Update file information.
		// $bizAttr = '';
		// $authority = 'eWPrivateRPublic';
		// $customerHeaders = array(
		//     'x-cos-acl' => 'public-read',
		//	   'Content-Type' => 'application/jpg',
		// );
		$ret = $this->cosobj->update($bucket, $dst, $bizAttr,$authority, $customerHeaders);
		return $ret;
	}
	/**
	 *	获取文件夹属性信息 
	 *	@param $folder cos上的目录  eg: '' 查看根目录  'course' 查看course 
	 *	
	 */
	public function statFolder($folder,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Stat folder.
		$ret = $this->cosobj->statFolder($bucket, $folder);
		return $ret;
	}
	/**
	 *	获取文件属性信息 
	 *	@param $dst cos 上的文件路径 eg: course/2/image.png
	 *	
	 */
	public function stat($dst,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Stat file.
		$ret = $this->cosobj->stat($bucket, $dst);
		return $ret;
	}
	/**
	 *	复制文件在cos上 
	 *	@param $dst cos上的文件路径
	 *	@param $todst cos上的文件路径
	 *	
	 */
	public function copyFile($dst, $todst,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Copy file.
		$ret = $this->cosobj->copyFile($bucket, $dst, $dst . '_copy');
		return $ret;
	}
	/**
	 *	cos上移动文件
	 *	@param $dst cos上的文件路径
	 *	@param $todst cos上的文件路径
	 *	
	 */
	public function moveFile($dst, $todst,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Move file.
		$ret = $this->cosobj->moveFile($bucket, $dst, $dst . '_move');
		return $ret;
	}
	/**
	 *	cos删除文件
	 *	@param $dst cos上的文件路径 云上文件地址 dirname/filename.png
	 *
	 *	
	 */
	public function delFile($dst,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Delete file.
		$ret = $this->cosobj->delFile($bucket, $dst );
		return $ret;
	}
	/**
	 *	cos删除文件夹
	 *	@param $folder cos上的文件夹 云上文件路径
	 *	
	 *	
	 */
	public function delFolder($folder,$bucket=''){
		if(empty($bucket)) $bucket = $this->bucket;
		// Delete folder.
		$ret = $this->cosobj->delFolder($bucket, $folder);
		return $ret;
	}


}

		
		
