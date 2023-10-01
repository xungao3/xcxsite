<?php
namespace xgphp\model;

class times extends \xg\model{
	protected $name='times';
	protected $conn=null;
	public function check_times($type=1){
		$this->del_times();
		return ($this->where(array(array('ip','=',xg_ip()),array('type','=',$type)))->value('times')>=10);
	}
	public function del_times(){
		$this->where(array(array('time','<',(XG_TIME-60*60))))->delete();
	}
	public function add_times($type=1){
		if($times=$this->where(array(array('ip','=',xg_ip()),array('type','=',$type)))->value('times')){
			$this->where(array(array('ip','=',xg_ip()),array('type','=',$type)))->save(array('times'=>($times+1),'time'=>XG_TIME));
		}else{
			$this->insert(array('times'=>1,'time'=>XG_TIME,'ip'=>xg_ip(),'type'=>$type));
		}
	}
}