<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
ini_set('display_errors',1);
error_reporting(E_ALL^E_NOTICE^E_WARNING^E_DEPRECATED^E_STRICT);
date_default_timezone_set('PRC');
define('XG',1);
define('XG_TIME',time());
if(!defined('XG_CHAR'))define('XG_CHAR','UTF-8');
header('Content-type:text/html;charset='.XG_CHAR);
if(version_compare(PHP_VERSION,'5.6.0','<')){
	exit('需要PHP5.6及以上版本支持。');
}
if(!defined('XG_ROOT'))define('XG_ROOT',str_replace('\\','/',dirname($_SERVER['PHP_SELF'])));
if(!defined('XG_STATIC'))define('XG_STATIC',XG_ROOT);
if(!defined('XG_PHP'))define('XG_PHP',__DIR__);
if(!defined('XG_PATH'))define('XG_PATH',dirname(__DIR__));
if(!defined('XG_BASE'))define('XG_BASE',XG_PHP.'/base');
if(!defined('XG_CORE'))define('XG_CORE',XG_PHP.'/core');
if(!defined('XG_EXTEND'))define('XG_EXTEND',XG_PATH.'/extend');
if(!defined('XG_RUNTIME'))define('XG_RUNTIME',XG_PATH.'/runtime');
if(!defined('XG_PUBLIC'))define('XG_PUBLIC',XG_PATH.'/public');
if(!defined('XG_CONFIG'))define('XG_CONFIG',XG_PATH.'/config');
if(!defined('XG_DATA'))define('XG_DATA',XG_PATH.'/data');
if(!defined('XG_CONF'))define('XG_CONF',XG_PATH.'/config');
if(!defined('XG_APPS'))define('XG_APPS',XG_PATH.'/apps');
if(!defined('XG_ADDONS'))define('XG_ADDONS',XG_PATH.'/addons');
if(!defined('XG_THEMES'))define('XG_THEMES',XG_PATH.'/themes');
if(!defined('XG_SYS'))define('XG_SYS',XG_PATH.'/sys');
require_once XG_BASE.'/function.php';
define('XG_TBPRE',xg_config('database.prefix'));
xg_slog('url='.xg('fullurl'));
xg_slog('from='.xg('referrer'));
xg_slog('agent='.xg('agent'));
xg_slog('ip='.xg_ip());
list($app,$ctl,$act)=xg_route()->data();
define('XG_APP',xg_filter_point($app));
define('XG_CTL',xg_filter_point($ctl));
define('XG_ACT',xg_filter_point($act));
$route_fun=xg_route()->fun();
xg_session('[start]');
if(!is_dir(XG_APPS.'/'.XG_APP)){
	xg_halt('无此模块！<br>'.XG_APP,404);
}
if(file_exists(XG_DATA.'/'.XG_APP.'/closed')){
	xg_error('模块被关闭');
}
$classname='\\apps\\'.XG_APP.'\\controller\\'.XG_CTL;
if(!$route_fun and !class_exists($classname)){
	xg_halt('无此控制器！<br>'.XG_CTL,404);
}
if(file_exists(XG_APPS.'/'.XG_APP.'/common.php'))require XG_APPS.'/'.XG_APP.'/common.php';
xg_data_info('xg_start');
$sys=xg_config('sys');
if($sys){
	$sys=array_keys($sys);
	foreach($sys as $sysi){
		foreach(glob(XG_PATH.'/sys/'.$sysi.'/hooks/*.php') as $file){
			require_once $file;
		}
	}
}
ob_start();
define('XG_LOADED',1);

try{
	if($route_fun){
		$out=$route_fun();
	}else{
		$class=new $classname();
		if(!method_exists($class,XG_ACT) and !method_exists($class,'__CALL')){
			xg_halt('无此操作！<br>'.XG_ACT,404);
		}
		$out=call_user_func([$class,$act]);
	}
	if(is_array($out)){
		xg_jsonmsg($out);
	}else{
		echo $out;
	}
}catch(\Throwable $e){
	xg_sys_error($e);
}

$out=ob_get_contents();
ob_clean();
$out=xg_view_filter($out);
$out=xg_restore_phptag($out);
echo $out;
$time=xg_data_info('xg_start','xg_end');
$mem=xg_data_info('xg_start','xg_end','m');
xg_slog('time='.$time.' memory='.$mem);
xg_save_slog();
?>