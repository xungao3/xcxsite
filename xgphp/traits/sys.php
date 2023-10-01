<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\traits;
trait sys{
	public function execute(){
		$sys=xg_input('get.sys');
		$controller=xg_input('get.controller','index','xg_filter_point');
		$action=xg_input('get.action','index','xg_filter_point');
		if(!xg_config('sys.'.$sys))return '没有此系统信息';
		if(class_exists($class="\\sys\\$sys\\controller\\$controller") or class_exists($class="\\sys\\$sys\\controller")){
			$object=new $class();
			if(method_exists($object,$action))return $object->$action();
		}
		return '';
	}
}
?>