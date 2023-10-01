<?php
namespace apps\home\controller;
class sitemap extends base{
	public function index(){
		$sitemap=xg_model('sitemap')->where('join',1)->where('sys','xg')->column('*','id');
		$alone=[];
		$join=[];
		foreach($sitemap as $v){
			if($v['join']){
				if($v['alone']){
					$alone[]=$v['cid'];
				}else{
					$join[$v['cid']]=$v;
				}
			}
		}
		$data=[];
		foreach($join as $id=>$v){
			$data[]=['url'=>xg_http_domain().'/'.xg_category($id,'name').'/','changefreq'=>$v['changefreq'],'priority'=>'1.0','time'=>date('Y-m-d')];
		}
		if($join)$contents=xg_model('content')->where('cid',array_keys($join))->time('updatetime')->fields('cid,id,updatetime')->select();
		foreach($contents as $k=>$v){
			$data[]=['url'=>xg_http_domain().xg_content_url($v['cid'],$v['id']),'changefreq'=>'monthly','priority'=>$join[$v['cid']]['priority'],'time'=>$v['updatetime']];
		}
		foreach($alone as $k=>$v){
			$alone[$k]=['url'=>xg_http_domain()."/sitemap-{$v}.xml"];
		}
		$xml=$this->fetch('index',['data'=>$data,'alone'=>$alone]);
		xg_exit($xml,'text/xml');
	}
	public function alone(){
		$id=xg_input('get.id');
		$info=xg_model('sitemap')->where('sys','xg')->where('cid',$id)->find();
		$contents=xg_model('content')->where('cid',$id)->time('updatetime')->fields('cid,id,updatetime')->select();
		if(!$info['join'])return;
		$data=[];
		$data[]=['url'=>xg_http_domain().'/'.xg_category($id,'name').'/','changefreq'=>$info['changefreq'],'priority'=>'1.0','time'=>date('Y-m-d')];
		foreach($contents as $k=>$v){
			$data[]=['url'=>xg_http_domain().xg_content_url($v['cid'],$v['id']),'changefreq'=>'monthly','priority'=>$info['priority'],'time'=>$v['updatetime']];
		}
		$xml=$this->fetch('index',['data'=>$data,'alone'=>$alone]);
		xg_exit($xml,'text/xml');
	}
}
?>