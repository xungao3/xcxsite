<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\libs;
class vcode{
	public function __construct(){
		if(xg_input('request.getpassword'))$this->type=1;
		if(xg_input('request.authaccount'))$this->type=2;
		$type=xg_input('request.type');
		if($type)$this->type=$type;
		if(!$this->type)$this->type=3;
	}
	protected function isemail($v){
		return preg_match("/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]{2,10}){1,2}$/",$v);
	}
	protected function ismobile($v){
		return preg_match("/^1[3|4|5|6|7|8|9]{1}[\d]{9}$/",$v);
	}
	protected function checkRegAccount($account){
		$tbname=xg_input('request.tbname');
		$tbname=preg_replace('/[^a-zA-Z0-9_]/','',$tbname);
		if(!$tbname)$tbname='member';
		if($this->isemail($account)){
			return xg_model($tbname)->where('email',$account)->value('userid');
		}elseif($this->ismobile($account)){
			return xg_model($tbname)->where('mobile',$account)->value('userid');
		}
		return false;
	}
	public function get($account=null){
		$err=$this->imgcode();
		if($err)return $err;
		if(!$account)$account=xg_input('request.account');
		if(!$account)$account=xg_input('request.mobile');
		if(!$account)$account=xg_input('request.email');
		$ismobile=$this->ismobile($account);
		$isemail=$this->isemail($account);
		if(!$isemail and !$ismobile)return "不是手机号或电子邮箱。";
		if($this->type==1){//注册过的账号验证
			if(!$user and !$this->checkRegAccount($account)){
				return "此".($isemail?"电子邮箱":"手机号")."没有被注册。";
			}
		}elseif($this->type==2){//任何账号（注册过的和未注册过的）验证
		}elseif($this->type==3){//未注册过的账号验证
			if($this->checkRegAccount($account)){
				return "此".($isemail?"电子邮箱":"手机号")."已经注册过了。";
			}
		}
		//一分钟后才可以获取
		if($lasttime=$this->in60s($account)){
			return '请'.($lasttime-(XG_TIME-60)).'秒后再获取。';
		}
		if($this->inHour($account)>=10){
			return '一个小时内最多发送10次验证码。';
		}
		if($this->inDay($account)>=20){
			return '一天内最多发送20次验证码。';
		}
		$client_ip=xg_ip();
		$ip_count=$this->ipCount($client_ip);
		if($ip_count and $ip_count>=20){
			return '此IP今天的验证码数量已经用完。';
		}
		$vcode=rand(100000,999999);
		if($error=$this->send($account,$vcode)){
			return $error;
		}else{
			xg_model("vcode")->insert(['account'=>$account,'vcode'=>$vcode,'type'=>(int)$this->type,'ip'=>$client_ip,'time'=>XG_TIME]);
			return false;
		}
	}
	
	public function check($account,$vcode,$type=3){
		$times=xg_model('times')->check_times(2);
		if($times){
			return '超过验证次数';
		}
		$this->timeOut();
		$check=$this->dbcheck($account,$vcode,$type);
		if($check==1){
			return false;
		}else{
			xg_model('times')->add_times(2);
			if($this->ismobile($account)){
				$accounttype='手机';
			}elseif($this->isemail($account)){
				$accounttype='电子邮箱';
			}
			if($check==-1){
				return "{$accounttype}验证码错误！";
			}elseif($check==-2){
				return "{$accounttype}验证码已过期！";
			}elseif($check==-3){
				return "请先获取{$accounttype}验证码！";
			}
		}
	}

	public function exp($account='',$vcode='',$type=''){
		$where=[];
		if($account)$where[]=['account','=',$account];
		if($vcode)$where[]=['vcode','=',$vcode];
		if($type)$where[]=['type','=',$type];
		if(count($where)>0){
			xg_model("vcode")->where($where)->update(['exp'=>1]);
		}
	}

	protected function send($account,$vcode){
		//if(xg_ip()=='127.0.0.1'){return false;}
		if($this->ismobile($account)){
			$info=xg_hooks('sms-send-vcode')->once($account,$vcode)->last();
			if(xg_iserr($info)){
				return $info[1];
			}
			return false;
		}else{
$content="

亲爱的用户您好，这封邮件是由 ".xg_config('site.site_name')." 发送的。
您正在进行邮箱验证，本次请求的验证码为：
{$vcode}

如果您并没有访问过我们的网站，或没有进行上述操作，请忽略这封邮件。您不需要退订或进行其他进一步的操作。

".xg_config('site.site_name')."管理团队
".date("Y年m月d日")."
http://".$_SERVER['HTTP_HOST']."/


";
			$info=xg_hooks('send-email')->once($account,xg_config('site.site_name')."邮箱验证码",nl2br($content))->last();
			if(xg_iserr($info)){
				return $info[1];
			}
			return false;
		}
	}
	
	//一分钟内的验证码记录
	protected function in60s($account){
		return xg_model('vcode')->where([['account','=',$account],['time','>',(XG_TIME-60)]])->value('time');
	}
	
	//一小时之内的验证码数量
	protected function inHour($account){
		return xg_model('vcode')->where([['account','=',$account],['time','>',(XG_TIME-60*60)]])->count();
	}
	
	//一天中的验证码数量
	protected function inDay($account){
		$this->timeOut24();//删除一天之外的验证码记录
		return xg_model('vcode')->where([['account','=',$account]])->count();
	}
	
	protected function ipCount($ip){
		$this->timeOut24();//删除一天之外的验证码记录
		return xg_model('vcode')->where([['ip','=',$ip]])->count();
	}
	
	protected function timeOut(){
		xg_model('vcode')->where('time','<',(XG_TIME-60*60*2))->update(['exp'=>1]);
		$this->timeOut24();
	}
	
	protected function timeOut24(){
		xg_model('vcode')->where('time','<',(XG_TIME-60*60*24))->delete();
	}
	
	protected function dbcheck($account,$vcode,$type){
		$log=(int)(xg_model('vcode')->where([['account','=',$account],['type','=',$type]])->count());
		if(!$log)return -3;//请先获取验证码
		$log=xg_model('vcode')->where([['account','=',$account],['type','=',$type],['vcode','=',$vcode]])->fields('exp')->find();
		if($log){
			if($log['exp'])return -2;//验证码已经过期
			return 1;
		}else{
			return -1;//无此验证码
		}
	}
	protected function imgcode(){
		$postval=xg_input('request.imgcode');
		if(!$postval)return '请填写图形验证码';
		if(!xg_check_verify($postval))return '图形验证码错误';
		return false;
	}
}
?>