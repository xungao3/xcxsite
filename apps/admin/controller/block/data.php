<?php
namespace apps\admin\controller\block;
trait data{
	function data(){
		if(!$thid=xg_input('thid'))xg_error('请设置主题id');
		$corr=xg_input('corr');
		$alone=xg_input('alone');
		$post=xg_input('post.');
		$bid=xg_input('bid');
		if($bid){
			$info=xg_model('app_block')->json('data')->find($bid);
		}
		if($post['data']){
			$post['data']=xg_jsonarr($post['data']);
			if($post['block']=='slide' or $post['block']=='img-nav'){
				foreach($post['data'] as $k=>$v){
					if($v['id'] and $v['cid'] and !$v['pic'] and $model=xg_category($v['cid'],'model')){
						$post['data'][$k]['pic']=xg_model($model,1)->where($v['id'])->value('pic');
					}
				}
			}
		}
		if($post['radius']){
			$post['radius']=trim($post['radius']);
			while(strpos($post['radius'],'  ')!==false){
				$post['radius']=str_replace('  ',' ',$post['radius']);
			}
		}
		if($post['margin']){
			$post['margin']=trim($post['margin']);
			while(strpos($post['margin'],'  ')!==false){
				$post['margin']=str_replace('  ',' ',$post['margin']);
			}
		}
		if($post['padding']){
			$post['padding']=trim($post['padding']);
			while(strpos($post['padding'],'  ')!==false){
				$post['padding']=str_replace('  ',' ',$post['padding']);
			}
		}
		if($post['html']=xg_input('post.html','','xg_safehtml,trim')){
			$post['html']=str_replace('src="'.xg_http_domain(),'src="',$post['html']);
			$post['html']=str_replace("src='".xg_http_domain(),"src='",$post['html']);
		}
		if($post['cateids']){
			$post['cateids']=xg_jsonarr($post['cateids']);
		}
		if($post['toplink']){
			$post['toplink']=xg_jsonarr($post['toplink']);
		}
		if($post['cateid']){
			$post['cateid']=xg_jsonarr($post['cateid']);
		}
		if(isset($post['footer-custom'])){
			$post['footer-custom']=str_replace('©','&copy;',$post['footer-custom']);
		}
		$data['pagename']=$post['pagename'];
		$data['block']=$post['block'];
		$data['obid']=$post['obid'];
		$data['order']=$post['order'];
		unset($post['bid'],$post['obid'],$post['thid'],$post['tid'],$post['pagename'],$post['block'],$post['order'],$post['status'],$post['order']);
		$data['data']=$post;
		$data['thid']=$thid;
		if(!$bid){
			if($data['order']==='')$data['order']=xg_model('app_block')->where(array('thid'=>$thid,'pagename'=>$data['pagename']))->max('order')+1;
			$bid=xg_model('app_block')->json('data')->add($data);
		}else{
			xg_model('app_block')->where(array('bid'=>$bid))->json('data')->save($data);
			if($info['obid'])$this->updateblock($info['obid'],$data);
		}
		if($corr){
			$this->copyblock($bid,true);
		}elseif($alone){
			$this->copyblock($bid);
		}
		xg_jsonok(['msg'=>'保存成功','bid'=>$bid]);
	}
}
?>