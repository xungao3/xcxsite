<?php
namespace apps\home\model;

class member extends \xg\model{
	protected $name='member';
	public function has_mobile($mobile,$userid=0){
		$where=[];
		$where[]=['mobile','=',$mobile];
		if($userid)$where[]=['userid','!=',$userid];
		return !!$this->where($where)->count();
	}
	public function has_username($username){
		return !!$this->where('username',$username)->count();
	}
}