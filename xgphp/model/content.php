<?php
namespace xgphp\model;

class content extends \xg\model{
	protected $name=null;
	protected $model=null;
	protected $contf=[];
	protected $catef=[];
	protected $conds=[];
	protected $condid=0;
	protected $condcid=0;
	function init(){
		$this->prefix($this->prefix=XG_TBPRE.'xg_');
		$cates=xg_category();
		$this->cates=$cates;
	}
	function multi_conds(){
		$conds=[];
		foreach($this->conds as $table=>$cond){
			if($cond)$conds[$this->prefix.$table]=xg_str($cond,' or ');
		}
		return $conds;
	}
	function nextid(){
		$result=$this->fields('id')->next();
		return $result['id'];
	}
	function previd(){
		$result=$this->fields('id')->prev();
		return $result['id'];
	}
	function next(){
		if($this->condid){
			$this->where->reset();
			if($this->condcid){
				$this->where('cid','=',$this->condcid);
			}
			$result=$this->where('id','>',$this->condid)->limit(1)->order('id asc')->find();
			$this->condcid=$this->condid=0;
		}
		return $result;
	}
	function prev(){
		if($this->condid){
			$this->where->reset();
			if($this->condcid){
				$this->where('cid','=',$this->condcid);
			}
			$result=$this->where('id','<',$this->condid)->limit(1)->order('id desc')->find();
			$this->condcid=$this->condid=0;
		}
		return $result;
	}
	function where($p1,$p2=null,$p3=null,$join='and'){
		if(is_numeric($p1)){
			$this->condid=$p1;
		}
		if($p1=='cid'){
			if(is_numeric($p2) or ($p2==='=' and is_numeric($p3))){
				if($condcid=(($p2==='=')?$p3:$p2))$this->condcid=$condcid;
				if($condcid===0 or $condcid==='0'){
					$models=xg_array_column(xg_models(),'name');
					foreach($models as $modelk=>$model){
					    $models[$modelk]=$model;
					}
					$this->names($models);
				}
			}
			if(is_string($p2) and strpos($p2,',')!==false)$p2=xg_arr($p2);
			if(is_string($p3) and strpos($p3,',')!==false or $p2=='in')$p3=xg_arr($p3);
			if(is_array($p2)){
				$p3=$p2;
				$p2='in';
			}
			if(is_array($p3) and $p2=='in'){
				$models=[];
				foreach($p3 as $id){
					if(is_numeric($id)){
						$model=$this->cates[$id]['model'];
						if($model){
							if(!$this->conds[$model])$this->conds[$model]=[];
							$this->conds[$model][]="cid=$id";
							$models[$model]=$model;
						}
					}elseif(xg_models()[$id]){
						$models[$id]=$id;
					}
				}
				$this->names($models);
			}else{
				if(is_numeric($p2)){
					$p3=$p2;
					$p2='=';
				}
				if(is_numeric($p3) and $p2=='='){
					if($model=$this->cates[$p3]['model']){
						$this->name($model);
					}
				}
			}
		}else{
			parent::where($p1,$p2,$p3,$join);
		}
		return $this;
	}
	public function content($id=null){
		if($this->contf){
			$this->catef[]='cid';
			$this->catef[]='newstime';
			$this->fields($this->contf);
		}
		$data=$this->find($id);
		$data['timestamp']=$newstime=strtotime($data['newstime']);
		$timedata=xg_arr(date('Y-m-d-H-i-s',$newstime),'-');
		$data['timedata']=[
			'y'=>$timedata[0],
			'm'=>$timedata[1],
			'd'=>$timedata[2],
			'h'=>$timedata[3],
			'i'=>$timedata[4],
			's'=>$timedata[5]
		];
		return $data;
	}
	public function fields($fields=null){
		$this->contf=$this->catef=[];
		parent::fields($fields);
		return $this;
	}
	public function search($keywords=null){
		if($keywords)$this->where('title','like','%'.$keywords.'%');
		if($this->catef){
			$this->catef[]='cid';
			$this->catef[]='newstime';
			$this->fields($this->catef);
		}
		$data=$this->select();
		$data=$this->outdata($data);
		return $data;
	}
	public function search_count($keywords=null){
		if($keywords)$this->where('title','like','%'.$keywords.'%');
		$data=$this->count();
		return $data;
	}
	public function contents($cids=null){
		if($cids)$this->where('cid',$cids);
		if($this->catef){
			$this->catef[]='cid';
			$this->catef[]='newstime';
			$this->fields($this->catef);
		}
		$data=$this->select();
		$data=$this->outdata($data);
		return $data;
	}
	public function outdata($data){
		foreach($data as $k=>$v){
			$data[$k]['timestamp']=$newstime=strtotime($v['newstime']);
			$timedata=xg_arr(date('Y-m-d-H-i-s',$newstime),'-');
			$data[$k]['timedata']=[
				'y'=>$timedata[0],
				'm'=>$timedata[1],
				'd'=>$timedata[2],
				'h'=>$timedata[3],
				'i'=>$timedata[4],
				's'=>$timedata[5]
			];
		}
		return $data;
	}
	public function name($name=null){
		if(substr($name,0,3)!=='xg_'){
			$this->model=$name;
			$name=$name;
		}else{
			$this->model=substr($name,3);
		}
		$this->contf=xg_models($this->model,'contf');
		$this->catef=xg_models($this->model,'catef');
		$jsonf=xg_models($this->model,'jsonf');
		$timef=xg_models($this->model,'timef');
		$this->json($jsonf)->time($timef);
		return parent::name($name);
	}
	public function recount($cids){
		if(!$cids)return $this;
		$tbpre=XG_TBPRE;
		$cids=xg_arr($cids);
		$sql='UPDATE '.XG_TBPRE.'category SET `count` = CASE id ';
		foreach($cids as $k=>$cid){
			$tbname=xg_category($cid,'model');
			if($tbname){
				$sql.=" WHEN $cid THEN (SELECT COUNT(id) FROM {$tbpre}xg_{$tbname} WHERE cid=$cid and status=1) ";
			}else{
				unset($cids[$k]);
			}
		}
		$sql.=' END WHERE id IN ('.xg_str($cids).')';
		if($cids){
			$this->query($sql);
			xg_cache_group('category',null);
			xg_cache('app-set',null);
		}
		return $this;
	}
}