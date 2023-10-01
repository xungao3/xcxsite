<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class hooks implements \IteratorAggregate{
	protected $app=null;
	protected $default=null;
	protected $name=null;
	protected $once=false;
	protected $data=[];
	protected static $hooks=null;
	protected static $addons=null;
 	public function __construct($name,$app=null){
		if(is_null($app) and defined('XG_APP')){
			$app=XG_APP;
		}
		$this->name=$name;
		$this->app=$app;
	}
	public function getIterator(){
		return new ArrayIterator($this->data);
	}
	function once(){
		$this->once=true;
		$args=func_get_args();
		$name=$this->name;
		return $this->exec($name,$args);
	}
	function def($default){
		$this->default=$default;
		return $this;
	}
	function last(){
		if($count=count($data=$this->data))return end(array_values($data));
		return $this->default;
	}
	function run(){
		$args=func_get_args();
		$name=$this->name;
		return $this->exec($name,$args);
	}
	function args($args){
		$name=$this->name;
		return $this->exec($name,$args);
	}
	function data(){
		return $this->data;
	}
	function exec($name,&$args){
		$hooks=$this->hooks();
		$app=$this->app;
		$name=$this->name;
		$once=$this->once;
		if(!is_null($hooks)){
			$classes=xg_extend($hooks['common'][$name],$hooks[$app][$name]);
			$classes=xg_extend($classes,xg('hooks.common.'.$name),xg('hooks.'.$app.'.'.$name));
			if($classes){
				$return=[];
				$argcount=count($args);
				foreach($classes as $key=>$class){
					if(!is_array($args)){
						$args2=[&$args];
					}else{
						$args2=&$args;
					}
					$args2[$argcount]=$return;
					$args2[$argcount+1]=$prev;
					if(xg_isclosure($class)){
						$run=call_user_func_array($class,$args2);
					}elseif(is_array($class)){
						foreach($class as $cls){
							if(xg_isclosure($cls)){
								$run=call_user_func_array($cls,$args2);
							}elseif(is_string($cls) and class_exists($cls)){
								$obj=new $cls();
								if($name=$this->method_exists($obj,$name)){
									$run=call_user_func_array([$obj,$name],$args2);
								}
							}
						}
					}elseif(class_exists($class)){
						$obj=new $class();
						if($name=$this->method_exists($obj,$name)){
							$run=call_user_func_array([$obj,$name],$args2);
						}
					}
					if(!is_null($run)){
						$prev=$run;
						$return[$key]=$run;
						$this->data=$return;
						if($once)return $this;
					}
				}
			}
		}
		return $this;
	}
	function hooks(){
		if(!file_exists(XG_DATA.'/installed'))return null;
		$return=self::$hooks;
		if(is_null($return)){
			$return=array();
			if(is_null($addons=self::$addons))$addons=self::$addons=xg_model('addons')->runhook(0)->fields('hooks,name')->where('status',1)->cache('hooks')->select();
			if($addons){
				foreach($addons as $addon){
					$name=$addon['name'];
					$arr=xg_jsonarr($addon['hooks']);
					foreach($arr as $app=>$classes){
						foreach($classes as $class=>$hooks){
							foreach($hooks as $hook){
								$return[$app][$hook][$name]=$class;
								$return[$app][xg_to_camel($hook)][$name]=$class;
								$return[$app][xg_to_under($hook)][$name]=$class;
								$return[$app][str_replace('_','-',xg_to_under($hook))][$name]=$class;
								$return[$app][xg_to_camel($hook,0)][$name]=$class;
							}
						}
					}
				}
			}
		}
		return $return;
	}
	function __tostring(){
		return '';
	}
	function method_exists($obj,$name){
		if(method_exists($obj,$name=xg_to_camel($name)))return $name;
		if(method_exists($obj,$name=xg_to_under($name)))return $name;
		if(method_exists($obj,$name=xg_to_camel($name,false)))return $name;
		return false;
	}
}
?>