<?php
namespace apps\admin\controller;
class category extends base{
	public function category(){
		$id=xg_input('get.id/i',0);
		if($id)$info=xg_model('category')->find($id);
		if(XG_POST){
			$post=xg_input('post.');
			$data=array();
			if(!$post['title'])xg_error('请填写标题');
			if(!$post['model'])xg_error('请选择所属模型');
			if($post['pid'] and xg_category($post['pid'],'model')!=$post['model'])xg_error('父分类与所选模型不同');
			if($info and $info['model']!=$post['model'] and xg_model($info['model'])->where('cid',$id)->count())xg_error('此分类下已经有内容<br>请先处理内容');
			$data['title']=$post['title'];
			$data['name']=$post['name'];
			$data['pid']=$post['pid'];
			$data['model']=$post['model'];
			$data['status']=$post['status'];
			$data['conttpl']=$post['conttpl'];
			$data['catetpl']=$post['catetpl'];
			$data['description']=$post['description'];
			if($id){
				xg_model('category')->time('updatetime')->update($data,$id);
			}else{
				$id=xg_model('category')->time('createtime,updatetime')->insert($data);
			}
			xg_cache_group('category',null);
			xg_success(array('msg'=>'提交成功'));
		}else{
			$catelist=xg_cate_select_cate();
			$models=xg_models();
			$catetpl=[];
			$conttpl=[];
			foreach($models as $v){
				if($v['type']!=1)continue;
				$model[$v['name']]=$v['title'];
				$catetpl['category/'.$v['name']]='使用[category/'.$v['name'].'.html]';
				$conttpl['content/'.$v['name']]='使用[content/'.$v['name'].'.html]';
			}
			foreach($models as $v){
				//$catetpl['home%category/'.$v['name']]='强制使用HOME模块[category/'.$v['name'].'.html]';
				//$conttpl['home%content/'.$v['name']]='强制使用HOME模块[content/'.$v['name'].'.html]';
			}
			$files=xg_filenames(XG_APPS.'/home/view/category',0);
			foreach($files as $file){
				$catetpl['category/'.$file]='使用[category/'.$file.']';
			}
			foreach($files as $file){
				//$catetpl['home%category/'.$file]='强制使用HOME模块[category/'.$file.']';
			}
			$files=xg_filenames(XG_APPS.'/home/view/content',0);
			foreach($files as $file){
				$conttpl['content/'.$file]='使用[content/'.$file.']';
			}
			foreach($files as $file){
				//$conttpl['home%content/'.$file]='强制使用HOME模块[content/'.$file.']';
			}
			foreach(xg_files(XG_THEMES,2) as $dir){
				$theme=xg_theme_config(basename($dir));
				if($theme['name']!=basename($dir))continue;
				foreach(xg_filenames($dir.'/view/category','html') as $file){
					$filename='category/'.basename($file);
					//$catetpl[$theme['name'].'!'.$filename]='强制使用主题'.$theme['title'].'['.$filename.']';
				}
				foreach(xg_filenames($dir.'/view/content','html') as $file){
					$filename='content/'.basename($file);
					//$conttpl[$theme['name'].'!'.$filename]='强制使用主题'.$theme['title'].'['.$filename.']';
				}
			}
			$this->assign('catetpl',$catetpl);
			$this->assign('conttpl',$conttpl);
			$this->assign('info',$info);
			$this->assign('model',$model);
			$this->assign('catelist',$catelist);
			$this->display();
		}
	}
	public function moveto(){
		$toid=xg_input('cid');
		if(!$toid){
			$this->display(['cates'=>xg_cate_select_cate(null,$model)]);
		}
		$ids=xg_input('id');
		$model=xg_input('model');
		xg_model($model,1)->where('id','in',$ids)->update(['cid'=>$toid]);
		xg_success('移动成功');
	}
	public function delete(){
		$cid=xg_input('get.id');
		$model=xg_category($cid,'model');
		$count=xg_model($model,1)->where(['cid'=>$cid])->count();
		if($count>0){
			if($confirm=xg_confirm('此分类有内容，确定删除？')){
				xg_error($confirm);
			}
		}
		xg_model('category')->delete($cid);
		xg_success('删除成功');
	}
	public function order(){
		$id=xg_input('id/i',0);
		$order=xg_input('order');
		xg_model('category')->where($id)->update(['order'=>$order]);
		xg_success();
	}
	public function xswitch(){
		$switch=xg_input('switch');
		$status=xg_input('status');
		if($switch=='recom'){
			$infoid=xg_input('id/i');
			$type='category';
			$recom=xg_input('recom');
			$data=['type'=>$type,'recom'=>$recom,'cateid'=>$infoid];
			if($status){
				if(!$id=xg_model('recom')->where($data)->value('id')){
					xg_model('recom')->add($data+['status'=>1,'order'=>0]);
				}else{
					xg_model('recom')->where($id)->save(['status'=>1]);
				}
				xg_jsonok(['msg'=>'已添加推荐','status'=>$status]);
			}else{
				xg_model('recom')->where($data)->delete();
				xg_jsonok(['msg'=>'已删除推荐','status'=>$status]);
			}
		}
		if($switch=='nav'){
			$title=xg_input('title');
			$type=xg_input('type');
			$infoid=xg_input('id/i');
			$level=xg_input('level/i',1);
			$data=['type'=>$type,'infoid'=>$infoid];
			if($status){
				$fun=function($infoid,$pid=0,$level=1)use(&$fun){
					$cate=xg_category($infoid);
					$data=['type'=>'category','infoid'=>$infoid];
					if(!$nid=xg_model('nav')->where($data)->value('id'))$nid=xg_model('nav')->add($data+['title'=>$cate['title'],'level'=>$level,'pid'=>$pid,'status'=>1,'order'=>0]);
					$direct=$cate['son'];
					foreach($direct as $id){
						$fun($id,$nid,$level+1);
					}
				};
				$fun($infoid);
				xg_jsonok(['msg'=>'已添加到导航栏','status'=>$status]);
			}else{
				$fun=function($infoid,$pid=0)use(&$fun){
					if(!$pid){
						$data=['type'=>'category','infoid'=>$infoid];
					}else{
						$data=['pid'=>$pid];
					}
					$ids=xg_model('nav')->where($data)->column('id');
					foreach($ids as $id){
						$fun(0,$id);
					}
					xg_model('nav')->where($data)->delete();
				};
				$fun($infoid);
				xg_jsonok(['msg'=>'已从导航栏删除','status'=>$status]);
			}
		}
	}
	public function categories(){
		$this->assign('nav',xg_model('admin/nav')->category());
		$this->assign('recom',xg_model('admin/recom')->category());
		$data=xg_model('category')->select();
		$this->assign('list',xg_cate_children($data));
		$this->display();
	}
}
?>