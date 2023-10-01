<?php
xg_route()->get('^/data/xg/contents','/app/index/data');
xg_route()->get('^/data/xg/allcate','/app/index/data');
xg_route()->get('^/data/xg/curcate','/app/index/data');
xg_route()->get('^/data/xg/custom','/app/index/data');
xg_route()->get('/data/xg/recom','/app/index/data');
$catelist=xg_category();
foreach($catelist as $k=>$v){
	xg_route()->get('/data/xg/xg-'.$v['model'],'/app/index/data',['cid'=>$k]);
}
?>