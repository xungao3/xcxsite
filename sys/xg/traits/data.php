<?php
namespace sys\xg\traits;
trait data{
	function data(){
		$param=xg_input('request.');
		$bid=xg_input('request.bid/i',0);
		$cid=xg_input('request.cid/i',0);
		$tid=xg_input('request.tid/i',0);
		$id=xg_input('request.id/i',0);
		$type=xg_input('request.type');
		$recom=xg_input('request.recom');
		$keywords=xg_input('request.keywords');
		$total=xg_input('request.total/i',0);
		$page=xg_input('request.page/i',1);
		$pagesize=xg_input('request.pagesize/i',24);
		if($type=='recom' and $recom){
			$info=$this->cache_recom($recom,$cid);
		}elseif($tid){
			$info=$this->cache_topic($tid);
		}elseif($bid){
			$info=$this->cache_block($bid,$param);
		}elseif($id){
			if($this->needuid and !$this->uid)xg_jsonmsg([]);
			$info=$this->cache_content($cid,$id,$this->uid);
		}elseif($keywords){
			if($this->needuid and !$this->uid)xg_jsonmsg([]);
			$info=$this->page($page,$pagesize)->search($keywords,$cid,$this->uid);
			if(!$info)$info=[];
			$info=xg_jsonstr($info);
		}elseif($cid){
			if($this->needuid and !$this->uid)xg_jsonmsg([]);
			$info=$this->page($page,$pagesize)->cache_contents($cid,$total,$this->uid);
		}
		return xg_exit($info,'text/plain');
	}
	function cache_recom($recom){
		$count=xg_input('count/i',12);
		$infos=xg_model("recom")->where("status",1)->where("recom",$recom)->fields("infoid,cateid,data")->limit($count)->json('data')->select();
		if(!$infos)return;
		if($recom=='focus'){
			$data=[];
			foreach($infos as $v){
				$v2=$v['data'];
				$v2['cid']=$v['cateid'];
				$v2['id']=$v['infoid'];
				$data[]=$v2;
			}
		}else{
			$cids=xg_array_column($infos,'cateid');
			$where=xg_where()->where(function($query)use($infos){
				foreach($infos as $info){
					$query->where_or(['cid'=>$info['cateid'],'id'=>$info['infoid']]);
				}
				return $query;
			});
			$data=xg_model('content')->where('cid',$cids)->where($where)->fields('id,cid,title,pic,newstime')->select();
		}
		$this->type='recom';
		$this->name=$recom;
		$md5=$this->md5();
		$file=$this->file();
		$path=$this->path();;
		$run=xg_hooks('cache-content')->run($data)->last();
		if($run)$data=$run;
		if($this->cache){
			$this->cache(['type'=>$this->type,'sys'=>$this->sys,'md51'=>$md5[0],'md52'=>$md5[1],'file'=>$file]);
			$data=$this->save($data);
		}
		return $data;
	}
	function cache_topic($tid){
		if(!$tid)return;
		$this->type='topic';
		$this->name=$tid;
		$md5=$this->md5();
		$file=$this->file();
		$path=$this->path();
		$data=$this->topic($tid);
		$run=xg_hooks('cache-content')->run($data)->last();
		if($run)$data=$run;
		if($this->cache){
			$this->cache(['type'=>'topic','sys'=>$this->sys,'cateid'=>$cid,'infoid'=>$id,'md51'=>$md5[0],'md52'=>$md5[1],'file'=>$file]);
			$data=$this->save($data);
		}
		return $data;
	}
	function cache_content($cid,$id,$uid=0){
		if(!$id||!$cid)return;
		$model=$this->cates($cid,'model');
		$this->type='xg-'.$model;
		$this->name=$id;
		$md5=$this->md5();
		$file=$this->file();
		$path=$this->path();
		//if($this->cache and file_exists($path))return $this->load();
		if($this->needuid)$this->where('uid',$uid);
		$data=$this->content($cid,$id);
		$run=xg_hooks('cache-content')->run($data)->last();
		if($run)$data=$run;
		if($this->cache){
			$this->cache(['type'=>'content','model'=>$model,'sys'=>$this->sys,'cateid'=>$cid,'infoid'=>$id,'md51'=>$md5[0],'md52'=>$md5[1],'file'=>$file]);
			$data=$this->save($data);
		}
		return $data;
	}
	function cache_contents($cid,$total=0,$uid=0){
		$page=xg_input('get.page');
		$options=xg_input('get.',[]);
		$pagesize=xg_input('get.pagesize',12);
		$this->type='contents';
		if($this->needuid)$this->where('uid',$uid);
		$data=$this->page($page,$pagesize)->contents($cid,1,$options);
		$run=xg_hooks('cache-contents')->run($data)->last();
		if($run)$data=$run;
		$this->name=$cid.'-'.$page.'-'.$pagesize.'-'.$total;
		$md5=$this->md5();
		$file=$this->file();
		$path=$this->path();
		if($this->cache){
			$this->cache(['type'=>$this->type,'sys'=>$this->sys,'cateid'=>$cid,'page'=>$page,'pagesize'=>$pagesize,'count'=>$total,'md51'=>$md5[0],'md52'=>$md5[1],'file'=>$file]);
			$data=$this->save($data);
		}
		return $data;
	}
	function cache_block($bid,$param=[]){
		if(!$bid)return;
		$block=xg_block($bid,$param);
		$run=xg_hooks('cache-block')->run($block)->last();
		if($run)$block=$run;
		$thid=$block['thid'];
		$pagename=$block['pagename'];
		$this->type=$block['from'];
		if($block['block']=='cate-boxes'){
			return $this->cateboxes($block,$param['cid']);
		}elseif($this->type=='curcate'){
			$this->name=$bid.'-'.$param['cid'];
		}elseif($this->type=='custom'){
			$idname=$this->sysinfo()['cateid'];
			$cids=xg_array_column($block['data'],'cid');
			$this->name=$bid.'-'.xg_str($cids);
		}elseif($this->type=='allcate'){
			$this->name=$bid.'-0';
		}
		if($block['block']=='cate-box'){
			$block=$this->catebox($block,$param['cid']);
		}elseif($block['block']=='slide'){
			$block=$this->slide($block,$param['cid']);
		}
		$md5=$this->md5();
		$file=$this->file();
		$path=$this->path();
		//if($this->cache and file_exists($path))return $this->load();
		if($this->cache){
			$this->cache(['type'=>$this->type,'sys'=>$this->sys,'thid'=>$thid,'pagename'=>$pagename,'cateid'=>$param['cid'],'infoid'=>$bid,'md51'=>$md5[0],'md52'=>$md5[1],'file'=>$file]);
			$block=$this->save($block);
		}
		return $block;
	}
}
?>