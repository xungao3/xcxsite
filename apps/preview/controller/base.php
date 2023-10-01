<?php
namespace apps\preview\controller;
class base extends \apps\admin\controller\base{
	function init(){
		$this->checklogin();
	}
}