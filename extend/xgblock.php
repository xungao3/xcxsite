<?php
class xgblock{
	protected $enbidmap=[];
	protected $debidmap=[];
	protected $bidi=0;
	public function __construct(){
	}
	function make_block_data($bid){
		$info=xg_model('app_block')->where($bid)->json('data')->fields('bid,obid,data,block,order')->find();
		unset($info['data']['pagename'],$info['data']['bid'],$info['data']['status']);
		$data=$info['data'];
		$bids=xg_model('app_block')->where('pagename',$bid)->column('bid');
		$data['children']=[];
		foreach($bids as $v){
			$data['children'][]=$this->make_block_data($v);
		}
		return ['name'=>$info['block'],'obid'=>$this->enbidmap[$info['obid']],'bid'=>$this->enbidmap[$bid],'order'=>$info['order'],'data'=>$data];
	}
	function decode_block_data($thid,$data,$page){
		if($data['data'] and $data['name']){
			$bid=xg_model('app_block')->json('data')->add(['pagename'=>$page,'obid'=>$data['obid'],'block'=>$data['name'],'order'=>$data['order'],'data'=>$data['data'],'thid'=>$thid,'status'=>1]);
			$this->debidmap[$data['bid']]=$bid;
			foreach($data['data']['children'] as $v){
				$this->decode_block_data($thid,$v,$bid);
			}
			return $bid;
		}
	}
	function make_page_data($pid){
		$data=xg_model('app_page')->json('data')->order('pid asc')->find($pid);
		$bids=xg_model('app_block')->where('pagename',$data['name'])->where('thid',$data['thid'])->order('`order` asc')->column('bid');
		unset($data['thid'],$data['pid']);
		$data['blocks']=[];
		foreach($bids as $v){
			$this->bidi++;
			$this->enbidmap[$v]=$this->bidi;
		}
		foreach($bids as $v){
			$data['blocks'][]=$this->make_block_data($v);
		}
		return $data;
	}
	function make_name($name){
		$num=substr($name,-1);
		if(is_numeric($num)){
			$name=substr($name,0,-1).($num+1);
		}else{
			$name=$name.'2';
		}
		return $name;
	}
	function decode_page_data($thid,$data){
		$blocks=$data['blocks'];
		unset($data['blocks']);
		$data['thid']=$thid;
		$data['name']=$data['name'];
		$data['title']=$data['title'];
		while(xg_model('app_page')->where(['thid'=>$thid,'name'=>$data['name']])->value('name')){
			$data['name']=$this->make_name($data['name']);
		}
		$pid=xg_model('app_page')->json('data')->add($data);
		foreach($blocks as $v){
			$this->decode_block_data($thid,$v,$data['name']);
		}
		return true;
	}

	function make_theme_data($thid){
		$data=xg_model('app_theme')->find($thid);
		unset($data['thid']);
		$pids=xg_model('app_page')->where('thid',$thid)->column('pid');
		$links=xg_model('app_links')->where('thid',$thid)->json('data')->value('data');
		$data['links']=$links;
		$data['pages']=[];
		foreach($pids as $v){
			$data['pages'][]=$this->make_page_data($v);
		}
		return $data;
	}
	function replace_obid($thid){
		if(!$this->debidmap)return;
		$sql='UPDATE '.XG_TBPRE.'app_block SET `obid` = CASE id ';
		foreach($this->debidmap as $bidi=>$bid){
				$sql.=" WHEN $bidi THEN $bid ";
		}
		$sql.=' END';
		$this->query($sql);
	}
	function decode_theme_data($thid,$data){
		$pages=$data['pages'];
		$links=['data'=>$data['links']];
		unset($data['pages'],$data['links']);
		$data['title']=$data['title'];
		while(xg_model('app_theme')->where(['title'=>$data['title']])->value('title')){
			$data['title']=$this->make_name($data['title']);
		}
		$thid=xg_model('app_theme')->add($data);
		$links['thid']=$thid;
		xg_model('app_links')->json('data')->add($links);
		foreach($pages as $v){
			$this->decode_page_data($thid,$v);
		}
		return true;
	}
}