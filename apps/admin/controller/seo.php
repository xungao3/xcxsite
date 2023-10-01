<?php
namespace apps\admin\controller;
class seo extends base{
	protected $sites=[];
	public function init(){
		parent::init();
		$data=xg_addons('submit');
		$sites=[];
		foreach($data as $v){
			$sites[$v['submit_info']['name']]=$v['submit_info']['title'];
		}
		$this->sites=$sites;
		$this->assign('sites',$sites);
	}
	public function sitemap(){
		if(XG_POST){
			$ids=xg_input('post.id');
			if(!$ids)xg_error('请选择分类');
			$sys=xg_input('request.sys','xg');
			$changefreq=xg_input('post.changefreq');
			$priority=xg_input('post.priority');
			$join=xg_input('post.join');
			$alone=xg_input('post.alone');
			$data=['sys'=>$sys];
			if($changefreq)$data['changefreq']=$changefreq;
			if($priority)$data['priority']=$priority;
			if(is_numeric($alone))$data['alone']=$alone;
			if(is_numeric($join))$data['join']=$join;
			foreach($ids as $id){
				if(xg_model('sitemap')->where('cid',$id)->where('sys',$sys)->value('cid')){
					xg_model('sitemap')->where('cid',$id)->where('sys',$sys)->update($data);
				}else{
					$data['cid']=$id;
					xg_model('sitemap')->where('cid',$id)->where('sys',$sys)->add($data);
				}
			}
			xg_success('保存成功');
		}
		$cates=xg_model('category')->alias('c')->join('sitemap as s','s.cid=c.id','left')->fields('s.changefreq,s.priority,s.join,s.alone,c.id,c.name,c.title')->order('c.id asc')->select();
		$this->display(['cates'=>$cates]);
	}
	public function manual(){
		$ids=xg_input('request.sid');
		$model=xg_input('request.model');
		$level=xg_input('request.level');
		$contids=xg_model('submit')->where('sid','in',$ids)->column('id','id');
		$res=xg_hooks('submit-manual')->run($model,$contids,$level)->data();
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
	public function submit(){
		$models=xg_models();
		$model=xg_input('get.model',array_keys($models)[0]);
		$subed=xg_input('get.subed');
		$unsub=xg_input('get.unsub');
		$site=xg_input('get.site');
		if(XG_AJAX){
			$page=xg_input('get.page',1);
			$pagesize=20;
			$where=xg_where()->where('model',$model);
			if($subed)$where->where('level_'.$subed,'>',0);
			if($unsub)$where->where('level_'.$unsub,'=',0);
			if($site)$where->where('site','=',$site);
			$count=xg_model('submit')->where($where)->count();
			$data=xg_model('submit')->where($where)->page($page,$pagesize)->time('level_1,level_2,level_3,level_4,level_5,level_6')->order('sid desc')->select();
			$ids=[];
			$where=xg_where();
			foreach($data as $k=>$v){
				if(!isset($ids[$v['cid']]))$ids[$v['cid']]=[];
				$ids[$v['cid']][]=$v['id'];
				$where->where_or(function($query)use($v){
					return $query->where('cid',$v['cid'])->where('id',$v['id']);
				});
			}
			$infos=xg_model('content')->where('cid',array_keys($ids))->where($where)->fields('cid,id,title')->select();
			foreach($data as $k=>$v){
				foreach($infos as $info){
					if($info['cid']==$v['cid'] and $info['id']==$v['id'])$data[$k]['title']=$info['title'];
				}
				$data[$k]['treepath']=xg_str(xg_category($v['cid'],'treepath'),'->');
				$data[$k]['cate']=xg_category($v['cid'],'title');
				$data[$k]['site']=$this->sites[$v['site']];
				$data[$k]['catelink']=xg_url('content/contents',['tbname'=>xg_category($v['cid'],'model'),'cid'=>$v['cid']]);
				$data[$k]['infolink']=xg_content_url($v['cid'],$v['id']);
			}
			$pagehtml=xg_pagehtml($count,$pagesize);
			xg_jsonok(['data'=>$data,'pagehtml'=>$pagehtml]);
		}
		$this->display(['models'=>$models,'model'=>$model,'subed'=>$subed,'unsub'=>$unsub,'site'=>$site]);
	}
	public function refresh(){
		$i=xg_input('get.i');
		$page=xg_input('get.page',1);
		$pagesize=1000;
		$model=xg_input('get.model');
		if($model){
			$models=xg_arr($model);
		}else{
			$models=array_keys(xg_models());
		}
		foreach($models as $modeli){
			$count=xg_model('content')->name($modeli)->count();
			$maxid=xg_model('submit')->where('model',$modeli)->max('id');
			$list=xg_model('content')->name($modeli)->fields("id,cid,'xg' as sys")->limit($pagesize)->where('id','>',$maxid)->order('id asc')->select();
			foreach($this->sites as $sitei=>$siten){
				foreach($list as $k=>$v){
					$list[$k]['model']=$modeli;
					$list[$k]['site']=$sitei;
				}
				xg_model('submit')->insert($list);
			}
		}
		if($count>($pcount=($page*$pagesize))){
			$rcount=$count-$pcount;
			$goto='?page='.($page+1);
			xg_success("已经刷新{$pcount}个，剩余{$rcount}...",$goto,false,0);
		}
		xg_success('刷新成功',xg_url('submit'));
	}
}
?>