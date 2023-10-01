<?php
namespace apps\admin\controller\block;
trait sets{
	function sets(){
		$bid=xg_input('request.bid/i');
		$thid=xg_input('request.thid');
		$pagename=xg_input('request.pagename');
		$block=xg_input('request.block');
		$blocks=xg_jsonarr(xg_fcont(XG_DATA.'/blocks.json'));
		$sets=$blocks['sets'][$block]['sets'];
		$names=$blocks['names'];
		if($bid){
			$info=xg_model('app_block')->json('data')->find($bid);
			$data=$info['data'];
		}
		$head='';
		$head.='<div class="xg-box-title xg-pd-0 xg-h-a xg-bg xg-flex"><ul class="xg-tab-title">';
		$i=0;
		foreach($sets as $k1=>$v1){
			$i++;
			$head.='<li'.($i==1?' class="xg-this"':'').' xg-id="'.$i.'">'.$k1.'</li>';
		}
		$head.='</ul><div class="xg-fr xg-mr-3 xg-flex-1 xg-omit xg-right">'.$info['pagename'].' '.$info['block'].' '.$bid.'</div></div>';
		$cont='';
		$i=0;
		$cont='';
		$cont.='<div class="xg-tab-content xg-box-cont xg-mg-0 xg-over-auto">';
		foreach($sets as $k1=>$v1){
			$i++;
			$v1=xg_arr($v1);
			$html='';
			$html.='<li class="xg-tab-item'.($i==1?' xg-this':'').'" xg-id="'.$i.'"><div class="xg-mt--4">';
			if($i===1){
				$html.=xg_form('order','排序',$info['order'])->number();
				$html.=xg_form('pagename','上级模块',$info['pagename']?$info['pagename']:$pagename)->text();
			}
			foreach($v1 as $v2){
				if($v2==''){
				}elseif($v2=='styles'){
					$html.=xg_form('styles','样式代码',$data['styles'])->textarea();
					
				}elseif($v2=='name'){
					$html.=xg_form('name','模块名称',$data['name'])->text();
					
				}elseif($v2=='link'){
					$html.=xg_form('link','链接内容',$data['link'])->text();
					
				}elseif($v2=='line-height'){
					$html.=xg_form('line-height','行高',$data['line-height'])->text();
					
				}elseif($v2=='classnames'){
					$html.=xg_form('classnames','类名列表',$data['classnames'])->text();
					
				}elseif($v2=='picurl'){
					$html.=xg_form('picurl','图片地址',$data['picurl'])->image();
					
				}elseif($v2=='weight'){
					$html.=xg_form('weight','字体粗细',$data['weight'])->text();
					
				}elseif($v2=='emit'){
					$html.=xg_form('emit','触发广播的内容',$data['emit'])->tip('比如：on.shownav')->text();
					
				}elseif($v2=='z-index'){
					$html.=xg_form('z-index','层叠顺序',$data['z-index'])->text();
					
				}elseif($v2=='wrap'){
					$html.=xg_form('wrap','是否换行',$data['wrap'])->values(['不换行','换行'])->radio();
					
				}elseif($v2=='pagenav'){
					$html.=xg_form('pagenav','分页方式',$data['pagenav'])->def('link')->values(['scroll'=>'滚动','link'=>'点击'])->radio();
					
				}elseif($v2=='scroll'){
					$html.=xg_form('scroll','是否滚动',$data['scroll'])->def(0)->values([0=>'不滚动','y'=>'垂直滚动','x'=>'横向滚动'])->radio();
					
				}elseif($v2=='popup-pos'){
					$html.=xg_form('popup-pos','弹出位置',$data['popup-pos'])->def('center')->values(['center'=>'居中','top'=>'上边弹出','right'=>'右边弹出','bottom'=>'下边弹出','left'=>'左边弹出'])->radio();

					
				}elseif($v2=='nav-source'){
					$html.=xg_form('nav-source','导航栏数据来源',$data['nav-source'])->def('custom')->values(['custom'=>'自定义','sysnav'=>'系统导航栏'])->radio();
					
				}elseif($v2=='form-type'){
					$html.=xg_form('form-type','表单类型',$data['form-type'])->def('text')->values(['text'=>'文本框','password'=>'密码框','image'=>'上传图片','date'=>'日期选择框'])->radio();//,'file'=>'上传文件','textarea'=>'多行文本框','time'=>'时间选择框','picker'=>'筛选器','checkbox'=>'多选框','radio'=>'单选框'




				}elseif($v2=='recom'){
					$html.=xg_form('recom','推荐内容',$data['recom'])->def('index')->values(xg_config('site.cont-recom'))->radio();//,'cate-prop'=>'分类加属性','prop-prop'=>'属性加属性'
				}elseif($v2=='recom-cate'){
					$html.=xg_form('recom-cate','推荐内容',$data['recom-cate'])->def('index')->values(['index'=>'首页推荐','cate'=>'分类页推荐','cont'=>'内容页推荐'])->radio();




				}elseif($v2=='region-level'){
					$html.=xg_form('region-level','地区级别',$data['region-level'])->def(1)->values([1=>'省份',2=>'城市',3=>'区县'])->radio();
				}elseif($v2=='region-status'){
					$html.=xg_form('region-status','地区状态',$data['region-status'])->def(0)->values(['所有的','开启的'])->radio();




				}elseif($v2=='src'){
					$html.=xg_form('src','链接地址',$data['src'])->text();




				}elseif($v2=='sidebar-source'){
					$html.=xg_form('sidebar-source','侧边菜单数据来源',$data['sidebar-source'])->def('costom')->values(['costom'=>'自定义分类','opcate'=>'参数分类','cate'=>'任意分类'])->radio();//,'cate-prop'=>'分类加属性','prop-prop'=>'属性加属性'
				}elseif($v2=='sidebar-data'){
					$html.=xg_form('sidebar-data','侧边菜单数据内容',$data['sidebar-data'])->text();
				}elseif($v2=='sidebar-holder'){
					$html.=xg_form('sidebar-holder','菜单模块预留高度',$data['sidebar-holder'])->text();
				}elseif($v2=='sidebar-width'){
					$html.=xg_form('sidebar-width','侧边菜单宽度',$data['sidebar-width'])->text();
				}elseif($v2=='options-btn'){
					$html.=xg_form('options-btn','是否显示按钮',$data['options-btn'])->def(1)->values(['不显示','显示'])->radio();








				}elseif($v2=='options-data'){
					$html.=xg_form('options-data','筛选内容',$data['options-data'])->textarea();
				}elseif($v2=='options-source'){
					//$html.=xg_form('options-source','筛选数据来源',$data['options-source'])->textarea();









				}elseif($v2=='cateid'){
					$html.=xg_form_item_label('分类ID','<a href="javascript:call_admin_link_select(\'cateid\',\'cateid\');" class="xg-color">点击内容分类</a>');
					$html.='<div class="xg-mt-3 xg-max-500 link-select-cateid"></div>';
					if($data['cateid'])$html.='<script>call_admin_link_select_show('.xg_jsonstr($data['cateid']).',\'cateid\',\'cateid\');</script>';






				}elseif($v2=='form-item-type'){
					$html.=xg_form('form-item-type','表单元素类型',$data['form-item-type'])->values(['text'=>'文本框','region-3'=>'区县'])->radio();
					//'region-1'=>'省份','region-2'=>'城市',





				}elseif($v2=='icon'){
					$html.=xg_form_item_icon('icon','按钮图标',[],$data['icon']);





				}elseif($v2=='opdata'){
					$html.=xg_form('opdata','选项数据',$data['opdata'])->tip('每行一条数据，比如：<br>a=选项1<br>b=选项2')->textarea();

				}elseif($v2=='form-name'){
					$html.=xg_form('form-name','表单名称',$data['form-name'])->text();
				}elseif($v2=='multi'){
					if($block=='checkbox')$html.=xg_form('multi',1)->hidden();
					if($block=='radio')$html.=xg_form('multi',0)->hidden();


					
					
					
					
					
					
				}elseif($v2=='source'){
					$option=array('custom'=>'自定义','curcate'=>'当前分类','allcate'=>'所有分类','recom'=>'推荐位');
					$html.=xg_form_item_radio('source','内容来源',$option,(isset($data['source'])?$data['source']:'custom'));
				}elseif($v2=='slide-vert'){
					$option=array('1'=>'垂直','0'=>'水平');
					$html.=xg_form_item_radio('slide-vert','滚动方向',$option,(isset($data['slide-vert'])?$data['slide-vert']:'0'));
				}elseif($v2=='slide-theme'){
					$option=array('a'=>'样式一','b'=>'样式二','c'=>'样式三');
					$html.=xg_form_item_radio('slide-theme','导航栏样式',$option,(isset($data['slide-theme'])?$data['slide-theme']:'a'));
				}elseif($v2=='slide-title-pos'){
					$option=array('top'=>'顶部','middle'=>'中间','bottom'=>'底部');
					$html.=xg_form_item_radio('slide-title-pos','标题位置',$option,(isset($data['slide-title-pos'])?$data['slide-title-pos']:'bottom'));
				}elseif($v2=='slide-imgs'){
					$html.=xg_view(true)->fetch('block/slide-imgs',['imgs'=>$data['imgs']]);
				}elseif($v2=='slide-data'){
					$html.=xg_form_item_label('选择内容','<a href="javascript:call_admin_link_select(\'slide\',\'data\');" class="xg-color">点击选择内容</a>');
					$html.='<div class="xg-mt-3 xg-max-500 link-select-slide"></div>';
					if($data)$html.='<script>call_admin_link_select_show('.xg_jsonstr($data['data']).',\'slide\',\'data\');</script>';
					
				}elseif($v2=='img-nav-data'){
					$html.=xg_form_item_label('选择内容','<a href="javascript:call_admin_link_select(\'img-nav\',\'data\');" class="xg-color">点击选择内容</a>');
					$html.='<div class="xg-mt-3 xg-max-500 link-select-img-nav"></div>';
					if($data)$html.='<script>call_admin_link_select_show('.xg_jsonstr($data['data']).',\'img-nav\',\'data\');</script>';
				
				}elseif($v2=='sys'){
					$option=array('xg'=>'讯高系统内容');
					if($sys=xg_config('sys')){
						foreach($sys as $k=>$v){
							$option[$k]=$v['title'];
						}
					}
					$html.=xg_form_item_radio('sys','系统来源',$option,$data['sys']);
					
					
				}elseif($v2=='cate-list'){
					$option=array('custom'=>'自定义','curcate'=>'子分类','sibling'=>'同级分类','parent'=>'父同级分类','allcate'=>'所有分类');
					$html.=xg_form_item_radio('cate-list','分类内容',$option,(isset($data['cate-list'])?$data['cate-list']:'custom'));
				}elseif($v2=='cate-list-level'){
					$html.=xg_form_item_number('cate-list-level','最多显示几层分类',$data['cate-list-level']?$data['cate-list-level']:2);
					
				}elseif($v2=='btn-size'){
					$option=array('small'=>'小按钮','middle'=>'中等按钮','large'=>'大按钮');
					$html.=xg_form_item_radio('btn-size','按钮大小',$option,(isset($data['btn-size'])?$data['btn-size']:'small'));
				}elseif($v2=='btn-text'){
					$html.=xg_form_item_text('btn-text','按钮文本',$data['btn-text']);
				}elseif($v2=='btn-icon'){
					$html.=xg_form_item_icon('btn-icon','按钮图标',[],$data['btn-icon']);
				}elseif($v2=='btn-action'){
					$option=array('fun'=>'函数','submit'=>'提交表单','share'=>'分享按钮','star'=>'点赞按钮','download'=>'下载文件','upload'=>'上传文件');
					$html.=xg_form_item_radio('btn-action','按钮动作',$option,(isset($data['btn-action'])?$data['btn-action']:'fun'));
					$html.=xg_form_item_textarea('btn-action-content','动作内容',$data['btn-action-content'],'可以包含字段，比如下载时用的文件地址[fileurl]或/upload/file-[id].zip。');
				}elseif($v2=='btn-data'){
					$html.=xg_view(true)->fetch('block/btn_items',['data'=>xg_jsonarr($data)]);
				}elseif($v2=='menu-data'){
					$html.=xg_view(true)->fetch('block/menu_items',['thid'=>$thid,'data'=>xg_jsonarr($data)]);
					
					
				
				
				
				
				
				
				}elseif($v2=='picker-data'){
					$html.=xg_view(true)->fetch('block/picker-data',['thid'=>$thid,'datas'=>$data['picker-data']]);
				}elseif($v2=='placeholder'){
					$html.=xg_form('placeholder','占位符信息',$data['placeholder'])->text();
					
					
					
					
					
					
					
				}elseif($v2=='link-data'){
					$html.=xg_form('link-data','点击事件携带的数据',$data['link-data'])->tip('比如填写cid,tid,id')->text();
				}elseif($v2=='success-link'){
					$html.=xg_form('success-link','提交成功后跳转的页面',$data['success-link'])->text();
				
				}elseif($v2=='auto-load'){
					$html.=xg_form('auto-load','是否自动加载更多',$data['auto-load'])->def(0)->values(['不自动加载','自动加载'])->radio();
					
				}elseif($v2=='show'){
					$html.=xg_form('show','显示条件',$data['show'])->tip('比如填写cont.title，当此内容属性存在值时将显示，可以使用||或&&运算符，不填写不限制')->text();
					
				}elseif($v2=='blocks'){
					// if($bid)$html.='<a class="xg-block xg-mt-3" href="javascript:call_admin_compile_blocks('.$bid.',1);">编辑子模块</a>';
				}elseif($v2=='list-url'){
					$html.=xg_form('list-url','内容列表获取地址',$data['list-url'])->text();
				}elseif($v2=='data-url'){
					$html.=xg_view(true)->fetch('block/data-url',['thid'=>$thid,'url'=>$data['data-url']]);
				}elseif($v2=='block-data'){
					$html.=xg_form_item_text('block-data','模块数据变量名称',$data['block-data'],'如果没有定义模块数据请求地址将会调用data中此变量的数据');
				}elseif($v2=='block-hook'){
					//$html.=xg_form_item_textarea('block-hook','模块数据钩子函数',$data['block-hook'],'比如：(s)=>{s.blockdata={a:1,b:2}}');
				}elseif($v2=='blocks-display'){
					$option=array('inline'=>'内联','flex'=>'弹性','flex-col'=>'垂直弹性','block'=>'块级','justify'=>'两侧对齐');
					$html.=xg_form_item_radio('blocks-display','子元素显示方式',$option,(isset($data['blocks-display'])?$data['blocks-display']:'inline'));
					
					
					
					
					
					
				}elseif($v2=='cmt-title'){
					$html.=xg_form_item_text('cmt-title','显示标题',$data['cmt-title']?$data['cmt-title']:'评论');
				}elseif($v2=='cart-title'){
					$html.=xg_form_item_text('cart-title','显示标题',$data['cart-title']?$data['cart-title']:'购物车');
					
					
					
					
					
					
					
				}elseif($v2=='nav-data'){
					$html.=xg_form_item_label('导航栏内容','<a href="javascript:call_admin_link_select(\'nav\',\'data\');" class="xg-color">点击选择内容</a>');
					$html.='<div class="xg-mt-3 xg-max-500 link-select-nav"></div>';
					if($data)$html.='<script>call_admin_link_select_show('.xg_jsonstr($data['data']).',\'nav\',\'data\');</script>';
				}elseif($v2=='nav-theme'){
					$option=array('a'=>'样式一','b'=>'样式二','c'=>'样式三','d'=>'竖版');
					$html.=xg_form_item_radio('nav-theme','导航栏样式',$option,(isset($data['nav-theme'])?$data['nav-theme']:'a'));
				}elseif($v2=='nav-index'){
					$option=array('1'=>'显示','0'=>'不显示');
					$html.=xg_form_item_radio('nav-index','是否显示首页链接按钮',$option,(isset($data['nav-index'])?$data['nav-index']:'1'));

					
					
					
					
					
				}elseif($v2=='box-style'){
					$html.=xg_form_item_textarea('box-style[box]','容器样式',$data['box-style']['box']);
					$html.=xg_form_item_textarea('box-style[title]','标题栏样式',$data['box-style']['title']);
					$html.=xg_form_item_textarea('box-style[title-text]','标题文本样式',$data['box-style']['title-text']);
					$html.=xg_form_item_textarea('box-style[content]','内容框样式',$data['box-style']['content']);
				}elseif($v2=='cont-list-style'){
					$html.=xg_form_item_textarea('cont-list-style[box]','子元素样式',$data['cont-list-style']['box']);
					$html.=xg_form_item_textarea('cont-list-style[img-box]','子元素图片容器样式',$data['cont-list-style']['img-box']);
					$html.=xg_form_item_textarea('cont-list-style[img]','子元素图片样式',$data['cont-list-style']['img']);
					$html.=xg_form_item_textarea('cont-list-style[title]','子元素标题样式',$data['cont-list-style']['title']);
					$html.=xg_form_item_textarea('cont-list-style[bottom]','子元素底部内容样式',$data['cont-list-style']['bottom']);
					$html.=xg_form_item_textarea('cont-list-style[desc]','子元素描述样式',$data['cont-list-style']['desc']);
					
					
					
					
					
					
					
				}elseif($v2=='show-desc'){
					$option=array('1'=>'显示','0'=>'不显示');
					$html.=xg_form_item_radio('show-desc','是否显示描述',$option,(isset($data['show-desc'])?$data['show-desc']:'0'));
				}elseif($v2=='pagesize'){
					$html.=xg_form_item_text('pagesize','每页显示数量',(isset($data['pagesize'])?$data['pagesize']:12),'');
				}elseif($v2=='show-count'){
					if($block=='cate-box')$def=15;
					if($block=='cate-box-img')$def=8;
					$html.=xg_form_item_text('show-count','显示数量',(isset($data['show-count'])?$data['show-count']:$def),'');
				}elseif($v2=='title'){
					$html.=xg_form_item_text('title','标题',(isset($data['title'])?$data['title']:''),'可以调用系统字段，比如：[classname]表示分类名称。');
				}elseif($v2=='info-left'){
					$html.=xg_form_item_text('info-left','左侧内容',(isset($data['info-left'])?$data['info-left']:'浏览[view]次'),'');
				}elseif($v2=='info-right'){
					$html.=xg_form_item_text('info-right','右侧内容',(isset($data['info-right'])?$data['info-right']:'[time.y]年[time.m]月[time.d]日'),'');
				}elseif($v2=='bottom-left'){
					$html.=xg_form_item_text('bottom-left','底部左侧内容',(isset($data['bottom-left'])?$data['bottom-left']:'浏览[view]次'),'');
				}elseif($v2=='bottom-right'){
					$html.=xg_form_item_text('bottom-right','底部右侧内容',(isset($data['bottom-right'])?$data['bottom-right']:'[time.y]年[time.m]月[time.d]日'),'');
				}elseif($v2=='cateids'){
					$html.=xg_form_item_label('选择内容分类','<a href="javascript:call_admin_link_select(\'cateids\',\'cateids\');" class="xg-color">点击内容分类</a>');
					$html.='<div class="xg-mt-3 xg-max-500 link-select-cateids"></div>';
					if($data['cateids'])$html.='<script>call_admin_link_select_show('.xg_jsonstr($data['cateids']).',\'cateids\',\'cateids\');</script>';
				}elseif($v2=='toplink'){
					$html.=xg_form_item_label('顶部链接地址','<a href="javascript:call_admin_link_select(\'toplink\',\'toplink\');" class="xg-color">点击内容分类</a>');
					$html.='<div class="xg-mt-3 xg-max-500 link-select-toplink"></div>';
					if($data['toplink'])$html.='<script>call_admin_link_select_show('.xg_jsonstr($data['toplink']).',\'toplink\',\'toplink\');</script>';
				}elseif($v2=='title-color'){
					$html.=xg_form_item_color('title-color','标题文本颜色',$data['title-color']);
				}elseif($v2=='desc-color'){
					$html.=xg_form_item_color('desc-color','描述文本颜色',$data['desc-color']);
					
					
					
				}elseif($v2=='box-theme'){
					$option=array('a'=>'主题一','b'=>'主题二','c'=>'主题三');
					$html.=xg_form_item_radio('box-theme','容器主题',$option,(isset($data['box-theme'])?$data['box-theme']:'a'));
				}elseif($v2=='list-theme'){
					$option=array('a'=>'样式一','b'=>'样式二','c'=>'样式三','d'=>'样式四','e'=>'样式五');
					$html.=xg_form_item_radio('list-theme','列表样式',$option,(isset($data['list-theme'])?$data['list-theme']:'a'));
					
					
					
					
				
				}elseif($v2=='icon-nav-list'){
					$html.=$aaa=xg_view(true)->fetch('block/icon_nav_list',['icons'=>xg_jsonarr($data['icon-nav-list']),'thid'=>$thid]);
				}elseif($v2=='icon-radius'){
					$html.=xg_form_item_text('icon-radius','图标圆角',$data['icon-radius']);
				}elseif($v2=='icon-margin'){
					$html.=xg_form('icon-margin','图标外边距',$data['icon-margin'])->style4in1();
				}elseif($v2=='icon-padding'){
					$html.=xg_form('icon-padding','图标内边距',$data['icon-padding'])->style4in1();
				}elseif($v2=='icon-bg-radius'){
					$html.=xg_form_item_text('icon-bg-radius','图标背景圆角',$data['icon-bg-radius']);
				}elseif($v2=='icon-width'){
					$html.=xg_form_item_text('icon-width','图标宽度',$data['icon-width']);
				}elseif($v2=='icon-height'){
					$html.=xg_form_item_text('icon-height','图标高度',$data['icon-height']);
				}elseif($v2=='icon-bg-width'){
					$html.=xg_form_item_text('icon-bg-width','图标背景宽度',$data['icon-bg-width']);
				}elseif($v2=='icon-bg-height'){
					$html.=xg_form_item_text('icon-bg-height','图标背景高度',$data['icon-bg-height']);
					
					
					
					
					
				
					
				}elseif($v2=='img-radius'){
					$html.=xg_form_item_text('img-radius','图片圆角',$data['img-radius']);
				}elseif($v2=='img-margin'){
					$html.=xg_form('img-margin','图片外边距',$data['img-margin'])->style4in1();
				}elseif($v2=='img-padding'){
					$html.=xg_form('img-padding','图片内边距',$data['img-padding'])->style4in1();
				}elseif($v2=='img-width'){
					$html.=xg_form_item_text('img-width','图片宽度',$data['img-width']);
				}elseif($v2=='img-height'){
					$html.=xg_form_item_text('img-height','图片高度',$data['img-height']);	
					
				}elseif($v2=='item-radius'){
					$html.=xg_form_item_text('item-radius','元素圆角',$data['item-radius']);
				}elseif($v2=='item-margin'){
					$html.=xg_form('item-margin','元素外边距',$data['item-margin'])->style4in1();
				}elseif($v2=='item-padding'){
					$html.=xg_form('item-padding','元素内边距',$data['item-padding'])->style4in1();
				}elseif($v2=='item-width'){
					$html.=xg_form_item_text('item-width','元素宽度',$data['item-width']);
				}elseif($v2=='item-height'){
					$html.=xg_form_item_text('item-height','元素高度',$data['item-height']);
					
				
					
				}elseif($v2=='html'){
					$html.=xg_form_item_editor('html','HTML内容',[],$data['html']);
				}elseif($v2=='custom'){
					$html.=xg_form_item_textarea('custom','自定义内容',$data['custom']);
				}elseif($v2=='indent'){
					$html.=xg_form_item_text('indent','段落缩进距离',$data['indent'],'比如填写2rem，表示两个字符宽度。');
				}elseif($v2=='para-dis'){
					$html.=xg_form_item_text('para-dis','段落间距离',$data['para-dis'],'比如填写0.5rem，表示0.5个字符高度。');
				}elseif($v2=='fontsize'){
					$html.=xg_form_item_text('fontsize','字体大小',$data['fontsize'],'比如填写1.2rem，表示1.2个字符大小。');
				}elseif($v2=='align'){
					$option=array('left'=>'左对齐','center'=>'居中对齐','right'=>'右对齐','justify'=>'两侧对齐');
					if($block=='btns')unset($option['justify']);
					$html.=xg_form_item_radio('align','对齐方式',$option,$data['align']?$data['align']:'left');
					
					
					
					
					
					
				}elseif($v2=='width'){
					if($block=='image'){
						$value=(isset($data['width'])?$data['width']:'10rem');
					}else{
						$value=$data['width'];
					}
					$html.=xg_form_item_text('width','宽度',$value);
				}elseif($v2=='height'){
					if($block=='image'){
						$value=(isset($data['height'])?$data['height']:'10rem');
					}else{
						$value=$data['height'];
					}
					$html.=xg_form_item_text('height','高度',$value);
				
					
					
					
					
					
				}elseif($v2=='bg-color'){
					$html.=xg_form_item_color('bg-color','背景颜色',$data['bg-color']);
				}elseif($v2=='theme-color2'){
					$html.=xg_form_item_color('theme-color2','主题颜色2',$data['theme-color2']);
				}elseif($v2=='theme-color'){
					$html.=xg_form_item_color('theme-color','主题颜色',$data['theme-color']);
				}elseif($v2=='text-color'){
					$html.=xg_form_item_color('text-color','文本颜色',$data['text-color']);
				}elseif($v2=='height'){
					$html.=xg_form_item_text('height','高度',$data['height']);
				}elseif($v2=='img-height'){
					$html.=xg_form_item_text('img-height','图片高度',$data['img-height']);
				}elseif($v2=='margin'){
					$html.=xg_form('margin','外边距',(isset($data['margin'])?$data['margin']:$def_margin))->style4in1();
				}elseif($v2=='padding'){
					$html.=xg_form('padding','内边距',(isset($data['padding'])?$data['padding']:''))->style4in1();
				}elseif($v2=='radius'){
					$html.=xg_form_item_text('radius','圆角',isset($data['radius'])?$data['radius']:'');
				}elseif($v2=='border'){
					$html.=xg_form_item_color('border','边框',isset($data['border'])?$data['border']:'','可以填写颜色值，比如#f00，也可以填写详细的边框信息，比如：1px #fff solid，其中1px表示边框宽度，solid表示直线。');
				}elseif($v2=='border-width'){
					$html.=xg_form('border-width','边框宽度',isset($data['border-width'])?$data['border-width']:'')->text();
				}
			}
			$html.='</div></li>';
			$cont.=$html;
		}
		$cont.='</div>';
		$btn1=' <button class="xg-btn block-info-btn">关联复制</button> ';
		$btn2=' <button class="xg-btn block-info-btn">单独复制</button> ';
		$btn3=' <button type="button" class="xg-btn block-dis-corr" data-bid="'.$bid.'" data-obid="'.$info['obid'].'">取消关联</button> ';
		$btn=$btn1.$btn2;
		if($info['obid']){
			$btn.=$btn3;
		}
		$cont.='<div class="block-info-btns"><button class="xg-btn block-info-btn">提交</button>'.$btn.'</div>';
		$html='<div class="xg-box xg-box-a xg-tab-box xg-border xg-theme-border xg-h-100">'.$head.$cont.'</div>';
		if($block=='loop-blocks' or $block=='popup-blocks')$formstyle=' style="position:fixed;width:100%;height:calc( 100% - 100px ); overflow-y:auto;"';
		$html.=xg_form_item_hidden('tid',$tid);
		$html.=xg_form_item_hidden('block',$block);
		$html.=xg_form_item_hidden('bid',$bid);
		$html.=xg_form_item_hidden('status',isset($info['status'])?$info['status']:1);
		$html='<form class="sets-form xg-form xg-h-100" action="'.xg_url('block/data',array('thid'=>$thid)).'"'.$formstyle.' method="post">'.$html.'</form><script>xg.tab("block-info").bind(".block-info .xg-tab-title",".block-info .xg-tab-content");</script>';
		xg_success(array('html'=>$html));
	}
}
?>