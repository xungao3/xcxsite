<?php
namespace apps\admin\controller;
class content extends base{
	use \xg\traits\modelfield;
	use \xg\traits\region;
	public function content(){
		$model=xg_input('get.model');
		if(!xg_allmodels($model))xg_error('没有此模型');
		xg_admin_auth('content-xg-'.$model,'没有权限添加修改'.xg_allmodels($model,'alias'));
		$id=xg_input('get.id/i',0);
		if($id){
			$fileid=$id;
		}else{
			if(!$fileid=xg_input('post.fileid/i',0))$fileid=-rand(100000000,999999999);
		}
		if(!$id)/*这里的id与fileid顺序不能颠倒*/$id=xg_input('post.id/i',0);
		$cid=xg_input('cid/i',0);
		if($id)$info=xg_model('content')->name($model)->fields('*')->find($id);
		$form=xg_allmodels($model,'form');
		if(XG_POST){
			$post=xg_input('post.');
			if($post['id']==-1)$id=null;
			$data=array();
			if(isset($post['title']) and !$post['title'])xg_error('请填写标题');
			if(isset($post['cid']) and !$cid and xg_allmodels($model,'type')==1)xg_error('请选择分类');
			if(count(xg_arr($post['pic']))>3)xg_error('题图最多上传3张');
			if(isset($post['pic']) and xg_allmodels($model,'type')==1 and !$post['pic'] and $confirm=xg_confirm('没有上传图片，是否确定提交？'))xg_error($confirm);
			if($post['id']!=-1 and !$info and xg_model('content')->name($model)->where('title',$post['title'])->count() and $confirm=xg_confirm('已存在此标题的内容，是否确定提交？'))xg_error($confirm);
			
			foreach($form as $k=>$v){
				if($v['type']=='editor'){
					$data[$v['name']]=xg_input('post.'.$v['name'],'','xg_trim,xg_safehtml');
				}elseif($v['type']=='checkbox' or $v['type']=='correlation'){
					$data[$v['name']]=xg_str($post[$v['name']]);
				}elseif($v['type']=='datetime'){
					$data[$v['name']]=strtotime($post['newstime']);
				}elseif(is_array($post[$v['name']])){
					$data[$v['name']]=xg_jsonstr($post[$v['name']]);
				}elseif(isset($post[$v['name']])){
					$data[$v['name']]=$post[$v['name']];
				}
			}
			
			$data['uid']=xg_login();
			$data['cid']=$cid;
			
			if($id){
				if(!isset($data['status']))$data['status']=$info['status'];
				xg_model('content')->name($model)->time('updatetime')->update($data,$id);
			}else{
				if(!isset($data['status']))$data['status']=1;
				$id=xg_model('content')->name($model)->time('createtime,updatetime')->insert($data);
				if($post['submit'])xg_hooks('submit-one')->run($model,$id);
			}
			if($fileid!=$id)xg_model('files')->where('infoid',$fileid)->update(array('infoid'=>$id));
			xg_model('content')->name($model)->recount($cid);
			xg_success(array('msg'=>$post['id']==-1?'另存成功':'提交成功','goto'=>xg('url'),'newid'=>$id,'cid'=>$cid,'url'=>xg_content_url($cid,$id)));
		}else{
			$catelist=xg_cate_select_cont(null,$model);
			$this->assign('info',$info?$info:['newstime'=>time(),'username'=>xg_session('user_auth.username')]);
			$this->assign('model',$model);
			$this->assign('catelist',$catelist);
			$this->assign('cid',$cid);
			$this->assign('fileid',$fileid);
			$this->assign('form',$form);
			$coversize=xg_config('site.cover-size');
			$this->assign('coversize',($coversize?$coversize:''));
			if($id){
				$this->assign('url',xg_content_url($info['cid'],$id));
				$this->assign('nextid',xg_model('content')->name($model)->where($id)->where('cid',$cid)->nextid());
				$this->assign('previd',xg_model('content')->name($model)->where($id)->where('cid',$cid)->previd());
			}
			return $this->display();
		}
	}
	public function submit(){
		$ids=xg_input('request.id');
		$model=xg_input('request.model');
		$level=xg_input('request.level');
		$res=xg_hooks('submit-manual')->run($model,$ids,$level)->data();
		$msg=[];
		foreach($res as $k=>$v){
			$name=xg_addons('submit')[$k]['submit_info']['title'];
			if($v){
				$msg[]=$name.'提交成功';
			}else{
				$msg[]=$name.'<span style="color:#ff0000;">未提交成功</span>';
			}
		}
		if(!$msg)xg_error('没有收到提交结果');
		xg_error(xg_str($msg,'<br>'));
	}
	public function contents(){
		$page=xg_input('get.page/i',1);
		$cid=xg_input('get.cid/i');
		$model=xg_input('get.model');
		$orderby=xg_input('get.orderby');
		$order=xg_input('get.order');
		xg_admin_auth('content-xg-'.$model,'没有权限管理'.xg_allmodels($model,'alias'));
		$keywords=xg_input('get.keywords','');
		$pagesize=15;
		$status=xg_input('get.status','');
		$where=new \xg\where();
		$where->alias('m');
		if($status===''){
			$where->where('status','!=',-1);
		}elseif($status!='all'){
			$status=intval($status);
			$where->where('status','=',$status);
		}
		if($cid){
			$where->where('cid','in',xg_child_ids($cid));
		}
		$mid=xg_allmodels($model,'id');
		$fields=xg_model('fields')->where(['mid'=>$mid,'adminf'=>1,'status'=>1])->order('lorder asc')->column('title','name');
		$dbfields=[];
		if($fields)$dbfields=xg_merge(['id','status','cid','newstime'],array_keys($fields));
		$adminfields=xg_model('fields')->where(['mid'=>$mdata['id'],'adminf'=>1,'status'=>1])->column('title','name');
		if($adminfields){
			$fields=xg_extend($fields,$adminfields);
		}
		if($keywords)$where->where(xg_str($fields?array_keys($fields):['title','description'],'|'),'like',"%$keywords%");
		$recomsets=xg_recom_sets('cont');
		if(XG_AJAX){
			$jsonf=xg_allmodels($model,'jsonf');
			$timef=xg_allmodels($model,'timef');
			$deforder='newstime desc,id desc';
			$dbobj=xg_model($model,1)->alias('m')->where($where)->order($deforder)->fields($dbfields);
			if($orderby){
				if(substr($orderby,0,6)=='recom-'){
					$dbobj->join(['recom','r'],['`r`.`infoid`'=>'`m`.`id`','r.recom'=>substr($orderby,6),'r.type'=>'model','r.model'=>$model,'r.status'=>1],'left')->with('`r`.`status` as `recom`')->order("`recom` $order");
				}else{
					$dbobj->order("`$orderby` $order");
				}
			}
			$dbobj2=clone $dbobj;
			$count=$dbobj2->count();
			$data=$dbobj->json($jsonf)->time($timef)->page($page,$pagesize)->select();
			$pagehtml=xg_pagehtml($count,$pagesize);
			$cates=xg_category();
			$nav=xg_model('admin/nav')->$model();
			$recom=xg_model('admin/recom')->$model();
			foreach($data as $k=>$v){
				if($v['cid']){
					$data[$k]['cate']='<a class="xg-admin-tab-link" href="'.xg_url('content/contents',['model'=>$model,'cid'=>$v['cid']]).'">'.$cates[$v['cid']]['title'].'</a>';
				}
				foreach($recomsets as $rk=>$rv){
					if($rk=='focus'){
						$data[$k]['recom-'.$rk]='<div align="center"><a href="'.xg_url('focus',['switch'=>'recom','recom'=>$rk,'model'=>$model,'cid'=>$v['cid'],'id'=>$v['id']]).'" class="xg-icon xg-switch" xg-status="'.xg_in_array($v['id'],$recom[$rk]).'" xg-switch="'.$rk.'"></a></div>';
					}else{
						$data[$k]['recom-'.$rk]='<div align="center"><a href="'.xg_url('xswitch',['switch'=>'recom','recom'=>$rk,'model'=>$model,'cid'=>$v['cid'],'id'=>$v['id']]).'" class="xg-icon xg-switch" xg-status="'.xg_in_array($v['id'],$recom[$rk]).'" xg-switch="recom"></a></div>';
					}
				}
				if(isset($v['newstime'])){
					$data[$k]['newstime']=xg_form('newstime['.$v['newstime'].']',null,$v['newstime'])->options(['attrs'=>['data-id'=>$v['id']],'classname'=>'xg-input-s newstime-input'])->base_datetime()->get();
				}
				$data[$k]['menu']='<a class="xg-fl xg-a xg-admin-tab-link" xg-tab-title="修改'.xg_allmodels($model,'alias').'-'.$v['id'].'" href="'.xg_url('content/content',['model'=>$model,'cid'=>$v['cid'],'id'=>$v['id']]).'">修改</a>';
				if($v['cid'])$data[$k]['menu'].='<a class="xg-fl xg-a" target="_blank" href="'.xg_content_url($v['cid'],$v['id']).'">预览</a>';
				$data[$k]['menu'].='<a class="xg-fl xg-a xg-a-status xg-a-status-'.$v['status'].' xg-ajax-get-status" href="'.xg_url('content/status',['model'=>$model,'id'=>$v['id']]).'">'.($v['status']?'停用':'启用').'</a>';
				if($v['status']===-1 or $v['status']==='-1'){
					$data[$k]['menu'].='<a class="xg-fl xg-a xg-ajax-get xg-ajax-restore xg-a-restore" href="'.xg_url('content/restore',['model'=>$model,'id'=>$v['id'],'status'=>1]).'">还原</a> <a class="xg-fl xg-a xg-a-del xg-ajax-get" href="'.xg_url('content/delete',['model'=>$model,'id'=>$v['id']]).'">彻底删除</a>';
				}elseif($status!==-1 and $status!=='all'){
					$data[$k]['menu'].='<a class="xg-fl xg-a xg-a-del xg-ajax-get" href="'.xg_url('content/softdelete',['model'=>$model,'id'=>$v['id']]).'">删除</a>';
				}
			}
			xg_jsonok(['data'=>$data,'pagehtml'=>$pagehtml]);
		}
		$catelist=xg_cate_select_cont(null,$model);
		$this->assign('model',$model);
		$this->assign('fields',$fields);
		$this->display(array('catelist'=>$catelist,'recomsets'=>$recomsets,'cates'=>$cates,'keywords'=>$keywords,'cid'=>$cid,'status'=>$status));
	}
	public function copy(){
		$model=xg_input('model');
		$ids=xg_input('id');
		if($ids){
			$this->copycont($ids);
		}
		xg_jsonok('复制成功');
	}
	public function copycont($ids){
		$model=xg_input('model');
		$data=xg_model($model,1)->where('id',$ids)->without('id,cmt,star,newstime')->select();
		$cids=[];
		foreach($data as $v){
			if($v['title']){
				$cids[]=$v['cid'];
				xg_model($model,1)->time('newstime')->insert($v);
			}
		}
		$cids=array_unique($cids);
		xg_model($model,1)->recount($cids);
	}
	public function focus(){
		$status=xg_input('status');
		$recomid=xg_input('recomid');
		if(XG_POST){
			$this->xswitch();
		}
		$recomid=xg_input('recomid/i');
		$infoid=xg_input('id/i');
		$cateid=xg_input('cid/i');
		if($cateid){
			$model=xg_category($cateid,'model');
		}else{
			$model=xg_input('model');
		}
		if(!$model)xg_error('没有模型名称');
		if($recomid){
			$where=xg_where()->where('id',$recomid);
		}elseif($infoid and $cateid){
			$model=xg_category($cateid,'model');
			$where=xg_where()->where('infoid',$infoid)->where('model',$model)->where('type','model');
		}
		if($where)$info=xg_model('recom')->where($where)->json('data')->value('data');
		if(!$info){
			$info=xg_model($model,1)->where('cid',$cateid)->fields('*')->find($infoid);
		}
		$this->display(['info'=>$info]);
	}
	public function newstime(){
		$time=xg_input('post.time',XG_TIME,'strtotime');
		$id=xg_input('id/i',0);
		$model=xg_input('model');
		xg_model($model,1)->where($id)->update(['newstime'=>$time]);
		xg_success('调整时间成功');
	}
	public function xswitch(){
		$switch=xg_input('switch');
		$status=xg_input('post.status',null);
		if(is_null($status))$status=xg_input('get.status');
		$model=xg_input('get.model');
		if($switch=='recom'){
			$infoid=xg_input('id/i');
			$cateid=xg_input('cid/i');
			$type=$model;
			$recom=xg_input('recom');
			$datas=['type'=>'model','model'=>$type,'recom'=>$recom,'infoid'=>$infoid,'cateid'=>$cateid];
			$data=xg_input('post.data');
			if($status){
				if(!$id=xg_model('recom')->where($datas)->value('id')){
					if($data)$datas['data']=$data;
					xg_model('recom')->json('data')->add(xg_merge($datas,['status'=>1,'order'=>0]));
				}else{
					if($data)$datas['data']=$data;
					xg_model('recom')->where($id)->json('data')->save(xg_merge($datas,['status'=>1]));
				}
				xg_jsonok(['msg'=>'已添加推荐','status'=>$status]);
			}else{
				xg_model('recom')->where($datas)->delete();
				xg_jsonok(['msg'=>'已删除推荐','status'=>$status]);
			}
		}
		if($switch=='nav'){
			$title=xg_input('title');
			$type=xg_input('type');
			$cateid=xg_input('cid/i');
			$infoid=xg_input('id/i');
			$level=xg_input('level/i',1);
			$data=['type'=>':'.$type,'infoid'=>$infoid,'cateid'=>$cateid];
			if($status){
				if(!$title)xg_error('请填写标题');
				if(!xg_model('nav')->where($data)->value('id'))xg_model('nav')->add($data+['title'=>$title,'level'=>1,'status'=>1,'order'=>0]);
				xg_jsonok(['msg'=>'已添加到导航栏','status'=>$status]);
			}else{
				xg_model('nav')->where($data)->delete();
				xg_jsonok(['msg'=>'已从导航栏删除','status'=>$status]);
			}
		}
	}
}
?>