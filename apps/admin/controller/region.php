<?php
namespace apps\admin\controller;
class region extends base{
	function data(){
		$level1=xg_model('region')->where('level',1)->select();
		$level1ids=[];
		foreach($level1 as $k=>$v){
			if($v['status']){
				$level1ids[]=$v['id'];
			}
		}
		if($level1ids){
			$level2=xg_model('region')->where('pid',$level1ids)->select();
			$level2ids=[];
			foreach($level2 as $k=>$v){
				foreach($level1 as $k2=>$v2){
					if($v2['id']==$v['pid']){
						$level1=xg_splice($level1,[$v],$k2+1);
					}
				}
				if($v['status']){
					$level2ids[]=$v['id'];
				}
			}
		}
		if($level2ids){
			$level3=xg_model('region')->where('pid',$level2ids)->select();
			$level3ids=[];
			foreach($level3 as $k=>$v){
				foreach($level1 as $k2=>$v2){
					if($v2['id']==$v['pid']){
						$level1=xg_splice($level1,[$v],$k2+1);
					}
				}
			}
		}
		foreach($level1 as $k=>$v){
			if($v['level']==1){
				$level1[$k]['level']='<span class="xg-blue">省份</span>';
			}elseif($v['level']==2){
			$level1[$k]['name']='|--'.$level1[$k]['name'];
				$level1[$k]['level']='<span class="xg-green">城市</span>';
			}elseif($v['level']==3){
			$level1[$k]['name']='&nbsp;&nbsp;&nbsp;&nbsp;|--'.$level1[$k]['name'];
				$level1[$k]['level']='<span class="xg-red">区县</span>';
			}
			$level1[$k]['status']=intval($v['status']);
		}
		xg_jsonok(['data'=>$level1]);
	}
	function xswitch(){
		$id=xg_input('id/i',0);
		$status=xg_input('status/i',0);
		xg_model('region')->where($id)->update(['status'=>$status]);
		xg_jsonok(['msg'=>$status?'已经添加':'已经取消','status'=>$status]);
	}
	function region(){
		if(XG_AJAX){
			$this->data();
		}
		$this->display();
	}
}
?>