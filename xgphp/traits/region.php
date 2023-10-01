<?php
namespace xg\traits;
trait region{
	function region(){
		$pid=xg_input('pid/i',0);
		$status=xg_input('status/i',null);
		$where=xg_where()->where('pid',$pid);
		if(!is_null($status))$where->where('status',$status);
		$data=xg_model('region')->where($where)->select();
		xg_jsonok(['data'=>$data]);
	}
}