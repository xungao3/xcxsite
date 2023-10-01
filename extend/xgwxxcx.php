<?php
class xgwxxcx{
	static function init($appid,$secret=''){
		return new self($appid,$secret);
	}
	function __construct($appid,$secret=''){
		$this->appid=$appid;
		$xcx=xg_config("xcx.{$appid}");
		if($xcx and !$secret)$secret=$xcx['secret'];
		$this->secret=$secret;
	}
	function login($data){
		$code = $data['code'];
		$encryptedData = $data['encryptedData'];
		$iv = $data['iv'];
		$signature = $data['signature'];
		$rawData = $data['rawData'];
		$info = $this->get_session_res($code);
		$sessionKey = $info['session_key'];
		if(!$info['phoneNumber']){
			$signature2 = sha1(htmlspecialchars_decode($rawData).$sessionKey);
			if ($signature2 !== $signature){
				xg_jsonerr("验签失败");
			}
			$userinfo = xg_jsonarr($data['userinfo']);
			if(!$userinfo or !is_array($userinfo)){
				xg_jsonerr('没有用户或手机号信息');
			}else{
				$info=xg_merge($info,$userinfo);
			}
		}else{
			$info['mobile']=$info['phoneNumber'];
		}
		$de = new \WXBizDataCrypt($this->appid,$sessionKey);
		$errCode = $de->decryptData($encryptedData,$iv,$data);
		if ($errCode == 0) {
			$info['nickname']=$info['nickName'];
			$info['avatar']=$info['avatarUrl'];
			return $info;
		} else {
			return xg_jsonerr('发生错误：code='.$errCode);
		}
	}
	function code2openid($code){
		$session_info=$this->get_session_key($code);
		if($session_info){
			return $session_info['openid'];
		}
	}
	function mobile($code){
		$token=$this->get_session_token();
		$res=xg_http_json("https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=$token",xg_jsonstr(['code'=>$code]));
		if($res and $res['phone_info'] and $res['phone_info']['purePhoneNumber'])return $res['phone_info']['purePhoneNumber'];
		if($res['errmsg'])xg_jsonerr($res['errmsg']);
		xg_jsonerr('没有获取到手机号');
	}
	function get_session_token(){
		if($token=xg_cache("xg-wxxcx-token-{$this->appid}"))return $token;
		$res=xg_http_json("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->secret}");
		xg_dlog($res);
		if($res['access_token']){
			xg_cache("xg-wxxcx-token-{$this->appid}",$res['access_token'],$res['expires_in']);
			return $res['access_token'];
		}
		xg_jsonerr('没有获取到ACCESS_TOKEN');
	}
	function get_session_key($code){
		$res=$this->get_session_res($code);
		return $res['session_key'];
	}
	function get_session_res($code){
		if($this->session_res){
			return $this->session_res;
		}
		$url = "https://api.weixin.qq.com/sns/jscode2session?"."appid=".$this->appid."&secret=".$this->secret."&js_code=".$code."&grant_type=authorization_code";
		$res = xg_http_json($url);
		if($res['errmsg'])xg_jsonerr($res['errmsg']);
		if(!$res['session_key'])xg_jsonerr("获取session_key发生错误");
		$this->session_res=$res;
		return $res;
	}
}
?>