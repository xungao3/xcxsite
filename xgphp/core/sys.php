<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class sys{
	public $type=null;
	public $name=null;
	public $cache=1;
	public $page=1;
	public $pagesize=24;
	public $where=null;
	public $needuid=0;
	function __construct(){
		if(xg_input('appname')=='preview')$this->cache=false;
		$this->needuid=xg_input('needuid');
		if($this->needuid)$this->cache=0;
		if(!XG_ROUTE)$this->cache=0;
		$this->where=xg_where();
	}
	function cache($data){
		if(!$data['file'])return;
		if($data['type']=='content')$data['type']=$data['model'];
		$id=xg_model('cache_file')->where('file',$data['file'])->value('id');
		if($id){
			return xg_model('cache_file')->where($id)->time('update_time')->save($data);
		}else{
			return xg_model('cache_file')->time('update_time')->add($data);
		}
	}
	function save($cont,$key=''){
		$file=$this->path();
		mkdir(dirname($file),0755,true);
		$cont=xg_jsonstr($cont);
		if($key)xg_encode($cont,$key);
		if($this->cache)xg_fcont($file,$cont);
		return $cont;
	}
	function load($key=''){
		$path=$this->path();
		if(file_exists($path))$cont=xg_fcont($path);
		if($cont and $key)xg_decode($cont,$key);
		if($cont)return xg_jsonarr($cont);
	}
	function path(){
		return XG_PUBLIC.$this->file();
	}
	function file(){
		$md5=$this->md5($this->name,$this->type);
		$file='/data/'.$this->sys.'/'.$this->type.'/'.$md5[0].'/'.$md5[1].'.json';
		return $file;
	}
	function md5(){
		$md5=md5($this->type.'-'.$this->name);
		return [substr($md5,0,2),substr($md5,2)];
	}
	function count($pagesize){
		$this->page=1;
		$this->pagesize=$pagesize;
		return $this;
	}
	function where($p1,$p2=null,$p3=null,$join='and'){
		$this->where->where($p1,$p2,$p3,$join);
		return $this;
	}
	function page($page,$pagesize){
		$this->page=$page;
		$this->pagesize=$pagesize;
		return $this;
	}
}
?>