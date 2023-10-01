<?php
namespace sys\xg\traits;
trait content{
	public function contents($cid,$status=1,$options=[]){
		$tbname=xg_category(xg_arr($cid)[0],'model');
		$catef=xg_models($tbname,"catef");
		$fields=xg_models($tbname,"fields");
		if(!xg_in_array('newstime',$catef))$catef[]='newstime';
		unset($options['pagename'],$options['cid'],$options['page'],$options['sys'],$options['pagesize'],$options['total'],$options['appname'],$options['jstime']);
		foreach($options as $f=>$v){
			if(!xg_in_array($f,$fields)){
				unset($options[$f]);
			}
		}
		$where=xg_where()->where($options);
		if(is_numeric($status))$where->where('status',$status);
		$result=xg_model('content')->where($where)->where('cid',xg_child_ids($cid))->page($this->page,$this->pagesize)->fields($catef)->order('newstime desc,id desc')->contents();
		if(!$result)$result=[];
		return $result;
	}
	public function content($cid,$id){
		$tbname=xg_category($cid,'model');
		$fields=xg_models()[$tbname]["contf"];
		return xg_model('content')->where('status',1)->where('cid',$cid)->fields($fields)->content($id);
	}
	public function topic($tid){
		$data=xg_model('topic')->where('status',1)->find($tid);
		if($data)$data['tid']=$data['id'];
		return $data;
	}
	public function search($keywords,$cid=0,$uid=0){
		if($keywords){
			if($cid){
				$tbname=xg_category($cid,'model');
				$fields=xg_models($tbname,"catef");
				if(!xg_in_array('newstime',$fields))$fields[]='newstime';
			}else{
				$fields='id,cid,pic,title,description,view,newstime,status';
			}
			if($this->needuid)$this->where('uid',$uid);
			$data=xg_model('content')->where('cid',$cid)->where('status',1)->fields($fields)->page($this->page,$this->pagesize)->search($keywords);
			$count=xg_model('content')->where('cid',$cid)->where('status',1)->search_count($keywords);
			return ['count'=>$count,'data'=>$data];
		}
		return [];
	}
	public function models(){
		return xg_models();
	}
	public function options(){
		$cid=xg_input('cid');
		$ops=xg_input('options');
		$model=xg_category($cid,'model');
		$form=xg_allmodels($model,'form');
		$opdata=[];
		if($ops and $form){
			$ops=xg_arr($ops);
			foreach($ops as $op){
				$type=$form[$op]['type'];
				if($type=='table'){
					$data=$form[$op]['data'];
					if($data['table'] and $data['show']){
						$datas=xg_model($data['table'])->fields([$data['show'],$data['field']])->select();
						if($datas){
							foreach($datas as $k=>$v){
								$datas[$k]=['value'=>$v[$data['field']],'text'=>$v[$data['show']]];
							}
							$opdata[]=['key'=>$op,'data'=>array_values($datas)];
						}
					}
				}elseif($type=='select' or $type=='checkbox'){
					$datas=xg_line_arr(xg_models($model,'form')[$op]['remark']);
					if($datas){
						foreach($datas as $k=>$v){
							$datas[$k]=['value'=>$v,'text'=>$v];
						}
						$opdata[]=['key'=>$op,'data'=>array_values($datas)];
					}
				}
			}
		}
		xg_jsonok(['opdata'=>$opdata]);
	}
	public function cates($cid=null,$col=null){
		$cates=xg_category($cid,$col);
		foreach($cates as $k=>$v){
			if(!$v['status'])unset($cates[$k]);
		}
		return $cates;
	}
}
?>