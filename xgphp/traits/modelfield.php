<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\traits;
trait modelfield{
	public function field_search(){
		$val=xg_input('val');
		$model=xg_input('model');
		$field=xg_input('field');
		$show=xg_input('show');
		$data=xg_model($model,1)->where('status',1)->select();
		$data=$data?$data:[];
		$pinyin=new \xg\libs\pinyin();
		foreach($data as $k=>$v){
			if(preg_match_all("/\[(.*?)\]/",$show,$matches)){
				$title=$show;
				foreach($matches[1] as $match){
					$title=str_replace("[{$match}]",$v[$match],$title);
				}
			}else{
				$title=$v[$show];
			}
			$data[$k]=['value'=>$v[$field],'title'=>$title];
			$data[$k]['pinyin']=$pinyin->pinyin($title);
			$data[$k]['letter']=$pinyin->letter($title);
		}
		xg_jsonok(['data'=>array_values($data)]);
	}
}
?>