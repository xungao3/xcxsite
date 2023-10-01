<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class view{
	protected $conf=array();
	protected $data=array();
	protected $runhook=1;
	public function __construct($conf=null){
		$this->config($conf);
		if(!$this->conf['temp'])xg_sys_error('tpl save path not defined!');
		//if($this->conf['path'] and (!file_exists($this->conf['path']) or !is_dir($this->conf['path'])))xg_sys_error('tpl dir not exist!',$tpl);
		$this->config('path',xg_replace_path($this->conf['path']));
		$this->config('temp',xg_replace_path($this->conf['temp']));
	}
	public function config($name,$value=null){
		if(is_array($name)){
			$this->conf=xg_merge($this->conf,$name);
		}elseif($value){
			$this->conf[$name]=$value;
		}elseif($name){
			return $this->conf[$name];
		}
		return $this->conf;
	}
	public function runhook($runhook){
		$this->runhook=$runhook;
		return $this;
	}
	public function fetch($tpl,$xg_view_data=array()){
		if(!$tpl)$tpl=XG_CTL.'/'.XG_ACT;
		$xg_view_data=$this->data=xg_merge($this->data,$xg_view_data,true);
		extract($xg_view_data,EXTR_OVERWRITE);
		ob_start();
		require $this->tpl_parse($tpl);
		$rt=ob_get_contents();
		ob_end_clean();
		if($this->runhook)$rt=xg_hooks('fetch','view')->def($rt)->run($rt)->last();
		return $rt;
	}
	protected function tpl_cont($tpl){
		return xg_fcont($this->tpl_path($tpl));
	}
	protected function tpl_path($tpl){
		if(($pos=strpos($tpl,'#'))!==false){
			$tpl=XG_ADDONS.'/'.substr($tpl,0,$pos).'/view/'.substr($tpl,$pos+1);
			$tpl2=XG_ADDONS.'/'.substr($tpl,0,$pos).'/'.substr($tpl,$pos+1);
		}elseif(($pos=strpos($tpl,'@'))!==false){
			$tpl=XG_SYS.'/'.substr($tpl,0,$pos).'/view/'.substr($tpl,$pos+1);
			$tpl2=XG_SYS.'/'.substr($tpl,0,$pos).'/'.substr($tpl,$pos+1);
		}elseif(($pos=strpos($tpl,'%'))!==false){
			$tpl=XG_APPS.'/'.substr($tpl,0,$pos).'/view/'.substr($tpl,$pos+1);
			$tpl2=XG_APPS.'/'.substr($tpl,0,$pos).'/'.substr($tpl,$pos+1);
		}elseif(($pos=strpos($tpl,'!'))!==false){
			$tpl=XG_THEMES.'/'.substr($tpl,0,$pos).'/view/'.substr($tpl,$pos+1);
			$tpl2=XG_THEMES.'/'.substr($tpl,0,$pos).'/'.substr($tpl,$pos+1);
		}elseif(substr($tpl,0,1)!='/'){
			$arr=xg_arr($tpl,'/');
			if(count($arr)==1)array_unshift($arr,XG_CTL);
			$tpl=xg_str($arr,'/');
		}
		
		if(substr($tpl,-5)=='.html')$tpl=substr($tpl,0,-5);
		$tpl.='.html';
		$tpl=trim(xg_replace_path($tpl),'/');
		
		if(substr($tpl2,-5)=='.html')$tpl2=substr($tpl2,0,-5);
		$tpl2.='.html';
		$tpl2=trim(xg_replace_path($tpl2),'/');
		if(!file_exists($tpl) and !file_exists($tpl2)){
			if($this->conf['theme'] and file_exists($this->conf['theme'].'/'.$tpl)){
				$tpl=$this->conf['theme'].'/'.$tpl;
			}elseif(file_exists($this->conf['path'].'/'.$tpl)){
				$tpl=$this->conf['path'].'/'.$tpl;
			}elseif(file_exists(XG_PATH.'/'.$tpl)){
				$tpl=XG_PATH.'/'.$tpl;
			}elseif(file_exists(XG_PHP.'/'.$tpl)){
				$tpl=XG_PHP.'/'.$tpl;
			}elseif(file_exists('/'.$tpl)){
				$tpl='/'.$tpl;
			}elseif(!file_exists($tpl)){
				xg_sys_error('tpl file not exist!',$tpl);
			}
		}elseif(file_exists($tpl2)){
			return $tpl2;
		}
		return $tpl;
	}
	protected function tpl_parse($tplname,$cache=0){
		$path=$this->parse($this->tpl_cont($tplname),$tplname);
		if($cache and $this->conf['cache']){
			if($cache==2 and xg_login()){
				$viewcachephp=$path.'-cache-2.php';
			}else{
				$viewcachephp=$path.'-cache-1.php';
			}
			if(!$this->get_cache_path($tplname,$viewcachephp) or !file_exists($viewcachephp)){
				ob_start();
				$data=$this->data;
				extract($data,EXTR_OVERWRITE);
				require $path;
				$cont=ob_get_contents();
				ob_end_clean();
				$this->save_file($viewcachephp,$cont);
			}
			return $viewcachephp;
		}
		return $path;
	}
	protected function parse_point($str){
		if(!preg_match('/\.([a-zA-Z0-9_\-]+)/',$str))return $str;
		$str=preg_replace_callback('/\.([a-zA-Z0-9_\-]+)/',function($rt){
			return '['.(is_numeric($rt[1])?'':'"').trim($rt[1],'"\'').(is_numeric($rt[1])?'':'"').']';
		},$str);
		return (strpos($str,'$')===0?'':'$').$str;
	}
	protected function parse_php($tpl){
		$tpl=preg_replace_callback('/\{\$([a-zA-Z0-9\._\[\]\$\?\:\'\"\-]+)\}/',function($rt){
			$rt=$rt[1];
			if(strpos($rt,'.')>0)$rt=$this->parse_point($rt);
			return '<?php echo '.(substr($rt,0,1)=='$'?$rt:'$'.$rt).';?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{([\(\$]+(.+?)[\)]?)\}/',function($rt){
			return '<?php echo '.$rt[1].';?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{\:([a-zA-Z0-9\._\[\]\$]+)\((.*?)\)[\;]*\}/',function($rt){
			return '<?php echo '.$rt[1].'('.$rt[2].');?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{include[\s]+file=\"([a-zA-Z0-9\-_\/@%#]+)\"(?: cache=\"([012])\")?[\s\/]{0,2}\}/is',function($rt){
			return '<?php require $this->tpl_parse("'.$rt[1].'"'.($rt[2]?','.$rt[2]:'').');?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{(if)[\s]+(.*?)}/is',function($rt){
			if(strpos($rt[2],'.')>0)$rt[2]=$this->parse_point($rt[2]);
			return '<?php '.$rt[1].'('.$rt[2].'){?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{(for)[\s]+(.*?)}/is',function($rt){
			if(strpos($rt[2],'.')>0)$rt[2]=$this->parse_point($rt[2]);
			return '<?php '.$rt[1].'('.$rt[2].'){?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{(else if|elseif)[\s]+(.*?)}/is',function($rt){
			if(strpos($rt[2],'.')>0)$rt[2]=$this->parse_point($rt[2]);
			return '<?php }'.$rt[1].'('.$rt[2].'){?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{\/(if|foreach|for)\}/is',function($rt){
			return '<?php };?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{else}/is',function($rt){
			return '<?php }else{?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{foreach ([\$]?[a-zA-Z0-9\._\[\]\$\'\"]+)[\s]+([\$]?[a-zA-Z0-9_]+)([\s]+([\$]?[a-zA-Z0-9_]+))*}/is',function($rt){
			if(strpos($rt[1],'.')>0)$rt[1]=$this->parse_point($rt[1]);
			if($rt[4]){
				return '<?php foreach('.(substr($rt[1],0,1)=='$'?$rt[1]:'$'.$rt[1]).' as '.(substr($rt[2],0,1)=='$'?$rt[2]:'$'.$rt[2]).'=>'.(substr($rt[4],0,1)=='$'?$rt[4]:'$'.$rt[4]).'){?>';
			}else{
				return '<?php foreach('.(substr($rt[1],0,1)=='$'?$rt[1]:'$'.$rt[1]).' as '.(substr($rt[2],0,1)=='$'?$rt[2]:'$'.$rt[2]).'){?>';
			}
		},$tpl);
		$tpl=preg_replace_callback('/\{echo(.*?)\/\}/is',function($rt){
			return '<?php echo '.$rt[1].';?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{php(.*?)\/\}/is',function($rt){
			return '<?php '.$rt[1].';?>';
		},$tpl);
		$tpl=preg_replace_callback('/\{table(.*?)[\/]?\}((.*?)\{\/table\})?/is',function($rt){
			if(preg_match_all('/([a-z]+)=\"([^\"]+)\"/',$rt[1],$params)){
				$arr=[];
				for($i=0;$i<count($params[1]);$i++){
					if($params[1][$i]=='url'){
						$url=$params[2][$i];
					}
					if($params[1][$i]=='id'){
						$id=$params[2][$i];
					}
				}
			}
			if(!$url)$url=xg('url');
			$html.=$rt[3];
			if($id)$html.='<?php $id="'.$id.'";?>';
			if($url)$html.='<?php $url="'.$url.'";?>';
			$html.='<?php require $this->tpl_parse("'.str_replace('\\','/',XG_PHP).'/view/table");?>';
			return $html;
		},$tpl);
		$tpl=preg_replace_callback('/\{model (.*?)[\/]?\}((.*?)\{\/model\})?/is',function($rt){
			if(preg_match_all('/([a-z]+)=\"([^\"]+)\"/',$rt[1],$params)){
				$arr=[];
				for($i=0;$i<count($params[1]);$i++){
					$arr[$params[1][$i]]=$params[2][$i];
				}
				if($arr['table']){
					$tpl='<?php ';
					$tpl.='$model=xg_model("'.$arr['table'].'");';
					$arr['var']=($arr['var']?preg_replace('/[^a-zA-Z0-9_]+/','',$arr['var']):'data');
					$arr['key']=($arr['key']?preg_replace('/[^a-zA-Z0-9_]+/','',$arr['key']):'key');
					$arr['val']=($arr['val']?preg_replace('/[^a-zA-Z0-9_]+/','',$arr['val']):'item');
					if($arr['fields'])$tpl.='$model->fields("'.$arr['fields'].'");';
					if($arr['where'])$tpl.='$model->where("'.$arr['where'].'");';
					if($arr['id'])$tpl.='$model->where("'.$arr['id'].'");';
					if($arr['order'])$tpl.='$model->order("'.$arr['order'].'");';
					if($arr['limit'])$tpl.='$model->limit("'.$arr['limit'].'");';
					if($arr['count'])$tpl.='$model->limit("0,'.$arr['count'].'");';
					if($arr['value']=='find'){
						$tpl.='$'.$arr['var'].'=$model->find();';
					}elseif($arr['value']=='select'){
						$tpl.='$'.$arr['var'].'=$model->select();';
					}elseif($arr['value']=='column'){
						$tpl.='$'.$arr['var'].'=$model->column('.($arr['column']?'"'.$arr['column'].'"':'').(($arr['column'] and $arr['colkey'])?',"'.$arr['colkey'].'"':'').');';
					}elseif($arr['value']=='value' and ($arr['field'] or $arr['fields'])){
						$tpl.='echo $model->value("'.($arr['field']?$arr['field']:$arr['fields']).'")';
					}elseif($arr['value']){
						$tpl.='echo $model->value("'.$arr['value'].'")';
					}else{
						$tpl.='$'.$arr['var'].'=$model->select();';
					}
					if($rt[3]){
						if($arr['value']=='select' or $arr['value']=='column' or !$arr['value']){
							$tpl.='foreach($'.$arr['var'].' as '.(($arr['key'] and $arr['val'])?'$'.$arr['key'].' => $'.$arr['val']:'$'.$arr['val']).'){
								 ?>'.$rt[3].'<?php 
							}';
						}
					}
					$tpl.=' ?>';
				}
			}
			return $tpl;
		},$tpl);
		if($this->runhook)$tpl=xg_hooks('parse-php','view')->def($tpl)->run($tpl)->last();
		return $tpl;
	}
	public function parse($tpl,$name=''){
		if(preg_match('/\{extend [a-z0-9]+=\"(.+?)\"[\s\/]{0,2}\}/is',$tpl,$rt)){
			if($name and $ext_tpl=$this->get_cache_cont($rt[1]) and $path=$this->get_cache_path($name))return $path;
			$cont=$this->parse_php($tpl);
			if($ext_tpl){
				$tpl=$ext_tpl;
			}else{
				$tpl=$this->tpl_cont($rt[1]);
				$this->save_php($rt[1],$tpl);
			}
			if(preg_match_all('/\{block name=\"(.+?)\"\}\{[\/]?block\}/is',$tpl,$rt)){
				for($i=0;$i<count($rt[0]);$i++){
					if(preg_match('/\{block name=\"'.$rt[1][$i].'\"\}(.*?)\{[\/]?block\}/is',$cont,$rt2)){
						$block=$this->parse($rt2[1]);
					}else{
						$block='';
					}
					$tpl=preg_replace('/\{block name=\"'.$rt[1][$i].'\"\}\{[\/]?block\}/is',$block,$tpl);
				}
			}
		}
		$tpl=preg_replace('/\{block name=\"(.+?)\"\}(.*?)\{[\/]?block\}/is','',$tpl);
		$tpl=preg_replace_callback('/\{html var=\"(.+?)\"}(.*?)\{\/html\}/is',function($rt){
			$var=$rt[1];
			$tpl='<?php ob_start();?>'.$rt[2].'<?php $'.$var.'=ob_get_contents();ob_end_clean();?>';
			return $tpl;
		},$tpl);
		$tpl=$this->parse_php($tpl);
		if($name)return $this->save_php($name,$tpl);
		return $tpl;
	}
	protected function cache_path2($name){
	}
	protected function cache_path($name){
		$name=xg_replace_path($this->tpl_path($name));
		$xgpath=xg_replace_path(XG_PATH);
		if(strpos($name,$xgpath)===0)$name=substr($name,strlen($xgpath));
		$name=md5($name);
		return $this->conf['temp'].'/'.$name.'.php';
	}
	protected function get_cache_cont($name){
		$path=$this->get_cache_path($name);
		if($path)return xg_fcont($path);
		return false;
	}
	protected function get_cache_path($name,$path=''){
		if(!$this->conf['cache'])return false;
		if(!$path)$path=$this->cache_path($name);
		if(file_exists($path)){
			$tpltime=filemtime($this->tpl_path($name));
			$phptime=filemtime($path);
			if(($phptime+$this->conf['cache'])>XG_TIME and $phptime>$tpltime){
				return $path;
			}
		}
		return false;
	}
	protected function save_file($path,$cont){
		mkdir(dirname($path),0755,true);
		$head='<?php if(!defined("XG"))exit("Access denied!");?>';
		$save=xg_fcont($path,(strpos($cont,$head)===0?'':$head).$cont);
		return $save;
	}
	protected function save_php($name,$cont){
		$path=$this->cache_path($name);
		$save=$this->save_file($path,$cont);
		if(!$save)xg_sys_error('tpl save php fail!',$name);
		return $path;
	}
	public function assign($name,$value=null){
		if(is_array($name)){
			$this->data=xg_merge($this->data,$name,true);
		}elseif(strpos($name,'.')>0){
			$names=xg_arr($name,'.');
			$tree=xg_tree($names,$value);
			$this->data=xg_merge($this->data,$tree,true);
		}else{
			$this->data[$name]=$value;
		}
	}
}