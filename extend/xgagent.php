<?php
class xgagent{
	public $os = array(
		'/windows nt 11/i' => 'Windows 11',
		'/windows nt 10/i' => 'Windows 10',
		'/windows phone 10/i' => 'Windows Phone 10',
		'/windows phone 8.1/i' => 'Windows Phone 8.1',
		'/windows phone 8/i' => 'Windows Phone 8',
		'/windows nt 6.3/i' => 'Windows 8.1',
		'/windows nt 6.2/i' => 'Windows 8',
		'/windows nt 6.1/i' => 'Windows 7',
		'/windows nt 6.0/i' => 'Windows Vista',
		'/windows nt 5.2/i' => 'Windows Server 2003',
		'/windows nt 5.1/i' => 'Windows XP',
		'/windows xp/i' => 'Windows XP',
		'/windows nt 5.0/i' => 'Windows 2000',
		'/windows me/i' => 'Windows ME',
		'/win98/i' => 'Windows 98',
		'/win95/i' => 'Windows 95',
		'/win16/i' => 'Windows 3.11',
		'/iphone/i' => 'iPhone',
		'/ipod/i' => 'iPod',
		'/ipad/i' => 'iPad',
		'/macintosh|mac os x/i' => 'Mac OS X',
		'/mac_powerpc/i' => 'Mac OS 9',
		'/SunOS/i' => 'SunOS',
		'/android/i' => 'Android',
		'/linux/i' => 'Linux',
		'/ubuntu/i' => 'Ubuntu',
		'/blackberry/i' => 'BlackBerry',
		'/webos/i' => 'Mobile'
	);
 	public $browser = array(
		'/hm\-/i'=> '红米手机浏览器',
		'/redmi/i'=> '红米手机浏览器',
		'/mix /i'=> '小米MIX手机浏览器',
		'/XiaoMi/i'=> '小米手机浏览器',
		'/miui/i'=> '小米手机浏览器',
		'/mi /i'=> '小米手机浏览器',
		'/RMX/i'=> 'Realme手机浏览器',
		'/vivo/i'=> 'Vivo手机浏览器',
		'/oppo/i'=> 'Oppo手机浏览器',
		'/Nubia/i'=> 'Nubia手机浏览器',
		'/GFIVE/i'=> '朵唯手机浏览器',
		'/HS\-/i'=> '海信手机浏览器',
		'/Honor/i'=> '荣耀浏览器',
		'/HarmonyOS/i'=> '鸿蒙浏览器',
		'/HUAWEI/i'=> '华为浏览器',
		'/symbian/i'=> '塞班浏览器',
		'/sony/i'=> '索尼手机浏览器',
		'/blackberry/i'=> '黑莓手机浏览器',
		'/nokia/i'=> '诺基亚手机浏览器',
		'/samsung/i'=> '三星手机浏览器',
		'/meizu/i'=> '魅族手机浏览器',
		'/coolpad/i'=> '酷派手机浏览器',
		'/amoi/i'=> '夏新手机浏览器',
		'/ktouch/i'=> '天语手机浏览器',
		'/alcatel/i'=> '阿尔卡特手机浏览器',
		'/ericsson/i'=> '索尼爱立信手机浏览器',
		'/philips/i'=> '飞利浦手机浏览器',
		'/maui/i'=> '摩托罗拉手机浏览器',
		'/zte/i'=> '中兴手机浏览器',
		'/longcos/i'=> '龙酷手机浏览器',
		'/gionee/i'=> '金立手机浏览器',
		'/benq/i'=> '明基手机浏览器',
		'/haier/i'=> '海尔手机浏览器',
		'/lenovo/i'=> '联想手机浏览器',
		'/leno/i'=> '联想手机浏览器',
		'/lg\-/i'=> 'LG手机浏览器',
		'/Nexus/i'=> 'Nexus手机浏览器',
		'/nec\-/i'=> 'NEC手机浏览器',
		'/HBuilderX/i' => 'HBuilderX',
		'/WindowsWechat/i'=> '微信电脑浏览器',
		'/micromessenger/i'=> '微信浏览器',
		'/mqqbrowser/i' => 'QQ手机浏览器',
		'/qqbrowser/i' => 'QQ浏览器',
		'/swan-baiduboxapp/i' => '百度小程序',
		'/baiduboxapp/i' => '百度客户端',
		'/360se/i' => '360浏览器',
		'/360ee/i' => '360浏览器',
		'/LBBROWSER/i' => '猎豹浏览器',
		'/world/i' => '世界之窗浏览器',
		'/msie/i' => 'IE浏览器',
		'/firefox/i' => 'Firefox浏览器',
		'/edg/i' => 'Edge浏览器',
		'/opera/i' => 'Opera浏览器',
		'/netscape/i' => '网景浏览器',
		'/UCWEB/i' => 'UC浏览器',
		'/Juzibrowser/i' => '桔子浏览器',
		'/chrome/i' => 'Chrome浏览器',
		'/safari/i' => 'Safari浏览器',
	);
 	public $spider = array(
		'/xgphp/i' => '讯高PHP',
		'/gptbot/i' => 'GPT蜘蛛',
		'/YisouSpider/i' => '神马蜘蛛',
		'/Googlebot\-Image/i' => '谷歌图片蜘蛛',
		'/googlebot/i' => '谷歌蜘蛛',
		'/baiduspider-image/i' => '百度图片蜘蛛',
		'/baidubot/i' => '百度蜘蛛',
		'/baiduspider/i' => '百度蜘蛛',
		'/Sogou Pic Spider/i' => '搜狗图片蜘蛛',
		'/sogoubot/i' => '搜狗蜘蛛',
		'/yodaoBot/i' => '有道蜘蛛',
		'/360spider/i' => '360蜘蛛',
		'/sosospider/i' => '搜搜蜘蛛',
		'/BingPreview/i' => '必应预览蜘蛛',
		'/bingbot/i' => '必应蜘蛛',
		'/bytespider/i' => '字节蜘蛛',
		'/BAOTA/i' => '宝塔面板',
		'/python/i'=> 'Python请求',
		'/okhttp/i' => 'okhttp',
		'/Go\-http/i' => 'Go-http',
		'/curl/i' => 'curl',
		'/sqlmap/i' => 'sqlmap',
		'/AhrefsBot/i' => 'AhrefsBot',
		'/Barkrowler/i' => 'Barkrowler',
		'/BLEXBot/i' => 'BLEXBot',
		'/Dataprovider/i' => 'Dataprovider',
		'/DotBot/i' => 'DotBot',
		'/MJ12bot/i' => 'MJ12bot',
		'/AppInsights/i' => 'AppInsights',
		'/Netcraft/i' => 'Netcraft',
		'/SemrushBot/i' => 'SemrushBot',
		'/PLAYSTATION/i' => 'PLAYSTATION',
		'/Dalvik/i' => 'Dalvik',
		'/Skype/i' => 'Skype',
		'/TelegramBot/i' => 'TelegramBot',
		'/spider/i' => 'spider',
		'/bot/i' => 'bot',
	);
	private $agent = null;
	public function __construct($agent=null){
		$this->agent = $agent?$agent:$_SERVER['HTTP_USER_AGENT'];
	}
	public function agent($agent=null){
		$this->agent = $agent;
		return $this;
	}
	public function data(){
		return [
			'os'=>$this->os(),
			'ip'=>$this->ip(),
			'browser'=>$this->browser(),
			'version'=>$this->version(),
			'spider'=>$this->spider(),
			'device'=>$this->device(),
			'agent'=>$this->agent,
		];
	}
	public function ip(){
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			return $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			return $_SERVER['REMOTE_ADDR'];
		}
	}
	public function os() { 
		foreach ($this->os as $regex => $value) { 
			if (preg_match($regex, $this->agent) ) {
				return $value;
			}
		} 
		return "";
	}
	public function device() {
		if($this->isMobile()){
			return '手机';
		}elseif($this->isTablet()){
			return '平板';
		}elseif($this->isDesktop()){
			return '电脑';
		}elseif($this->isBot()){
			return '机器人';
		}elseif($this->spider()){
			return '蜘蛛';
		}
		return "";
	}
	public function browser() {
		$browser="";
		foreach ($this->browser as $regex => $value) { 
			if (preg_match($regex, $this->agent ) ) {
				return $value;
			}
		}
		return "";
	}
	public function version(){
		if($this->isDesktop()){
			$detected = $this->browser();
			$d = array_search($detected, $this->browser);
			$browser = str_replace(array("/i","/"), "", $d);
			$regex = "/(?<browser>version|{$browser})[\/]+(?<version>[0-9.|a-zA-Z.]*)/i";
			if (preg_match_all($regex, $this->agent, $matches)) {
				$found = array_search($browser, $matches["browser"]);
				return $matches["version"][$found];
			}
		}
		return "";
	}
	public function spider() {
		$spider="";
		foreach ($this->spider as $regex => $value) { 
			if (preg_match($regex, $this->agent ) ) {
				return $value;
			}
		}
		return "";
	}
	public function isMobile(){
		if (preg_match('/mobile|phone|ipod/i', $this->agent) ) {
			return true;
		}else{
			return false;
		}
	}
	public function isTablet(){
		if (preg_match('/tablet|ipad/i', $this->agent) ) {
			return true;
		}else{
			return false;
		}
	}
	public function isDesktop(){
		if (!$this->isMobile() && !$this->isTablet() ) {
			return true;
		}else{
			return false;
		}
	}
	public function isBot(){
		if (preg_match('/bot|spider/i', $this->agent) ) {
			return true;
		}else{
			return false;
		}
	}
	public function __toString(){
		return $this->agent;
	}
}
?>