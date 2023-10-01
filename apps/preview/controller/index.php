<?php
//中文
namespace apps\preview\controller;
class index extends base{
	function init(){
		parent::init();
	}
	function index(){
		$thid=xg_input('get.thid');
		$pagename=xg_input('get.pagename','index');
		$page=xg_model('app_page')->where(['thid'=>$thid,'name'=>$pagename])->json('data')->find();
		$datas=['thid'=>$thid,'pagename'=>$pagename,'page'=>$page];
		$blocksets=xg_jsonarr(xg_fcont(XG_DATA.'/blocks.json'))['sets'];
		$datas['names']=[];
		foreach($blocksets as $k=>$v){
			$datas['names'][$k]=$v['title'];
		}
		if(XG_DEBUG or !$vuecont=xg_cache('vuecont')){
			$vuecont=[];
			$univues=xg_dirnames(dirname(__DIR__).'/view/uniapp/');
			foreach(glob(dirname(__DIR__).'/view/components/*',GLOB_ONLYDIR) as $vue){
				$name=basename($vue);
				$cont=xg_fcont($vue.'/'.$name.'.vue');
				if(!$cont)continue;
				$cont=str_replace(['<block','</block>'],['<uni-block','</uni-block>'],$cont);
				$cont=str_replace(['<image','</image>'],['<uni-image','</uni-image>'],$cont);
				$data='';
				foreach($univues as $uni){
					$data.="'{$uni}':Vue.defineAsyncComponent(()=>loadModule('../components/{$uni}.vue',loader())),";
				}
				if(preg_match_all('/\<(xg\-[a-zA-Z0-9\-]+).*?\>.*?\<\/(?:xg\-[a-zA-Z0-9\-]+)\>/is',$cont,$rt)){
					foreach($rt[1] as $v){
						$data.="'$v':Vue.defineAsyncComponent(()=>loadModule('../components/$v.vue',loader())),";
					}
				}
				$components="components:{{$data}},";
				$cont=str_replace("data()","{$components}data()",$cont);
				$vuecont[$name]=$cont;
			}
			foreach($univues as $vue){
				$cont=xg_fcont(dirname(__DIR__).'/view/uniapp/'.$vue.'/'.$vue.'.vue');
				if(!$cont)continue;
				$vuecont[$vue]=$cont;
			}
			xg_cache('vuecont',$vuecont);
		}
		$datas['vue']=$vuecont;
		$this->display('index',$datas);
	}
}