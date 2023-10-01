<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\traits;
trait addons{
	public function execute(){
		$addon=xg_input('get.addon',XG_APP,'xg_filter_point');
		$controller=xg_input('get.controller','index','xg_filter_point');
		$action=xg_input('get.action','index','xg_filter_point');
		$addons=xg_model('addons')->where('status',1)->cache('addons')->column('*','name');
		if(!$addons[$addon])return;
		if(class_exists($class="\\addons\\$addon\\controller\\$controller") or class_exists($class="\\addons\\$addon\\controller")){
			$object=new $class();
			if(method_exists($object,$action))return $object->$action();
		}
		return '';
	}
}
?>