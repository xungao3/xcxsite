<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
function xg_vcode(){
	static $obj=null;
	if(is_null($obj))$obj=new \xg\libs\vcode();
	return $obj;
}
function xg_member_groups($id=0,$col=null){
	$data=xg_model('member_group')->where('status')->column();
	if($id){
		if($col)return $data[$id][$col];
		return $data[$id];
	}
	if($col){
		foreach($data as $k=>$v){
			$data[$k]=$v[$col];
		}
	}
	return $data;
}
function xg_clear_login($prefix=XG_APP){
	xg_session('user_auth',null,$prefix);
	xg_session('user_auth_sign',null,$prefix);
	xg_cookie('user_auth',null,$prefix);
	xg_cookie('user_auth_sign',null,$prefix);
}
function xg_admin_auth($name,$info=null){
	if(!xg_have_auth($name,$info)){
		$auth=xg_cache('admin-auth');
		xg_error($info?$info:('没有权限'.$auth[$name]['title']));
	}
}
function xg_have_auth($name,$info=null){
	if(!$userid=xg_login('admin'))return false;
	$auth=xg_model('admin_auth')->cache('admin-auth')->order('`order` asc')->column('*','name');
	$admin=xg_model('admin')->json('auth')->where($userid)->cache('admin-'.$userid)->fields('auth,groupid,username,userid')->find();
	if(intval($admin['groupid'])===1)return true;
	$name=str_replace('_','-',$name);
	if(!$auth[$name] and !$info)return true;
	if($admin['auth'][$name])return true;
	if($admin['auth'][$auth[$name]['with']])return true;
	return false;
}
function xg_set_login($userid,$username='',$password=null,$group=1,$remember=true,$prefix=XG_APP){
	if(!$password){
		$main_key=xg_config('site.main_key');
		$user_auth=array('userid'=>$userid,'username'=>$username,'hash'=>md5(md5($userid.'_'.$main_key).'_'.$main_key));
		$user_auth_sign=xg_data_auth_sign($user_auth);
		xg_session('user_auth',$user_auth,$prefix);
		xg_session('user_auth_sign',$user_auth_sign,$prefix);
		return session_id();
	}
	$user_auth=array('userid'=>$userid,'username'=>$username,'group'=>$group,'module'=>$prefix,'password'=>xg_encode(md5($password)));
	$user_auth_sign=xg_data_auth_sign($user_auth);
	xg_session('user_auth',$user_auth,$prefix);
	xg_session('user_auth_sign',$user_auth_sign,$prefix);
	if($remember){
		xg_cookie('user_auth',json_encode($user_auth),array('prefix'=>$prefix,'expire'=>365*24*60*60));
		xg_cookie('user_auth_sign',$user_auth_sign,array('prefix'=>$prefix,'expire'=>365*24*60*60));
	}
	return session_id();
}
function xg_data_auth_sign($data){
	if(!is_array($data))$data=(array)$data;
	ksort($data);
	$code=http_build_query($data);
	$sign=sha1($code);
	return $sign;
}
function xg_cookie_login($prefix=XG_APP){
	if(!xg_session('user_auth','',$prefix)){
		$user_auth=xg_jsonarr(xg_cookie('user_auth','',$prefix),true);
		if($uid=intval($user_auth['userid'])){
			if($user_auth['module']=='admin'){
				$user=xg_model('admin')->where('userid',$uid)->find();
			}else{
				$user=xg_model('member')->where('userid',$uid)->find();
			}
			if($user and $user['status']<=0){
				xg_clear_login($prefix);
				return;
			}
			if($user){
				$check=md5(xg_decode($user_auth['password']).$user['salt'])==$user['password'];
				if($check){
					$user_auth['group']=$user['group'];
					$user_auth['username']=$user['username'];
					xg_session('user_auth',$user_auth,$prefix);
					xg_session('user_auth_sign',xg_data_auth_sign($user_auth),$prefix);
				}
			}
		}
	}
}
function xg_check_group($group=0,$prefix=XG_APP){
	if($group){
		$user=xg_session('user_auth','',$prefix);
		if(strpos($group,'|')>-1){
			$groups=explode('|',$group);
		}else{
			$groups=array($group);
		}
		if(!xg_in_array($user['group'],$groups))return false;
	}
	return true;
}
function xg_loginuser($prefix=XG_APP,$isadmin=false){
	$userid=xg_login($prefix);
	if($userid){
		if($isadmin)return xg_model('user')->find($userid);
		return xg_model('member')->find($userid);
	}
}
function xg_admin(){
	return xg_login('admin');
}
function xg_login($prefix=XG_APP){
	xg_cookie_login($prefix);
	$user=xg_session('user_auth','',$prefix);
	$user_auth_sign=xg_session('user_auth_sign','',$prefix);
	if(empty($user)){
		return 0;
	}else{
		$rt=($user_auth_sign==xg_data_auth_sign($user))?$user['userid']:0;
		return $rt;
	}
}
function xg_check_password($user,$password){
	if(md5(md5($password).$user['salt'])==$user['password']){
		return true;
	}
	return false;
}
function xg_cookie($name,$value='',$option=null){
	$config=array(
		'prefix'=>xg_config('config.cookie_prefix'),
		'expire'=>xg_config('config.cookie_expire'),
		'path'=>xg_config('config.cookie_path'),
		'domain'=>xg_config('config.cookie_domain'),
	);
	$config['expire']=$config['expire']?$config['prefix']:null;
	$config['path']=$config['path']?$config['path']:'/';
	$config['domain']=$config['domain']?$config['domain']:null;
	if(!is_null($option)){
		if(is_numeric($option)){
			$option=array('expire'=>$option);
		}elseif(is_string($option)){
			if(strpos($option,'&')){
				parse_str($option,$option);
			}else{
				$option=array('prefix'=>$option);
			}
		}
		$config=array_merge($config,$option);
	}
	$config['prefix']=($config['prefix']?trim($config['prefix'],'_'):'').'_';
	if(is_null($name)){
		if(empty($_COOKIE))return;
		$prefix=empty($value)?$config['prefix']:$value;
		if(!empty($prefix)){
			foreach($_COOKIE as $key=>$val){
				if(0===stripos($key,$prefix)){
					setcookie($key,'',time()-3600,$config['path'],$config['domain']);
					unset($_COOKIE[$key]);
				}
			}
		}
		return;
	}
	$name=$config['prefix'].$name;
	if(''===$value){
		if(isset($_COOKIE[$name])){
			return $_COOKIE[$name];
		}else{
			return null;
		}
	}else{
		if(is_null($value)){
			setcookie($name,'',time()-3600,$config['path'],$config['domain']);
			unset($_COOKIE[$name]);
			// 删除指定cookie
		}else{
			// 设置cookie
			if(is_array($value)){
				$value=json_encode($value);
			}
			$expire=$config['expire']?time()+intval($config['expire']):0;
			setcookie($name,$value,$expire,$config['path'],$config['domain'],false,true);
			$_COOKIE[$name]=$value;
		}
	}
}
function xg_session($name,$value='',$prefix=XG_APP){
	if(xg_config('config.var_session_id') and $_REQUEST[xg_config('config.var_session_id')]){
		session_id($_REQUEST[xg_config('config.var_session_id')]);
	}elseif(isset($name['id'])){
		session_id($name['id']);
	}
	if(!$prefix)$prefix=xg_config('config.session_prefix');
	if(is_array($name)){
		ini_set('session.auto_start',0);
		if(isset($name['name']))session_name($name['name']);
		if(isset($name['path']))session_save_path($name['path']);
		if(isset($name['domain']))ini_set('session.cookie_domain',$name['domain']);
		if(isset($name['expire']))ini_set('session.gc_maxlifetime',$name['expire']);
		if(isset($name['use_trans_sid']))ini_set('session.use_trans_sid',$name['use_trans_sid']?1:0);
		if(isset($name['use_cookies']))ini_set('session.use_cookies',$name['use_cookies']?1:0);
		if(isset($name['cache_limiter']))session_cache_limiter($name['cache_limiter']);
		if(isset($name['cache_expire']))session_cache_expire($name['cache_expire']);
		session_start();
	}elseif(''===$value){
		if(0===strpos($name,'[')){
			// session 操作
			if('[pause]'==$name){
				// 暂停session
				session_write_close();
			}elseif('[start]'==$name){
				$sess_name=session_name();
				if(session_start()){
					setcookie($sess_name,session_id(),null,'/',null,null,true);
				}
			}elseif('[destroy]'==$name){
				// 销毁session
				$_SESSION=array();
				session_unset();
				session_destroy();
			}elseif('[regenerate]'==$name){
				// 重新生成id
				session_regenerate_id();
			}
		}elseif(0===strpos($name,'?')){
			// 检查session
			$name=substr($name,1);
			if(strpos($name,'.')){
				// 支持数组
				list($name1,$name2)=explode('.',$name);
				return $prefix?isset($_SESSION[$prefix][$name1][$name2]):isset($_SESSION[$name1][$name2]);
			}else{
				return $prefix?isset($_SESSION[$prefix][$name]):isset($_SESSION[$name]);
			}
		}elseif(is_null($name)){
			// 清空session
			if($prefix){
				unset($_SESSION[$prefix]);
			}else{
				$_SESSION=array();
			}
			session_destroy();
		}elseif($prefix){
			// 获取session
			if(strpos($name,'.')){
				list($name1,$name2)=explode('.',$name);
				return isset($_SESSION[$prefix][$name1][$name2])?$_SESSION[$prefix][$name1][$name2]:null;
			}else{
				return isset($_SESSION[$prefix][$name])?$_SESSION[$prefix][$name]:null;
			}
		}else{
			if(strpos($name,'.')){
				list($name1,$name2)=explode('.',$name);
				return isset($_SESSION[$name1][$name2])?$_SESSION[$name1][$name2]:null;
			}else{
				return isset($_SESSION[$name])?$_SESSION[$name]:null;
			}
		}
	}elseif(is_null($value)){
		// 删除session
		if($prefix){
			unset($_SESSION[$prefix][$name]);
		}else{
			unset($_SESSION[$name]);
		}
	}else{
		// 设置session
		if($prefix){
			if(!is_array($_SESSION[$prefix])){
				$_SESSION[$prefix]=array();
			}
			$_SESSION[$prefix][$name]=$value;
		}else{
			$_SESSION[$name]=$value;
		}
	}
}
?>