<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class model extends database{
	public function __construct($conf=null){
		if(is_string($conf)){
			if(!$this->name and $conf!='content')$this->name=$conf;
			$conf=null;
		}
		if(!$conf)$conf=xg_config('database');
		parent::__construct($conf);
		$this->init();
	}
	public function init(){
		
	}
	public function update_status($where,$status=null){
		if(is_object($where) and get_class($where)=='xg\where'){
			$where=$where->get();
		}else{
			$id=$where;
			$where=new \xg\where();
			if(is_array($id)){
				$where->where('[pk]','in',$id);
			}else{
				$where->where('[pk]','=',(int)$id);
			}
			$where=$where->get();
		}
		if(is_null($status))$status=($this->where($where)->value('status')?0:1);
		if($this->where($where)->update(['status'=>$status]))return $status;
		return false;
	}
}
?>