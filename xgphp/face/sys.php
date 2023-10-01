<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\face;
interface sys{
	 // public function contents($cid,$count=null,$fields=null);
	 // public function content($cid,$id,$fields=null);
	 // public function sets($sets);
	 // public function cates();
	 // public function rels();
	 public function sets($sets=null);
	 public function sysinfo();
	 public function catebox($v,$cid=null);
	 public function comment();
	 public function comments();
	 public function contents($cid);
	 public function content($cid,$id);
	 public function models();
	 public function cates();
	 public function data();
}
?>