<?php
abstract class xgsubmit{
	protected $config=[];
	protected $cids=[];
	protected $info=[];
	protected $models=[];
	protected $count=null;
	protected $result=['success','continue','done','error'];
	protected $level=['hour','day','week','common'];
	protected $sys='xg';
	public function __construct(){
		$this->models=xg_models();
		$this->init();
	}
	abstract protected function submit($ids,$level);
	function init(){}
	protected function submited($level,$data){	
		if(!$leveli=$this->leveli($level))return;
		foreach($data as $k=>$v){
			if(!$v['sid']){
				unset($data[$k]);
			}
		}
		$ids=xg_array_column($data,'sid');
		xg_model('submit')->where('sid',$ids)->time('level_'.$leveli)->update();
	}
	public function set($a,$b=null){
		if(is_array($a)){
			$this->config=xg_merge($this->config,$a);
		}else{
			$this->config[$a]=$b;
		}
		return $this;
	}
	protected function leveli($level){
		$leveli=array_search($level,$this->level);
		if($leveli===false)return;
		$leveli+=1;
		if($leveli>6)return;
		return $leveli;
	}
	protected function contents($level){
		$limit=$this->config[$level.'_count'];
		if(!$leveli=$this->leveli($level))return [];
		$where=new \xg\where();
		$where->where('sys',$this->sys)->where('site',$this->site)->where('level_'.$leveli,0);
		if($this->config[$level.'_cids'])$where->where('cid','in',xg_arr($this->config[$level.'_cids']));
		$this->count=xg_model('submit')->where($where)->count();
		$data=xg_model('submit')->where($where)->order('id desc')->fields('sid,id,cid,model')->limit($limit)->select();
		return $data;
	}
	protected function done($data,$result,$level,$cachename){
		$limit=$this->config[$level.'_count'];
		if(
		!$data 
		or (
			$result=='done' 
			or $result=='error' 
			or (
				$result=='continue' 
				and (
					!$limit
					or  $limit>=$this->count
					)
				
			) 
			or (
				$result=='success' 
				and $limit>=$this->count
			)
		)){
			xg_cache($cachename,XG_TIME,strtotime(date('Y-m-d',XG_TIME).' + 1 day'),'submit');
			if($data)$this->submited($level,$data);
		}
	}
	public function auto(){
		foreach($this->level as $level){
			$cachename='submit-'.$this->sys.'-'.$level;
			if($this->config[$level.'_open'] and $this->config[$level.'_cids']and !xg_cache($cachename)){
				$data=$this->contents($level);
				$result=$this->submit($this->urls($data),$level);
				$this->done($data,$result[0],$level,$cachename);
			}
		}
	}
	public function manual($model,$ids,$level){
		if(is_numeric($level))$level=$this->level[$level-1];
		if($this->support[$level]){
			if(!$ids or !$model)return false;
			$ids=xg_arr($ids);
			$data=xg_model("xg_{$model}")->alias('t')->join('submit as s','s.id=t.id','left')->fields('t.id,t.cid,s.id,s.sid,s.site')->where('t.id',$ids)->where('s.site',$this->site)->select();
			$result=$this->submit($this->urls($data),$level);
			if($result[0]=='success' or $result[0]=='continue'){
				$this->submited($level,$data);
				return true;
			}else{
				return false;
			}
		}
	}
	public function one($model,$id){
		if(!$id)return;
		foreach($this->level as $level){
			$this->manual($model,$id,$level);
		}
	}
	protected function ids($cids){
		$ids=[];
		$cids=xg_arr($cids);
		foreach($cids as $cid){
			foreach($this->cids as $id=>$vid){
				if($cid==$vid)$ids[]=$id;
			}
		}
		return $ids;
	}
	protected function urls($data){
		$urls=[];
		foreach($data as $v){
			$urls[]=xg_http_domain($this->config['domain'],$this->config['https']).xg_content_url($v['cid'],$v['id']);
		}
		return $urls;
	}
	protected function level($ids,$level){
		$ids=xg_arr($ids);
		$result=$this->submit($this->urls($ids),$level);
		$this->submited($level,$ids);
		$this->log($level,$ids,$result);
		return $result;
	}
}