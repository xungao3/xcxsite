<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class controller{
	protected $view;
	protected $sys=null;
 	public function __construct() {
		if(!defined('XG_POST'))define('XG_POST',$_SERVER['REQUEST_METHOD']=='POST');
		if(!defined('XG_AJAX'))define('XG_AJAX',xg_isajax());
		if(XG_POST)xg_slog('ISPOST');
		xg_hooks('init')->run();
		$this->init();
	}
 	protected function init(){
	}
 	protected function display($tpl='',$data=array()) {
		$cont=$this->fetch($tpl,$data);
		$cont=xg_view_filter($cont);
		$cont=xg_restore_phptag($cont);
		if(xg_route()->cache){
			xg_cache_html($cont,xg_route()->cache);
		}
		xg_exit($cont);
	}
 	protected function fetch($tpl='',$data=array()) {
		if(is_array($tpl)){
			$data=$tpl;
			$tpl='';
		}
		$data['xg']['config']=xg_config('site');
		if($this->sys and strpos($tpl,'@')===false){
			$ctl=xg_input('get.controller');
			$act=xg_input('get.action');
			if($tpl){
				$tpla=xg_arr($tpl,'/');
				if(!$tpla[1]){
					$tpl=$ctl.'/'.$tpla[1];
				}
			}else{
				$tpl=$ctl.'/'.$act;
			}
			$tpl=$this->sys.'@'.$tpl;
		}
		return xg_view()->fetch($tpl,$data);
	}
 	protected function assign($name,$value='') {
		return xg_view()->assign($name,$value);
	}
}
?>