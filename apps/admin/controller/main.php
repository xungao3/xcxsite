<?php
namespace apps\admin\controller;
class main extends base{
	public function main(){
		$models=xg_model('model')->where(['menu'=>1,'status'=>1])->column('name,alias','name');
		$this->assign('models',$models);
		return $this->display();
	}
	public function index(){
		$counts=[];
		$models=xg_models();
		foreach($models as $m){
			$count1=xg_model($m['name'],1)->where('status','=',1)->count();
			$count2=xg_model($m['name'],1)->where('status','!=',1)->count();
			$counts[]=['id'=>$m['id'],'title'=>$m['title'],'name'=>$m['name'],'type'=>$m['type'],'alias'=>$m['alias'],'count1'=>$count1,'count2'=>$count2];
		}
		$this->assign('counts',$counts);
		$this->assign('colors',[
			'#1f497d', '#4f81bd', '#c0504d', '#9bbb59', '#8064a2', '#4bacc6', '#f79646', '#7030a0',
			'#8db3e2', '#938953', '#548dd4', '#95b3d7', '#92cddc', '#17365d', '#366092', '#4f6128',
			'#953734', '#76923c', '#5f497a', '#31859b', '#0f243e', '#244061', '#632423', '#0070c0',
			'#3f3151', '#205867', '#974806', '#92d050', '#00b0f0', '#002060'
		]);
		return $this->display();
	}
}
?>