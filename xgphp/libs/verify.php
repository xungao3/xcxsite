<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\libs;
class verify{
	public function make($id=1,$length=4,$width=200,$height=50){
		$im=imagecreatetruecolor($width,$height);
		$bg=imagecolorallocate($im,rand(210,240),rand(210,240),rand(210,240));
		imagefill($im,0,0,$bg);
		$strs=array_merge(range(2,9),range('A','Z'));
		shuffle($strs);
		$strs=array_slice($strs,0,4);
		xg_session('xg_verify_code_'.$id,$this->en(join($strs)));
		$strs=array_slice($strs,0,$length);
		for($i=0;$i<$length;$i++){
			$color=imagecolorallocate($im,rand(100,200),rand(100,200),rand(100,200));
			imagefttext($im,mt_rand($width/10,$width/7),0,$i*($width-20)/$length+mt_rand(10,20),mt_rand(35,$height-5),$color,XG_PHP.'/libs/verify.ttf',$strs[$i]);
		}
		for($i=0;$i<$width;$i++){
			$color=imagecolorallocate($im,rand(0,255),rand(0,255),rand(0,255));
			$a=rand(0,$width);
			$b=rand(0,$height);
			imagesetpixel($im,$a,$b,$color);
		}
		ob_start();
		imagepng($im);
		$data=ob_get_contents();
		ob_end_clean();
		xg_exit($data,'image/png');
	}
	public function check($code,$id=1){
		$session=xg_session('xg_verify_code_'.$id);
		xg_session('xg_verify_code_'.$id,null);
		return $this->en($code)==$session;
	}
	protected function en($str){
		return md5(substr(md5(strtoupper($str)),5,20));
	}
}