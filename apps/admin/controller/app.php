<?php
namespace apps\admin\controller;
class app extends base{
	function cache(){
		if(XG_AJAX){
			$page=xg_input('get.page',1);
			$type=xg_input('get.type');
			$pagesize=20;
			$where=xg_where();
			if($type){
				if($type=='content'){
					$systems=xg_config('sys');
					foreach($systems as $sys=>$v){
						$models=xg_sys($sys)->models();
						foreach($models as $model=>$v2){
							$where->where_or(function($where)use($sys,$model){
								return $where->where('sys',$sys)->where('type',$model);
							});
						}
					}
				}else{
					$where->where('type',$type);
				}
			}
			$count=xg_model('cache_file')->where($where)->count();
			$data=xg_model('cache_file')->where($where)->page($page,$pagesize)->time('updatetime')->order('updatetime desc')->select();
			$ids=[];
			$where=xg_where();
			$systems=[];
			foreach($data as $k=>$v){
				$sys=$v['sys'];
				if(!$systems[$sys])$systems[$sys]=xg_sys($sys);
				$data[$k]['sysname']=xg_config("sys.$sys.title");
				if($sys=='xg'){
					$data[$k]['cate']=xg_str($systems[$sys]->cates()[$v['cateid']]['treepath'],'->');
				}else{
					$data[$k]['cate']=$systems[$sys]->cates()[$v['cateid']]['title'];
				}
			}
			$pagehtml=xg_pagehtml($count,$pagesize);
			xg_jsonok(['data'=>$data,'pagehtml'=>$pagehtml]);
		}
		$this->display();
	}
	function appdata(){
		$name=xg_input('get.name');
		$name=str_replace(['/','.','\\'],'',$name);
		if(XG_POST){
			$post=xg_input('post.');
			xg_model('app_list')->where('name',$name)->json('data')->update(['data'=>$post]);
			xg_success('保存成功');
		}
		$data=xg_model('app_list')->where('name',$name)->json('data')->value('data');
		$this->assign('data',$data);
		$this->assign('name',$name);
		$this->display('app/data/'.$name);
	}
	function appset(){
		$id=xg_input('get.id/i',0);
		if(XG_POST){
			$post=xg_input('post.');
			if($id){
				xg_model('app_list')->where($id)->update($post);
			}else{
				xg_model('app_list')->add($post);
			}
			xg_success('保存成功');
		}
		if($id)$app=xg_model('app_list')->find($id);
		$this->assign('app',$app);
		$this->display();
	}
	function applist(){
		$applist=xg_model('app_list')->select();
		$this->assign('applist',$applist);
		$tpls=xg_filenames(XG_APPS.'/admin/view/app/data/');
		foreach($tpls as $key=>$tpl){
			$tpls[$key]=substr($tpl,0,-5);
		}
		$this->display(['tpls'=>$tpls]);
	}
	function clear(){
		$page=xg_input('get.page',1);
		$ids=xg_input('request.id',[]);
		$type=xg_input('get.type');
		$where=xg_where();
		if($type){
			if($type=='content'){
				$systems=xg_config('sys');
				foreach($systems as $sys=>$v){
					$models=xg_sys($sys)->models();
					foreach($models as $model=>$v2){
						$where->where_or(function($where)use($sys,$model){
							return $where->where('sys',$sys)->where('type',$model);
						});
					}
				}
			}else{
				$where->where('type',$type);
			}
		}
		if($ids)$where->where('id',$ids);
		$where2=clone $where;
		$pagesize=5000;
		$count=xg_model('cache_file')->where($where)->count();
		$list=xg_model('cache_file')->where($where)->fields("type,id,file")->page($page,$pagesize)->order('id asc')->select();
		$dcount=min($page*$pagesize,$count);
		$rcount=$count-$dcount;
		if($rcount>=0 and $list){
			$goto=xg_url('clear?page='.($page+1).'&type='.$type);
			foreach($list as $v){
				unlink(XG_PUBLIC.$v['file']);
			}
			xg_success("已删除{$dcount}条，剩余{$rcount}...",$goto,false,0);
		}
		xg_model('cache_file')->where($where2)->delete();
		xg_success('删除成功');
	}
}
?>