(function(win, doc, $) {
let loc = win['location'];
let data = {};
win.thid=win.thid||xg.parseurl().params.thid;

win.systems=win.systems||{};
win.systems.xg={cidname:'cid',idname:'id',name:'xg',idtitle:'系统内容',cidtitle:'系统分类',title:'系统'};

$(document).ready(function(){
	win.select_link_sets={
		cateids:{
			select:{
				'系统分类':xg.url('link/categories')
			},
			btns:[
				{class:'xg-icon xg-icon-trash xg-fr',fun:'call_admin_link_title_remove'}
			]
		},
		classid:{
			select:{
				'系统分类':xg.url('link/category')
			},
			btns:[
				{class:'xg-icon xg-icon-trash xg-fr',fun:'call_admin_link_title_remove'}
			]
		},
		toplink:{
			select:{
				'系统分类':xg.url('link/category')
			},
			btns:[
				{class:'xg-icon xg-icon-trash xg-fr',fun:'call_admin_link_title_remove'}
			]
		},
		cateid:{
			select:{
				'系统分类':xg.url('link/category')
			},
			btns:[
				{class:'xg-icon xg-icon-trash xg-fr',fun:'call_admin_link_title_remove'}
			]
		},
		nav:{
			select:{
				'系统分类':xg.url('link/categories'),
				'系统内容':xg.url('link/contents')
			},
			btns:[
				'move',
				{class:'xg-icon xg-icon-trash xg-fr',fun:'call_admin_link_title_remove'},
				{class:'xg-icon xg-icon-backcolor xg-fr',fun:'call_admin_link_color_edit'},
				{class:'xg-icon xg-icon-edit-sign xg-fr',fun:'call_admin_link_title_edit'},
				{class:'xg-icon xg-fr',fun:'call_admin_link_slide_img'}
			]
		},
		slide:{
			select:{
				'系统分类':xg.url('link/categories'),
				'系统内容':xg.url('link/contents')
			},
			btns:[
				'move',
				{class:'xg-icon xg-icon-picture xg-fr',fun:'call_admin_link_slide_img'},
				{class:'xg-icon xg-icon-trash xg-fr',fun:'call_admin_link_title_remove'},
				{class:'xg-icon xg-icon-backcolor xg-fr',fun:'call_admin_link_color_edit'},
				{class:'xg-icon xg-icon-edit-sign xg-fr',fun:'call_admin_link_title_edit'},
				{class:'xg-icon xg-fr',fun:'call_admin_link_slide_img'}
			]
		},
		'img-nav':{
			select:{},
			btns:[
				'move',
				{class:'xg-icon xg-icon-picture xg-fr',fun:'call_admin_link_slide_img'},
				{class:'xg-icon xg-icon-trash xg-fr',fun:'call_admin_link_title_remove'},
				{class:'xg-icon xg-icon-backcolor xg-fr',fun:'call_admin_link_color_edit'},
				{class:'xg-icon xg-icon-edit-sign xg-fr',fun:'call_admin_link_title_edit'},
				{class:'xg-icon xg-fr',fun:'call_admin_link_slide_img'}
			]
		}
	};
	win.input_link_sets={
		cids:{
			'系统分类':xg.url('link/categories',{thid:thid})
		},
		tids:{
			'系统分类':xg.url('link/topics',{thid:thid})
		},
		menu_link:{
			'系统分类':xg.url('link/category',{thid:thid,type:'page'}),
			'系统内容':xg.url('link/content'),
			'选择页面':xg.url('link/page',{thid:thid})
		}
	};
});



win['call_admin_save_config']=function(){
	var id=xg.iframe({src:xg.url('addons/save_config'),width:500,height:350,ok:1,onok:function(){
		var data=xg.iframe(id).$('.xg-form').serialize();
		location=xg.url('addons/save_config')+'?'+data;
	}});
}
win['call_admin_init_load_config']=function(){
xg.mod('upload',function(){
	var submit_count=0,submit_win;
	xg.upload({callback:function(cont){
		var data=JSON.parse(cont[0]||'');
		if(data){
			if(data['xg-saved-addon-config']){
				if(win.addon_name){
					if(data['xg-saved-addon-config']['data']&&data['xg-saved-addon-config']['data'][win.addon_name]){
						submit(data['xg-saved-addon-config']['data'][win.addon_name],win.addon_name,data['xg-saved-addon-config']['names'][win.addon_name]);
					}else{
						xg.msg('没有此插件的设置信息',3);
					}
				}else{
					var data2=data['xg-saved-addon-config']['data'];
					var names=data['xg-saved-addon-config']['names'];
					var html='';
					html+='<div class="xg-pd-3 xg-form xg-left">';
					html+='<div class="xg-form-item xg-t-m-2">';
					for(let name in names){
						html+='<label class="xg-label-checkbox">';
						html+='<input type="checkbox" class="load-config-name" value="'+name+'" />'+names[name];
						html+='</label>';
					}
					html+='</div>';
					html+='</div>';
					submit_win=xg.win({cont:html,title:'选择需要导入设置的插件',width:500,height:350,ok:1,onok:function(){
						submit_count=$('.load-config-name:checked').length;
						if(submit_count==0)return;
						$('.load-config-name:checked').each(function(){
							var name=$(this).val();
							submit(data2[name],name,names[name]);
						});
						return false;
					}});
				}
			}else{
				xg.msg('此文件没有插件设置信息',3);
			}
		}else{
			xg.msg('文件内容为空',3);
		}
	}}).bind('.load-config');
	function submit(config,name,title){
		xg.ajax.post(xg.url('addons/config',{name:name}),config,function(data){
			submit_count--;
			if(data.ok===true){
				if(win.addon_name){
					xg.ok(title+'<br>插件设置导入成功',function(){
						location.reload();
					});
				}
			}else if(data.msg){
				xg.msg(data.msg,3);
			}
			if(!win.addon_name&&submit_count<=0){
				xg.ok('插件设置导入成功');
				xg.close(submit_win);
			}
		},'json');
	}
});
}










win['call_admin_page']=function(thid,pid) {
	let data={thid:thid};
	if(pid)data['pid']=pid;
	parent.page_win=parent.xg.iframe(xg.url('page/page',data),600,467,'页面信息',{onclose:function(){
		location.reload();
	}});
}
win['call_admin_icon']=function(input,target) {
	var id=xg.iframe(xg.url('icon/icon'),650,470,'选择图标');
	xg.iframe(id).target_win=target||win;
	xg.iframe(id).target_input=input;
	xg.iframe(id).icon_win_id=id;
}
win['call_admin_menu']=function(thid) {
	parent.menu_win=parent.xg.iframe({src:xg.url('page/menu',{thid:thid}),width:700,height:500,ok:1,cancel:1,title:'底部菜单',onok:function(){
	}});
}


win['call_admin_block_show_set'] = function(block, bid) {
	let sets = block_sel_list[block]['sets'];
	return win['call_admin_sets_html'](block, bid);
}
win['call_admin_link_select'] = function(selname,inputname) {
	selname+='';
	win.link_select={};
	win.link_select_index={};
	win.link_select_index[selname]=0;
	let iframe={};
	let select=(win.select_link_sets[selname]&&win.select_link_sets[selname].select)||{};
	win.link_select[selname]=[];
	for(let i in select){
		iframe[i]=xg.urladd(select[i],'selname',selname);
	}
	$('.link-select-item').each(function(){
		let data=$(this).data();
		console.log(data);
		let key=data['key'];
		data['order']=win.link_select_index[selname];
		xg.push(win.link_select[selname],data,key);
		win.link_select_index[selname]++;
	});
	xg.iframe({cont:iframe,width:800,height:500,ok:1,cancel:1,onok:function(){
		data=win.link_select[selname];
		win['call_admin_link_select_show'](data,selname,inputname);
		win.link_select=null;
	},oncancel:function(){
		win.link_select=null;
	},oncallback:function(){
		win.link_select=null;
	}});
}
win['link_select_data']={};
win['call_admin_link_select_show']=function(data,selname,inputname){
	if(!data)return;
	if(xg.isarr(data)){
		data.sort(function(a,b){return a.order-b.order;});
		win['link_select_data'][selname]=[];
		for(let i in data){
			let datai=data[i];
			datai['order']=i;
			win['link_select_data'][selname].push(datai);
		}
	}else{
		win['link_select_data'][selname]=data;
	}
	let names={}
	let select=(win.select_link_sets[selname]&&win.select_link_sets[selname].select)||{};
	for(let i in select){
		let v=select[i];
		names[v['name']]=v['name'];
	}
	var data=win['link_select_data'][selname];
	let html='<textarea name="'+inputname+'" class="xg-hide">'+JSON.stringify(data)+'</textarea>';
	var move=false;
	if(xg.isarr(data)){
		for(let i in data){
			let attr='';
			let str='';
			for(let j in data[i]){
				attr+=' data-'+j+'="'+data[i][j]+'"';
				str=xg.urladd(str,j,data[i][j]);
			}
			html+='<div class="xg-mt-2 xg-pd-2 xg-radius-3 xg-border-c link-select-item xg-drag-item"'+attr+' xg-drag-id="'+xg.sha1(str)+'">';
			let btns=(win.select_link_sets[selname]||{}).btns;
			if(btns){
				for(let j in btns){
					if(btns[j]=='move'){
						html+='<a class="xg-drag-btn xg-icon xg-icon-fullscreen xg-fr"></a>';
						move=true;
					}else{
						html+='<a href="javascript:'+btns[j].fun+'('+data[i]['order']+',\''+selname+'\',\''+inputname+'\')" class="'+btns[j].class+'"></a>';
					}
				}
			}
			for(let sysi in win.systems){
				if(data[i][win.systems[sysi].cidname]&&data[i][win.systems[sysi].idname]){
					html+='['+win.systems[sysi].idtitle+']'
				}else if(data[i][win.systems[sysi].cidname]){
					html+='['+win.systems[sysi].cidtitle+']'
				}
			}
			html+=data[i]['title'];
			html+='</div>';
		}
	}else if(xg.isobj(data)&&!xg.iseobj(data)){
		let attr='';
		html+='<div class="xg-mt-2 xg-pd-2 xg-radius-3 xg-border-c"'+attr+'>';
		let btns=(win.select_link_sets[selname]||{}).btns;
		if(btns){
			for(let j in btns){
				html+='<a href="javascript:'+btns[j].fun+'(0,\''+selname+'\',\''+inputname+'\')" class="'+btns[j].class+'"></a>';
			}
		}
		for(let sysi in win.systems){
			if(data[win.systems[sysi].cidname]&&data[win.systems[sysi].idname]){
				html+='['+win.systems[sysi].idtitle+']'
			}else if(data[win.systems[sysi].cidname]){
				html+='['+win.systems[sysi].cidtitle+']'
			}
		}
		html+=data['title'];
		html+='</div>';
	}else{
		html='';
	}
	$('.link-select-'+selname).html(html);
	if(move){
		let fromid;
		xg.drag('.link-select-'+selname,function(id){
			fromid=id;
		},function(id){
			let datas={};
			let data=win['link_select_data'][selname];
			let i1,i2;
			for(let i in data){
				let str='';
				for(let j in data[i]){
					str=xg.urladd(str,j,data[i][j]);
				}
				if(xg.sha1(str)==fromid)i1=i;
				if(xg.sha1(str)==id)i2=i;
			}
			data=xg.moveitem(data,i1,i2);
			for(let i in data){
				data[i].order=i;
			}
			win['call_admin_link_select_show'](data,selname,inputname);
		});
	}
}
win['call_admin_link_input'] = function(selname,input,target){
	win.link_select=null;
	iframe=$.extend({},win.input_link_sets[selname]);
	for(let name in iframe){
		iframe[name]=xg.urladd(iframe[name],'selname',selname);
	}
	win.link_input_value=$(input).val();
	var id=xg.iframe({cont:iframe,width:800,height:500,ok:1,cancel:1,onok:function(){
		$(input).val(win.link_input_value);
		return true;
	}});
	return;
	
	
	
	
	
	win.input_link={};
	win.input_link_index=0;
	var target=target||win;
	iframe=$.extend({},win.input_link_sets[selname]);
	for(let name in iframe){
		iframe[name]=xg.urladd(iframe[name],'selname',selname);
		iframe[name]=xg.urladd(iframe[name],'input',input);
		if(target.$(input).val()){
			iframe[name]=xg.urladd(iframe[name],'cids',target.$(input).val());
			iframe[name]=xg.urladd(iframe[name],'tids',target.$(input).val());
		}
	}
	var id=xg.iframe({cont:iframe,width:800,height:500,ok:1,cancel:1,onok:function(){
		var data=win.input_link[selname];
		var vals=[];
		if(xg.isarr(data)){
			for(let i in data){
				vals.push(data[i]);
			}
			var val=vals.join(',');
		}else{
			var val=data;
		}
		target.$(input).val(val);
	}});
	var ifr=xg.iframe(id);
	if(xg.iswin(ifr)){
		ifr.target_win=target;
	}else{
		for(var j in ifr){
			if(xg.iswin(ifr[j]))ifr[j].target_win=target;
		}
	}
}




win['call_admin_link_slide_img'] = function(order,selname,inputname){
	let img='';
	let data=win['link_select_data'][selname];
	for(let i in data){
		if(data[i]['order']==order){
			if(data[i]['pic'])img=data[i]['pic'];
			if(data[i]['img'])img=data[i]['img'];
		}
	}
	xg.iframe({src:xg.url('block/slide_img',{img:img}),width:600,height:400,ok:1,cancel:1,onok:function(){
		if(xg.isunde(win.slide_img))return;
		for(let i in data){
			if(data[i]['order']==order){
				data[i]['img']=win.slide_img;
				win['call_admin_link_select_show'](data,selname,inputname);
				xg.ok('修改成功');
				break;
			}
		}
		win.slide_img=undefined;
	}});
}

win['call_admin_link_title_edit'] = function(order,selname,inputname){
	let data=win['link_select_data'][selname];
	for(let i in data){
		if(data[i]['order']==order){
			xg.input('输入标题',function(t){
				data[i]['title']=t;
				win['call_admin_link_select_show'](data,selname,inputname);
			});
		}
	}
}

win['call_admin_link_color_edit'] = function(order,selname,inputname){
	let data=win['link_select_data'][selname];
	for(let i in data){
		if(data[i]['order']==order){
			xg.color('输入标题',function(t){
				data[i]['color']=t;
				win['call_admin_link_select_show'](data,selname,inputname);
			});
		}
	}
}

win['call_admin_link_title_remove'] = function(order,selname,inputname){
	let data=win['link_select_data'][selname];
	if(xg.isarr(data)){
		for(let i in data){
			if(data[i]['order']==order){
				data=xg.delete(data,data[i],'order');
				win['call_admin_link_select_show'](data,selname,inputname);
				return;
			}
		}
	}else if(xg.isobj(data)){
		win['call_admin_link_select_show']({},selname,inputname);
	}
}

win['call_admin_sets_html'] = function(block, bid, iscopy) {
	xg.ajax.get(xg.url('block/sets', {
		thid: thid,
		pagename: win.blockpagename||win.pagename,
		block: block,
		bid: bid
	}),function(data) {
		if (data.ok) {
			$('.block-info').html(data.html);
			form();
		} else if (data.msg) {
			xg.err(data.msg);
		}
	}, 'json');
	function form(){
		$('.block-dis-corr').click(function(){
			const that=this;
			const data={thid:thid};
			data.bid=$(this).data().bid;
			data.obid=$(this).data().obid;
			const url=xg.url('block/discorr',data);
			xg.ajax.get(url,{},function(data){
				if(data.ok){
					$(that).remove();
					xg.msg('取消成功');
				}else if(data.msg){
					xg.msg(data.msg);
				}
			},'json');
		});
		$('.sets-form').submit(function(e){
			var btn=xg.subbtn(e);
			var data={thid:thid};
			if(btn=='关联复制'){
				data['corr']=1;
			}else if(btn=='单独复制'){
				data['alone']=1;
			}
			var url=xg.url('block/data',data);
			xg.ajax.post(url,$(this).serialize(),function(data){
				if(data.ok===true){
					let bid=$('.sets-form input[name="bid"]').val();
					$('.sets-form input[name="bid"]').val(data.bid);
					reload_block(bid);
					//if($('#preview').length)$('#preview')[0].contentWindow.refresh();
				}else{
					xg.err(data.msg);
				}
			},'json');
			return false;
		});
		return false;
	}
}
})(window, document, jQuery);
