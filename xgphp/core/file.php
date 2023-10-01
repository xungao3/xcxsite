<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class file{
	protected $config;
	protected $type;
	protected $error;
 	public function __construct($type=null){
		$this->type=xg_input('type',$type);
		$this->config=xg_config('file');
		if(!$this->type and xg_input('isimg'))$this->type='image';
		if($this->type=='video'){
			$this->config=xg_merge($this->config,['allow'=>xg_arr(xg_config('site.upload_video_ext')),'max'=>xg_byteconvert(xg_config('site.upload_video_max'))]);
		}elseif($this->type=='image'){
			$this->config=xg_merge($this->config,['allow'=>xg_arr(xg_config('site.upload_image_ext')),'max'=>xg_byteconvert(xg_config('site.upload_image_max'))]);
		}else{
			$this->config=xg_merge($this->config,['allow'=>xg_arr(xg_config('site.upload_file_ext')),'max'=>xg_byteconvert(xg_config('site.upload_file_max'))]);
		}
	}
 	public function validation($file=null){
		if(!$file)$file=$_FILES;
		if($file['file']['error']){
			$code=array(
				1=>'上传的文件大小超过了最大值',
				2=>'上传文件的大小超过了表单最大值',
				3=>'文件只有部分被上传',
				4=>'没有文件被上传',
				6=>'找不到临时文件夹',
				7=>'文件写入失败'
			);
			if($code[$file['file']['error']])return $this->error($code[$file['file']['error']]);
			return $this->error('上传文件发生错误，错误码：'.$file['file']['error']);
		}
		$ext=xg_file_ext($file['file']['name']);
		if($file['file']['size']<=0)return $this->error('上传文件发生错误，文件内容为空');
		if(xg_in_array($ext,$this->config['disallow']))return $this->error('禁止上传此类型的文件');
		if(!xg_in_array($ext,$this->config['allow'])){
			if($this->type=='video')return $this->error('请上传视频格式的文件');
			if($this->type=='image')return $this->error('请上传图片格式的文件');
			return $this->error('未允许上传此类型的文件');
		}
		if($this->config['max'] and $file['file']['size']>($this->config['max']))return $this->error('上传文件超过系统设置限制大小');
	}
 	public function allow($ext=null){
		if($ext){
			if(xg_in_array($ext,['json','svg']))$this->config['disallow']=array_diff($this->config['disallow'],[$ext]);
			$this->config['allow'][]=$ext;
		}
		return $this;
	}
 	public function content($file=null){
		if(!$file)$file=$_FILES;
		$this->validation($file);
		if($this->error)return $this->error();
		return xg_fcont($file['file']['tmp_name']);
	}
 	public function download($url=null){
		if(!$url)$url=xg_input('url');
		if($this->config['abspath']){
			$savedir=$this->config['abspath'];
			if(!file_exists($savedir))mkdir($savedir,0755,true);
			$urldir=str_replace(XG_PUBLIC,'',$savedir);
			$urldir=str_replace(XG_PATH,'',$savedir);
		}else{
			$urldir=$this->path();
			$savedir=XG_PUBLIC.$urldir;
		}
		if(substr($url,0,7)=='http://' or substr($url,0,8)=='https://'){
			$ext=strtolower(end(xg_arr(xg_arr($url,'?')[0],'.')));
			if(xg_in_array($ext,$this->config['disallow']) or !xg_in_array($ext,$this->config['allow'])){
				if($this->type=='video'){
					$ext='mp4';
				}elseif($this->type=='image'){
					$ext='jpg';
				}else{
					return $this->error('未允许上传此类型的文件');
				}
			}
			$filename=$this->name($ext);
			$savepath=$savedir.$filename;
			$filecont=xg_http($url);
			if(!$filecont)return $this->error('下载网络文件发生错误');
			$save=xg_fcont($savepath,$filecont);
			if(!$save)return $this->error('保存网络文件发生错误');
			$fileurl=$urldir.$filename;
		}else{
			$fileurl=$url;
			$savepath=XG_PUBLIC.$url;
		}
		$md5=md5_file($savepath);
		$sha1=sha1_file($savepath);
		$filesize=filesize($savepath);
		return [
			'path'=>$savepath,
			'url'=>$fileurl,
			'ext'=>$ext,'md5'=>$md5,
			'sha1'=>$sha1,
			'filesize'=>$filesize,
			'filename'=>$filename,
			'name'=>$file['file']['name']
		];
	}
 	public function upload($file=null){
		if(!$file)$file=$_FILES;
		$ext=xg_file_ext($file['file']['name']);
		$this->validation($file);
		if($this->error)return $this->error();
		if($this->config['abspath']){
			$savedir=$this->config['abspath'];
			if(!file_exists($savedir))mkdir($savedir,0755,true);
			if($this->config['absroot']){
				$urldir=str_replace($this->config['absroot'],'',$savedir);
			}else{
				$urldir=str_replace(XG_PUBLIC,'',$savedir);
				$urldir=str_replace(XG_PATH,'',$savedir);
			}
		}else{
			$urldir=$this->path();
			$savedir=XG_PUBLIC.$urldir;
		}
		$filename=$this->name($ext);
		$filesize=$file['file']['size'];
		$savepath=$savedir.$filename;
		$move=move_uploaded_file($file['file']['tmp_name'],$savepath);
		if(!$move)return $this->error('移动上传的文件发生错误');
		$md5=md5_file($savepath);
		$sha1=sha1_file($savepath);
		return [
			'path'=>$savedir.$filename,
			'url'=>$urldir.$filename,
			'ext'=>$ext,'md5'=>$md5,
			'sha1'=>$sha1,
			'filesize'=>$filesize,
			'filename'=>$filename,
			'name'=>$file['file']['name']
		];
	}
	public function error($error=null){
		if(is_null($error)){
			$error=$this->error;
		}
		if($error)return xg_rterr($error);
	}
	public function config($key,$value=null){
		return $this->set($key,$value);
	}
	public function set($key,$value=null){
		if(is_array($key)){
			$this->config=xg_extend($this->config,$key);
		}else{
			$this->config[$key]=$value;
		}
		return $this;
	}
	protected function name($ext){
		$filename=substr(md5(microtime()),0,16).'.'.$ext;
		return $filename;
	}
	protected function path(){
		$urldir=$this->config['path'].date('Ym',XG_TIME).'/'.date('d',XG_TIME).'/';
		$savedir=XG_PUBLIC.$urldir;
		if(!file_exists($savedir))mkdir($savedir,0755,true);
		return $urldir;
	}
	public function resize($fileurl,$resize){
		$ext=xg_file_ext($fileurl);
		if(xg_in_array($ext,array('png','gif','jpg','jpeg'))){
			if(strpos($resize,'*')>-1){
				$resize=xg_arr($resize,'*');
				list($width,$height)=$resize;
				$scale=false;
			}elseif(is_numeric($resize)){
				$width=$height=$resize;
				$scale=true;
			}
			if($width){
				xg_image_resize($fileurl,$fileurl,$width,$height,$scale);
			}else{
				$resize=null;
			}
		}
	}
	public function png2jpg($fileurl){
		$ext=xg_file_ext($fileurl);
		if($ext=='png'){
			$data=imagecreatefrompng(XG_PUBLIC.$fileurl);
			if($data){
				unlink(XG_PUBLIC.$fileurl);
				$fileurl=substr($fileurl,0,-3).'jpg';
				imagejpeg($data,XG_PUBLIC.$fileurl,90);
			}
		}
		return $fileurl;
	}
}
?>