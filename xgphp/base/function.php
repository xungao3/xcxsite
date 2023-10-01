<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
spl_autoload_register(function($class){
	$class=trim(str_replace('\\','/',$class),'/');
	if(file_exists($dir=XG_PATH.'/'.$class.'.php')){
		include $dir;
		return true;
	}elseif(substr($class,0,3)=='xg/'){
		if(file_exists($dir=XG_PHP.'/'.substr($class,3).'.php')){
			include $dir;
			return true;
		}
		if(file_exists($dir=XG_CORE.'/'.substr($class,3).'.php')){
			include $dir;
			return true;
		}
	}else{
		if(file_exists($dir=XG_PATH.'/extend/'.$class.'.php')){
			include $dir;
			return true;
		}
	}
	return false;
});

define('XG_DEBUG',file_exists(XG_DATA.'/debug'));
if(file_exists(XG_APPS.'/common.php'))require XG_APPS.'/common.php';
require 'table.php';
require 'content.php';
require 'session.php';
require 'formitem.php';
require 'temp.php';
foreach(xg_files(XG_PATH.'/hooks/','php',1) as $file){
	include $file;
}
xg_hooks('xg')->run();
function xg_sys_error($str,$var=null){
	if($str instanceof \Throwable){
		xg_halt($str);
	}else{
		$lang=include XG_BASE.'/lang.php';
		if($lang[$str])$str=$lang[$str];
		xg_halt($str.($var?'<br>'.$var:''));
	}
	exit;
}
function xg_halt($e=array(),$code=500){
	$a=[];
	if($e instanceof \Throwable){
		$a[0]='发生错误：'.$e->getMessage().'<br>在'.$e->getFile().' 第 '.$e->getLine().' 行';
		$all=$e->getTrace();
	}else{
		$all=debug_backtrace();
		array_shift($all);
		if($all[0]['function']=='xg_sys_error')array_shift($all);
		if($e)$a=array(is_array($e)?'发生错误 '.$e['message']."<br>\r\n".'在 '.$e['file'].' 第'.$e['line'].'行':$e);
	}
	foreach($all as $v){
		if($v['file'])$a[]='在 '.$v['file'].' 第'.$v['line'].'行 函数名:'.$v['function'];
	}
	if($a)$e=implode("<br>\r\n",$a);
	if($e){
		if($code==404){
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
		}elseif($code==500){
			header('HTTP/1.1 500 Internal Server Error');
		}
		xg_rlog("\r\n{$e}");
		if(!defined('XG_DEBUG') or XG_DEBUG===0)$e='发生错误';
		if(xg_isajax())xg_jsonmsg(['ok'=>-1,'msg'=>$e]);
		echo print_r($e,true).'<br>'.xg_php_copy();exit;
	}
}
register_shutdown_function('xg_halt');


function xg_exit($str='',$type='text/html',$charset=XG_CHAR){
	$time=xg_data_info('xg_start','xg_end');
	$mem=xg_data_info('xg_start','xg_end','m');
	xg_slog('time='.$time.' memory='.$mem);
	xg_save_slog();
	header("Content-type:{$type};charset={$charset}");
	$str=xg_restore_phptag($str);
	exit(xg_utf8(xg_hooks('exit')->def($str)->run($str,$type)->last()));
}
function xg($name=false,$value=false){
	static $xg;
	$names=array();
	if(is_string($name) and strpos($name,'.')){
		$names=xg_arr($name,'.');
	}elseif(is_array($name)){
		$names=$name;
	}
	if($name===null){
		$xg=array();
		return $xg;
	}elseif($name===false){
		return $xg;
	}elseif($value!==false){
		$tree=xg_tree($names,$value);
		$xg=xg_merge($xg,$tree,true);
	}
	if(count($names)>0){
		return xg_tree_val($names,$xg);
	}
	if($name=='url')return xg_input('server.REQUEST_URI');
	if($name=='baseurl'){
		$url=xg_input('server.REQUEST_URI');
		return strpos($url,'?')>-1?substr($url,0,strpos($url,'?')):$url;
	}
	if($name=='fullurl')return xg_http_domain().xg_input('server.REQUEST_URI');
	if($name=='domain' or $name=='host')return xg_input('server.HTTP_HOST');
	if($name=='referrer' or $name=='from')return xg_input('server.HTTP_REFERER');
	if($name=='agent')return xg_input('server.HTTP_USER_AGENT');
	return $xg[$name];
}
function xg_isclosure($callback){
	return (($callback instanceof Closure) or ($callback instanceof \Closure) or (is_object($callback) and is_callable($callback)));
}
function xg_isfun($v){
	return xg_isclosure($v);
}
function xg_route(){
	static $route=null;
	if(is_null($route))$route=new \xg\route();
	return $route;
}





function xg_sys($name){
	$class="\\sys\\$name\\sys";
	if(class_exists($class)){
		return new $class();
	}
}

function xg_hooks($name,$app=null){
	if(is_null($app) and defined('XG_APP')){
		$app=XG_APP;
	}
	return new \xg\hooks($name,$app);
}
function xg_form($name='',$title='',$value=null,$values=[]){
	return new \xg\form($name,$title,$value,$values);
}
function xg_to_camel($string,$first=true){
	$str=str_replace(' ','',ucwords(str_replace(['_','-'],' ',$string)));
	if(!$first)$str=lcfirst($str);
	return $str;
}
function xg_to_under($string){
	return strtolower(preg_replace('/(?<!^)[A-Z]/','_$0',$string));
}
function xg_method_exists($obj,$name){
	if(method_exists($obj,$name=xg_to_camel($name)))return $name;
	if(method_exists($obj,$name=xg_to_under($name)))return $name;
	if(method_exists($obj,$name=xg_to_camel($name,false)))return $name;
	return false;
}


function xg_confirm($confirm){
	$confirmed=xg_input('xg-confirmed',[]);
	$yes=xg_input('xg-yes');
	if(!xg_in_array($confirm,$confirmed)){
		$confirmed[]=$confirm;
		return ['confirm'=>$confirm,'confirmed'=>$confirmed];
	}
}
function xg_isemail($v){
	return preg_match("/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9]{2,10}){1,2}$/",$v);
}
function xg_ismobile($v){
	return preg_match("/^1[3|4|5|6|7|8|9]{1}[\d]{9}$/",str_replace('+86','',$v));
}
function xg_make_file_base64_data($data){
	if(is_string($data))$data=xg_jsonarr($data);
	if($data and is_array($data)){
		foreach($data as $k=>$v){
			if(is_array($v)){
				$data[$k]=xg_make_file_base64_data($v);
			}elseif(is_string($v)){
				if(substr($v,0,8)=='/upload/' and file_exists(XG_PUBLIC.$v)){
					$cont=xg_fcont(XG_PUBLIC.$v);
					$data[$k]=array('path'=>$v,'base64'=>base64_encode($cont));
				}
			}
		}
	}
	return $data;
}
// function xg_decode_file_base64_data($data){
// 	if(is_string($data))return $data;
// 	if(is_array($data)){
// 		foreach($data as $k=>$v){
// 			if(is_array($v)){
// 				if($v['path'] and $v['base64']){
// 					$tag='';
// 					$ext=xg_fileext($v['path']);
// 					if(!xg_in_array($ext,array('jpg','jpeg','png','gif','svg'))){
// 						$ext='jpg';
// 					}
// 					$url='/upload/'.date('Ym').'/'.date('d').'/'.substr(md5(microtime()),0,10).'.'.$ext;
// 					mkdir(dirname(XG_PUBLIC.$url),0755,true);
// 					xg_fcont(XG_PUBLIC.$url,base64_decode($v['base64']));
// 					if(xg_table_exist('xg_file')){
// 						$info=array(
// 							'url'=>$url,
// 							'ext'=>$ext,
// 							'size'=>filesize(XG_PUBLIC.$url),
// 							'upload_time'=>XG_TIME,
// 							'tag'=>$tag
// 						);
// 						xg_model('xg_file')->insert($info);
// 					}
// 					$data[$k]=$url;
// 				}else{
// 					$data[$k]=xg_decode_file_base64_data($v);
// 				}
// 			}
// 		}
// 	}
// 	return $data;
// }
function xg_file_ext($v){
	return pathinfo($v)['extension'];
}
function xg_file(){
	return new \xg\file();
}
function xg_select($tbname,$where=array(),$fields,$order=''){
	return xg_model($tbname)->where($where)->fields($fields)->order($order)->select();
}
function xg_copy($from,$to){
	$from=rtrim($from,'\\/*');
	$to=rtrim($to,'\\/*');
	if(is_dir($from)){
		$files=xg_filenames($from,-1);
		foreach($files as $file){
			xg_copy($from.'/'.$file,$to.'/'.$file);
		}
	}elseif(file_exists($from)){
		mkdir(dirname($to),0755,true);
		copy($from,$to);
	}
}
/**
 * @param	$set=0	files		当前文件夹的所有文件，不包括子文件夹文件
 * 			$set=1	all files	当前文件夹的所有文件，包括子文件夹文件
 * 			$set=2	dir			当前文件夹的所有文件夹，不包括子文件夹
 * 			$set=3	all dir		当前文件夹的所有文件夹，包括子文件夹
 * 			$set=-1	all			当前文件夹的所有文件夹，包括子文件夹
*/
function xg_files($dir,$ext=null,$set=0){
	if(is_int($ext)){
		$set=$ext;
		$ext=null;
	}
	if($set==2 or $set==3){
		$files=glob($dir.'/*',GLOB_ONLYDIR);
	}else{
		$files=glob($dir.'/*');
	}
	$result=[];
	foreach($files as $file){
		if($set==1 or $set==3){
			if(is_dir($file))$result=array_merge($result,xg_files($file,$ext,$set));
		}
		if(($set==1 or $set==0)){
			if(is_file($file) and !$ext or ($ext and xg_file_ext($file)==$ext))$result[]=$file;
		}elseif(($set==2 or $set==3) and is_dir($file)){
			$result[]=$file;
		}else{
			$result[]=$file;
		}
	}
	return $result;
}
function xg_dirnames($dir){
	$files=xg_files($dir,null,2);
	foreach($files as $key=>$file){
		$files[$key]=basename($file);
	}
	return $files;
}
function xg_dirs($dir){
	$files=xg_files($dir,null,3);
	return $files;
}
function xg_filenames($dir,$ext=null){
	$files=xg_files($dir,$ext,0);
	foreach($files as $key=>$file){
		$files[$key]=basename($file);
	}
	return $files;
}
function xg_filesnames($dir,$ext=null){
	$files=xg_files($dir,$ext,1);
	foreach($files as $key=>$file){
		$files[$key]=basename($file);
	}
	return $files;
}
function xg_fcont($f,$v=false){
	if($v===false)return file_get_contents($f);
	mkdir(dirname($f),0755,true);
	return file_put_contents($f,$v);
}
function xg_cont($f,$v=false){
	return xg_fcont($f,$v);
}
function xg_replace_path($path){
	return rtrim(xg_loop_replace('//','/',str_replace("\\",'/',$path),'/'));
}
function xg_addslashes($value){
	if(is_array($value)){
		foreach($value as $k=>$v){
			$value[$k]=xg_addslashes($v);
		}
		return $value;
	}elseif(is_object($value)){
		foreach($value as $k=>$v){
			$value->$k=xg_addslashes($v);
		}
		return $value;
	}else{
		if(PHP_VERSION<6 and get_magic_quotes_gpc())return xg_filter_phptag($value);
		return addslashes(xg_filter_phptag($value));
	}
}
function xg_stripslashes($value,$charset=XG_CHAR){
	if(is_array($value)){
		foreach($value as $k=>$v){
			$value[$k]=xg_stripslashes($v,$charset);
		}
		return $value;
	}elseif(is_object($value)){
		foreach($value as $k=>$v){
			$value->$k=xg_stripslashes($v,$charset);
		}
		return $value;
	}elseif(is_string($value)){
		$value=html_entity_decode($value,ENT_QUOTES | ENT_IGNORE,$charset);
		return stripslashes($value);
	}else{
		return $value;
	}
}
function xg_rgbi($color,$i,$opacity=null){
	return xg_rgba($color,$opacity)[$i];
}
function xg_rgba($color,$opacity=null){
	if($color[0]=='#'){
		$color=substr($color, 1);
	}
	if(strlen($color)==6){
		$hex=[$color[0].$color[1],$color[2].$color[3],$color[4].$color[5]];
	}elseif(strlen($color)==3) {
		$hex=[$color[0].$color[0],$color[1].$color[1],$color[2].$color[2]];
	}else{
		return null;
	}
	$rgb=array_map('hexdec',$hex);
	if(!is_null($opacity))array_pop($rgb,$opacity);
	return $rgb;
}
function xg_px($v,$u=''){
	$v=is_string($v)?strtolower($v):$v;
	if(is_string($v) and (substr($v,-1)=='%' or substr($v,-2)=='px' or substr($v,-2)=='em' or substr($v,-3)=='rem'))return $v;
	if(is_numeric($v))return $v+($u?$u:'px');
}
function xg_byteconvert($input){
	preg_match('/([\d\.]+)(\w*)/',$input,$matches);
	$type=strtolower($matches[2]);
	switch($type){
	case "b":
		$output=$matches[1];
		break;
	case "kb":
	case "k":
		$output=$matches[1]*1024;
		break;
	case "mb":
	case "m":
		$output=$matches[1]*1024*1024;
		break;
	case "gb":
	case "g":
		$output=$matches[1]*1024*1024*1024;
		break;
	case "tb":
	case "t":
		$output=$matches[1]*1024*1024*1024*1024;
		break;
	default:
		$output=$matches[1];
	}
	return is_numeric($output)?$output:((int)$matches[1]);
}
function xg_format_bytes($size){ 
	$units=array('B','KB','MB','GB','TB'); 
	for($i=0; $size>=1024&&$i<4; $i++)$size /=1024; 
	return round($size,2).$units[$i]; 
}
function xg_download($cont,$name){
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$name);
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	if(is_file($cont)){
		header('Content-Length: '.filesize($cont));
		set_time_limit(0);
		$file=@fopen($cont,"rb");
		while(!feof($file)){
			print(@fread($file,1024*8));
			ob_flush();
			flush();
		}
	}else{
		header('Content-Length: '.strlen($cont));
		echo $cont;
	}
	xg_exit();
}
function xg_redirect($url,$moved=false){
	if($moved)header('HTTP/1.1 301 Moved Permanently');
	header('Location:'.$url);
	xg_exit();
}
function xg_http_url($v=''){
	if(substr($v,0,7)=='http://' or substr($v,0,8)=='https://')return $v;
	if(!$v)return '';
	$v=xg_http_domain().'/'.ltrim($v,'/');
	return $v;
}
function xg_html_file_url($html=''){
	$urlpath=xg_http_url(XG_STATIC);
	$html=str_ireplace(
		array('src="/upload/',"src='/upload/",'src=/upload/'),
		array('src="'.$urlpath.'upload/',"src='".$urlpath."upload/",'src='.$urlpath.'upload/'),
		$html);
	return $html;
}
function xg_checklogin(){
	if($user=xg_loginuser()){
		return $user;
	}
	xg_error(array('msg'=>'请先登录','needlogin'=>1));
}
function xg_check_verify($code,$id=1){
	$verify=new \xg\libs\verify();
	return $verify->check($code,$id);
}
function xg_array_unique($list,$field){
	$vals=array();
	foreach($list as $key=>$row){
		if(!xg_in_array($row[$field],$vals)){
			$vals[]=$row[$field];
		}else{
			unset($list[$key]);
		}
	}
	return $list;
}
function xg_array_column($arr,$col){
	$vals=array();
	foreach($arr as $key=>$val){
		foreach($val as $key2=>$val2){
			if($key2==$col)$vals[]=$val2;
		}
	}
	return $vals;
}
function xg_array_values($arr){
	$vals=array();
	if(is_array($arr)){
		$vals=array_values($arr);
	}
	return $vals;
}
function xg_array_sort($list,$field,$sort=SORT_DESC){
	foreach($list as $key=>$row){
		$fields[$key]=$row[$field];
	}
	array_multisort($fields,$sort,$list);
	return $list;
}
function xg_array_find($array,$keys){
	$keys=xg_arr($keys);
	$array=xg_arr($array);
	return array_intersect_key($array,array_flip($keys));
}
function xg_loop_replace($f,$t,$c){
	while(strpos($c,$f)!==false){
		$c=str_replace($f,$t,$c);
	}
	return $c;
}
function xg_image_resize($source_path,$target_path,$target_width,$target_height,$scale=false){
	$source_info=getimagesize($source_path);
	$source_width=$source_info[0];
	$source_height=$source_info[1];
	$source_mime=$source_info['mime'];
	
	if($scale){
		$width_ratio=$source_width / $target_width;
		$height_ratio=$source_height / $target_height;
		if($width_ratio>$height_ratio){
			$target_width=$source_width / $width_ratio;
			$target_height=$source_height / $width_ratio;
		}else{
			$target_width=$source_width / $height_ratio;
			$target_height=$source_height / $height_ratio;
		}
		if($target_width>=$source_width){
			$target_width=$source_width;
			$target_height=$source_height;
			//return false;
		}
		$cropped_width=$source_width;
		$cropped_height=$source_height;
		$source_x=0;
		$source_y=0;
	}else{
		$source_ratio=$source_height / $source_width;
		$target_ratio=$target_height / $target_width;
		// 源图过高
		if($source_ratio>$target_ratio){
			$cropped_width=$source_width;
			$cropped_height=$source_width * $target_ratio;
			$source_x=0;
			$source_y=($source_height-$cropped_height)/ 2;
		}elseif($source_ratio<$target_ratio){ // 源图过宽
			$cropped_width=$source_height / $target_ratio;
			$cropped_height=$source_height;
			$source_x=($source_width-$cropped_width)/ 2;
			$source_y=0;
		}else{ // 源图适中
			$cropped_width=$source_width;
			$cropped_height=$source_height;
			$source_x=0;
			$source_y=0;
		}
	}
	 
	switch($source_mime){
		case 'image/gif':
			$source_image=imagecreatefromgif($source_path);
			break;
		 
		case 'image/jpeg':
			$source_image=imagecreatefromjpeg($source_path);
			break;
		 
		case 'image/png':
			$source_image=imagecreatefrompng($source_path);
			break;
		 
		default:
			return false;
			break;
	}
	$target_image=imagecreatetruecolor($target_width,$target_height);
	$cropped_image=imagecreatetruecolor($cropped_width,$cropped_height);
	 
	// 裁剪
	imagecopy($cropped_image,$source_image,0,0,$source_x,$source_y,$cropped_width,$cropped_height);
	// 缩放
	imagecopyresampled($target_image,$cropped_image,0,0,0,0,$target_width,$target_height,$cropped_width,$cropped_height);
	$target_type=xg_file_ext($target_path);
	switch($target_type){
		case 'gif':
			imagegif($target_image,$target_path);
			break;
		 
		case 'jpeg':
			imagejpeg($target_image,$target_path,90);
			break;
		 
		case 'jpg':
			imagejpeg($target_image,$target_path,90);
			break;
		 
		case 'png':
			imagepng($target_image,$target_path);
			break;
		 
		default:
			imagejpeg($target_image,$target_path,90);
			break;
	}
	imagedestroy($source_image);
	imagedestroy($target_image);
	imagedestroy($cropped_image);
	return true;
}
function xg_data_info($start,$end='',$dec=4){
	static $_info=array();
	static $_mem=array();
	if(is_float($end)){
		$_info[$start]=$end;
	}elseif(!empty($end)){
		if(!isset($_info[$end]))$_info[$end]=microtime(true);
		if(function_exists('memory_get_usage') and $dec=='m'){
			if(!isset($_mem[$end]))$_mem[$end]=memory_get_usage();
			return number_format(($_mem[$end]-$_mem[$start])/1024);
		}else{
			return number_format(($_info[$end]-$_info[$start]),$dec);
		}
	}else{
		$_info[$start]=microtime(true);
		if(function_exists('memory_get_usage'))$_mem[$start]=memory_get_usage();
	}
}
function xg_trait($name){
	$class='\\xg\\traits\\'.$name;
	if(class_exists($class)){
		$trait=new $class();
	}
	$class='\\traits\\'.$name;
	if(class_exists($class)){
		$trait=new $class();
	}
	return $trait;
}
function xg_ip($type=0){
	$type=$type?1:0;
	static $ip=null;
	if($ip!==null)return $ip[$type];
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$arr=explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
		$pos=array_search('unknown',$arr);
		if(false!==$pos)unset($arr[$pos]);
		$ip=trim($arr[0]);
	}elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
		$ip=$_SERVER['HTTP_CLIENT_IP'];
	}elseif(isset($_SERVER['REMOTE_ADDR'])){
		$ip=$_SERVER['REMOTE_ADDR'];
	}
	$long=sprintf("%u",ip2long($ip));
	$ip=$ip?array($ip,$long):array('0.0.0.0',0);
	return $ip[$type];
}
function xg_base64($file){
	$target=XG_RUNTIME.'/'.md5(microtime());
	$read=fopen($file,'r');
	stream_filter_append($read,'convert.base64-encode');
	$write=fopen($target,'w');
	stream_copy_to_stream($read,$write);
	fclose($read);
	fclose($write);
	$base64=xg_fcont($target);
	unlink($target);
	return $base64;
}
function xg_encode($data,$key='',$expire=0){
	$key=md5(empty($key)?xg_config('site.main_key'):$key);
	$data=base64_encode($data);
	$x=0;
	$len=strlen($data);
	$l=strlen($key);
	$char='';
	for($i=0; $i<$len; $i++){
		if($x==$l)$x=0;
		$char.=substr($key,$x,1);
		$x++;
	}
	$str=sprintf('%010d',$expire?$expire+XG_TIME:0);
	for($i=0; $i<$len; $i++){
		$str.=chr(ord(substr($data,$i,1))+(ord(substr($char,$i,1)))%256);
	}
	return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}
function xg_decode($data,$key=''){
	$key=md5(empty($key)?xg_config('site.main_key'):$key);
	$data=str_replace(array('-','_'),array('+','/'),$data);
	$mod4=strlen($data)%4;
	if($mod4){
		$data.=substr('====',$mod4);
	}
	$data=base64_decode($data);
	$expire=substr($data,0,10);
	$data=substr($data,10);
	if($expire>0&&$expire<XG_TIME){
		return '';
	}
	$x=0;
	$len=strlen($data);
	$l=strlen($key);
	$char=$str='';
	for($i=0; $i<$len; $i++){
		if($x==$l)$x=0;
		$char.=substr($key,$x,1);
		$x++;
	}
	$arr=[];
	$arr2=[];
	for($i=0; $i<$len; $i++){
		$arr2[]=ord(substr($data,$i,1));
		if(ord(substr($data,$i,1))<ord(substr($char,$i,1))){
			$str.=chr((ord(substr($data,$i,1))+256)-ord(substr($char,$i,1)));
			$arr[]=(ord(substr($data,$i,1))+256)-ord(substr($char,$i,1));
		} else {
			$str.=chr(ord(substr($data,$i,1))-ord(substr($char,$i,1)));
			$arr[]=(ord(substr($data,$i,1)))-ord(substr($char,$i,1));
		}
	}
	return base64_decode($str);
}
function xg_char($value){
	return xg_tochar($value);
	return $value;
}
function xg_tochar($value,$charset=XG_CHAR){
	if(is_array($value)){
		foreach($value as $k=>$v){
			$k2=xg_tochar($k,$charset);
			$value[$k2]=xg_tochar($v,$charset);
			if($k!=$k2)unset($value[$k]);
		}
		return $value;
	}elseif(is_object($value)){
		foreach($value as $k=>$v){
			$k2=xg_tochar($k,$charset);
			$value->$k2=xg_tochar($v,$charset);
			if($k!=$k2)unset($value->$k);
		}
		return $value;
	}elseif(is_string($value)){
		return mb_convert_encoding($value,$charset,'UTF-8,GBK,GB2312,BIG5');
	}else{
		return $value;
	}
}
function xg_utf8($value){
	if(strtoupper(XG_CHAR)!='UTF-8' and strtoupper(XG_CHAR)!='UTF8')return xg_tochar($value,'UTF-8');
	return $value;
}
function xg_pqhtml($html,$charset=XG_CHAR){
	require_once(XG_PHP.'/libs/phpQuery.php');
	phpQuery::$defaultCharset=$charset;
	return $doc=phpQuery::newDocumentHTML($html);
}
function xg_formathtml($dochtml,$charset=XG_CHAR){
	$dochtml=htmlspecialchars_decode($dochtml);
	$dochtml=preg_replace("/[\t\r\n]+/",'',$dochtml);
	while(strpos($dochtml,'  ')!==false){
		$dochtml=str_replace('  ',' ',$dochtml);
	}
	$dochtml=xg_safehtml($dochtml);
	$doc=xg_pqhtml($dochtml,$charset);
	$dochtml=xg_formatdoc($doc,$charset);
	$dochtml=str_ireplace('%5B','[',$dochtml);
	$dochtml=str_ireplace('%5D',']',$dochtml);
	return $dochtml;
}
function xg_replace_limit($search,$replace,$content,$limit=-1){
	if(is_array($search)){
		foreach($search as $k=>$v){
			$search[$k]='`'.preg_quote($search[$k],'`').'`';
		}
	}else{
		$search='`'.preg_quote($search,'`').'`';
	}
	return preg_replace($search,$replace,$content,$limit);
}
function xg_get_dom_tag($v,$c,$k){
	$tagdom=pq($v)->get(0)->previousSibling;
	if($k==0)$tagdom=pq($v)->next()->get(0)->previousSibling;
	if($k==($c-1))$tagdom=pq($v)->prev()->get(0)->nextSibling;
	return $tagdom;
}
function xg_format_children($doc,$charset=XG_CHAR){
	$doc->find('body,p,table,tbody,tr,td,th,center,strong,span,div,font,img')->each(function($v){
		$nodeName=strtolower($v->nodeName);
		if($nodeName=='p'){
			pq($v)->style('display:block;margin:0;');
		}
		if($nodeName=='span'){
			pq($v)->replaceWith(pq($v)->html());
		}
		if($nodeName=='strong'){
			pq($v)->replaceWith(pq($v)->html());
		}
		if($nodeName=='center'){
			pq($v)->replaceWith(pq($v)->html());
		}
		if($nodeName=='font'){
			pq($v)->replaceWith(pq($v)->html());
		}
		if($nodeName=='table'){
			$style=pq($v)->attr('style');
			pq($v)->style('width:100%;border-collapse:collapse;border-spacing:0;'.$style);
		}
		if(xg_in_array($nodeName,array('td','th'))){
			$style=pq($v)->attr('style');
			pq($v)->style('border:1px solid #ccc;padding:3px 6px;line-height:1.3em;box-sizing:border-box;'.$style);
		}
		if($nodeName=='br'){
			pq($v)->remove();
		}
		if($nodeName=='img'){
			$style='max-width:100%;width:auto;height:auto;'.pq($v)->attr('style');
			pq($v)->css($style,'','width:100%;');
			if(pq($v)->parent('p')->size()==0){
				$v=pq('<p style="text-indent:0px;">'.pq($v)->htmlOuter().'</p>');
			}else{
				$pstyle=pq($v)->parent('p')->attr('style');
				pq($v)->parent('p')->css('text-indent:0px;'.$pstyle);
			}
		}
	});
	return $doc;
}
function xg_formatdoc($doc,$charset=XG_CHAR){
	$dochtml=$html=pq($doc)->htmlOuter();;
	$dochtml=str_ireplace('<center>','<p align="center">',$dochtml);
	$dochtml=str_ireplace('</center>','</p>',$dochtml);
	$dochtml=str_ireplace('<p>?</p>','',$dochtml);
	$dochtml=str_ireplace('<b>','<strong>',$dochtml);
	$dochtml=str_ireplace('</b>','</strong>',$dochtml);
	$dochtml=str_ireplace('</strong><strong>','',$dochtml);;
	$dochtml=str_ireplace('<p></p>','',$dochtml);
	$dochtml=str_ireplace('<p align="center"></p>','',$dochtml);
	$dochtml=str_ireplace('>','>',$dochtml);
	$dochtml=str_ireplace('<','<',$dochtml);
	$doc=xg_pqhtml($dochtml,$charset);
	$doc=xg_format_children($doc,$charset);
	if($doc==false)return '';
	$dochtml=$html=pq($doc)->htmlOuter();;
	$dochtml=str_ireplace('<imgsrc=','<img src=',$dochtml);
	$dochtml=str_ireplace('<palign=','<p align=',$dochtml);
	$dochtml=str_ireplace('pstyle','p',$dochtml);
	while(strpos($dochtml,'</p></p>')!==false){
		$dochtml=str_ireplace('<p><p align="center">','<p align="center">',$dochtml);
		$dochtml=str_ireplace('<p align="center"><p>','<p align="center">',$dochtml);
		$dochtml=str_ireplace('<p><p>','<p>',$dochtml);
		$dochtml=str_ireplace('</p></p>','</p>',$dochtml);
	}
	$dochtml=str_ireplace('\"','"',$dochtml);
	$dochtml=str_ireplace('%5C%22','',$dochtml);
	$dochtml=str_ireplace('%5B','[',$dochtml);
	$dochtml=str_ireplace('%5D',']',$dochtml);
	$dochtml=str_ireplace('jpg\/"','jpg"',$dochtml);
	$dochtml=str_ireplace('jpeg\/"','jpeg"',$dochtml);
	$dochtml=str_ireplace('gif\/"','gif"',$dochtml);
	$dochtml=str_ireplace('png\/"','png"',$dochtml);
	$dochtml=str_ireplace('jpg\/\'','jpg\'',$dochtml);
	$dochtml=str_ireplace('jpeg\/\'','jpeg\'',$dochtml);
	$dochtml=str_ireplace('gif\/\'','gif\'',$dochtml);
	$dochtml=str_ireplace('png\/\'','png\'',$dochtml);
	$dochtml=str_ireplace('</p>',"</p>\r\n\r\n",$dochtml);
	return $dochtml;
}
function xg_is_color($v){
	return preg_match('/^#([0-9a-fA-F]{6}|[0-9a-fA-F]{3})$/',$v);
}
function xg_htmlimgsize($html){
	$doc=xg_pqhtml($html,'utf-8');
	$style="display:block;max-width:100%;height:auto;margin:0 auto;";
	foreach($doc->find('img')as $k=>$v){
		$s=pq($v)->attr('style');
		if($s){
			pq($v)->style($style.';'.$s);
		}else{
			pq($v)->style($style);
		}
		$u=pq($v)->attr('src');
		if(substr($u,0,8)=='/upload/'){
			if(file_exists(XG_PUBLIC.$u)){
				$u=xg_http_domain().XG_STATIC.substr($u,1);
			}
		}else{
			if(file_exists(ECMS_ROOT.$u)){
				$u=xg_http_domain().$u;
			}
		}
		pq($v)->attr('src',$u);
	}
	$html=pq($doc)->htmlOuter();
	return $html;
}
function xg_html_img_width($html,$width='100%'){
	$doc=xg_pqhtml($html,'utf-8');
	$plist=pq($doc)->find('img');
	$style='width:'.$width.';';
	foreach($plist as $p){
		$s=pq($p)->attr('style');
		if($s){
			pq($p)->style($s.';'.$style);
		}else{
			pq($p)->style($style);
		}
	}
	$html=pq($doc)->htmlOuter();
	return $html;
}
function xg_htmlp($html,$style=''){
	$doc=xg_pqhtml($html,'utf-8');
	$plist=pq($doc)->find('p');
	foreach($plist as $p){
		$s=pq($p)->attr('style');
		if(pq($p)->find('img')->count()>0)$s.='text-indent:0;';
		if($s){
			pq($p)->css($style.';'.$s,'margin:0;text-indent:0;');
		}else{
			pq($p)->css($style);
		}
	}
	$html=pq($doc)->htmlOuter();
	if(strpos($style,'indent')!==false)$html=str_replace('>　　','>',$html);
	$html=str_ireplace('%5B','[',$html);
	$html=str_ireplace('%5D',']',$html);
	return $html;
}
function xg_subtext($str,$length=0,$dot='...',$charset='utf-8'){
	if($length<1)return $str;
	$strlen=mb_strlen($str,$charset);
	if($strlen<$length)return $str;
	$newstr=$str;
	$newstr=mb_substr($str,0,$length,$charset);
	if($newstr!=$str)$newstr=$newstr.$dot;
	return $newstr;
}
function xg_date($time=XG_TIME,$type=0){
	if($type==0){
		return date('Y-m-d H:i',$time);
	}elseif($type==1){
		return date('Y-m-d H:i:s',$time);
	}elseif($type==2){
		return date('Y-m-d',$time);
	}elseif($type==3){
		return date('Y年m月d日',$time);
	}elseif($type==4){
		$t=XG_TIME-$time;
		if($t>60){
			$f=array(
				'31536000'=>'年',
				'2592000'=>'个月',
				'604800'=>'星期',
				'86400'=>'天',
				'3600'=>'小时',
				'60'=>'分钟',
				'1'=>'秒'
			);
			if(floor($t/86400)>1){
				return date("Y-m-d",$time);
			}else{
				foreach($f as $k=>$v){
					if(0!=$c=floor($t/(int)$k)){
						return $c.$v.'前';
					}
				}
			}
		}else{
			return "刚刚";
		}
	}
}
function xg_format_time($time=XG_TIME,$type=1){
	if($type==1){
		return date('Y-m-d H:i:s',$time);
	}else if($type==3){
		return date('Y年m月d日 H:i:s',$time);
	}else if($type==2){
		$t=XG_TIME-$time;
		if($t>60){
			$f=array(
				'31536000'=>'年',
				'2592000'=>'个月',
				'604800'=>'星期',
				'86400'=>'天',
				'3600'=>'小时',
				'60'=>'分钟',
				'1'=>'秒'
			);
			if(floor($t/86400)>1){
				return date("Y-m-d H:i:s",$time);// H:i:s
			}else{
				foreach($f as $k=>$v){
					if(0!=$c=floor($t/(int)$k)){
						return $c.$v.'前';
					}
				}
			}
		}else{
			return "刚刚";
		}
	}
}
function xg_is_ssl(){
	if(isset($_SERVER['HTTPS'])&&('1'==$_SERVER['HTTPS']|| 'on'==strtolower($_SERVER['HTTPS']))){
		return true;
	}elseif(isset($_SERVER['SERVER_PORT'])&&('443'==$_SERVER['SERVER_PORT'])){
		return true;
	}
	return false;
}
function xg_http_json($url,$post=null,$header=array(),$flag=0,$gzip=false,$timeout=60){
	return xg_jsonarr(xg_http($url,$post,$header,$flag,$gzip,$timeout));
}
function xg_http($url,$post=null,$header=array(),$flag=0,$gzip=false,$timeout=60){
	$ch=curl_init();
	if(!$flag){
		curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,FALSE);
	}
	curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($ch,CURLOPT_TIMEOUT,$timeout);
	if(!empty($post)){
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
	}
	curl_setopt($ch,CURLOPT_USERAGENT,'http://xgphp.xg3.cn/');
	if($gzip){
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch,CURLOPT_ENCODING,'gzip');
		curl_setopt($ch,CURLOPT_HTTPHEADER,array('Accept-Encoding:gzip,deflate'));
	}
	curl_setopt($ch,CURLOPT_URL,$url);
	$a='curl'.'_'.'exec';
	$ret=$a($ch);
	if(!$ret){
		xg_slog($url);
		xg_slog(curl_getinfo($ch,CURLINFO_HTTP_CODE));
		xg_slog(curl_error($ch));
	}
	curl_close($ch);
	return $ret;
}
function xg_domain_main($domain=''){
	$url=$domain ? $domain : $_SERVER['HTTP_HOST'];
	$data=explode('.', $url);
	$co_ta=count($data);
	//判断是否是双后缀
	$zi_tow=true;
	$host_cn='com.cn,net.cn,org.cn,gov.cn';
	$host_cn=explode(',', $host_cn);
	foreach($host_cn as $host){
		if(strpos($url,$host)){
			$zi_tow=false;
		}
	}
	if($zi_tow == true){
		if($url == 'localhost'){
			$host=$data[$co_ta-1];
		}else{
			$host=$data[$co_ta-2].'.'.$data[$co_ta-1];
		}
	}else{
		$host=$data[$co_ta-3].'.'.$data[$co_ta-2].'.'.$data[$co_ta-1];
	}
	return $host;
}
function xg_http_domain($domain='',$ssl=null){
	if(!$domain)$domain=$_SERVER['HTTP_HOST'];
	return (((is_null($ssl) and xg_is_ssl()) or $ssl)?'https':'http').'://'.$domain; 
}
function xg_num2letter($number) {
    $number=(int)$number;
    if($number<=0)return "";
    $str="";
    $base26=range('a','z');
    while($number>0){
        $number--;
        $remainder=$number%26;
        $number=($number-$remainder)/26;
        $str=$base26[$remainder].$str;
    }
    return $str;
}
function xg_randstr($length){
	$low_ascii_bound=97;
	$upper_ascii_bound=122;
	while($i<$length){
		$randnum=mt_rand($low_ascii_bound,$upper_ascii_bound);
		$str=$str.chr($randnum);
		$i++;
	}
	return $str;
}
function xg_line_arr($str){
	$str=str_replace("\r","\n",$str);
	$str=str_replace("\n\n","\n",$str);
	return xg_arr($str,"\n");
}
function xg_trim($value){
	if(is_array($value)){
		foreach($value as $k => $v){
			$value[$k]=xg_trim($v);
		}
		return $value;
	}elseif(is_object($value)){
		foreach($value as $k => $v){
			$value->$k=xg_trim($v);
		}
		return $value;
	}elseif(is_string($value)){
		return trim($value);
	}else{
		return $value;
	}
}
function xg_is_config_fun($fun){
	if(substr($fun,-strlen('_input_fun'))=='_input_fun'){
		return true;
	}
	if(substr($fun,-strlen('_show_fun'))=='_show_fun'){
		return true;
	}
	return false;
}
// 全局的安全过滤函数
function xg_safehtml($text,$type='html',$charset=XG_CHAR){
	// 无标签格式
	$text_tags='';
	// 只保留链接
	$link_tags='<a>';
	// 只保留图片
	$image_tags='<img>';
	// 只存在字体样式
	$font_tags='<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
	// 标题摘要基本格式
	$base_tags=$font_tags.'<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike><section><header><footer><article><nav><audio><video>';
	// 兼容Form格式
	$form_tags=$base_tags.'<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
	// 内容等允许HTML的格式
	$html_tags=$base_tags.'<meta><ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
	// 全HTML格式
	$all_tags=$form_tags.$html_tags.'<!DOCTYPE><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
	// 过滤标签
	//pre标签内的代码也会被转义 $text=html_entity_decode($text,ENT_NOQUOTES,$charset);
	if($type=='text'){
		$typetags=$text_tags;
	}elseif($type=='link'){
		$typetags=$link_tags;
	}elseif($type=='image'){
		$typetags=$image_tags;
	}elseif($type=='font'){
		$typetags=$font_tags;
	}elseif($type=='base'){
		$typetags=$base_tags;
	}elseif($type=='form'){
		$typetags=$form_tags;
	}elseif($type=='html'){
		$typetags=$html_tags;
	}elseif($type=='all'){
		$typetags=$all_tags;
	}
	$text=strip_tags($text,$typetags);
	
	// 过滤攻击代码
	if($type !='all'){
		// 过滤危险的属性，如：过滤on事件lang js
		while(preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|codebase|dynsrc|lowsrc)([^><]*)/i',$text,$mat)){
			$text=str_ireplace($mat[0],$mat[1].$mat[3],$text);
		}
		while(preg_match('/(<[^><]+)(window\.|javascript:|js:|about:|file:|document\.|vbs:|cookie)([^><]*)/i',$text,$mat)){
			$text=str_ireplace($mat[0],$mat[1].$mat[3],$text);
		}
	}
	return $text;
}
//获取分页html
function xg_pagehtml($count,$pagesize=10,$curpage=0,$temp=''){
	if($count<=$pagesize)return false;
	$page=xg_page($count,$pagesize,'pc',$temp);
	if($curpage)$page->nowPage=$curpage;
	return $page->show();
}
//获取手机版分页html
function xg_mpagehtml($count,$pagesize=10,$curpage=0,$temp=''){
	if($count<=$pagesize)return false;
	$page=xg_page($count,$pagesize,'mobile',$temp);
	if($curpage)$page->nowPage=$curpage;
	return $page->show();
}
//获取分页html
function xg_page($count,$pagesize=10,$theme='',$temp=''){
	$page=new \xg\libs\page($count,$pagesize);
	if(!$theme)return $page;
	if($theme=='pc'){
		$page->maxcount=10;
	}elseif($theme=='mobile'){
		$page->maxcount=0;
	}
	$page->config('prev','上一页');
	$page->config('next','下一页');
	$page->config('end','尾页');
	$page->config('first','首页');
	
	if($theme=='pc'){
		$page->config('theme','%FIRST% %PREV% %PAGE% %NEXT% %END% %ROWS% %COUNT%');
	}elseif($theme=='mobile'){
		$page->config('theme','%FIRST% %PREV% %NEXT% %END%');//%LINK_PAGE%
	}
	if($temp)$page->config('temp',$temp);
	return $page;
}
function xg_deldir($dirname,$nodel=array()){
	$dirname=xg_replace_path($dirname);
	if(!file_exists($dirname)){
		return false;
	}
	if((is_file($dirname) or is_link($dirname))){
		return unlink($dirname);
	}
	if($nodel){
		if(is_string($nodel))$nodel=xg_arr($nodel);
		foreach($nodel as $k=>$v){
			if($dirname==xg_replace_path($v))return false;
		}
	}
	$dir=dir($dirname);
	if($dir){
		while(false !==$entry=$dir->read()){
			if($entry=='.' || $entry=='..'){
				continue;
			}
			xg_deldir($dirname.'/'.$entry,$nodel);
		}
	}
	rmdir($dirname);
	if(gettype($dir)=='object')$dir->close();
	return;
}
function xg_copydir($dirname,$toname){
	$dirname=xg_replace_path($dirname);
	$toname=xg_replace_path($toname);
	if(is_dir($dirname.'/'.$entry))mkdir($toname.'/'.$entry,0755,true);
	if((is_file($dirname))){
		return copy($dirname,$toname);
	}
	$dir=dir($dirname);
	if($dir){
		while(false !==$entry=$dir->read()){
			if($entry=='.' || $entry=='..'){
				continue;
			}
			xg_copydir($dirname.'/'.$entry,$toname.'/'.$entry);
		}
	}
	if(gettype($dir)=='object')$dir->close();
	return;
}
function xg_json_filter($cont){
	if(is_array($cont)){
		foreach($cont as $k=>$v){
			$cont[$k]=xg_json_filter($cont[$k]);
		}
	}elseif(is_string($cont)){
		if(xg_config('config.filter_upload_path') and substr($cont,0,8)=='/upload/'){
			if(file_exists(XG_PUBLIC.$cont)){
				$cont=xg_http_domain().XG_STATIC.substr($cont,1);
			}
		}
	}
	return $cont;
}
function xg_view_filter($cont){
	$path=xg_config('static');
	foreach($path as $k=>$v){
		$cont=str_replace($k,$v,$cont);
	}
	$cont=xg_view_file_version($cont);
	$cont=xg_tochar($cont,XG_CHAR);
	return $cont;
}
function xg_filter_point($value){
	if(is_array($value)){
		foreach($value as $k=>$v){
			$value[$k]=xg_filter_point($v);
		}
	}elseif(is_object($value)){
		foreach($value as $k=>$v){
			$value->$k=xg_filter_point($v);
		}
	}elseif(is_string($value)){
		$value=str_replace('.','',$value);
	}
	return $value;
}
function xg_filter_phptag($value){
	if(defined('NO_FILTER_PHPTAG'))return $value;
	if(is_string($value)){
		$value=str_replace(array('<?php','<? ','<?	','<?=','?>'),array('<----?php','<----? ','<----?	','<----?=','?---->'),$value);
	}
	return $value;
}
function xg_restore_phptag($value){
	if(defined('NO_FILTER_PHPTAG'))return $value;
	if(is_string($value)){
		$value=str_replace(array('<----?php','<----? ','<----?	','<----?=','?---->'),array('<?php','<? ','<?	','<?=','?>'),$value);
	}
	return $value;
}
function xg_cache_path($name){
	$path=XG_RUNTIME.'/cache';
	$name=md5($name);
	$dir=$path.'/'.substr($name,0,2).'/';
	if(!file_exists($dir))mkdir($dir,0755,true);
	return $dir.substr($name,2,16).'.php';
}
function xg_cache_group($group,$name,$delete=false){
	$group=preg_replace('/[^a-zA-Z0-9\._\-]+/','',$group);
	if(!$group)return;
	$path=XG_RUNTIME.'/cache/'.$group.'.php';
	$data=include $path;
	if($data and $delete){
		$data=array_diff($data,[$name]);
		if(!$data)@unlink($path);
	}elseif($name){
		$data[]=$name;
		mkdir(dirname($path),0755,true);
	}elseif(is_null($name) and $data){
		foreach($data as $n){
			@unlink(xg_cache_path($n));
		}
		@unlink($path);
	}
	if($data)xg_fcont($path,"<?php if(!defined('XG'))exit('Access denied!');return unserialize('".serialize($data)."');?>");
}
function xg_cache($name,$data=false,$exp=0,$group=''){
	$path=XG_RUNTIME.'/cache';
	$filepath=xg_cache_path($name);
	static $memcache=[];
	if($data===false){
		if($memcache[$name]){
			return $memcache[$name];
		}
	}
	if($data){
		if($exp>0)$exp=$exp>XG_TIME?$exp:($exp+XG_TIME);
		$data="<?php if(!defined('XG'))exit('Access denied!');/*".str_replace(['"',"'","/"],'',$name)."*/return unserialize('".str_replace("'","\\'",serialize(['exp'=>$exp,'data'=>$data]))."');?>";
		xg_fcont($filepath,$data);
		xg_cache_group($group,$name);
		xg_slog('cache '.$name.' '.$filepath);
		return $data;
	}elseif(is_null($data)){
		$rt=@unlink($filepath);
		xg_cache_group($group,$name,true);
		xg_slog('delete cache '.$name);
		return $rt;
	}elseif(file_exists($filepath)){
		$data=include $filepath;
		if($data){
			if($data['exp'] and $data['exp']<XG_TIME){
				xg_slog('cache expire '.$name);
				@unlink($filepath);
				return null;
			}
			xg_slog('read cache '.$name);
			$memcache[$name]=$data['data'];
			return $data['data'];
		}
	}
	return null;
}
function xg_db($conf=null){
	if(!$conf)$conf=xg_config('database');
	$db=new \xg\database($conf);
	return $db;
}
function xg_model($name='',$prefix=null){
	if($prefix===1){
		$modelname=$name;
		$name='content';
		$prefix=null;
	}
	if(class_exists($class='\\model\\'.$name)){
		$model=new $class();
	}elseif(class_exists($class='\\xgphp\\model\\'.$name)){
		$model=new $class(basename($name));
	}else{
		if(($pos=strpos($name,'@'))!==false){
			$sys=substr($name,0,$pos);
			$name=substr($name,$pos+1);
			$class="\\sys\\$sys\\model\\$name";
		}elseif(($pos=strpos($name,'#'))!==false){
			$addon=substr($name,0,$pos);
			$name=substr($name,$pos+1);
			$class="\\addons\\$addon\\model\\$name";
		}else{
			$arr=xg_arr(trim($name,'/'),'/');
			if(count($arr)==2){
				$app=$arr[0];
				$name=$arr[1];
			} else {
				$app=defined('XG_APP')?XG_APP:'';
			}
			if($app=='xgphp'){
				$class='\\xg\\model\\'.$name;
			}else{
				$class='\\apps\\'.$app.'\\model\\'.$name;
			}
		}
		if(class_exists($class)){
			$model=new $class(basename($name));
		}else{
			$model=new \xg\model(basename($name));
			if($sys)$model->sys($sys);
		}
	}
	if($modelname){
		$model->name($modelname);
	}
	if($prefix)$model->prefix($prefix);
	return $model;
}
function xg_where(){
	return  new \xg\where();
}
function xg_is_where($v){
	if(!is_object($v))return false;
	return get_class($v)=='xg\\where';
}
function xg_php_copy(){
	return '讯高小程序<br><a href="//xgphp.xg3.cn" target="_blank">xgphp.xg3.cn</a> &copy; 讯高科技';
}
function xg_to_guid_string($mix){
	if(is_object($mix)){
		return spl_object_hash($mix);
	} elseif(is_resource($mix)){
		$mix=get_resource_type($mix).strval($mix);
	} else {
		$mix=serialize($mix);
	}
	return md5($mix);
}
function xg_query($url){
	parse_str($url,$params);
	return $params;
}
function xg_rurl($url=false,$params=array(),$file=''){
	$url=xg_url($url,$params,$file);
	$url=substr($url,strlen(XG_ROOT)-1);
	return $url;
}
function xg_url($url=false,$params=array(),$file=''){
	if($sys=xg_sysurl($url,$params))return $sys;
	if($url=='/'){
		return XG_ROOT;
	}
	if($url===1)return xg('url');
	if($url===2)return xg('fullurl');
	if(is_array($url)){
		$params=$url;
		$url='';
	}
	if(strpos($url,'?')>-1){
		$arr=xg_arr($url,'?');
		$url=$arr[0];
		$query=$arr[1];
		if($query){
			parse_str($query,$query2);
			if($query2)$params=xg_merge($query2,$params);
		}
	}
	if(substr($url,-5)=='.html')$url=substr($url,0,-5);
	$arr=xg_arr(trim($url,'/'),'/');
	if(count($arr)==1){
		$ctl=XG_CTL;
		if($arr[0]){
			$act=$arr[0];
		}else{
			$act=XG_ACT;
		}
	}elseif(count($arr)==2){
		$ctl=$arr[0];
		$act=$arr[1];
	}elseif(count($arr)==3){
		$app=$arr[0];
		$ctl=$arr[1];
		$act=$arr[2];
	}
	if(!$app)$app=XG_APP;
	if(xg_config('app.map'))$appmap=array_flip(xg_config('app.map'));
	if(xg_config('app.rename'))$rename=xg_config('app.rename');
	$reapp=$rename[$app];
	if($file){
		$file=strtolower($file);
		if(substr($file,-4)!=='.php')$file=$file.'.php';
	}
	if($appmap[$app]){
		$app2=$appmap[$app];
	}elseif($appmap[$reapp]){
		$app2=$appmap[$reapp];
	}elseif($reapp){
		$app2=$reapp;
	}
	$cur=xg('url');
	$params=($params?http_build_query($params):'');
	if(XG_ROUTE){
		$url=XG_ROOT.($app2?$app2:$app).'/'.$ctl.'/'.$act.'.html'.($params?'?'.$params:'');
	}else{
		$url=($file?(dirname($_SERVER['SCRIPT_NAME']).'/'.$file):$_SERVER['SCRIPT_NAME']).'?xgapp='.$app.'&xgctl='.$ctl.'&xgact='.$act.($params?'&'.$params:'');
	}
	if(strpos($url,'/'.XG_BIND_APP.'/')===0){
		$url=substr($url,strlen('/'.XG_BIND_APP));
	}
	return $url;
}
function xg_sysurl($url,$params=array(),$app=''){
	if(preg_match('/([a-zA-Z0-9\-_]+)@([a-zA-Z0-9\-_]+).?([a-zA-Z0-9\-_]*)/',(String)$url,$rt)){
		$sys=$rt[1];
		$ctl=$rt[2];
		$act=$rt[3];
		$params=array_merge(array('sys'=>$sys,'controller'=>$ctl,'action'=>$act),$params);
		return xg_url(($app?$app:XG_APP).'/sys/execute/',$params);
	}
}
function xg_addon($url,$params=array(),$app=''){
	if(strpos($url,'://')!==false){
		$url=parse_url($url);
		$addon=$url['scheme'];
		$controller=$url['host'];
		$action=trim($url['path'],'/');
		if(isset($url['query'])){
			parse_str($url['query'], $query);
			$params=array_merge($query,$params);
		}
	}else{
		$get=xg_input('get.');
		$addon=$get['addon'];
		$controller=$get['controller'];
		if($url)$action=$url;
	}
	$params=array_merge(array('addon'=>$addon,'controller'=>$controller,'action'=>$action),$params);
	return xg_url(($app?'/'.$app.'/':'').'addons/execute',$params);
}
function xg_strip_whitespace($content){
	$stripStr='';
	$tokens=token_get_all($content);
	$last_space=false;
	for($i=0,$j=count($tokens); $i<$j; $i++){
		if(is_string($tokens[$i])){
			$last_space=false;
			$stripStr.=$tokens[$i];
		} else {
			switch($tokens[$i][0]){
				case T_COMMENT:
				case T_DOC_COMMENT:
					break;
				case T_WHITESPACE:
					if(!$last_space){
						$stripStr.=' ';
						$last_space=true;
					}
					break;
				default:
					$last_space=false;
					$stripStr.=$tokens[$i][1];
			}
		}
	}
	return $stripStr;
}
function xg_custom_sid(){
	$arr=xg_line_arr(xg_config('site.custom_sid'));
	$custom_sid=array();
	if($arr and $arr[0]){
		foreach($arr as $v){
			$arr2=str2arr($v,'=');
			if($arr2[0] and $arr2[1] and is_numeric($arr2[1]))$custom_sid[$arr2[0]]=$arr2[1];
		}
	}
	return $custom_sid;
}
function xg_enid($id,$hashid_key=''){
	if(!$hashid_key)$hashid_key=xg_config('site.hashid_key');
	static $hashids=[];
	if(!$hashids[$hashid_key])$hashids[$hashid_key]=new \xg\libs\Hashids($hashid_key,10,'abcdefghijklmnopqrstuvwxyz');
	return $hashids[$hashid_key]->encode($id);
}
function xg_deid($id,$hashid_key=''){
	if(!isset($cus_hashid))$cus_hashid=xg_custom_sid();
	if($cus_hashid[$id]){
		return $cus_hashid[$id];
	}
	if(!$hashid_key)$hashid_key=xg_config('site.hashid_key');
	static $hashids=[];
	if(!$hashids[$hashid_key])$hashids[$hashid_key]=new \xg\libs\Hashids($hashid_key,10,'abcdefghijklmnopqrstuvwxyz');
	$idarr=(array)$hashids[$hashid_key]->decode($id);
	return (int)$idarr[0];
}
function xg_intid($id){
	if(is_string($id) and strpos($id,',')>-1){
		$isstr=true;
		$id=explode(',',$id);
	}
	if(is_array($id)){
		for($i=0;$i<count($id);$i++){
			if(isset($id[$i]))$id[$i]=intid($id[$i]);
		}
		return $isstr?implode(',',$id):$id;
	}
	return is_numeric($id)?$id:xg_deid($id);
}
function xg_strid($id){
	if(is_string($id) and strpos($id,',')>-1){
		$isstr=true;
		$id=explode(',',$id);
	}
	if(is_array($id)){
		for($i=0;$i<count($id);$i++){
			if(isset($id[$i]))$id[$i]=xg_strid($id[$i]);
		}
		return $isstr?implode(',',$id):$id;
	}
	return (is_numeric($id) and $id>=0)?xg_enid($id):$id;
}
function xg_slog(){
	$args=func_get_args();
	if(!$GLOBALS['_XG']['xg_slog_list'])$GLOBALS['_XG']['xg_slog_list']=array();
	foreach($args as $str){
		if(gettype($str)=='array' or gettype($str)=='object'){
			$str=print_r($str,true);
		}
		$GLOBALS['_XG']['xg_slog_list'][]=date("Y-m-d H:i:s")."	".$str;
	}
}
function xg_rlog(){
	$args=func_get_args();
	$dir=XG_RUNTIME.'/log/';
	mkdir($dir,0755,true);
	foreach($args as $str){
		if(gettype($str)=='array' or gettype($str)=='object'){
			$str=print_r($str,true);
		}
		file_put_contents($dir.date('Ymd').'.log',"=======================================\r\n".$str."\r\n",FILE_APPEND);
	}
}
function xg_dlog(){
	$str=func_get_args();
	foreach($str as $s){
		if(gettype($s)=='array' or gettype($s)=='object'){
			$s=print_r($s,true);
		}
		file_put_contents(XG_PATH.'/test.txt',$s."\r\n",FILE_APPEND);
	}
}
function xg_save_slog(){
	$list=$GLOBALS['_XG']['xg_slog_list'];
	$dir=XG_RUNTIME.'/log/';
	foreach(glob($dir.'*') as $k=>$v){
		if(filemtime($v)<strtotime(date('Y-m-d H:i:s').'-1 month ')){
			@unlink($v);
		}
	}
	mkdir($dir,0755,true);
	file_put_contents($dir.date('Ymd').'.log',"=======================================\r\n".xg_str($list,"\r\n")."\r\n",FILE_APPEND);
}
function xg_rterr($msg='',$goto='',$num=false){
	xg_slog('rterr='.substr(print_r(xg_char($msg),true),0,100).' '.$goto.' '.$num);
	return array(0,$msg,$goto,'rtmsg',$num);
}
function xg_rtok($msg='',$goto='',$num=false){
	xg_slog('rtok='.substr(print_r(xg_char($msg),true),0,100).' '.$goto.' '.$num);
	return array(1,$msg,$goto,'rtmsg',$num);
}
function xg_iserr($info){
	if(!is_array($info))return false;
	$c=count($info);
	if($c!=5)return false;
	$in=array_intersect_key($info,range(0,$c-1));
	if(count($in)!=$c)return false;
	if($info[3]!='rtmsg')return false;
	return $info[0]===0;
}
function xg_isok($info){
	if(!is_array($info))return false;
	$c=count($info);
	if($c!=5)return false;
	$in=array_intersect_key($info,range(0,$c-1));
	if(count($in)!=$c)return false;
	if($info[3]!='rtmsg')return false;
	return $info[0]===1;
}
function xg_rtjson($msg='',$goto='',$num=false){
	if($goto){
		if(is_array($msg[1])){
			$msg[1]['goto']=$goto;
		}else{
			$msg[1]=array(
				'goto'=>$goto,
				'msg'=>$msg[1]
			);
		}
	}
	if($num!==false){
		return xg_jsonmsg($num,$msg[1],$msg[2]);
	} else {
		if($msg[0]){
			return xg_jsonok($msg[1],$msg[2]);
		} else {
			return xg_jsonerr($msg[1],$msg[2]);
		}
	}
}
function xg_rtmsg($msg='',$goto='',$num=false){
	if(!$goto)$goto=$msg[2];
	if($num===false and $msg[4]!==false){
		$num=$msg[4];
	}
	if(xg_isajax()){
		xg_exit(xg_rtjson($msg,$goto,$num),'application/json');
	} else {
		if(is_array($msg[1])){
			$message=$msg[1]['msg'];
		} else {
			$message=$msg[1];
		}
		if($msg[0]){
			return xg_success($message,$goto,$num);
		} else {
			return xg_error($message,$goto,$num);
		}
	}
}
function xg_success($msg='',$goto='',$num=false,$wait=3){
	if(xg_isajax()){
		if($goto){
			$goto=$goto;
			if(is_array($msg)){
				$msg['goto']=$goto;
			} else {
				$msg=array(
					'msg'=>$msg,
					'goto'=>$goto
				);
			}
		}
		if(is_array($num)){
			if(is_string($msg))$msg=array(
				'msg'=>$msg
			);
			$msg=array_merge($msg,$num);
			return xg_jsonok($msg);
		}elseif($num!==false){
			return xg_jsonmsg($num,$msg);
		}else{
			if(is_string($msg))$msg=['msg'=>$msg];
			$msg['wait']=$wait;
			if($num!==false)$msg['num']=$num;
			return xg_jsonok($msg);
		}
	} else {
		if(is_array($msg)){
			if(!$goto)$goto=$msg['goto'];
			$msg=$msg['msg'];
		}
		if(!$goto)$goto='javascript:void(0);';
		$info=array(
			'message'=>$msg,
			'goto'=>$goto,
			'num'=>$num,
			'noadmin'=>1,
			'wait'=>$wait
		);
		xg_exit(xg_filter_fetch(xg_msg_view(),$info));
	}
}
function xg_error($msg='',$goto='',$num=false,$wait=3){
	if(xg_isajax()){
		if($goto){
			$goto=$goto;
			if(is_array($msg)){
				$msg['goto']=$goto;
			} else {
				$msg=array(
					'msg'=>$msg,
					'goto'=>$goto
				);
			}
		}
		if(is_array($num)){
			if(is_string($msg))$msg=array(
				'msg'=>$msg
			);
			$msg=array_merge($msg,$num);
			return xg_jsonerr($msg);
		}elseif($num!==false){
			return xg_jsonmsg($num,$msg);
		}else{
			if(is_string($msg))$msg=['msg'=>$msg];
			$msg['wait']=$wait;
			if($num!==false)$msg['num']=$num;
			return xg_jsonerr($msg);
		}
	}else{
		if(is_array($msg)){
			if(!$goto)$goto=$msg['goto'];
			$msg=$msg['msg'];
		}
		if(!$goto)$goto='javascript:void(0);';
		$info=array(
			'message'=>$msg,
			'goto'=>$goto,
			'num'=>$num,
			'iserr'=>1,
			'noadmin'=>1,
			'wait'=>$wait
		);
		xg_slog('error msg:'.xg_jsonstr($info));
		xg_exit(xg_filter_fetch(xg_msg_view(),$info));
	}
}
function xg_msg_view(){
	if(file_exists(XG_APPS.'/'.XG_APP.'/view/public/message.html')){
		$view=XG_APPS.'/'.XG_APP.'/view/public/message';
	}else{
		$view='/view/message';
	}
	return $view;
}
function xg_view_file_http($content){
	preg_match_all('/["\']{1}([^"\']*[\.jpg|\.png|\.gif|\.jpeg])["\']{1}/is',$content,$matchs);
	$matchs[1]=array_unique($matchs[1]);
	foreach($matchs[1]as $k=>$v){
		$v2=$v;
		if(substr($v,0,1)=='/'){
			if($siteurl=xg_config('site.site_url')){
				if(substr($v2,0,strlen(URL_PATH))==URL_PATH){
					$v2=substr($v2,strlen(URL_PATH));
				}
				$v2=$siteurl.$v2;
			} else {
				$v2=xg_http_domain(xg_config('site.public_domain')).$v2;
			}
		}
		$content=str_replace($v,''.$v2,$content);
	}
	return $content;
}
function xg_view_file_version($content){
	preg_match_all('/(?:href|src)=["\']{1}([^"\']*[\.css|\.js])["\']{1}/is',$content,$matchs);
	$matchs[1]=array_unique($matchs[1]);
	foreach($matchs[1]as $k=>$v){
		if(XG_APP=='home' and xg_config('site.home-theme') and substr($v,0,$len1=strlen($str1='/static/themes/'.xg_config('site.home-theme').'/'))==$str1){
			$v2=substr($v,$len1);
			$path1=XG_PATH.'/themes/'.xg_config('site.home-theme').'/static/'.$v2;
			$path2=XG_PUBLIC.$str1.$v2;
			$version1=@filemtime($path1);
			$version2=@filemtime($path2);
			if($version1>$version2 or !file_exists($path2)){
				@unlink($path2);
				mkdir(dirname($path2),0755,true);
				@copy($path1,$path2);
				$version=$version1;
			}else{
				$version=$version2;
			}
		}elseif(substr($v,0,1)=='/'){
			$version=@filemtime(XG_PUBLIC.substr($v,strlen(XG_STATIC)-1));
		}else{
			$version='';
		}
		if($version)$content=str_replace($v,$v.'?v='.date('YmdHis',$version),$content);
	}
	return $content;
}
function xg_filter_fetch($tpl='',$data=array()){
	$cont=xg_fetch($tpl,$data);
	$cont=xg_view_filter($cont);
	return $cont;
}
function xg_fetch($tpl='',$data=array()){
	$view=xg_view();
	$cont=$view->fetch($tpl,$data);
	return $cont;
}
function xg_view($conf=array(),$new=false){
	static $view;
	if($conf===true){
		$new=true;
		$conf=null;
	}
	if(!$view or $new){
		$conf=xg_merge(xg_config('view'),$conf);
		$obj=new \xg\view($conf);
		if($new)return $obj;
		$view=$obj;
	}
	return $view;
}
function xg_isajax(){
	if(xg_input('get._ajax'))return true;
	if(xg_input('post._ajax'))return true;
	$value=$_SERVER['HTTP_X_REQUESTED_WITH'];
	$result=$value&&'xmlhttprequest'==strtolower($value)?true:false;
	return $result;
}
function xg_array_map_recursive($filter,$data){
	$result=array();
	foreach($data as $key=>$val){
		$result[$key]=is_array($val)?xg_array_map_recursive($filter,$val):call_user_func($filter,$val);
	}
	return $result;
}
function xg_input($name,$default='',$filter=null,$datas=null){
	if(strpos($name,'.')){
		list($method,$name)=explode('.',$name,2);
	} else {
		$method='request';
	}
	if(substr($name,-2)=='/i'){
		$filter='intval';
		$name=substr($name,0,-2);
	}
	switch(strtolower($method)){
		case 'get':
			$input=&$_GET;
			break;
		case 'post':
			$input=&$_POST;
			break;
		case 'put':
			parse_str(file_get_contents('php://input'),$input);
			break;
		case 'param':
			switch($_SERVER['REQUEST_METHOD']){
				case 'POST':
					$input=$_POST;
					break;
				case 'PUT':
					parse_str(file_get_contents('php://input'),$input);
					break;
				default:
					$input=$_GET;
			}
			break;
		case 'path':
			$input=array();
			if(!empty($_SERVER['PATH_INFO'])){
				$depr='/';
				$input=explode($depr,trim($_SERVER['PATH_INFO'],$depr));
			}
			break;
		case 'request':
			$input=&$_REQUEST;
			break;
		case 'session':
			$input=&$_SESSION;
			break;
		case 'cookie':
			$input=&$_COOKIE;
			break;
		case 'server':
			$input=&$_SERVER;
			break;
		case 'globals':
			$input=$GLOBALS;
			break;
		case 'data':
			$input=&$datas;
			break;
		default:
			return null;
	}
	if($name==''){
		$data=$input;
		$filters=isset($filter)?$filter:'trim,strip_tags';
		if($filter instanceof \Closure){
			if(is_array($data)){
				$data=xg_array_map_recursive($filter,$data);
			}else{
				$data=$filter($data);
			}
		}elseif($filters){
			if(is_string($filters)){
				$filters=explode(',',$filters);
			}
			foreach($filters as $filter){
				$data=xg_array_map_recursive($filter,$data);
			}
		}
	}elseif(isset($input[$name])){
		$data=$input[$name];
		$filters=isset($filter)?$filter:'trim,strip_tags';
		if($filter instanceof \Closure){
			if(is_array($data)){
				$data=xg_array_map_recursive($filter,$data);
			}else{
				$data=$filter($data);
			}
		}elseif($filters){
			if(is_string($filters)){
				$filters=explode(',',$filters);
			}elseif(is_int($filters)){
				$filters=array(
					$filters
				);
			}
			foreach($filters as $filter){
				if(function_exists($filter)){
					$data=is_array($data)?xg_array_map_recursive($filter,$data):$filter($data);
				} else {
					$data=filter_var($data,is_int($filter)?$filter:filter_id($filter));
					if(false===$data){
						return isset($default)?$default:null;
					}
				}
			}
		}
	} else {
		$data=isset($default)?$default:null;
	}
	is_array($data)&&array_walk_recursive($data,'xg_sql_filter');
	return xg_utf8($data);
}
function xg_sql_filter(&$value){
	if(preg_match('/^(EXP|NEQ|GT|EGT|LT|ELT|OR|XOR|LIKE|NOTLIKE|NOT BETWEEN|NOTBETWEEN|BETWEEN|NOTIN|NOT IN|IN|BIND)$/i',$value)){
		$value.=' ';
	}
}
function xg_zero_fill($number,$length) {
	$string=(string)$number;
	while(strlen($string)<$length)$string='0'.$string;
	return $string;
}
function xg_ucwords($str){
	return str_replace(' ','',ucwords(str_replace('_',' ',$str)));
}
function xg_in_array($val,$arr){
	if(is_array($arr)){
		return in_array($val,$arr);
	}
	return false;
}
function xg_arr($str,$glue=','){
	if(!is_array($str)){
		return explode($glue,$str);
	} else {
		return $str;
	}
}
function xg_str($arr,$glue=','){
	if(is_array($arr)){
		return implode($glue,$arr);
	} else {
		return $arr;
	}
}
function xg_tree_val($ka,$a){
	$v=$a;
	while($k=array_shift($ka)){
		$v=$v[$k];
	}
	return $v;
}
function xg_tree($ka,$v){
	$a=$v;
	while($k=array_pop($ka)){
		$a2=[];
		$a2[$k]=$a;
		$a=$a2;
	}
	return $a;
}
function xg_merge(){
	$list=func_get_args();
	$new=array();
	if($list[count($list)-1]===true){
		$recursive=true;
	}
	if($list[count($list)-1]===1){
		$plus=true;
	}
	foreach($list as $arr){
		if(is_array($arr)){
			if($plus){
				$new=$new+$arr;
			}elseif($recursive){
				$new=array_merge_recursive($new,$arr);
			}else{
				$new=array_merge($new,$arr);
			}
		}
	}
	return $new;
}
function xg_extend(){
	$list=func_get_args();
	$new=array();
	foreach($list as $arr){
		if(is_array($arr)){
			foreach($arr as $k=>$v){
				$new[$k]=$v;
			}
		}
	}
	return $new;
}
function xg_splice($source,$new,$n){
	$parta=array_slice($source,0,$n);
	$partb=array_slice($source,$n);
	return xg_merge($parta,$new,$partb);
}
function xg_theme_config($name,$key=null){
	$config=xg_jsonarr(xg_fcont(XG_THEMES.'/'.$name.'/config.json'));
	if($key)return $config[$key];
	return $config;
}
function xg_load_sys_config($name,$key=null){
	$config=xg_jsonarr(xg_fcont(xg_replace_path(XG_PATH.'/sys/'.$name).'/config.json'));
	if($key)return $config[$key];
	return $config;
}
function xg_sys_config($sys,$name=null){
	$key='sys.'.$sys;
	if($name)$key=$key.'.'.$name;
	return xg_config($key);
}
function xg_load_addon_config($name,$key=null){
	$config=xg_jsonarr(xg_fcont(xg_replace_path(XG_PATH.'/addons/'.$name).'/config.json'));
	if($key)return $config[$key];
	return $config;
}
function xg_addon_config($addon,$name=null){
	$key='addon.'.$addon;
	if($name)$key=$key.'.'.$name;
	return xg_config($key);
}
function xg_addons($type=null){
	static $result=null;
	if(is_null($result)){
		$model=xg_model('addons');
		if($type)$model->where('type',$type)->where('status',1);
		$list=$model->column('name');
		$result=[];
		foreach($list as $name){
			$result[$name]=xg_merge(xg_load_addon_config($name),xg_addon_config($name));
		}
	}
	return $result;
}
function xg_config($name,$value=null,$default=null){
	static $data=[];
	$namearr=xg_arr($name,'.');
	$key=$namearr[0];
	if(file_exists(XG_PATH.'/config/'.$key.'.php')){
		$data[$key]=include(XG_PATH.'/config/'.$key.'.php');
	}
	if(defined('XG_APP')){
		if(file_exists(XG_PATH.'/apps/'.XG_APP.'/config/'.$key.'.php')){
			$data2=include(XG_PATH.'/apps/'.XG_APP.'/config/'.$key.'.php');
			$data[$key]=xg_merge($data[$key],$data2);
		}
	}
	$data=xg_merge($data,xg('hooks.config'),true);
	if($key=='site'){
		$data['site']=xg_site_config();
	}
	if($key=='addon' and $addon=$namearr[1]){
		if(!$config=$data['addon'][$addon]){
			$config=$data['addon'][$addon]=xg_jsonarr(xg_model('addons')->where('name',$addon)->value('config'));
		}
		if($name2=$namearr[2]){
			return $config[$name2];
		}
		return $config;
	}
	if(strpos($name,'.')===false){
		if(is_null($value))return isset($data[$name])?$data[$name]:$default;
		$data[$name]=$value;
		return null;
	}
	if(is_null($value)){
		$value=xg_tree_val($namearr,$data);
		return $value?$value:$default;
	}
	$data[$name[0]][$name[1]]=$value;
	return null;
}
function xg_site_config($name=''){
	if(!file_exists(XG_DATA.'/installed'))return;
	static $config;
	$config=xg_cache('site_config');
	if(!$config){
		$where=array('status'=>1);
		$list=xg_model('config')->where($where)->fields(array('type','name','value','group'))->select();
		$config=array();
		foreach($list as $k=>$v){
			if($v['type']=='int'){
				$v['value']=intval($v['value']);
			}elseif($v['type']=='bool'){
				$v['value']=!!$v['value'];
			}
			$config[$v['name']]=$v['value'];
		}
		$theme_color=$config['app_theme_color'];
		xg_cache('site_config',$config,24*60*60);
	}
	if($name)return $config[$name];
	return $config;
}
function xg_jsonok($msg="",$goto=''){
	return xg_jsonmsg(true,$msg,$goto);
}
function xg_jsonerr($msg="",$goto=''){
	return xg_jsonmsg(false,$msg,$goto);
}
function xg_jsonmsg($ok=false,$msg="",$goto=''){
	if(is_array($ok)){
		$return=$ok;
	}elseif(is_array($msg)){
		$msg['ok']=!!$ok;
		$return=$msg;
	}elseif($msg){
		$return=array(
			"ok"=>!!$ok,
			"msg"=>$msg
		);
	}else{
		$return=array(
			"ok"=>!!$ok
		);
	}
	if($goto)$return['goto']=$goto;
	$return=xg_hooks('jsonmsg')->def($return)->run($return)->last();
	$return=xg_json_filter($return);
	$return=xg_jsonstr($return);
	xg_exit($return,'application/json');
}
function xg_jsonstr($v){
	$v=xg_utf8($v);
	if(version_compare(PHP_VERSION,'5.4.0','<')/* or xg_isajax() */){
		return json_encode($v);
	} else {
		return json_encode($v,JSON_UNESCAPED_UNICODE);
	}
}
function xg_jsonarr($v,$p=true){
	if(is_string($v)){
		$rt=json_decode($v,$p);
	} else {
		$rt=$v;
	}
	return $rt;
}
?>