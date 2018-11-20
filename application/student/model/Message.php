<?php
namespace app\student\model;
use think\Model;
use think\Db;
use think\Validate;
/*
 * 消息表 message
 * @ yr
*/
class Message extends Model{
    protected $table = 'nm_message';
    //自定义初始化
    /**
     * [getMessageList  获取学生列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    userid  学生id
     * @return   array
     */
    public function getMessageList($userid,$limitstr,$type){
		if($type == 0){
			$typewhere = '1=1';
		}else if($type == 1){
			$typewhere = 'type != 11';
		}else if($type == 2){
			$typewhere = 'type = 11';
		}
        $lists =Db::table($this->table)
            ->field('id,type,title,content,addtime,istoview')
            ->where('userid','eq',$userid)
            ->where('delflag','eq',1)
            ->where('usertype','eq',3)
			->where($typewhere)
            ->order('addtime desc')
            ->limit($limitstr)
            ->select();
        return  $lists;
    }
    /**
     * [getMessageCount 获取学生列表]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param    userid  学生id
     * @return   array
     */
    public function getMessageCount($userid,$type){
		if($type == 0){
			$typewhere = '1=1';
		}else if($type == 1){
			$typewhere = 'type != 11';
		}else if($type == 2){
			$typewhere = 'type = 11';
		}
        $lists = Db::table($this->table)
            ->where('userid','eq',$userid)
            ->where('delflag','eq',1)
            ->where('usertype','eq',3)
			->where($typewhere)
            ->count();
        return  $lists;
    }
    /**
     * [updateMsgStatus 修改查看状态]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param
     * @return   array
     */
    public function updateMsgStatus($where,$data){

        $result = $this->allowField(true)->where($where)->update($data);
        return $result;
    }
    /**
     * [saveAllMsg 批量修改]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param
     * @return   array
     */
    public function saveAllMsg($data){
        $result = $this->saveAll($data);
        return $result;
    }
    /**
     * [ getNewMsg 查询出最新的5条消息]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param
     * @return   array
     */
    public function getNewMsg($studentid){
        $result = Db::table($this->table)
            ->field('content,id,type,addtime,userid,title,istoview')
            ->where('delflag','eq',1)
            ->where('istoview','eq',0)
            ->where('userid','eq',$studentid)
            ->where('usertype','eq',3)
            ->order('addtime desc')
            ->select();
        return $result;
    }
    /**
     * [ getNewMsg 查询出最新的5条消息]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param
     * @return   array
     */
    public function getNewMsgCount($studentid){
        $result = Db::table($this->table)
            ->where('delflag','eq',1)
            ->where('istoview','eq',0)
            ->where('userid','eq',$studentid)
            ->where('usertype','eq',3)
            ->count();
        return $result;
    }
    /**
     * [ getMsgCount 根据条件查询消息的数量]
     * @Author yr
     * @DateTime 2018-04-21T13:58:56+0800
     * @param
     * @return   array
     */
    public function getMsgCount($where){
        $result = Db::table($this->table)
            ->where($where)
            ->count();
        return $result;
    }
}







