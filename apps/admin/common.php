<?php
require XG_APPS."/app/common.php";

xg('hooks.form.cids-before.xg',function($that){
	$name=$that->name;
	$name2=$that->name2();
	$that->btns['cids']='<button type="button" class="xg-btn cids-'.$name2.'">选择分类</button><script>$(".cids-'.$name2.'").click(function(){
		window.call_admin_link_input("cids",".xg-form-item-'.$name2.' input[name=\''.$name.'\']",window);
	});</script>';
	return $that->text();
});

xg('hooks.form.style4in1-before.xg',function($that){
	$name=$that->name;
	$name2=$that->name2();
	$val=$that->value;
	$t=$r=$b=$l=$a=0;
	if($val){
		$val=str_replace(['rem','em','px'],'',$val);
		$val=xg_arr($val,' ');
		if(count($val)==2){
			$t=$val[0];
			$r=$val[1];
			$b=$val[0];
			$l=$val[1];
		}elseif(count($val)==4){
			$t=$val[0];
			$r=$val[1];
			$b=$val[2];
			$l=$val[3];
		}elseif(count($val)==1){
			$t=$val[0];
			$r=$val[0];
			$b=$val[0];
			$l=$val[0];
		}
		if($t==$r and $t==$b and $t==$l)$a=$t;
	}
	$html='
	<input class="style4in1-input-'.$name2.'" type="hidden" name="'.$name.'" value="'.$value.'">
	<div class="xg-flex xg-flex-col">
		<div class="xg-flex xg-mt-2">
			<div class="xg-w-20 xg-mg-1 xg-ml-0">上边距</div>
			<div class="xg-w-20 xg-mg-1">右边距</div>
			<div class="xg-w-20 xg-mg-1">下边距</div>
			<div class="xg-w-20 xg-mg-1">左边距</div>
			<div class="xg-w-20 xg-mg-1 xg-mr-0">所有边距</div>
		</div>
		<div class="xg-flex style4in1-'.$name2.'">
			<input data-n="t" min="-30" max="30" type="number" step="0.05" class="xg-w-20 xg-mg-1 xg-ml-0 '.$name2.'-t" value="'.$t.'">
			<input data-n="r" min="-30" max="30" type="number" step="0.05" class="xg-w-20 xg-mg-1 '.$name2.'-r" value="'.$r.'">
			<input data-n="b" min="-30" max="30" type="number" step="0.05" class="xg-w-20 xg-mg-1 '.$name2.'-b" value="'.$b.'">
			<input data-n="l" min="-30" max="30" type="number" step="0.05" class="xg-w-20 xg-mg-1 '.$name2.'-l" value="'.$l.'">
			<input data-n="a" min="-30" max="30" type="number" step="0.05" class="xg-w-20 xg-mg-1 xg-mr-0 '.$name2.'-a" value="'.$a.'">
		</div>
	</div>
	<script>
	$(".style4in1-'.$name2.'").find("input").change(function(){
		const n=$(this).data("n");
		if(n=="a"){
			$(".style4in1-'.$name2.'").find("input").val($(this).val());
		}else{
			$(`.'.$name2.'-${n}`).val($(this).val());
		}
		const v=[];
		v.push($(".'.$name2.'-t").val()+"rem");
		v.push($(".'.$name2.'-r").val()+"rem");
		v.push($(".'.$name2.'-b").val()+"rem");
		v.push($(".'.$name2.'-l").val()+"rem");
		$(".style4in1-input-'.$name2.'").val(v.join(" "));
	});$(".'.$name2.'-t").change();
	</script>
	';
	return $that->wraphtml($html);
});

xg('hooks.form.tids-before.xg',function($that){
	$name=$that->name;
	$name2=$that->name2();
	$that->btns['tids']='<button type="button" class="xg-btn tids-'.$name2.'">选择专题</button><script>$(".tids-'.$name2.'").click(function(){
		if(window.input_link_sets&&window.input_link_sets.topic_links){
			var target=window;
		}else{
			var target=parent;
		}
		target.call_admin_link_input("topic_links",".xg-form-item-'.$name2.' input[name=\''.$name.'\']",window);
	});</script>';
	return $that->text();
});



function xg_adminlog($type='',$infoid=0,$infoname=''){
	$userid=xg_login('admin');
	$user_auth=xg_session('user_auth');
	$username=$user_auth['username'];
	xg_model('admin_log')->insert(
		array(
			'type'=>$type,
			'userid'=>$userid,
			'username'=>$username,
			'time'=>XG_TIME,
			'ip'=>xg_ip(),
			//模块名称 为了区分app和admin两个模块
			'module'=>XG_APP,
			'infoid'=>$infoid,
			'infoname'=>''.$infoname
		)
	);
}

function pagelink($item=[]){
	if($item['id']){
		return '[cont]?cid='.$item['cid'].'&id='.$item['id'];
	}else if($item['cid']){
		return '[cate]?cid='.$item['cid'];
	}
	return '';
}
?>