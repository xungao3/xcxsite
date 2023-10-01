<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
function xg_content($tbname,$id){
	if(is_numeric($tbname))$tbname=xg_category($tbname,'model');
	$info=xg_model('content')->name($tbname)->where('status',1)->fields('*')->find($id);
	return $info;
}
function xg_save_model($id){
	$info=xg_model('model')->fields('name,title,alias,type,menu,remark')->find($id);
	$fields=xg_model('fields')->where(['sys'=>0,'mid'=>$id])->without('id,mid,createtime,updatetime')->select();
	$data=['info'=>$info,'fields'=>$fields];
	return $data;
}
function xg_load_model($data){
	$info=$data['info'];
	$name=$info['name'];
	$title=$info['title'];
	$table='xg_'.$name;
	if(xg_model('model')->where('name',$name)->value('id'))return '已存在此模型';
	$info['status']=1;
	$tbpre=XG_TBPRE;
	$sql=str_replace(['[--xg-table-name--]','[--xg-table-title--]'],["{$tbpre}xg_{$name}",$title],xg_model_table_state());
	xg_db()->exec($sql);
	$id=xg_model('model')->time('createtime,updatetime')->insert($info);
	$fields=xg_model_sys_fields();
	foreach($fields as $k=>$v){
		$fields[$k]['mid']=$id;
	}
	xg_model('fields')->insert($fields);
	$fields=$data['fields'];
	foreach($fields as $f){
		if(!xg_field_info($table,$f['name'])){
			xg_add_column($table,$f['name'],$f['type'],$f['length'],$f['title']);
		}
		if(!xg_model('fields')->where('name',$f['name'])->value('id')){
			$f['mid']=$id;
			xg_model('fields')->time('createtime,updatetime')->insert($f);
		}
	}
}
function xg_model_sys_fields(){
	return [
		['sys'=>1,'form'=>'text','type'=>'char','length'=>80,'title'=>'标题','name'=>'title','status'=>1,'contf'=>1,'adminf'=>1,'catef'=>1],
		['sys'=>1,'form'=>'image','type'=>'varchar','length'=>120,'title'=>'封面图','name'=>'pic','status'=>1,'contf'=>1,'catef'=>1],
		['sys'=>1,'form'=>'category','type'=>'int','length'=>10,'title'=>'分类','name'=>'cid','status'=>1,'contf'=>1,'catef'=>0,'adminf'=>1],
		['sys'=>1,'form'=>'text','type'=>'varchar','length'=>140,'title'=>'关键词','name'=>'keywords','status'=>1,'contf'=>1,'catef'=>0],
		['sys'=>1,'form'=>'textarea','type'=>'varchar','length'=>140,'title'=>'描述','name'=>'description','status'=>1,'contf'=>1,'catef'=>0],
		['sys'=>1,'form'=>'datetime','type'=>'int','length'=>10,'title'=>'时间','name'=>'newstime','status'=>1,'contf'=>1,'catef'=>0,'adminf'=>1],
		['sys'=>1,'form'=>'text','type'=>'varchar','length'=>30,'title'=>'发布用户','name'=>'username','status'=>1,'contf'=>1,'catef'=>1],
		['sys'=>1,'form'=>'','type'=>'int','length'=>10,'title'=>'浏览数','name'=>'view','status'=>1,'contf'=>1,'catef'=>0,'adminf'=>1],
		['sys'=>1,'form'=>'','type'=>'int','length'=>10,'title'=>'点赞数','name'=>'star','status'=>1,'contf'=>1,'catef'=>0],
		['sys'=>1,'form'=>'','type'=>'int','length'=>10,'title'=>'评论数','name'=>'cmt','status'=>1,'contf'=>1,'catef'=>0],
		['sys'=>1,'form'=>'','type'=>'tinyint','length'=>4,'title'=>'状态','name'=>'status','status'=>1,'contf'=>0,'catef'=>0]
	];
}
function xg_model_table_state(){
	return "CREATE TABLE IF NOT EXISTS `[--xg-table-name--]` (
		`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '內容ID',
		`uid` int(10) UNSIGNED NOT NULL COMMENT '用户ID',
		`username` varchar(30) NOT NULL COMMENT '发布用户',
		`sub` tinyint(4) NOT NULL COMMENT '是否投稿',
		`pic` varchar(120) NOT NULL COMMENT '封面图',
		`title` char(80) NOT NULL COMMENT '标题',
		`cid` int(10) UNSIGNED NOT NULL COMMENT '所属分类',
		`keywords` varchar(140) NOT NULL COMMENT '关键字',
		`description` varchar(140) NOT NULL COMMENT '描述',
		`view` int(10) UNSIGNED NOT NULL COMMENT '浏览量',
		`createtime` int(10) UNSIGNED NOT NULL COMMENT '创建时间',
		`updatetime` int(10) UNSIGNED NOT NULL COMMENT '更新时间',
		`newstime` int(10) UNSIGNED NOT NULL COMMENT '显示时间',
		`status` tinyint(4) NOT NULL COMMENT '数据状态',
		`correlation` varchar(100) NOT NULL COMMENT '相关信息',
		`star` int(11) UNSIGNED NOT NULL COMMENT '点赞',
		`top` tinyint(2) NOT NULL COMMENT '置顶',
		`cmt` int(11) NOT NULL COMMENT '评论',
		PRIMARY KEY (`id`) USING BTREE
	) ENGINE = MyISAM COMMENT = '[--xg-table-title--]';";
}
function xg_allmodels($name=null,$col=null){
	if($cache=xg_cache('models')){
		$result=$cache;
	}else{
		$data=xg_model('model')->select();
		$result=[];
		foreach($data as $v){
			$form=$v['form'];
			$contf=$catef=$fieldlist=['id','cid','uid','sub','top','status','view','newstime','status'];
			$timef=$jsonf=[];
			$fields=xg_model('fields')->where('mid',$v['id'])->where('status',1)->json('data')->order('forder asc')->column('*','name');
			foreach($fields as $f){
				$form[$f['name']]=['name'=>$f['name'],'title'=>$f['title'],'type'=>$f['form'],'data'=>$f['data'],'remark'=>$f['remark']];
				$fieldlist[]=$f['name'];
				if($f['contf'])$contf[]=$f['name'];
				if($f['form']=='datetime')$timef[]=$f['name'];
				if($f['catef'])$catef[]=$f['name'];
				if($f['jsonf'])$jsonf[]=$f['name'];
			}
			$catef=array_unique($catef);
			$contf=array_unique($contf);
			$fieldlist=array_unique($fieldlist);
			$result[$v['name']]=['name'=>$v['name'],'title'=>$v['title'],'type'=>$v['type'],'alias'=>$v['alias'],'id'=>$v['id'],'form'=>$form,'fields'=>$fieldlist,'contf'=>$contf,'catef'=>$catef,'timef'=>$timef,'jsonf'=>$jsonf,'status'=>$v['status']];
		}
		xg_cache('models',$result);
	}
	if($name!==null){
		if($col)return $result[$name][$col];
		return $result[$name];
	}
	return $result;
}
function xg_models($name=null,$col=null){
	$result=xg_allmodels();
	foreach($result as $k=>$v){
		if($v['status']<=0){
			unset($result[$k]);
		}
	}
	if($name!==null){
		if($col)return $result[$name][$col];
		return $result[$name];
	}
	return $result;
}
function xg_contents($cid,$page=1,$pagesize=10){
	$count=xg_model('content')->where('cid',$cid)->count();
	$list=xg_model('content')->where('cid',$cid)->page($page,$pagesize)->select();
	foreach($list as $k=>$v){
		$v['pic']=xg_http_url($v['pic']);
		$list[$k]=$v;
	}
	return ['list'=>$list,'cid'=>$cid,'count'=>$count,'page'=>$page,'pagesize'=>$pagesize];
}
function xg_content_url($cid,$id){
	if($url=xg_hooks('content-url')->run($cid,$id)->last()){
		return $url;
	}
	if($id and $cid){
		$model=xg_category($cid,'model');
		if(!XG_ROUTE)return xg_url('home/content/index',['model'=>$model,'cid'=>$cid,'id'=>$id]);
		return '/'.xg_category($cid,'name',-1).'/'.$id.'.html';
	}
}
function xg_category_url($cid){
	if($cid){
		if(!XG_ROUTE)return xg_url('home/category/index',['cid'=>$cid]);
		return '/'.xg_category($cid,'name',-1).'/';
	}
}
function xg_child_ids($ids){
	$ids=xg_arr($ids);
	$cates=xg_category();
	$children=[];
	foreach($ids as $id){
		$children[]=$id;
		$children=xg_merge($children,$cates[$id]['children']);
	}
	$children=array_unique($children);
	return $children;
}
function xg_cateids($v=null){
	if($v===null){
		return array_values(xg_category(null,'id',1));
	}
	if(is_string($v))$v=xg_jsonarr($v);
	$cids=[];
	foreach($v as $v2){
		$cids[]=$v2['cid'];
	}
	return $cids;
}
function xg_cate_children($data,$pid=0){
	$arr=[];
	foreach($data as $v){
		if($v['pid']==$pid){
			$children=xg_cate_children($data,$v['id']);
			if($children)$v['children']=$children;
			$arr[$v['id'].'']=$v;
		}
	}
	return array_values($arr);
}
function xg_cate_select_cate($cur=0,$data=null,$model=null){
	if(!$data)$data=xg_cate_tree();
	if($cur==-1){
		$arr=[''=>'请选择',0=>'顶级分类'];
		$cur=0;
	}else{
		$arr=[0=>'顶级分类'];
	}
	foreach($data as $v){
		$parents=xg_category($v['id'],'parents');
		if($cur==$v['id'] or xg_in_array($cur,$parents) or ($model and $model!=$v['model']))continue;
		$arr[$v['id']]=['classname'=>'xg-bg-black-'.(is_array($parents)?count($parents)+1:1),'value'=>$v['id'],'title'=>str_repeat('&nbsp;&nbsp;',is_array($parents)?count($parents):0).'|-'.$v['title'].'['.$v['name'].']'.'['.$v['id'].']'];
		if($v['children']){
			$arr2=xg_cate_select_cate($cur,$v['children'],$model);
			$arr=$arr+$arr2;
		}
	}
	return $arr;
}
function xg_cate_select_cont($data=null,$model=null){
	if(!$data)$data=xg_cate_tree();
	$arr=[];
	foreach($data as $v){
		$parents=xg_category($v['id'],'parents');
		if($model and $model!=$v['model'])continue;
		$arr[$v['id']]=['classname'=>'xg-bg-black-'.(is_array($parents)?count($parents)+1:1),'value'=>$v['id'],'title'=>str_repeat('&nbsp;&nbsp;',is_array($parents)?count($parents):0).'|-'.$v['title'].'['.$v['name'].']'.'['.$v['id'].']'];
		if($v['children']){
			$arr2=xg_cate_select_cont($v['children'],$model);
			$arr=$arr+$arr2;
		}
	}
	return $arr;
}
function xg_categories(){
	$cachename='categories';
	if($cache=xg_cache($cachename))return $cache;
	$data=xg_model('category')->select();
	xg_cache($cachename,$data,0,'category');
	return $data;
}
function xg_cate_tree($id=0){
	$data=xg_categories();
	if($id==0){
		$cachename='category-tree';
		if($cache=xg_cache($cachename))return $cache;
		$data=xg_cate_children($data,$id);
		xg_cache($cachename,$data,0,'category');
	}else{
		$data=xg_cate_children($data,$id);
	}
	return $data;
}
function xg_category($id=null,$field=null){
	$cachename='category';
	if($cache=xg_cache($cachename)){
		$data=$cache;
	}else{
		$data=[];
		$fun=function($cates)use(&$fun,&$data){
			foreach($cates as $cate){
				if($cate['pid']){
					$parents=$data[$cate['pid']]['parents'];
					$parents[]=$cate['pid'];
					$treepath=$data[$cate['pid']]['treepath'];
					$treepath[]=$cate['title'];
					$data[$cate['pid']]['son'][]=$cate['id'];
					foreach($parents as $pid){
						$data[$pid]['children'][]=$cate['id'];
						$data[$pid]['count']+=$cate['count'];
					}
				}else{
					$treepath[0]=$cate['title'];
				}
				$data[$cate['id']]=['name'=>$cate['name'],'pid'=>$cate['pid'],'selfcount'=>$cate['count'],'count'=>$cate['count'],'model'=>$cate['model'],'title'=>$cate['title'],'id'=>$cate['id'],'status'=>$cate['status'],'parents'=>$parents,'order'=>$cate['order'],'treepath'=>$treepath,'catetpl'=>$cate['catetpl'],'conttpl'=>$cate['conttpl']];
				$fun($cate['children']);
			}
		};
		$cates=xg_cate_tree();
		$fun($cates);
		if($data){
			xg_array_sort($data,'order',SORT_ASC);
			xg_cache($cachename,$data,0,'category');
		}
	}
	if($id===0){
		$top=['title'=>'顶级分类','son'=>[],'children'=>[]];
		foreach($data as $k=>$v){
			if($v['pid']==0){
				$top['son'][]=$k;
				$top['children']=xg_merge($top['children'],$v['children']);
			}
		}
		$top['children']=array_unique($top['children']);
		if($field)return $top[$field];
		return $top;
	}elseif($id!==null){
		if($field)return $data[$id][$field];
		return $data[$id];
	}
	if($field){
		if(strpos($field,',')!==false and $fields=xg_arr($field)){
			foreach($data as $k=>$v){
				$tmp=[];
				foreach($fields as $field){
					$tmp[$field]=$v[$field];
				}
				$data[$k]=$tmp;
			}
		}else{
			foreach($data as $k=>$v){
				$data[$k]=$v[$field];
			}
		}
	}
	return $data;
}



function xg_recom_sets($recom='cont'){
	$data=xg_config('site.'.$recom.'-recom');
	$data=xg_line_arr($data);
	foreach($data as $k=>$v){
		unset($data[$k]);
		$v=xg_arr($v,'=');
		$data[$v[0]]=$v[1];
	}
	return $data;
}


function xg_cache_html($html,$path=null){
	if(!$path)$path=xg('baseurl');
	if(!xg_file_ext($path))$path.='.html';
	$cid=xg_input('get.cid/i',0);
	$id=xg_input('get.id/i',0);
	$sys=xg_input('get.sys','xg');
	$where=xg_where();
	$data=[];
	if($cid and $id){
		if(xg_config('site.html-cache-cont')){
			$data['cateid']=$cid;
			$data['contid']=$id;
		}
	}elseif($cid){
		if(xg_config('site.html-cache-cate')){
			$data['cateid']=$cid;
		}
	}
	if($data){
		$where->where($data);
		$data['file']=$path;
		$data['sys']=$sys;
		if($id=xg_model('html_file')->where($where)->value('id')){
			xg_model('html_file')->where($id)->time('time')->update(['file'=>$path]);
		}else{
			xg_model('html_file')->time('time')->add($data);
		}
		xg_cont(XG_PUBLIC.$path,$html);
	}
}


function xg_nav_tree($pid=0,$status=null){
	$list=xg_nav_data();
	$fun=function($pid=0)use(&$fun,$list,$status){
		$data=[];
		foreach($list as $v){
			if($v['type']=='category' and xg_category($v['infoid'],'status')<=0)continue;
			if($status>0 and $v['status']<=0)continue;
			if($v['pid']==$pid){
				$v['children']=$fun($v['id']);
				$data[]=$v;
			}
		}
		return $data;
	};
	return $fun($pid);
}
function xg_nav_data(){
	return xg_model('nav')->cache('xg-nav-data')->order('`order` asc')->column();
}
function xg_nav_link($v){
	if($v['type']=='category'){
		return xg_category_url($v['infoid']);
	}elseif($v['type']=='link'){
		return $v['link'];
	}else{
		return xg_content_url($v['cateid'],$v['infoid']);
	}
}
?>