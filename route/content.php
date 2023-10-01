<?php
if(file_exists(XG_DATA.'/installed')){
	xg_route()->get('/sitemap.xml$','/sitemap/index');
	xg_route()->get('/sitemap-<id>.xml$','/sitemap/alone',['id'=>'\d+']);
	xg_route()->get('/topic/<id>$','/topic/index',['id'=>'\d+']);
	xg_route()->get('/data/xg/nav','/app/index/nav',['id'=>'\d+']);
	if(xg_config('site.img-site-url'))xg_route()->redirect('^/upload',xg_config('site.img-site-url').xg('url'));
	$catelist=xg_category();
	foreach($catelist as $k=>$v){
		xg_route()->cache(xg('baseurl'))->get('/'.$v['name'].'/<id>$','/content/index',['tbname'=>$v['model'],'id'=>'\d+','cid'=>$k]);
		xg_route()->cache(xg('baseurl'))->get('/'.$v['name'].'/index_<page>$','/category/index',['cid'=>$k,'tbname'=>$v['model'],'page'=>'\d+']);
		xg_route()->cache(xg('baseurl'))->get('/'.$v['name'].'/page_<page>$','/category/index',['cid'=>$k,'tbname'=>$v['model'],'page'=>'\d+']);
		xg_route()->cache(xg('baseurl').'index')->get('/'.$v['name'].'/$','/category/index',['cid'=>$k,'tbname'=>$v['model']]);
		xg_route()->cache(xg('baseurl').'/index')->get('/'.$v['name'].'$','/category/index',['cid'=>$k,'tbname'=>$v['model']]);
	}
}
?>