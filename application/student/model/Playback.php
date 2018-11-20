<?php

namespace app\student\model;
/*
 * 课程回放 model
 * @ yr
*/
use think\Model;
use think\Db;

class Playback extends Model
{
    //
    protected $table = 'nm_playback';
    /**
     * [getVideourl 获取课程视频回放url]
     * @Author yr
     * @DateTime 2018-04-19T15:31:56+0800
     * @param    [int]        $toteachid [toteachid]
     * @return   [type]                  [description]
     */
    public function getVideourl($toteachid){
        $res = Db::table($this->table.' p')
            ->field('p.playpath,p.https_playpath,p.duration,p.serial,t.coursename,t.intime,t.type,t.timekey,t.lessonsid,t.teacherid')
            ->join('nm_toteachtime t','p.toteachid = t.id','LEFT')
            ->where('p.toteachid','eq',$toteachid)
            ->where('p.delflag','eq',1)
            ->order('p.starttime')
            ->select();
        return $res;
    }
}
