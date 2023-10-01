<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\traits;
trait file{
	public $fileobj=null;
	public function upload(){
		$rt=$this->doupload();
		xg_success($rt);
	}
	public function fileobj(){
		if($this->fileobj)return $this->fileobj;
		return $this->fileobj=new \xg\file($type);
	}
	public function doupload(){
		$url=xg_input('request.url',$this->url);
		$isimg=xg_input('request.isimg/i',$this->isimg);
		$isvideo=xg_input('request.isvideo/i',$this->isvideo);
		$infoid=xg_input('request.infoid/i',$this->infoid);
		$fid=xg_input('request.fid/i',$this->fid);
		$resize=xg_input('request.resize',$this->resize);
		$model=xg_input('request.model',$this->model);
		$png2jpg=xg_input('request.png2jpg',$this->png2jpg);
		$urlname=xg_input('request.urlname',$this->urlname);
		if($isimg){
			$type="image";
		}elseif($isvideo){
			$type="video";
		}
		$uid=xg_login(XG_APP);
		$file=$this->fileobj();
		if($url){
			$upload=$file->download();
		}else{
			$upload=$file->upload();
		}
		if($resize)$file->resize($upload['path'],$resize);
		if($png2jpg)$upload['url']=$file->png2jpg($upload['url']);
		if($error=$file->error() and xg_iserr($error)){
			return ['msg'=>$error[1],'ok'=>false];
		}
		$id=xg_model('files')->insert(array(
			'infoid'=>($infoid?$infoid:$fid),
			'uid'=>$uid,
			'type'=>$model,
			'isimg'=>$isimg,
			'url'=>$upload['url'],
			'name'=>$upload['name'],
			'ext'=>$upload['ext'],
			'size'=>$upload['filesize'],
			'createtime'=>$upload['filetime'],
			'updatetime'=>$upload['filetime'],
			'md5'=>$upload['md5'],
			'sha1'=>$upload['sha1']
		));
		$rt=array('ok'=>true,'msg'=>'上传成功','name'=>$upload['filename'],'fileurl'=>$upload['url'],'fid'=>$id);
		if($isimg)$rt['imgurl']=$upload['url'];
		if($urlname)$rt[$urlname]=$upload['url'];
		return $rt;
	}
}
?>