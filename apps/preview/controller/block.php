<?php
namespace apps\preview\controller;
class block extends base{
	function save(){
		$bid=xg_input('bid/i');
		$info=xg_model('app_block')->where($bid)->json('data')->find();
		$sets=xg_jsonarr(xg_fcont(XG_DATA.'/blocks.json'))['sets'];
		$blockname=$sets[$info['block']]['title'];
		$themename=xg_model('app_theme')->where($info['thid'])->value('title');
		$pagename=xg_model('app_page')->where('name',$info['pagename'])->value('title');
		$savename=$themename.'-'.$pagename.'-'.$blockname.'-'.date('Ymd').'-'.date('His').'.json';
		$data=xgblock()->make_block_data($bid);
		$data=xg_jsonstr(['xg-saved-theme-block'=>$data]);
		xg_download($data,$savename);
	}
}