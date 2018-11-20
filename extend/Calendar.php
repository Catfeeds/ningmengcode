<?php
class Calendar {
	protected $year  ;
	protected $month ;
	protected $day   ;//当前日
	public    $starttime ;
	public    $endtime ;
	public    $array ;
	/**
	 * [__construct 初始化日期]
	 * @Author wyx
	 * @DateTime 2018-04-24T20:02:27+0800
	 * @param    integer                  $year  [需要设置的年]
	 * @param    integer                  $month [需要设置的月]
	 * @param    integer                  $day   [需要设置的日]
	 */
	public function __construct($year=0,$month=0,$day=0){
        !$year  ? $this->year  = date('Y'):$this->year  = $year;
        !$month ? $this->month = date('m'):$this->month = $month;
        !$day   ? $this->day   = date('d'):$this->day   = $day;//当前日
        //计算开始时间
        $this->starttime = $this->getleftday();
        //计算结束时间
        $this->endtime = $this->getrightday();
        //设置数组
        $this->array = $this->construct_array() ;

    }
    /**
     * [getrightday 指定月的当周的最右日期]
     * @Author
     * @DateTime 2018-04-24T20:05:53+0800
     * @return   [type]                   [description]
     */
    protected function getrightday(){
    	$year  = $this->year;
    	$month = $this->month;
    	$day   = $this->day;
        //找到最后一天 以及周几 并填满到周日
        $currenttime = mktime(23,59,59,$month,$day,$year) ;
        for(;;){
            $currenttime+=86400 ;
            $wmonth = date('m',$currenttime);
            if($month!=$wmonth){
                static $w = 0 ;
                if(date('w',$currenttime)==1 && $w == 0){//最后一天是周日的情况
                    return $currenttime - 86400 ;
                }else{
                    $cweek = date('w',$currenttime);
                    if($cweek==0){//变月后的第一个星期日
                        return $currenttime ;
                    }
                }
                $w++ ;
            }

        }
    }
    /**
     * [getleftday 指定月的当周的最左日期]
     * @Author wyx
     * @DateTime 2018-04-24T20:06:59+0800
     * @return   [type]                   [description]
     */
    protected function getleftday(){
    	$year  = $this->year;
    	$month = $this->month;
    	$day   = $this->day;
        //找到最后一天 以及周几 并填满到周日
        $currenttime = mktime(0,0,0,$month,$day,$year) ;
        for(;;){
            $currenttime-=86400 ;
            $wmonth = date('m',$currenttime);
            if($month!=$wmonth){
                static $w = 0 ;
                if(date('w',$currenttime)==0 && $w == 0){//变月了当天是周日 需要退出
                    return $currenttime+=86400 ;
                }else{
                    $cweek = date('w',$currenttime);
                    if($cweek==1){//变月后的第一个 周一
                        return $currenttime ;
                    }
                }
                $w++ ;
            }
        }
    }
    /**
     * [construct_array 格式化为二维数组]
     * @Author wyx
     * @DateTime 2018-04-24T20:18:52+0800
     * @return   [type]                   [description]
     */
    protected function construct_array(){
    	$starttime = $this->starttime ;
    	$endtime   = $this->endtime ;

        $arr = [] ;
        $floor = 0 ;
        for($i = $starttime;$i<=$endtime;){
            static $num = 1 ;
            if($num==7){
                $arr[$floor][] = date('Y-m-d',$i) ;

                $floor++ ;//分组
                $num=0 ;//一维下标
            }else{
                $arr[$floor][] = date('Y-m-d',$i) ;
            }

            $i+=86400 ;
            $num++;
        }
        return $arr ;
    }
}



?>