<?php
namespace sys\xg\traits;
trait app{
	public function sets($sets=null){
		return [];
	}
	public function sysinfo(){
		$siteurl=xg_config('site.site-url');
		if(!$siteurl)$siteurl=xg_http_domain();
		$imgurl=xg_config('site.img-site-url');
		if(!$imgurl)$imgurl=$siteurl;
		return [
			'url'=>$siteurl,
			'url'=>$imgurl,
			'contid'=>'id',
			'cateid'=>'cid',
		];
	}
	public function cateboxes($block,$cid){
		$this->page=1;
		$this->pagesize=xg_input('count');
		$this->name=$block['bid'].'-'.$cid;
		$md5=$this->md5();
		$file=$this->file();
		$path=$this->path();
		$data=$this->contents($cid);
		if($this->cache){
			$this->cache(['type'=>'custom','sys'=>$this->sys,'cateid'=>$cid,'infoid'=>$block['bid'],'md51'=>$md5[0],'md52'=>$md5[1],'file'=>$file]);
			$data=$this->save($data);
		}
		return $data;
	}
	public function catebox($block,$cid=null){
		if($block['source']=='allcate'){
			$cids=array_values(xg_category(null,'id'));
		}elseif($block['source']=='curcate'){
			$cids=[];
			if($cid)$cids[]=$cid;
		}elseif($block['source']=='custom'){
			$cids=xg_array_column($block['cateids'],'cid');
		}else{
			$cids=[];
		}
		if($cids and $cids=xg_child_ids($cids)){
			$list=$this->count($block['show_count']>0?$block['show_count']:8)->contents($cids);
			if($list){
				$block['list']=$list;
			}else{
				$block['list']=[];
			}
		}
		return $block;
	}
	public function slide($block,$cid=null){
		if($block['source']=='allcate'){
			$cids=array_values(xg_category(null,'id'));
		}elseif($block['source']=='curcate'){
			$cids=[];
			if($cid)$cids[]=$cid;
		}elseif($block['source']=='custom'){
			$cids=xg_array_column($block['data'],'cid');
		}else{
			$cids=[];
		}
		if($cids and $cids=xg_child_ids($cids)){
			$list=$this->count(5)->contents($cids);
			if($list){
				$block['list']=$list;
			}else{
				$block['list']=[];
			}
		}
		return $block;
	}
}
?>