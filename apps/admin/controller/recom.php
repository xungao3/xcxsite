<?php
namespace apps\admin\controller;
class recom extends base{
	public function recom(){
		$recom=xg_input('recom');
		$cid=xg_input('cid');
		$sets=xg_recom_sets('cont');
		$model=xg_input('model');
		if(XG_AJAX){
			$page=xg_input('get.page',1);
			$where=[];
			if($model=='category'){
				$where[]=['type','=',$model];
			}elseif($model){
				$where[]=['model','=',$model];
			}
			if($recom)$where[]=['recom','=',$recom];
			if($cid)$where[]=['cateid','=',$cid];
			$pagesize=20;
			$count=xg_model('recom')->where($where)->count();
			$data=xg_model('recom')->where($where)->order('`order` asc')->page($page,$pagesize)->select();
			$pagehtml=xg_pagehtml($count,$pagesize);
			foreach($data as $k=>$v){
				$data[$k]['order']='<input class="xg-td-input xg-td-input-s order-input xg-px-2 xg-radius-3" type="number" name="order" value="'.$v['order'].'" data-id="'.$v['id'].'">';
				$data[$k]['menu']='';
				$data[$k]['recom']=$sets[$v['recom']]?$sets[$v['recom']]:$v['recom'];
				if($v['type']=='category'){
					$title=xg_category($v['cateid'],'treepath');
					$data[$k]['title']=xg_str($title,'->');
				}elseif($v['model']){
					$data[$k]['title']=xg_model($v['model'],1)->where($v['infoid'])->value('title');
				}
				$data[$k]['menu'].='<a class="xg-fl xg-a xg-a-del xg-ajax-get" href="'.xg_url('recom_del',['id'=>$v['id']]).'">删除</a>';
			}
			xg_jsonok(['data'=>$data,'pagehtml'=>$pagehtml]);
		}
		$catelist=xg_cate_select_cont();
		$this->assign('catelist',$catelist);
		$this->assign('model',$model);
		$this->assign('cid',$cid);
		$this->assign('sets',$sets);
		$this->assign('recom',$recom);
		$this->display();
	}
	public function recom_del(){
		$id=xg_input('id');
		xg_model('recom')->delete($id);
		xg_success('删除成功');
	}
	public function recom_order(){
		$id=xg_input('id/i',0);
		$order=xg_input('order/i',0);
		xg_model('recom')->where($id)->save(['order'=>$order]);
		xg_success();
	}
	public function nav_list(){
		$data=xg_nav_tree();
		$this->display(['data'=>$data]);
	}
	public function nav(){
		$id=xg_input('id/i');
		$type=xg_input('type');
		if(XG_POST){
			$data=xg_input('post.');
			if(!$data['title'])xg_error('请填写标题');
			if($id){
				xg_model('nav')->where($id)->save($data);
			}else{
				xg_model('nav')->add($data);
			}
			xg_success('保存成功');
		}
		if($id)$info=xg_model('nav')->find($id);
		$this->display(['info'=>$info,'id'=>$id,'type'=>$type]);
	}
	public function nav_del(){
		$id=xg_input('id');
		$fun=function($nid)use(&$fun){
			$ids=xg_model('nav')->where('pid',$nid)->column('id');
			foreach($ids as $id){
				$fun($id);
			}
			xg_model('nav')->where($nid)->delete();
		};
		$fun($id);
		xg_cache('xg-nav-data',null);
		xg_success('删除成功');
	}
	public function nav_order(){
		$ids=xg_input('ids');
		$ids=xg_arr($ids);
		$data='';
		$tbname=XG_TBPRE.'nav';
		foreach($ids as $i=>$id){
			$data.=" WHEN $id THEN $i ";
		}
		$ids=xg_str($ids);
		if($data)xg_db()->query("update $tbname set `order` = CASE id $data END WHERE id IN($ids);");
		xg_success('排序成功');
	}
}
?>