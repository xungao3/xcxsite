<?php
namespace apps\install\controller;

class base extends \apps\base{
	protected function config($name=''){
		if($name){
			$v=xg_model('config')->where('name',$name)->value('value');
			return $v;
		}
	}
}
?>