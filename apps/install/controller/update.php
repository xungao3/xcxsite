<?php
namespace apps\install\controller;

class update extends base {
	public function init(){
		parent::init();
		if(!file_exists(XG_DATA.'/installed'))xg_error('系统未安装');
	}
	public function index(){
		$this->display();
	}
	protected function http($act){
		$url='http://e.xg3.cn/get/v2/'.$act;
		return xg_http_json($url);
	}
	public function update(){
		$cur=xg_cont(XG_DATA.'/version');
		$next=$this->http('nextver?cur='.$cur);
		if(is_array($next)){
			if($next['ok']===true){
				$ver=$next['nextver'];
				$path=XG_RUNTIME.'/package/'.$ver.'.zip';
				if($ver){
					$url=$next['nexturl'];
					$cont=xg_http($url);
					xg_cont($path,$cont);
					$zip=new \PclZip($path);
					$zip->extract(PCLZIP_OPT_PATH,XG_PATH);
					if(file_exists(XG_DATA.'/update/'.$ver.'.php')){
						require_once(XG_DATA.'/update/'.$ver.'.php');
					}
					xg_cont(XG_DATA.'/version',$ver);
					xg_success('已经升级到'.$ver);
				}
				xg_jsonmsg(['msg'=>'已经升级为最新版','ok'=>1]);
			}elseif($next['msg']){
				xg_error($next['msg']);
			}
		}else{
			xg_error('获取版本信息发生错误');
		}
	}
}