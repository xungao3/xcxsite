<?php
namespace apps\admin\controller;
class block extends base{
	use block\sets;
	use block\data;
	function slide_img(){
		$img=xg_input('get.img','');
		return $this->display(['img'=>$img]);
	}
	function load(){
		$thid=xg_input('thid/i');
		$page=xg_input('page');
		$cont=xg_file()->allow('json')->content();
		$data=xg_jsonarr($cont)['xg-saved-theme-block'];
		if(!$data)xg_error('数据错误');
		if(xgblock()->decode_block_data($thid,$data,$page))xg_success('导入成功');
		xg_error('导入失败');
	}
	public function discorr(){
		$bid=xg_input('get.bid/i');
		$obid=xg_input('get.obid/i');
		if(!$bid)xg_error('ID错误');
		xg_model('app_block')->where($bid)->update(['obid'=>0]);
		if($bid==$obid and $nbid=xg_model('app_block')->where('obid',$obid)->value('bid')){
			xg_model('app_block')->where('obid',$obid)->update(['obid'=>$nbid]);
		}
		xg_success();
	}
	protected function updateblock($bid,$data){
		$pages=xg_model('app_block')->where('obid',$bid)->column('pagename');
		foreach($pages as $p){
			$data['pagename']=$p;
			if($data['data'])$data['data']['pagename']=$p;
			unset($data['bid'],$data['obid'],$data['data']['bid']);
			xg_model('app_block')->where('pagename',$p)->where('obid',$bid)->json('data')->update($data);
		}
	}
	protected function copyselfblockdata($bid,$pid=0){
		$data=xg_model('app_block')->json('data')->find($bid);
		unset($data['bid'],$data['data']['bid']);
		if($pid)$data['pagename']=$pid;
		$nid=xg_model('app_block')->json('data')->add($data);
		if($bids=xg_model('app_block')->where('pagename',$bid)->values('bid')){
			foreach($bids as $id){
				$this->copyselfblockdata($id,$nid);
			}
		}
		return $nid;
	}
	public function copychildblock($from,$to){
		$list=xg_model('app_block')->where('pagename',$from)->json('data')->select();
		foreach($list as $k=>$v){
			$from2=$v['bid'];
			unset($v['bid'],$v['data']['pagename'],$v['data']['bid']);
			$v['pagename']=$to;
			$to2=xg_model('app_block')->json('data')->add($v);
			$this->copychildblock($from2,$to2);
		}
	}
	public function copyselfblock(){
		$bid=xg_input('bid');
		$nid=$this->copyselfblockdata($bid);
		if($nid){
			if(xg_input('get.moveto')){
				$this->moveblock($nid);
			}
			xg_jsonok('复制成功');
		}
		xg_jsonerr('没有复制成功');
	}
	protected function copyblock($bid,$corr){
		$data=xg_model('app_block')->json('data')->find($bid);
		if($data['obid']){
			$obid=$data['obid'];
		}else{
			$obid=$bid;
			xg_model('app_block')->where('bid',$bid)->update(['obid'=>$obid]);
		}
		$thid=$data['thid'];
		unset($data['bid'],$data['data']['bid']);
		$pagename=$data['pagename'];
		$pages=xg_model('app_page')->where('thid',$thid)->group('name')->values('name');
		if($corr){
			$bpages=xg_model('app_block')->where('thid',$thid)->where('obid',$obid)->values('pagename');
		}
		foreach($pages as $p){
			$data['pagename']=$p;
			if($corr)$data['obid']=$obid;
			if($data['block']=='menu'){
				if(is_numeric($p))continue;
				if($id=xg_model('app_block')->where(['block'=>'menu','pagename'=>$p,'thid'=>$thid])->value('bid')){
					xg_model('app_block')->where($id)->json('data')->save($data);
				}else{
					xg_model('app_block')->json('data')->add($data);
				}
			}else{
				if($data['data'])$data['data']['pagename']=$p;
				if($corr){
					if(!xg_in_array($p,$bpages)){
						$newid=xg_model('app_block')->json('data')->add($data);
						$this->copychildblock($bid,$newid);
					}
					$bpages[]=$p;
				}else{
					$newid=xg_model('app_block')->json('data')->add($data);
					$this->copychildblock($bid,$newid);
				}
			}
		}
	}
	function remove(){
		if(!$bid=xg_input('get.bid'))xg_error('模块id错误');
		xg_model('app_block')->delete($bid);
		xg_success('删除成功');
	}
	function moveblock($bid=null){
		if(!$bid and !$bid=xg_input('get.bid/i'))xg_error('模块id错误');
		if($moveto=xg_input('get.moveto')){
			xg_model('app_block')->where($bid)->update(['pagename'=>$moveto]);
			xg_success(XG_ACT=='moveblock'?'移动成功':'复制成功');
		}
		$thid=xg_input('get.thid/i');
		$pagename=xg_input('get.pagename');
		$data=xg_model('app_block')->where([['thid','=',$thid],['block','=','blocks'],['pagename','=',$pagename],['bid','!=',$bid]])->where_or([['thid','=',$thid],['block','=','popup'],['pagename','=',$pagename],['bid','!=',$bid]])->json('data')->select();
		$pages=xg_model('app_page')->where([['thid','=',$thid]])->select();
		$blocks=[$pagename=>'本页顶级模块'];
		foreach($pages as $k=>$v){
			$blocks[$v['name']]='页面-'.$v['name'].'-'.$v['title'];
		}
		foreach($data as $k=>$v){
			$blocks[$v['bid']]=$v['bid'].'-'.$v['block'].($v['data']['name']?'-'.$v['data']['name']:'');
		}
		$this->display(['blocks'=>$blocks,'pagename'=>$pagename]);
	}
	function order(){
		$arr=xg_input('order');
		$ids=[];
		if($arr){
			$sql='update ';
			$sql.=XG_TBPRE.'app_block set `order`=( case bid ';
			foreach($arr as $bid=>$order){
				$ids[]=$bid;
				$sql.=' when '.$bid.' then '.$order.' ';
			}
			$sql.='end) where bid in ('.xg_str($ids).')';
			xg_db()->query($sql);
		}
		xg_jsonok();
	}
	function blocks(){
		$parent=xg_input('pagename');
		$data=xg_model('app_block')->where('pagename',$parent)->json('data')->select();
		if(is_numeric($parent)){
			$block=xg_model('app_block')->json('data')->find($parent);
		}
		return $this->display(['data'=>$data,'block'=>$block]);
	}
	function block(){
		if(!$thid=xg_input('get.thid'))xg_error('请设置主题id');
		$pagename=xg_input('request.pagename');
		$this->assign('thid',$thid);
		$this->assign('pagename',$pagename);
		$blocks=$blockarr=xg_blocks($thid)[$pagename];
		$sets=xg_jsonarr(xg_fcont(XG_DATA.'/blocks.json'))['sets'];
		$this->assign('blockarr',$blockarr);
		$this->assign('sets',$sets);
		return $this->display();
	}
	function icon_nav(){
		$index=xg_input('get.index');
		$thid=xg_input('get.thid');
		$this->display(['thid'=>$thid,'index'=>$index]);
	}
	function btn_item(){
		$index=xg_input('get.index');
		$thid=xg_input('get.thid');
		$this->display(['thid'=>$thid,'index'=>$index]);
	}
	function menu_item(){
		$index=xg_input('get.index');
		$thid=xg_input('get.thid');
		$this->display(['thid'=>$thid,'index'=>$index]);
	}
}
?>