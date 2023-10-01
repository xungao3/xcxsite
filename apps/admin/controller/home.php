<?php
namespace apps\admin\controller;
class home extends base{
	public function theme_reload(){
		$this->theme_install();
	}
	public function theme_status(){
		$name=xg_input('name');
		$app=xg_input('app');
		$status=xg_input('status');
		xg_model('theme')->where('name','!=',$name)->save(['status'=>0]);
		xg_model('theme')->where('name',$name)->save(['status'=>$status]);
		if($status){
			$data=['value'=>$name];
		}else{
			$data=['value'=>''];
		}
		xg_model('config')->where('name',$app.'-theme')->update($data);
		xg_cache('site_config',null);
		xg_success('设置完成');
	}
	public function theme_install(){
		$name=xg_input('name');
		$app=xg_input('app');
		$data=xg_theme_config($name);
		if($data['name']!=$name)xg_error('主题名称错误');
		$data['status']=1;
		if(xg_model('theme')->where('name',$name)->value('id')){
			xg_model('theme')->where('name',$name)->save($data);
		}else{
			xg_model('theme')->add($data);
		}
		xg_model('config')->where('name',$app.'-theme')->update(['value'=>$name]);
		xg_cache('site_config',null);
		xg_deldir(XG_PUBLIC.'/static/themes/'.$name);
		xg_copydir(XG_PATH.'/themes/'.$name.'/files',XG_PUBLIC.'/static/themes/'.$name);
		xg_copydir(XG_PATH.'/themes/'.$name.'/static',XG_PUBLIC.'/static/themes/'.$name);
		xg_success('处理完成');
	}
	public function theme_uninstall(){
		$name=xg_input('name');
		$app=xg_input('app');
		xg_model('theme')->where('name',$name)->delete();
		xg_model('config')->where('name',$app.'-theme')->update(['value'=>'']);
		xg_cache('site_config',null);
		xg_deldir(XG_PUBLIC.'/static/themes/'.$name);
		xg_success('处理完成');
	}
	public function cache_delete(){
		$id=xg_input('id/i',0);
		$type=xg_input('get.type');
		if($type=='cates'){
			$where=xg_where()->where([['contid','=',0],['cateid','!=',0]]);
		}elseif($type=='conts'){
			$where=xg_where()->where([['contid','!=',0],['cateid','!=',0]]);
		}elseif($type=='selected'){
			$where=xg_where()->where('id','in',$id);
		}elseif($id){
			$file=xg_model('html_file')->where($id)->value('file');
			@unlink(XG_PUBLIC.$file);
			xg_model('html_file')->delete($id);
			xg_success('删除成功');
		}
		if($where)$files=xg_model('html_file')->where($where)->values('file');
		if($files){
			foreach($files as $file){
				@unlink(XG_PUBLIC.$file);
			}
			xg_model('html_file')->where($where)->delete();
			xg_success('删除成功');
		}
		xg_success('没有删除任何文件');
	}
	public function cache(){
		$cate=xg_input('get.cate/i',0);
		$keywords=xg_input('get.keywords');
		if(XG_AJAX){
			$page=xg_input('get.page/i',1);
			$pagesize=20;
			$where=xg_where();
			if($cate)$where->where('cateid',$cate);
			if($keywords){
				$conts=xg_model('content')->where('cid',$cate)->fields('title,cid,id')->search($keywords);
				$where->where(function($q)use($conts){
					foreach($conts as $cont){
						$q->where_or([['cateid','=',$cont['cid']],['contid','=',$cont['id']]]);
					}
					return $q;
				});
			}
			$data=xg_model('html_file')->where($where)->order('time desc')->page($page,$pagesize)->select();
			foreach($data as $k=>$v){
				$cate=xg_category($v['cateid'],'treepath');
				$cate=xg_str($cate,'->');
				$data[$k]['cate']=$cate;
				$data[$k]['exist']=file_exists(XG_PUBLIC.$v['file']);
				if($v['contid']){
					$cont=xg_model('content')->where('cid',$v['cateid'])->field('title')->content($v['contid']);
					$data[$k]['cont']=$cont['title'];
				}
				$menu='';
				$menu.='<a class="xg-a" href="'.str_replace('/index.html','/',$v['file']).'" target="_blank">预览</a>';
				$menu.='<a class="xg-a xg-ajax-get xg-a-del" xg-table-reload="table-1" href="'.xg_url('cache_delete',['id'=>$v['id']]).'">删除</a>';
				$data[$k]['menu']=$menu;
			}
			xg_jsonok(['data'=>$data]);
		}
		$this->display(['cate'=>$cate,'keywords'=>$keywords]);
	}
	public function theme(){
		$dirs=glob(XG_PATH.'/themes/*',GLOB_ONLYDIR);
		$insed=xg_model('theme')->column('name,status','name');
		$themes=array();
		foreach($dirs as $k=>$v){
			$name=basename($v);
			$data=xg_jsonarr(xg_fcont($v.'/config.json'));
			if($data['name']!=$name)continue;
			if($insed[$name]){
				$data['status']=$insed[$name]['status'];
				$data['insed']=1;
			}
			$themes[]=$data;
		}
		$this->display('theme',['themes'=>$themes]);
	}
}
?>