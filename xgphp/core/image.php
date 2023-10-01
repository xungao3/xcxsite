<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class image{
	public $config = null;
	public $im = null;
	public $source_width = 0;
	public $source_height = 0;
	public $source_mime = '';
	public static function init($config = null) {
		$class=new self();
		return $class->config($config);
	}
	/**
	 * @param $config 传入参数
	 * @param $config['file'] 图片文件
	 * @param $config['color'] 字体颜色
	 * @param $config['size'] 字体大小
	 * @param $config['savepath'] 保存路径
	 * @param $config['fontfile'] 字体文件路径
	 * @param $config['angle'] 角度
	 * @param $config['fontfile'] 字体文件路径
	 * @param $config['shadow'] 是否添加阴影 如果是true则自动生成阴影颜色，可以设置为#开始的16进制颜色，也可以设置为rgb数组
	 * @param $config['center'] 文本是否居中对齐
	 */
	public function config($config = null) {
		$this->config = $config;
		return $this;
	}
	/**
	 * PHP实现图片上写入实现文字自动换行
	 * @param $fontsize 字体大小
	 * @param $angle 角度
	 * @param $font 字体路径
	 * @param $string 要写在图片上的文字
	 * @param $width 预先设置图片上文字的宽度
	 * @param $flag  换行时单词不折行
	 */
	public function wordWrap($fontsize, $angle, $font, $string, $width, $flag = false) {
		$content = "";//\r\n
		if ($flag) {//只支持英文不折行
			$words = explode(" ", $string);
			foreach ($words as $key => $value) {
				$teststr = $content . " " . $value;
				$testbox = imagettfbbox($fontsize, $angle, $font, $teststr);
				//判断拼接后的字符串是否超过预设的宽度
				if (($testbox[2] > $width)) {
					$content .= "\r\n";
				}
				$content .= $value . " ";
			}
		} else {
			//将字符串拆分成一个个单字 保存到数组 letter 中
			for ($i = 0; $i < mb_strlen($string); $i++) {
				$letter[] = mb_substr($string, $i, 1);
			}
			foreach ($letter as $l) {
				$teststr = $content . " " . $l;
				$testbox = imagettfbbox($fontsize, $angle, $font, $teststr);
				// 判断拼接后的字符串是否超过预设的宽度
				if (($testbox[2] > $width) && ($content !== "")) {
					$content .= "\r\n";
				}
				$content .= $l;
			}
		}
		return $content;
	}
	public function textBox($fontsize, $angle, $font, $string, $width, $flag = false){
		$text=$this->wordWrap($fontsize, $angle, $font, $string, $width, $flag);
		$testbox = imagettfbbox($fontsize, $angle, $font, $text);
		return $testbox;
	}
	public function createIm() {
		if($this->im!==null)return true;
		$filepath=$this->config['file'];
		$source_info = getimagesize($filepath);
		$this->source_width = $source_info[0];
		$this->source_height = $source_info[1];
		$this->source_mime = $source_info['mime'];
		switch ($this->source_mime){
			case 'image/gif':
				$this->im = imagecreatefromgif($filepath);
				break;
			case 'image/jpeg':
				$this->im = imagecreatefromjpeg($filepath);
				break;
			case 'image/png':
				$this->im = imagecreatefrompng($filepath);
				break;
			default:
				return false;
				break;
		}
		return true;
	}
	/**
	 * 输出数据
	*/
	public function data($ext='png'){
		ob_start();
		switch ($ext){
			case 'gif':
				imagegif($this->im);
				break;
			case 'png':
				imagepng($this->im);
				break;
			default:
				imagejpeg($this->im);
				break;
		}
		$data = ob_get_contents();
		ob_end_clean();
		return $data;
	}
	/**
	 * 输出到文件或浏览器
	 * savepath 有此参数为输出到文件，没有的话则输出到浏览器
	*/
	public function output($savepath='') {
		if(!$savepath)$savepath=$this->config['savepath'];
		if (!$savepath) {
			header("content-type:image/png");
			imagepng($this->im);
			imagedestroy($this->im);
		} else {
			if($savepath===true){
				$savepath=$this->config['file'];
			}
			$ext=strtolower(end(explode('.',$savepath)));
			switch ($ext){
				case 'gif':
					imagegif($this->im,$savepath);
					break;
				case 'png':
					imagepng($this->im,$savepath);
					break;
				default:
					imagejpeg($this->im,$savepath);
					break;
			}
			imagedestroy($this->im);
		}
	}
	/**
	 * 处理像素
	 * 10w表示宽度的10%
	 * 10h表示高度的10%
	 * 10%表示高度或宽度的10%
	*/
	public function size($v,$i=0){
		if(substr($v,-1)=='w'){
			return (str_replace('w','',$v)*$this->source_width/100);
		}elseif(substr($v,-1)=='h'){
			return (str_replace('h','',$v)*$this->source_height/100);
		}elseif(substr($v,-1)=='%'){
			return (str_replace('%','',$v)*(($i==0||$i==2)?$this->source_height:$this->source_width)/100);
		}
		return (int)$v;
	}
	/**
	 * 实现写入IM资源
	 * @param $im2 表示IM资源
	 * @param $myset[margin] 上 右 下 左的边距，如果下边距不为0则以下边距开始输出 如果右边距不为0则以右边距开始输出
	 * @param $myset[size] 如果有size则为添加边长为size的正方形图片
	 * @param $myset[width] 添加的图片宽度
	 * @param $myset[height] 添加的图片高度
	 */
	public function addRes($im2,$myset=array()){
		$this->createIm();
		$set=array_merge(array(
			'margin'=>array('30w','30w','30w','30w'),
			'size'=>'',
			'width'=>'40w',
			'height'=>'40w',
			'radius'=>'1w',
		),$myset);
		extract($set);
		$margin[0]=$this->size($margin[0],0);
		$margin[1]=$this->size($margin[1],1);
		$margin[2]=$this->size($margin[2],2);
		$margin[3]=$this->size($margin[3],3);
		$size=$this->size($size,1);
		$width=$this->size($width,1);
		$height=$this->size($height,0);
		$radius=$this->size($radius,1);
		$oWidth=imagesx($im2);
		$oHeight=imagesy($im2);
		$ox=0;
		$oy=0;
		$oSize=0;
		if($size){
			$width=$size;
			$height=$size;
			if($oWidth>$oHeight){
				$ox=($oWidth-$oHeight)/2;
				$oy=0;
				$oSize=$oHeight;
			}else{
				$ox=0;
				$oy=($oHeight-$oWidth)/2;
				$oSize=$oWidth;
			}
		}elseif($width){
			$height=$width/$oWidth*$oHeight;
		}elseif($height){
			$width=$height/$oHeight*$oWidth;
		}else{
			$height=$oHeight;
			$width=$oWidth;
		}
		if($margin[1]){
			$x=$this->source_width-$width-$margin[1];
		}else{
			$x=$margin[3];
		}
		if($margin[2]){
			$y=$this->source_height-$height-$margin[2];
		}else{
			$y=$margin[0];
		}
		if($width!=$oWidth or $height!=$oHeight){
			$im3 = imagecreatetruecolor($width, $height);
			//缩略图片到指定大小
			imagecopyresampled($im3, $im2, 0, 0, $ox, $oy, $width, $height, $oSize?$oSize:$oWidth, $oSize?$oSize:$oHeight);
			imagedestroy($im2);
		}else{
			$im3=$im2;
		}
		if($radius){
			$im3=$this->radius($im3,$radius);
		}
		imagecopy($this->im,$im3,$x,$y,0,0,$width,$height);
		imagedestroy($im3);
		return $this;
	}
	/**
	 * 实现写入图片文件
	 * @param $filepath 表示文件路径
	 * @param $myset[margin] 上 右 下 左的边距，如果下边距不为0则以下边距开始输出 如果右边距不为0则以右边距开始输出
	 * @param $myset[size] 如果有size则为添加边长为size的正方形图片
	 * @param $myset[width] 添加的图片宽度
	 * @param $myset[height] 添加的图片高度
	 */
	public function addImg($filepath,$myset=array()){
		$source_info = getimagesize($filepath);
		$mime = $source_info['mime'];
		switch ($mime){
			case 'image/gif':
				$im = imagecreatefromgif($filepath);
				break;
			case 'image/jpeg':
				$im = imagecreatefromjpeg($filepath);
				break;
			case 'image/png':
				$im = imagecreatefrompng($filepath);
				break;
			default:
				$im = null;
				break;
		}
		$this->addRes($im,$myset);
		imagedestroy($im);
		return $this;
	}
	/**
	 * 实现写入图片内容
	 * @param $cont 表示文件内容
	 * @param $myset[margin] 上 右 下 左的边距，如果下边距不为0则以下边距开始输出 如果右边距不为0则以右边距开始输出
	 * @param $myset[size] 如果有size则为添加边长为size的正方形图片
	 * @param $myset[width] 添加的图片宽度
	 * @param $myset[height] 添加的图片高度
	 */
	public function addCont($cont,$myset=array()){
		$im=imagecreatefromstring($cont);
		$this->addRes($im,$myset);
		imagedestroy($im);
		return $this;
	}
	public function radius($src_img,$radius){
		$w=imagesx($src_img);
		$h=imagesy($src_img);

		$img = imagecreatetruecolor($w, $h);
		//这一句一定要有
		imagesavealpha($img, true);
		//拾取一个完全透明的颜色,最后一个参数127为全透明
		$bg = imagecolorallocatealpha($img, 255, 255, 255, 127);
		imagefill($img, 0, 0, $bg);
		$r = $radius; //圆 角半径
		for ($x = 0; $x < $w; $x++) {
			for ($y = 0; $y < $h; $y++) {
				$rgbColor = imagecolorat($src_img, $x, $y);
				if (($x >= $radius && $x <= ($w - $radius)) || ($y >= $radius && $y <= ($h - $radius))) {
					//不在四角的范围内,直接画
					imagesetpixel($img, $x, $y, $rgbColor);
				} else {
					//在四角的范围内选择画
					//上左
					$y_x = $r; //圆心X坐标
					$y_y = $r; //圆心Y坐标
					if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
					//上右
					$y_x = $w - $r; //圆心X坐标
					$y_y = $r; //圆心Y坐标
					if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
					//下左
					$y_x = $r; //圆心X坐标
					$y_y = $h - $r; //圆心Y坐标
					if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
					//下右
					$y_x = $w - $r; //圆心X坐标
					$y_y = $h - $r; //圆心Y坐标
					if (((($x - $y_x) * ($x - $y_x) + ($y - $y_y) * ($y - $y_y)) <= ($r * $r))) {
						imagesetpixel($img, $x, $y, $rgbColor);
					}
				}
			}
		}
		return $img;
	}
	public function addQrcode($text,$myset=array()){
		ob_start();
		QRcode::png($text);
		$data=ob_get_contents();
		ob_end_clean();
		$im=imagecreatefromstring($data);
		$width=imagesx($im);
		$height=imagesy($im);
		$im2 = imagecreatetruecolor($width-10, $height-10);
		//生成的二维码边框太宽 所以处理一下边框
		imagecopyresampled($im2, $im, 0, 0, 5, 5, $width-10, $height-10, $width-10, $height-10);
		$this->addRes($im2,$myset);
		imagedestroy($im);
		imagedestroy($im2);
		return $this;
	}
	/**
	 * 实现写入文字
	 * @param $text 要写入的文字
	 * @param $myset[margin] 上 右 下 左的边距，如果下边距不为0则 以下边距开始输出
	 * @param $myset[size] 字体大小
	 * @param $myset[color] 颜色，可以设置为#开始的16进制颜色，也可以设置为rgb数组
	 * @param $myset[angle] 角度
	 * @param $myset[fontfile] 字体文件路径
	 * @param $myset[shadow] 是否添加阴影 如果是true则自动生成阴影颜色，可以设置为#开始的16进制颜色，也可以设置为rgb数组
	 * @param $config[center] 文本是否居中对齐
	 */
	public function addText($text,$myset=array()) {
		$this->createIm();
		$set=array_merge(array(
			'margin'=>array('10w','10w',0,'10w'),
			'size'=>($this->config['size']?$this->config['size']:20),
			'color'=>($this->config['color']?$this->config['color']:'#000000'),
			'angle'=>($this->config['angle']?$this->config['angle']:0),
			'fontfile'=>($this->config['fontfile']?$this->config['fontfile']:''),
			'shadow'=>(isset($this->config['shadow'])?$this->config['shadow']:true),
			'center'=>(isset($this->config['center'])?$this->config['center']:true),
		),$myset);
		extract($set);
		$margin[0]=$this->size($margin[0],0);
		$margin[1]=$this->size($margin[1],1);
		$margin[2]=$this->size($margin[2],2);
		$margin[3]=$this->size($margin[3],3);
		$size=$this->size($size,1);
		$x=$margin[3];
		$width=$this->source_width-$margin[3]-$margin[1];
		$words_text = $this->wordWrap($size, $angle, $fontfile, $text, $width);
		$text_box = $this->textBox($size, $angle, $fontfile, $text, $width);
		if($margin[2]){
			$y=$this->source_height-$text_box[1]-$margin[2];
		}else{
			$y=$margin[0]-$text_box[7];
		}
		if($shadow){
			$color2 = $this->getcolor($color,$shadow);
			$color2 = imagecolorallocatealpha($this->im, $color2['r'], $color2['g'], $color2['b'], 0);
		}
		$color = $this->getcolor($color);
		$color = imagecolorallocatealpha($this->im, $color['r'], $color['g'], $color['b'], 0);
		if($center){
			$text_arr=explode("\r\n",$words_text);
			$text_total_height=0;
			foreach($text_arr as $text){
				$text_box2 = imagettfbbox($size, $angle, $fontfile, $text);
				$text_width=$text_box2[2];
				$text_x=($width-$text_width)/2+$x;
				$text_y=$y+$text_total_height;
				$text_total_height=$text_total_height+$text_box2[3]-$text_box2[5]+$size/2;
				$this->text($size, $angle, $text_x, $text_y, $color, $color2, $fontfile, $text, $shadow);
			}
		}else{
			$this->text($size, $angle, $x, $y, $color, $color2, $fontfile, $words_text, $shadow);
		}
		return $this;
	}
	public function text($size, $angle, $x, $y, $color, $color2, $fontfile, $words_text, $shadow){
		if($shadow){
			imagettftext($this->im, $size, $angle, $x+1, $y-1, $color2, $fontfile, $words_text);
			imagettftext($this->im, $size, $angle, $x-1, $y-1, $color2, $fontfile, $words_text);
			imagettftext($this->im, $size, $angle, $x-1, $y+1, $color2, $fontfile, $words_text);
			imagettftext($this->im, $size, $angle, $x+1, $y+1, $color2, $fontfile, $words_text);
		}
		$info=imagettfbbox($size, 0, $fontfile, $words_text);
		$h=$info[1]-$info[7];
		imagettftext($this->im, $size*0.75, $angle, $x, $y-($h-$size)/2-$size*0.125, $color, $fontfile, $words_text);
	}
	public function getcolor($color,$inverse=false){
		if(is_string($color) and substr($color,0,1)=='#'){
			$color=$this->hex2rgb($color);
		}elseif(is_array($color)){
			$color=$color;
		}else{
			$color=array('r'=>0,'g'=>0,'b'=>0);
		}
		if($inverse===true){
			return array('r'=>(255-$color['r']),'g'=>(255-$color['g']),'b'=>(255-$color['b']));
		}elseif(is_string($inverse) and substr($inverse,0,1)=='#'){
			$color=$this->hex2rgb($inverse);
		}elseif(is_array($inverse)){
			$color=$inverse;
		}
		return $color;
	}
	/** 
	* 将16进制颜色转换为RGB
	*/
	public function hex2rgb($hexColor){
		if(substr($hexColor,0,1)!='#')return array();
		$color=str_replace('#','',$hexColor);
		if(strlen($color)>3){
			$rgb=array(
				'r'=>hexdec(substr($color,0,2)),
				'g'=>hexdec(substr($color,2,2)),
				'b'=>hexdec(substr($color,4,2))
			);
		}else{
			$r=substr($color,0,1).substr($color,0,1);
			$g=substr($color,1,1).substr($color,1,1);
			$b=substr($color,2,1).substr($color,2,1);
			$rgb=array(
				'r'=>hexdec($r),
				'g'=>hexdec($g),
				'b'=>hexdec($b)
			);
		}
		return $rgb;
	}
}
?>