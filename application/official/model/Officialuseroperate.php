<?php
/**
*操作日志模型
**/
namespace app\official\model;
use think\Model;
use think\Db;
use think\Validate;
use login\Authorize;
use app\official\model\Officialuser;
class Officialuseroperate extends Model
{	
	protected $pk    = 'id';
	protected $table = 'nm_officialuseroperate';



    /**
     * [addOfficialUserOperateRecord 添加操作日志]
     * @Author zzq
     * @DateTime 2018-05-10
     * @param    [int]                 $id    [操作者的id]
     * @param    [string]                 $info    [操作内容]
     * @return   [array]                          [description]
     */
    public function addOfficialUserOperateRecord($id,$info){

    	$data = [];
    	$obj = new Officialuser();
    	$ret = $obj->getOfficialUserById($id);
    	if($ret){
            if($ret['data']){
                $data['username'] = $ret['data']['username'];
            }else{
                $data['username'] = '';
            }
    		
    	}else{
    		$data['username'] = '';
    	}
    	
    	$data['userid'] = $id;
    	$data['operateinfo'] = $info;
    	$data['address'] = '';
    	$data['ip'] = $_SERVER['REMOTE_ADDR'];
    	$data['browser'] = '';
    	$data['addtime'] = time();
    	// var_dump($data);
    	// die;
    	$res = $this->save($data);
    	if($res){
    		return true;
    	}else{
    		return false;
    	}
    }


    
    /**
     * [addOperateRecord //封装了管理员的操作日志]
     * @Author zzq
     * @DateTime 2018-05-10
     * @param    [string]                 $operateinfo    [操作内容]
     * @return   [array]                          [description]
     */
    public function addOperateRecord($operateinfo){
        
        //添加操作日志
        $ouid = session('ouid', '', 'official');
        if(!$ouid){
            $ouid = 0;//表示未知操作
        }
        return $this->addOfficialUserOperateRecord($ouid,$operateinfo);         
    }

    /**
     * [getUserOperateRecordListt 获取操作记录列表]
     * @Author zzq
     * @DateTime 
     * @param    
     * @return   [array]                              [description]
     */
    public function getUserOperateRecordList($where,$orderbys,$pagenum,$pernum){
        // var_dump($where);
        // die;
        $orderbys = $orderbys?$orderbys : 'id desc';
        $field = 'id,username,addtime,ip,operateinfo';
        $pagenum = $pagenum?$pagenum:$this->pagenum;
        try{
            $lists = Db::table($this->table)->page($pagenum,$pernum)->order($orderbys)->where($where)->field($field)->select();
            // var_dump($this->getLastSql());
            // die;
            if(!$lists){
                $lists = [];
            }
            if($lists){
                foreach($lists as $k => $v){
                    $lists[$k]['addtime'] = Date('Y-m-d H:i:s',$lists[$k]['addtime']);
                }
            }
            $count = $this->getUserOperateRecordListCount($where);
            $pagenum = ceil($count/$pernum);
            $ret = [];
            $ret['lists'] = $lists;
            $ret['count'] = $count;
            $ret['pagenum'] = $pagenum;
            $ret['pernum'] = $pernum;
            //获取页码数
            return return_format($ret,0,lang('success')) ;
        }catch(\Excpetion $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }

        return $ret;
    }

    /**
     * [getUserOperateRecordListCount 获取操作记录数目]
     * @Author
     * @DateTime 2018-05-03
     * @param    [array]            $where    [筛选条件]
     * @return   [int]              $count        [查询的数目]
     */
    public function getUserOperateRecordListCount($where){

        try{
            $count = Db::table($this->table)->where($where)->count();
        }catch(\Excpetion $e){
            $count = 0;
        }

        return $count;
    }

    /**
     * [delUserOperateRecord 删除操作记录]
     * @Author
     * @DateTime 2018-05-03
     * @param    [$ids]            string    [id连接的字符串]
     * @return   []           
     */
    public function delUserOperateRecord($ids){
        $where = [];
        $arr = explode(',', $ids);
        if(count($arr) == 1){
            $where['id'] = ['EQ',$ids];
        }else{
            $where['id'] = ['IN',$arr];
        }
        try{
            $res = $this->where($where)->delete();
            if($res){
                return return_format('',0,lang('success')) ;
            }else{
                return return_format('',50029,lang('50029')) ;
            }
            
        }catch(\Exception $e){
            return return_format($e->getMessage(),50004,lang('50004')) ;
        }
    }
}