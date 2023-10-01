<?php
xg('hooks.view.parse-php.xg-recom-category',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$tpl=preg_replace_callback('/\{recom\-category[\s]*(?<p>.*?)\}(?<r>.*?)\{\/recom\-category\}/is',function($rt){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt['p'],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		if(!$p['cid'] and !$p['tbname'])return;
		$var=($p['var']?preg_replace('/[^a-zA-Z0-9_]+/','',$p['var']):'data');
		$tpl='<?php $'.$var.'=[];$ids=[];';
		if($p['model']){
			$tpl.='$tbname="'.$p['model'].'";'."\r\n";
		}elseif($p['tbname']){
			$tpl.='$tbname="'.$p['tbname'].'";'."\r\n";
		}else{
			$tpl.='$tbname=xg_category('.$p['cid'].',"model");'."\r\n";
		}
		$tpl.='$def_fields=xg_models($tbname,"catef");'."\r\n";
		$tpl.='$ids=xg_model("recom")->where("status",1)->where("type","category")->where("recom","'.$p['recom'].'")->order("`order` asc")->column("cateid");'."\r\n";
		$tpl.='if($ids){ ';
		$tpl.='$ids=array_values($ids);'."\r\n";
		$tpl.='$cates=xg_model("category")->where("status",1)->where("id","in",$ids)->select();'."\r\n";
		$tpl.='foreach($cates as $k=>$v){ $cates[$k]["order"]=array_search($v["id"],$ids);}$cates=xg_array_sort($cates,"order",SORT_ASC);'."\r\n";
		$tpl.='foreach($cates as $v){';
			$tpl.='$cid=$v["id"];$title=$v["title"];'."\r\n";
			$tpl.='$model=xg_model("content")->name($tbname)->where("cid",$cid)->where("status",1)->limit(0,12);'."\r\n";
			$tpl.='$model->fields('.($p['fields']?'"'.$p['fields'].'"':'$def_fields').');'."\r\n";
			if($p['with']){
				if(strpos($p['with'],'$')!==0)$p['with']="'$p[with]'";
				$tpl.='$model->with('.$p['with'].');'."\r\n";
			}
			$tpl.='$model->where("cid",xg_child_ids($cid));'."\r\n";
			if($p['where'])$tpl.='$model->where("'.$p['where'].'");'."\r\n";
			$tpl.='$model->order("'.($p['order']?$p['order']:'top desc,id desc').'");'."\r\n";
			if($p['limit'])$tpl.='$model->limit("'.$p['limit'].'");'."\r\n";
			if($p['count'])$tpl.='$model->limit("0,'.$p['count'].'");'."\r\n";
			$tpl.='$'.$var.'=$model->contents();'."\r\n";
			if($rt['r'])$tpl.='?>'.$rt['r'].'<?php ';
		$tpl.='}}';
		$tpl.=' ?>';
		return $tpl;
	},$tpl);
	return $tpl;
});
xg('hooks.view.parse-php.xg-recom-contents',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$tpl=preg_replace_callback('/\{recom\-contents[\s]*(?<p>.*?)\}(?<r>.*?)\{\/recom\-contents\}/is',function($rt)use(&$fun){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt['p'],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		if((!$p['recom'] or (!$p['cid'] and !$p['tbname'])) and !$p['pid'])return;
		$var=($p['var']?preg_replace('/[^a-zA-Z0-9_]+/','',$p['var']):'data');
		$tpl='<?php $'.$var.'=[];$ids=[];';
		if($p['model']){
			$tpl.='$tbname="'.$p['model'].'";'."\r\n";
		}elseif($p['tbname']){
			$tpl.='$tbname="'.$p['tbname'].'";'."\r\n";
		}else{
			$tpl.='$tbname=xg_category('.$p['cid'].',"model");'."\r\n";
		}
		$tpl.='$def_fields=xg_models($tbname,"catef");'."\r\n";
		$tpl.='if(!$ids)$ids=xg_model("recom")->where("status",1)->where("model",$tbname)->where("recom","'.$p['recom'].'")->order("`order` asc")->column("infoid");'."\r\n";
		$tpl.='if($ids){ ';
		$tpl.='$ids=array_values($ids);'."\r\n";
		$tpl.='$model=xg_model("content")->name($tbname)->where("status",1)->where("id","in",$ids)->limit(0,12);'."\r\n";
		$tpl.='$model->fields('.($p['fields']?'"'.$p['fields'].'"':'$def_fields').');'."\r\n";
		if($p['cid'])$tpl.='$model->where("cid",xg_child_ids('.$p['cid'].'));'."\r\n";
		if($p['where'])$tpl.='$model->where("'.$p['where'].'");'."\r\n";
		$tpl.='$model->order("'.($p['order']?$p['order']:'top desc,id desc').'");'."\r\n";
		if($p['limit'])$tpl.='$model->limit("'.$p['limit'].'");'."\r\n";
		if($p['with'])$tpl.='$model->with("'.$p['with'].'");'."\r\n";
		if($p['count'])$tpl.='$model->limit("0,'.$p['count'].'");'."\r\n";
		$tpl.='$'.$var.'=$model->contents();'."\r\n";
		if($rt['r'])$tpl.='?>'.$rt['r'].'<?php ';
		$tpl.='}';
		$tpl.=' ?>';
		return $tpl;
	},$tpl);
	return $tpl;
});
xg('hooks.view.parse-php.xg-recom-parent-contents',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$tpl=preg_replace_callback('/\{recom\-parent\-contents?[\s]*(?<p>.*?)\}(?<r>.*?)\{\/recom\-parent\-contents?\}/is',function($rt)use(&$fun){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt['p'],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		if(!$p['cid'])return;
		$var=($p['var']?preg_replace('/[^a-zA-Z0-9_]+/','',$p['var']):'data');
		$tpl='<?php ';
		$tpl.='$pid=xg_category('.$p['cid'].',"pid");$tbname=xg_category('.$p['cid'].',"model");'."\r\n";
		$tpl.='$cids=[];'."\r\n";
		$tpl.='$cids=xg_category($pid,"children");'."\r\n";
		$tpl.='if($cids){ ';
		$tpl.='$cids=array_values($cids);'."\r\n";
		$tpl.='$def_fields=xg_models($tbname,"catef");'."\r\n";
		$tpl.='$model=xg_model("content")->name($tbname)->where("status",1)->where("cid","in",xg_child_ids($cids))->limit(0,12);'."\r\n";
		$tpl.='$model->fields('.($p['fields']?'"'.$p['fields'].'"':'$def_fields').');'."\r\n";
		if($p['where'])$tpl.='$model->where("'.$p['where'].'");'."\r\n";
		$tpl.='$model->order("'.($p['order']?$p['order']:'top desc,id desc').'");'."\r\n";
		if($p['recom'])$tpl.='if($recom)$model->where("id","in",$recom);'."\r\n";
		if($p['limit'])$tpl.='$model->limit("'.$p['limit'].'");'."\r\n";
		if($p['with'])$tpl.='$model->with("'.$p['with'].'");'."\r\n";
		if($p['count'])$tpl.='$model->limit("0,'.$p['count'].'");'."\r\n";
		$tpl.='$'.$var.'=$model->contents();'."\r\n";
		if($rt['r'])$tpl.='?>'.$rt['r'].'<?php ';
		$tpl.='}';
		$tpl.=' ?>';
		return $tpl;
	},$tpl);
	return $tpl;
});
xg('hooks.view.parse-php.xg-recom-self-contents',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$tpl=preg_replace_callback('/\{recom\-self\-contents?[\s]*(?<p>.*?)\}(?<r>.*?)\{\/recom\-self\-contents?\}/is',function($rt)use(&$fun){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt['p'],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		if(!$p['cid'])return;
		$var=($p['var']?preg_replace('/[^a-zA-Z0-9_]+/','',$p['var']):'data');
		$tpl='<?php ';
		$tpl.='$tbname=xg_category('.$p['cid'].',"model");'."\r\n";
		$tpl.='$def_fields=xg_models($tbname,"catef");'."\r\n";
		$tpl.='$model=xg_model("content")->name($tbname)->where("status",1)->where("cid","=",'.$p['cid'].')->limit(0,12);'."\r\n";
		$tpl.='$model->fields('.($p['fields']?'"'.$p['fields'].'"':'$def_fields').');'."\r\n";
		if($p['where'])$tpl.='$model->where("'.$p['where'].'");'."\r\n";
		$tpl.='$model->order("'.($p['order']?$p['order']:'top desc,id desc').'");'."\r\n";
		if($p['recom'])$tpl.='if($recom)$model->where("id","in",$recom);'."\r\n";
		if($p['limit'])$tpl.='$model->limit("'.$p['limit'].'");'."\r\n";
		if($p['with'])$tpl.='$model->with("'.$p['with'].'");'."\r\n";
		if($p['count'])$tpl.='$model->limit("0,'.$p['count'].'");'."\r\n";
		$tpl.='$'.$var.'=$model->contents();'."\r\n";
		if($rt['r'])$tpl.='?>'.$rt['r'].'<?php ';
		$tpl.=' ?>';
		return $tpl;
	},$tpl);
	return $tpl;
});
xg('hooks.view.parse-php.xg-contents',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$tpl=preg_replace_callback('/\{contents[\s]*(?<p>.*?)\}(?<r>.*?)\{\/contents\}/is',function($rt){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt['p'],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		if(!$p['cid'] and !$p['tbname'] and !$p['model'])return;
		$var=($p['var']?preg_replace('/[^a-zA-Z0-9_]+/','',$p['var']):'data');
		$tpl='<?php $'.$var.'=[];$ids=[];';
		if($p['model']){
			$tpl.='$tbname="'.$p['model'].'";'."\r\n";
		}elseif($p['tbname']){
			$tpl.='$tbname="'.$p['tbname'].'";'."\r\n";
		}else{
			$tpl.='$tbname=xg_category('.$p['cid'].',"model");'."\r\n";
		}
		$tpl.='$def_fields=xg_models($tbname,"catef");'."\r\n";
		$tpl.='$page='.($p['page']?$p['page']:1).';'."\r\n";
		$tpl.='$pagesize='.($p['pagesize']?$p['pagesize']:($p['count']?$p['count']:24)).';'."\r\n";
		$tpl.='$model=xg_model("content")->name($tbname)->where("status",1);'."\r\n";
		$tpl.='$model->fields('.($p['fields']?'"'.$p['fields'].'"':'$def_fields').');'."\r\n";
		if($p['where'])$tpl.='$model->where("'.$p['where'].'");'."\r\n";
		if($p['cid'])$tpl.='$model->where("cid",xg_child_ids('.$p['cid'].'));'."\r\n";
		if($p['recom']){
			$tpl.='$recom=xg_model("recom")->where("status",1)->where("model",$tbname)->where("recom","'.$p['recom'].'")->column("infoid");'."\r\n";
		}
		if($p['recom'])$tpl.='if($recom)$model->where("id","in",$recom);'."\r\n";
		if($p['limit'])$tpl.='$model->limit("'.$p['limit'].'");'."\r\n";
		$tpl.='$model->order("'.($p['order']?$p['order']:'top desc,id desc').'");'."\r\n";
		$tpl.='$model->page($page,$pagesize);'."\r\n";
		if($p['with'])$tpl.='$model->with("'.$p['with'].'");'."\r\n";
		$tpl.='$'.$var.'=$model->contents();'."\r\n";
		$tpl.='$model=xg_model("content")->name($tbname)->where("status",1);'."\r\n";
		if($p['where'])$tpl.='$model->where("'.$p['where'].'");'."\r\n";
		if($p['cid'])$tpl.='$model->where("cid",xg_child_ids('.$p['cid'].'));'."\r\n";
		if($p['recom'])$tpl.='if($recom)$model->where("id","in",$recom);'."\r\n";
		$tpl.='$count=$model->count();'."\r\n";
		if($rt['r'])$tpl.='?>'.$rt['r'].'<?php ';
		$tpl.=' ?>';
		return $tpl;
	},$tpl);
	return $tpl;
});
xg('hooks.view.parse-php.xg-content',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$tpl=preg_replace_callback('/\{content[\s]*(?<p>.*?)\}(?<r>.*?)\{\/content\}/is',function($rt){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt['p'],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		if(!$p['id'] or (!$p['cid'] and !$p['tbname'] and !$p['model']))return;
		$var=($p['var']?preg_replace('/[^a-zA-Z0-9_]+/','',$p['var']):'info');
		$tpl='<?php ';
		if($p['model']){
			$tpl.='$tbname="'.$p['model'].'";'."\r\n";
		}elseif($p['tbname']){
			$tpl.='$tbname="'.$p['tbname'].'";'."\r\n";
		}else{
			$tpl.='$tbname=xg_category('.$p['cid'].',"model");'."\r\n";
		}
		$tpl.='$def_fields=xg_models($tbname,"contf");'."\r\n";
		$tpl.='$model=xg_model("content")->name($tbname)->where('.intval($p['id']).');'."\r\n";
		$tpl.='$'.$var.'=$model->find();'."\r\n";
		if($rt['r'])$tpl.='?>'.$rt['r'].'<?php ';
		$tpl.=' ?>';
		return $tpl;
	},$tpl);
	return $tpl;
});
xg('hooks.view.parse-php.xg-page',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$tpl=preg_replace_callback('/\{page[\s]*(?<p>.*?)[\/]?\}/is',function($rt)use(&$fun){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt['p'],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		if($p['temp']){
			if(strpos($p['temp'],'$')!==0)$p['temp']="'$p[temp]'";
		}
		$tpl='<?php echo xg_pagehtml('.($p['count']?$p['count']:'$count').','.($p['pagesize']?$p['pagesize']:'$pagesize').','.($p['page']?$p['page']:'$page').','.($p['temp']?$p['temp']:'null').');?>';
		return $tpl;
	},$tpl);
	return $tpl;
});




xg('hooks.view.parse-php.xg-nav',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$arr=[];
	while(preg_match('/(?:\{nav([^\{\}]*)\})(.+)(?:(\{\/nav\})+)/isx',$tpl,$rt)){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt[1],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		$str=str_replace($rt[0],'<?php $navfun=function($data){if(is_numeric($data)){ $items=xg_nav_tree($data,1);}else{ $items=$data;}foreach($items as $key=>$item){?>'.$rt[2].'<?php }};$navfun('.($p["pid"]?$p["pid"]:0).');?>',$rt[0]);
		$tpl=str_replace($rt[0],$str,$tpl);
	};
	return $tpl;
});
xg('hooks.view.parse-php.xg-cate',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$arr=[];
	while(preg_match('/(?:\{cate([^\{\}]*)\})(.*?)(?:(\{\/cate\}))/isx',$tpl,$rt)){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt[1],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		$str=str_replace($rt[0],'<?php $catefun=function($data){if(is_numeric($data)){ $items=xg_cate_tree($data);}else{ $items=$data;}?>'.$rt[2].'<?php };$catefun('.($p["pid"]?$p["pid"]:0).');?>',$rt[0]);
		$tpl=str_replace($rt[0],$str,$tpl);
	};
	return $tpl;
});
xg('hooks.view.parse-php.xg-topics',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$tpl=preg_replace_callback('/\{topics[\s]*(?<p>.*?)\}(?<r>.*?)\{\/topics\}/is',function($rt){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt['p'],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		$var=($p['var']?preg_replace('/[^a-zA-Z0-9_]+/','',$p['var']):'data');
		$tpl='<?php ';
		$tpl.='$model=xg_model("topic")->where("'.$p['where'].'");'."\r\n";
		if($p['fields'] or $p['field'])$tpl.='$model->fields("'.($p['fields']?$p['fields']:$p['field']).'");'."\r\n";
		if($p['limit'] or $p['count'])$tpl.='$model->limit("'.($p['limit']?$p['limit']:$p['count']).'");'."\r\n";
		$tpl.='$'.$var.'=$model->select();'."\r\n";
		if($rt['r'])$tpl.='?>'.$rt['r'].'<?php ';
		$tpl.=' ?>';
		return $tpl;
	},$tpl);
	return $tpl;
});
xg('hooks.view.parse-php.xg-select-table',function($tpl,$all,$prev){
	if($prev)$tpl=$prev;
	$tpl=preg_replace_callback('/\{select\-table[\s]*(?<p>.*?)\}(?<r>.*?)\{\/select\-table\}/xis',function($rt){
		if(preg_match_all('/([a-z]+)=[\"\']?([^\"]+)[\"\']?/',$rt['p'],$rt2)){
			$p=[];
			for($i=0;$i<count($rt2[1]);$i++){
				$p[$rt2[1][$i]]=$rt2[2][$i];
			}
		}
		if(!$p['tbname'] and !$p['table'])return;
		$var=($p['var']?preg_replace('/[^a-zA-Z0-9_]+/','',$p['var']):'data');
		$tpl='<?php ';
		if($p['table']){
			$tpl.='$tbname="'.$p['table'].'";'."\r\n";
		}elseif($p['tbname']){
			$tpl.='$tbname="'.$p['tbname'].'";'."\r\n";
		}
		$tpl.='$model=xg_model($tbname)->where("'.$p['where'].'");'."\r\n";
		if($p['fields'] or $p['field'])$tpl.='$model->fields("'.($p['fields']?$p['fields']:$p['field']).'");'."\r\n";
		if($p['limit'] or $p['count'])$tpl.='$model->limit("'.($p['limit']?$p['limit']:$p['count']).'");'."\r\n";
		if($p['json'])$tpl.='$model->json("'.$p['json'].'");'."\r\n";
		if($p['time'])$tpl.='$model->time("'.$p['time'].'");'."\r\n";
		$tpl.='$'.$var.'=$model->select();'."\r\n";
		if($rt['r'])$tpl.='?>'.$rt['r'].'<?php ';
		$tpl.=' ?>';
		return $tpl;
	},$tpl);
	return $tpl;
});
?>