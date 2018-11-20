<?php

namespace app\appteacher\model;

use think\Model;
use think\Db;
class Organ extends Model
{
	protected $table= 'nm_organ';
    //
    public function getOrganid($domain){
    	return Db::table($this->table)->where('domain','eq',$domain)->where('auditstatus','eq',3)->field('id,imageurl')->find();
       // print_r(Db::table($this->table)->getlastsql());
			//return $domain;
    }
		public function getOrganname($organid){
			return Db::table($this->table)->where('id','eq',$organid)->field('organname')->find();
		}
}
