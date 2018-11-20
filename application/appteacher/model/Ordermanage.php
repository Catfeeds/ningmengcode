<?php

namespace app\appteacher\model;

use think\Model;
use think\Db;
class Ordermanage extends Model
{
	protected $table = 'nm_ordermanage';

    /*
	 * [getOrderList 获取订单列表]
	 * @Author wyx
	 * @DateTime 2018-04-21T09:54:41+0800
	 * @return   [array]        [返回订单列表数组]
	 */
	public function getOrderList($where,$limitstr){
		return Db::table($this->table)
            ->where($where)
            ->field('id,ordernum,curriculumid,classname,orderstatus,ordertime,studentid,paytype,amount,coursename,teacherid')
            ->limit($limitstr)
            ->order('ordertime','desc')
            ->select();
	}

	/**
	 * [orderAnalysis 获取订单各个状态的数量]
	 * @Author wyx
	 * @DateTime 2018-04-21T15:07:31+0800
	 * @param    [int]          $organid [机构标记]
	 * @return   [array]                  [description]
	 */
	public function orderAnalysis($teacherid,$organid){
		return Db::table($this->table)
		->where('organid',$organid)
		->where('teacherid',$teacherid)
		->group('orderstatus')
		->column('orderstatus,count(orderstatus) num') ;
	}

	/**
	 * [getOrderListCount 获取订单列表总行数]
	 * @Author wyx
	 * @DateTime 2018-04-21T09:54:41+0800
	 * @return   [array]        [返回订单列表数组]
	 */
	public function getOrderListCount($where){
		return Db::table($this->table)->where($where)->count();
	}
	/*
	 * [getOrderDetail 获取订单详情]
	 * @Author wyx
	 * @DateTime 2018-04-21T10:48:46+0800
	 * @param    [int]         $orderid [订单id]
	 * @param    [int]         $organid [机构id]
	 * @return   [array]                [返回查询结果]
	 */
	public function getOrderDetail($orderid){
		$field = 'ordernum,curriculumid,orderstatus,ordertime,studentid,ordersource,paytype,originprice,discount,amount,coursename,classname,teacherid' ;
		return Db::table($this->table)
            ->field($field)
		    ->where('id','eq',$orderid)
		    ->find() ;
	}



}
