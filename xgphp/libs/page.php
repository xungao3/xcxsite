<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\libs;
class page{
	public $pagesize;
	public $param;
	public $rscount;
	public $pagecount;
	public $maxcount=11;
	public $now=1;
	public $name='page';
	public $data=[];
	protected $config = array(
		'prev'=>'上一页',
		'next'=>'下一页',
		'first'=>'首页',
		'end'=>'尾页',
		'theme'=>'%ROWS% %COUNT% %FIRST% %PREV% %PAGE% %NEXT% %END%',
		'temp'=>''
	);
	public function __construct($rscount,$pagesize=12,$param=array()){
		$this->rscount=$rscount;
		$this->pagesize=$pagesize;
		$this->param=$param?$param:$_GET;
		unset($this->param['xg']);
		$this->now=max(intval($_GET[$this->name]),1);
	}
	public function config($name,$value){
		if(isset($this->config[$name])){
			$this->config[$name]=$value;
		}
	}
	protected function url($page){
		if($this->config['temp']){
			return str_ireplace('[PAGE]',$page,$this->config['temp']);
		}
		return str_ireplace('[PAGE]',$page,$this->url);
	}
	public function arr(){
		$this->create();
		return $this->data[0];
	}
	public function json(){
		$this->create();
		return xg_jsonstr($this->data[0]);
	}
	protected function create(){
		if($this->data)return $this->data;
		if(!$this->rscount)return;
		$this->param[$this->name]='[PAGE]';
		$arr=[];
		$param=$this->param;
		$path=parse_url(xg_rurl(),PHP_URL_PATH);
		$this->url=urldecode(xg_url($path,$param));
		$this->pagecount=$arr['pagecount']=(string)ceil($this->rscount/$this->pagesize);
		$this->now=$arr['now']=$arr['pagecount']=min($this->now,max($this->pagecount,1));
		$tmp=$this->maxcount/2;
		$tmp2=ceil($tmp);
		$prev=($this->now-1>0)?'<a class="xg-page-prev" href="'.$this->url($this->now-1).'">'.$this->config['prev'].'</a>': '';
		$next=($this->now+1<=$this->pagecount)?'<a class="xg-page-next" href="'.$this->url($this->now+1).'">'.$this->config['next'].'</a>': '';
		$arr['prev']=($this->now-1>0)?$this->now-1:null;
		$arr['next']=($this->now+1<=$this->pagecount)?$this->now+1:null;
		$first='';
		if($this->now>1)$arr['first']=1;
		if(($this->maxcount==0 and $this->now>1) or ($this->maxcount>0 and $this->pagecount>$this->maxcount and ($this->now-$tmp)>=1)){
			$first='<a class="xg-page-first" href="'.$this->url(1).'">'.$this->config['first'].'</a>';
		}
		$end='';
		if($this->now<$this->pagecount)$arr['end']=$this->pagecount;
		if(($this->maxcount==0 and $this->now<$this->pagecount) or ($this->maxcount>0 and $this->pagecount>$this->maxcount and ($this->now+$tmp)< $this->pagecount)){
			$end='<a class="xg-page-end" href="'.$this->url($this->pagecount).'">'.$this->config['end'].'</a>';
		}
		$page="";
		for($i=1;$i<=$this->maxcount;$i++){
			if(($this->now-$tmp)<=0){
				$pagei=$i;
			}elseif(($this->now+$tmp-1)>=$this->pagecount){
				$pagei=$this->pagecount-$this->maxcount+$i;
			}else{
				$pagei=$this->now-$tmp2+$i;
			}
			if($pagei>0 and $this->now!=$pagei){
				if($pagei<=$this->pagecount){
					$page.=' <a class="xg-page-link" href="'.$this->url($pagei).'">'.$pagei.'</a> ';
					$arr['nums'][$pagei]=$pagei;
				}else{
					break;
				}
			}else{
				if($pagei>0 and $this->pagecount!=1){
					$page.=' <span class="xg-page-cur xg-bg xg-border-color">'.$pagei.'</span> ';
					$arr['nums'][$pagei]=null;
				}
			}
		}
		return $this->data=[$arr,$prev,$next,$first,$page,$end,$this->rscount,$this->pagecount];
	}
	public function show(){
		$this->create();
		list($arr,$prev,$next,$first,$page,$end,$rscount,$pagecount)=$this->data;
		$page_str=str_replace(
				array('%PREV%','%NEXT%','%FIRST%','%PAGE%','%END%','%ROWS%','%COUNT%'),
				array($prev,$next,$first,$page,$end,'<span class="xg-page-rows">共'.$rscount.'条记录</span>','<span class="xg-page-count">共'.$pagecount.'页</span>'),
				$this->config['theme']
			);
		return "<div class=\"xg-page\">{$page_str}</div>";
	}
}