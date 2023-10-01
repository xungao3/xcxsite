<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class route{
	protected $rules=[];
	protected $url='';
	protected $info=null;
	protected $data=null;
	protected $fun=null;
	public $cache=null;
	protected $cacheset=null;
	public function __construct(){
		$this->defapp=(defined('XG_BIND_APP')?XG_BIND_APP:'home');
		$this->defctl='index';
		$this->defact='index';
		$this->appmap=xg_config('app.map');
		if(XG_ROOT=='/'){
			$len=0;
		}else{
			$len=strlen(XG_ROOT)-1;
		}
		$this->xg=substr($_GET['xg'],$len);
		$url=xg('url');
		$baseurl=xg('baseurl');
		if($this->xg){
			$mode=1;
		}else{
			$mode=0;
		}
		define('XG_ROUTE',$mode);
		unset($_GET['xg'],$_REQUEST['xg']);
	}
	protected function init(){
		if(file_exists(XG_APPS.'/route.php'))require XG_APPS.'/route.php';
		foreach(xg_files(XG_PATH.'/route','php',1) as $file){
			require $file;
		}
		$this->xg=str_replace('.html','',$this->xg);//preg_replace('/[^a-zA-Z0-9_\/\-\.]*/','',str_replace('.html','',$this->xg));
		$url=$this->xg;
		if($match=$this->match('redirect',$url)){
			xg_redirect($match['url'],$match['moved']);
		}elseif($_POST and $match=$this->match('post',$url)){
			return $match;
		}elseif($match=$this->match('get',$url)){
			return $match;
		}elseif($match=$this->match('request',$url)){
			return $match;
		}else{
			return [];
		}
	}
	public function app(){
		return $this->data()[0];
	}
	public function cache($path){
		$this->cacheset=$path;
		return $this;
	}
	public function ctl(){
		return $this->data()[1];
	}
	public function act(){
		return $this->data()[2];
	}
	public function fun(){
		return $this->fun;
	}
	public function data(){
		if($this->data)return $this->data;
		if(xg_config('config.route')){
			if(is_null($this->info))$this->info=$this->init();
			if($this->info)$this->xg='/'.$this->info[0].'/'.$this->info[1].'/'.$this->info[2];
		}
		if($this->xg){
			if($this->xg=='/'){
				$this->xg="$this->defapp/$this->defctl/$this->defact";
			}else{
				$this->xg=trim($this->xg,'/');
			}
			if(count(explode('/',$this->xg))==1){
				$this->xg=$this->xg."/$this->defctl/$this->defact";
			}elseif(count(explode('/',$this->xg))==2){
				$this->xg="/$this->defapp/".$this->xg;
			}elseif(count(explode('/',$this->xg))==3){
				$this->xg="/".$this->xg;
			}
			list($app,$ctl,$act)=explode('/',trim($this->xg,'/'));
		}else{
			if(!$app=preg_replace('/[^a-zA-Z0-9_]*/','',$_GET['xgapp'])){
				$app=$this->defapp;
			}
			if(!$ctl=preg_replace('/[^a-zA-Z0-9_]*/','',$_GET['xgctl'])){
				$ctl=$this->defctl;
			}
			if(!$act=preg_replace('/[^a-zA-Z0-9_]*/','',$_GET['xgact'])){
				$act=$this->defact;
			}
			$this->xg='/'.implode('/',array($app,$ctl,$act));
		}
		if($this->appmap&&xg_in_array($app,$this->appmap) and $app!=$this->appmap[$app])xg_halt('无此模块！<br>'.$app,404);
		if($this->appmap[$app])$app=$this->appmap[$app];
		$this->data=[$app,$ctl,$act];
		if($this->info[3] and xg_isfun($this->info[3]))$this->fun=$this->info[3];
		if($this->info[4])$this->cache=$this->info[4];
		return $this->data;
	}
	public function match($type,$url){
		$rules=$this->rules[$type];
		foreach($rules as $v){
			if(preg_match('/'.$v['rule'].'/i',$url,$rt)){
				$arr=xg_arr(trim($v['url'],'/'),'/');
				$app=$this->defapp;
				$ctl=$this->defctl;
				$act=$this->defact;
				if(!$v['app'] and !$v['ctl'] and !$v['act']){
					if(count($arr)>=1)$act=array_pop($arr);
					if(count($arr)>=1)$ctl=array_pop($arr);
					if(count($arr)>=1)$app=array_pop($arr);
				}else{
					if($v['app'])$app=$v['app'];
					if($v['ctl'])$ctl=$v['ctl'];
					if($v['act'])$act=$v['act'];
				}
				if($v['fun'])$fun=$v['fun'];
				if($v['cache'])$cache=$v['cache'];
				$route=[$app,$ctl,$act,$fun,$cache];
				if($v['param'])$_GET=xg_merge($_GET,$v['param']);
				foreach($v['tags'] as $k2=>$v2){
					if(is_array($v2) and xg_isfun($v2[1])){
						$_GET[$v2[0]]=$v2[1]($this,$rt[$k2+1]);
					}else{
						$_GET[$v2]=$rt[$k2+1];
					}
				}
				if($type=='redirect')return $v;
				return $route;
			}
		}
	}
	protected function set($rule,$url,$param=[],$type='request',$ext=null){
		if(is_array($rule))extract($rule,EXTR_OVERWRITE);
		if(!$rule)return;
		$rule=str_replace(array('(',')',' ','\\','/','-','.'),array('','','','/','\/','\-','\.'),$rule);
		if(xg_isfun($url)){
			$fun=$url;
			$url=null;
		}
		$tags=[];
		if(preg_match('/<([a-z0-9_]+)>/i',$rule))$rule=preg_replace_callback('/<([a-z0-9_]+)>/i',function($v)use(&$param,&$tags){
			$v=$v[1];
			if($param[$v] and is_string($param[$v])){
				$rt='('.trim($param[$v],' ()').')';
				$tags[]=$v;
				return $rt;
			}elseif(xg_isfun($param[$v])){
			    $fun=$param[$v];
				$tags[]=[$v,$fun];
				unset($param[$v]);
				return '(.*+)';
			}else{
				return '(.*+)';
			}
		},$rule);
		// if(preg_match('/\[([dw])\]/',$rule))$rule=preg_replace_callback('/\[([dw])\]/i',function($v)use(&$param,&$tags){
		// 	$v=$v[1];
		// 	return "(\\{$v}+)";
		// },$rule);
		if($rule)$this->rules[$type][]=[
			'rule'=>$rule,'url'=>$url,'ext'=>$ext,
			'app'=>$app,'ctl'=>$ctl?$ctl:$controller,'act'=>$act?$act:$action,
			'moved'=>$moved,'fun'=>$fun,'cache'=>$this->cacheset,
			'param'=>$param,'tags'=>$tags
		];
		$this->cacheset=null;
		return $this;
	}
	public function get($rule,$url=null,$param=[],$ext=null){
		return $this->set($rule,$url,$param,'get',$ext);
	}
	public function post($rule,$url=null,$param=[]){
		return $this->set($rule,$url,$param,'post');
	}
	public function request($rule,$url=null,$param=[]){
		return $this->set($rule,$url,$param,'request');
	}
	public function redirect($rule,$url=null,$moved=false){
		return $this->set($rule,$url,$moved,'redirect');
	}
}
?>