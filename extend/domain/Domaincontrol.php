<?php
/**
 *	控制项目的访问，如果域名没有在nm_organ 表中存在通过的，禁止进入项目，直接跳转到 官网
 *
 */
namespace domain;
use think\Db;
use think\Cache;
class Domaincontrol {
	/**
	 *	初始化
	 *
	 *
	 *
	 */
	public function __construct(){
		//
	}
	/**
	 *	监测 域名
	 *
	 *
	 */
	public function runCheck(){
		$devidedomain = $this->getVisitDomain();
		//获取 域名分析 后的 结果 boolean 直接返回 ，字符串继续 从缓存中 检验
		if(is_bool($devidedomain)){
			return $devidedomain ;
		}else{// 返回为二级域名 或者 完整域名
			//检查缓存
			if($devidedomain == 'www') {// 如果是官网直接放行
				if( $this->checkURI() ){ // 排除 ‘/’
					return true ; // 直接方行
				}else{
					return rtrim(config('param.server_url'),'/\\').'/officialweb';
				}
			} 
			// 查询 域名是否注册 
			$flag = $this->checkOrganInCache($devidedomain);

			if($flag){ // 存在 如果 请求路径 为空 自动匹配
				if( $this->checkURI() ){ // 排除 ‘/’
					return true ; // 直接方行
				}else{
					return '/organweb';
				}
			}else{
				return $flag;
			}
		}
	}
	/**
	 *	检测是否有资源 请求，有返回 true  没有返回 false
	 *
	 *
	 */
	public function checkURI(){
		$length = strlen($_SERVER['REQUEST_URI']) ;
		if($length>1){ // 排除 ‘/’
			return true ; // 有资源请求
		}else{
			return false; // 域名访问
		}
	}
	/**
	 *	检查二级域名是否存在于 缓存中
	 *	存在就返回true
	 *	否则就返回false
	 *	
	 * 	存入键 domaincontrol-domain  格式
	 */
	public function checkOrganInCache($domain){
		$flag = Cache::has('domaincontrol-'.$domain);
		if($flag){//如果 存在 直接 返回 true 
			return $flag ;
		}else{	//如果 不存在 查询数据中 是否存在  
			return $this->checkOrganInTable($domain) ;
		}
	}
	/**
	 *	检查域名是否存在于数据表中
	 *	存在返回   true
	 *	不存在返回 false
	 *	由 checkOrganInCache() 调用
	 *
	 */
	private function checkOrganInTable($domain){

		$search = Db::table('nm_organ')->where(['domain'=>$domain,'auditstatus'=>3])->field('id')->find() ;
		if( empty($search) ){// 为空 则 返回 false
			return false ;
		}else{// 如果存在 则 缓存 并 返回 true
			$this->insertOrganToCache($domain) ;
			return true ;
		}
		
	}
	/**
	 *	将查询到的结果，存如缓存中 由 checkOrganInTable() 调用
	 *	无需返回值
	 *	在机构 状态 开关的地方 需要 对缓存处理 
	 */
	private function insertOrganToCache($domain){
		Cache::set('domaincontrol-'.$domain, 1 , 86400*2) ;
	}
	/**
	 *	域名拆分，如果是二级域名 返回二级，如果是独立整域名返回完整域名
	 *	为了便于切换， 将配置放入 param 文件中
	 *	config('param.http_name').$_SERVER['HTTP_HOST']
	 */
	private function getVisitDomain(){
		// 如果域名包含  extra/param 中的maindomain 的部分 则返回二级 ，此种情况 要求域名 拆分后长度为3 ，即禁止出现三级域 
		$maindomain = config('param.maindomain') ;
		$userdomain = $_SERVER['HTTP_HOST'] ;
		if( strpos($userdomain,$maindomain) === false ){// 如果不存在 返回完整域名 用来匹配是否注册
			return $userdomain;
		}else{// 
			$temparr = explode('.',$userdomain) ;
			$length  = count($temparr) ;
			if( $length == 2 ){// 直接一级域 访问的情况
				return true ;
			}elseif($length == 3){// 使用 二级域名 访问的 情况
				return $temparr[0] ;
			}else{// 使用 三级域 或者更多的情况 
				return false ;
			}
		}
	}
}