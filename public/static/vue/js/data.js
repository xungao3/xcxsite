const s={};

s.linkthis=function(d){
	const s=this;
	var data=s.hook('linkthis',d);
	if(data===true)return data;
	if(s.isstr(d)){
		if(d=='index'){
			if(s.linkdata(s.options)['pagename']=='index'){
				return true;
			}
		}else if(d.indexOf('?')===-1){
			return s.pagename==d;
		}else{
			d=s.query2obj(d.substr(d.indexOf('?')+1));
		}
	}
	if(d){
		if(d[s.idname]){
			if(s.id==d[s.idname]&&s.cid==d[s.cidname]){
				return true;
			}
		}else if(d[s.cidname]&&!s[s.idname]){
			if(s.cid==d[s.cidname]&&s.page==d.page){
				return true;
			}else if(s.cid==d[s.cidname]){
				return true;
			}
		}
	}
	return false;
}
s.blockemit=function(){
	const s=this;
	if(!s.block||!s.block.emit)return;
	const arr=s.block.emit.split(';');
	for(let i of arr){
		let block;
		if(i.includes('@')){
			block=i.split('@')[0];
			i=i.split('@')[1];
		}
		let arri=i.split('=');
		let val=arri[1];
		val=s.blockinfo(val);
		const data={block,key:arri[0],val};
		s.o('xg-emit',data);
	}
}
s.urldata=function(d){
	const s=this;
	return s.linkdata(d);
}
s.link=function(e,param){
	const s=this;
	if(e){
		if(e.currentTarget&&e.currentTarget.dataset){
			var d=e.currentTarget.dataset.link;
		}else if(s.isobj(e)){
			if(e.link){
				var d=e.link;
			}else{
				var d=e;
			}
		}else{
			var d=e;
		}
		var json;
		try{
			json=JSON.parse(d);
		}catch(e){}
		if(json){
			d=json;
		}
		var link=s.hook('link',d);
		if(link===false)return;
		if(link){
			uni.navigateTo({url:link,fail:function(){uni.redirectTo({url:link});}});
			return;
		}
		if(s.isstr(d)){
			if(d.indexOf('[cate]')===0){
				for(let sys in s.cidnames){
					let rt=new RegExp(`[\\?\\&]${s.cidnames[sys]}\\=([\\d]+)`).exec(d);
					if(rt&&rt[1]){
						let data={};
						data[s.cidnames[sys]]=rt[1];
						var d=s.linkdata(data);
					}
				}
			}else if(d.indexOf('[cont]')===0){
				for(let sys in s.cidnames){
					let rt=new RegExp(`[\\?\\&]${s.cidnames[sys]}\\=([\\d]+)\\&${s.idnames[sys]}\\=([\\d]+)`).exec(d);
					if(rt&&rt[1]&&rt[2]){
						let data={};
						data[s.cidnames[sys]]=rt[1];
						data[s.idnames[sys]]=rt[2];
						var d=s.linkdata(data);
					}
				}
			}else if(d=='index'){
				var d={pagename:'index'};
			}else{
				if(s.cont){
					d=s.blockinfo(d,s.cont);
				}
				//d=decodeURIComponent(d);
				if(d.indexOf('/pages/')===0){
					var link=d;
				}else{
					var link='/pages/page?pagename='+d.replace('?','&')+'&'+s.obj2query(param);
				}
			}
		}
		if(!link&&s.isobj(d)){
			if(s.isobj(param))d=Object.assign({},d,param);
			if(d.pagename=='index'){
				var link='/pages/index';
			}else{
				var link='/pages/page?'+s.obj2query(d);
			}
		}
		if(link)uni.navigateTo({url:link,fail:function(){uni.redirectTo({url:link});}});
	}
}

s.linkdata=function(d,json){
	const s=this;
	var data=s.hook('linkdata',d);
	if(s.isobj(data)&&!s.iseobj(data)){
		if(s.inxg()&&json)data=JSON.stringify(data);
		return data;
	}
	var data={};
	var pagename;
	let links=s.contlinks;
	for(let page in links){
		let linki=links[page];
		for(let sys in linki){
			let cidname=s.cidnames[sys];
			let idname=s.idnames[sys];
			let linkj=linki[sys];
			if(!d[idname])continue;
			if(s.isstr(linkj)&&linkj.split(',').indexOf(d[cidname]+'')>-1){
				pagename=page;
				data={pagename:pagename};
				data[cidname]=d[cidname];
				data[idname]=d[idname];
				break;
			}
		}
		if(pagename)break;
	}
	if(!pagename){
		let links=s.catelinks;
		for(let page in links){
			let linki=links[page];
			for(let sys in linki){
				let cidname=s.cidnames[sys];
				let idname=s.idnames[sys];
				let linkj=linki[sys];
				if(!d[cidname])continue;
				if(s.isstr(linkj)&&linkj.split(',').indexOf(d[cidname]+'')>-1){
					pagename=page;
					data={pagename:pagename};
					data[cidname]=d[cidname];
					break;
				}
			}
			if(pagename)break;
		}
	}
	if(!pagename){
		let links=s.topiclinks;
		for(let page in links){
			let linki=links[page];
			for(let sys in linki){
				let linkj=linki[sys];
				if(!d.tid)continue;
				if(s.isstr(linkj)&&linkj.split(',').indexOf(d.tid+'')>-1){
					pagename=page;
					data={pagename:pagename};
					data.tid=d.tid;
					break;
				}
			}
			if(pagename)break;
		}
	}
	if(!pagename&&d.pagename){
		for(let sys in s.systems){
			let cidname=s.cidnames[sys];
			let idname=s.idnames[sys];
			if(d[cidname]||d[idname]){
				pagename=d.pagename;
				data={pagename:pagename};
				if(d[cidname])data[cidname]=d[cidname];
				if(d[idname])data[idname]=d[idname];
				break;
			}
		}
	}
	if(s.iseobj(data)){
		let pagename=d.pagename||'index';
		data={pagename};
	}
	if(s.inxg()&&json)data=JSON.stringify(data);
	return data;
}
s.rem=function(){
	const s=this;
	// #ifdef H5
	try{uni.onWindowdataize((data)=>{rem(parseFloat(data.size.windowWidth));});}catch(e){}
	// #endif
	uni.getSystemInfo({
		success(data) {
			rem(data.screenWidth);
		}
	});
	function rem(size){
		if(size>480)size=480;
		s.fontsize=size/22.5;
	}
}
s.kefu=function(){
	
}
s.slogout=function(){
	const s=this;
	s.confirm('确定退出登录吗？',function(){
		s.request({
			url:s.url('user/logout'),
			success:function(data){
				if(data.ok){
					uni.redirectTo({url:'/pages/index'});
				}else if(data.msg){
					s.msg(data.msg);
				}
			}
		});
	});
}

s.history=function(cid,id,title){
	const s=this;
	var history=s.storage('browse-history');
	if(!s.isarr(history))history=[];
	if(cid){
		var data={cid,id,title};
		data.time=new Date().getTime();
		if(cid==1){
			data.tbname='case';
			data.cate='案例';
		}
		if(cid==2){
			data.tbname='designer';
			data.cate='设计师';
		}
		history.push(data);
		if(history.length>100)history.shift();
		s.storage('browse-history',history);
	}
	return history;
}

s.favorite=function(cid,id,title,iscase){
	const s=this;
	var favorite=s.storage('user-favorite');
	if(!s.isarr(favorite))favorite=[];
	if(cid){
		for(let i in favorite){
			if(favorite[i].id==id&&favorite[i].cid==cid){
				favorite.splice(i,1);
				s.storage('user-favorite',favorite);
				s.msg('成功取消收藏',function(){
					if(!iscase)uni.redirectTo({url:'/user/favorite?ecid='+cid})
				});
				return favorite;
			}
		}
		var data={cid,id,title};
		data.time=new Date().getTime();
		if(cid==1){
			data.tbname='case';
			data.cate='案例';
		}
		if(cid==2){
			data.tbname='designer';
			data.cate='设计师';
		}
		favorite.push(data);
		s.msg('收藏成功');
		if(favorite.length>20)favorite.shift();
		s.storage('user-favorite',favorite);
	}
	return favorite;
}
s.blockcond=function(cond){
	const s=this;
	if(!cond)return;
	var logicalOperators = ["&", "|"];
	var tokens = cond.replace(/[\&]+/g,'&').replace(/[\|]+/g,'|').split(/([&|])/);
	var stack = [];
	for (let i = 0; i < tokens.length; i++) {
		let token = tokens[i];
		if (logicalOperators.includes(token)) {
			stack.push(token);
		} else {
			if(token=='true'||token=='1'){
				stack.push(1);
			}else if(token=='false'||token=='0'){
				stack.push(0);
			}else{
				let boolfalse;
				if(token.indexOf('!')===0){
					token=token.substr(1);
					boolfalse=true;
				}
				const keys=token.split('.');
				let value=s;
				const vname=keys[0];
				if(vname=='on'&&s.on){
					value=s.on;
					keys.shift();
				}else if(vname=='cont'&&s.cont){
					value=s.cont;
					keys.shift();
				}else if(vname=='conts'&&s.conts){
					value=s.conts;
					keys.shift();
				}else if(vname=='block'&&s.block){
					value=s.block;
					keys.shift();
				}
				for(let key of keys){
					if(value)value=value[key];
				}
				if(s.isarr(value))value=value.length;
				if(s.isobj(value))value=!s.iseobj(value);
				stack.push(boolfalse?!value:value);
			}
		}
		if (stack.length === 3) {
			var op2 = stack.pop();
			var operator = stack.pop();
			var op1 = stack.pop();
			var operationResult = performOperation(op1, operator, op2);
			stack.push(operationResult);
		}
	}
	if (stack.length !== 1) {
		return false;//throw new Error("无效的条件表达式: " + condition);
	}
	return stack[0];
	function performOperation(op1, operator, op2) {
		switch (operator) {
			case "&":
				return op1 && op2;
			case "|":
				return op1 || op2;
			default:
				return false;//throw new Error("无效的运算符: " + operator);
		}
	}
}
s.checklogin=function(prompt){
	const s=this;
	// s.o('show-wx-login',function(){
	// 	s.showwxlogin=true;
	// });
	s.o('userinfo',function(user){
		s.user=user;
	});
	s.logged().then((user)=>{
		if(user){
			s.o('userinfo',user);
		}else{
			if(!s.inxg()||(typeof parent==='object'&&parent.viewmode)){
				if(prompt)s.msg('请先登录',function(){
					let backurl='/pages/page?'+s.obj2query(s.linkdata(s.options));
					//backurl=encodeURIComponent(backurl);
					if(s.login_page){
						s.link(s.login_page,{backurl});
					}else{
						let page=s.findobj(s.pages,'type','login');
						if(page)s.link(page.name,{backurl});
					}
				});
			}
			// // #ifdef MP-WEIXIN
			// s.o('show-wx-login');
			// // #endif
			// // #ifdef MP-BAIDU
			
			// // #endif
		}
	});
}
s.logged=function(){
	const s=this;
	return new Promise(function(a,b){
		s.request({
			url:s.url('user/checklogin',{sys:s.sys}),
			success:function(data){
				if(data.ok){
					a(data.user);
				}else{
					a(false);
				}
			},fail:function(){
				b(false);
			}
		});
	});
}



import htmlstyle from './html.js';
s.rehtml=function(html){
	const s=this;
	if(!html)return '';
	const imgurl=s.getinfo.imgurl||s.getinfo.url||s.xgimgurl||s.c.xgimgurl||s.xgurl;
	html=html.replace(/　/g,'');
	html=html.replace(/src=([\"\'])?(\/upload\/)/g,"src=$1"+imgurl+"$2");
	html=html.replace(/src=([\"\'])?(\/d\/)/g,"src=$1"+imgurl+"$2");
	const styles=[];
	if(s.block&&s.block.indent)styles.push(`text-indent:${s.block.indent};`);
	if(s.block&&s.block.para_dis)styles.push(`margin-bottom:${s.block.para_dis};`);
	const pstyle=styles.join('');
	const imgstyle=`max-width:100%;max-height:none;width:auto;height:auto;`;
	html=htmlstyle(html,pstyle,imgstyle);
	return html ;
};



s.bottominfo=function(list){
	const s=this;
	if(s.isarr(list)&&s.block){
		for(let i in list){
			list[i].left=s.blockinfo(s.block.info_left||s.block.bottom_left,list[i]);
			list[i].right=s.blockinfo(s.block.info_right||s.block.bottom_right,list[i]);
		}
	}
	return list;
}

s.blockinfo=function(text,info){
	const s=this;
	if(text&&s.isstr(text)){
		text=text.replace(new RegExp('(\\\[[a-z0-9A-Z\.\_]+\\\])', 'gm'),function(match,a,b) {
			var tag=match.replace('[','').replace(']','');
			if(tag.substr(0,5)=='time.')return s.format(info['timestamp']||info['news_time']||info['newstime'],tag.substr(5).split(''));
			if(tag.includes('.')){
				let taga=tag.split('.');
				let info2=info;
				if(!info2||taga[0]=='block'||taga[0]=='on'||taga[0]=='conts'||taga[0]=='cont'||taga[0]=='options'){
					info2=s;
				}
				for(let tagi of taga){
					if(info2&&info2[tagi]){
						info2=info2[tagi];
					}else{
						info2='';
					}
				}
				return info2;
			}
			if(tag=='date')return s.format(info['timestamp']||info['news_time']||info['newstime'],0);
			if(tag=='time')return s.format(info['timestamp']||info['news_time']||info['newstime'],1);
			if(tag=='datetime')return s.format(info['timestamp']||info['news_time']||info['newstime'],2);
			return (info[tag]?info[tag]:'');
		});
		text=text.replace('&amp;','&').replace('&copy;','©');
		if(text&&s.extrfuncs(text).length){
			for(let extr of s.extrfuncs(text)){
				if(s.isfun(s[extr.func])){
					const args=extr.args;
					const exec=s[extr.func](...args);
					text=text.replace(extr.str,exec);
				}
			}
		}
	}
	return text;
};
//提取函数
s.extrfuncs=function(input){
	const regex = /(\w+)\(([^)]*)\)/g;
	const matches = [];
	let match;
	while ((match = regex.exec(input)) !== null) {
		const str=match[0];
		const func = match[1];
		const argsString = match[2];
		const args = argsString.split(',').map(arg => arg.trim());
		matches.push({str,func,args});
	}
	return matches;
}

s.addtocart=function(cid,id){
	const s=this;
	s.request({url:s.url('app/index/cart',{sys:s.sys,cid,id}),success:function(res){
		if(res.ok===true){
			s.o('add-to-cart-ok',{cid,id});
		}
		if(res.msg)s.msg(res.msg);
	},dataType:'json'});
}

s.cont=function(cont){
	const s=this;
	var cid=s.cid;
	var id=s.id;
	if(cid&&id){
		if(!id)return {};
		if(cont){
			s.g.contents=s.g.contents||{};
			s.g.contents[cid]=s.g.contents[cid]||{};
			s.g.contents[cid][id]=cont;
		}else{
			try{
				cont=s.g.contents[cid][id];
			}catch(e){
				
			}
		}
		return cont;
	}
}
s.conts=function(conts){
	const s=this;
	var cid=s.cid;
	var page=s.page;
	if(cid){
		if(!cid)return [];
		if(conts){
			s.g.list=s.g.list||{};
			s.g.list[cid]=s.g.list[cid]||{};
			s.g.list[cid][page]=conts;
		}else{
			try{
				conts=s.g.list[cid][page];
			}catch(e){
				
			}
		}
		return conts;
	}
}
s.blocksubmit=function(){
	const s=this;
	const blocks=s.g.datas.blocks;
	const forms=s.findobjs(blocks,'block','form');
	let form;
	for(form of forms){
		if(s.findobj(form,'bid',s.block.bid)){
			break;
		}else{
			form=null;
		}
	}
	if(form){
		const inputs=s.findobjs(form,'block','input');
		for(let input of inputs){
			s.o('xg-form-value-'+input.bid,s);
		}
	}else{
		s.o('xg-form-value',s);
	}
	let url=(form&&(form.submit_url||form.data_url))||s.block.submit_url||s.block.data_url;
	let link=(form&&form.success_link)||s.block.success_link;
	if(url)url=s.url(s.blockinfo(url),{sys:s.sys});
	if(link)link=s.blockinfo(link);
	if(!url)url=s.url('app/index/submit',{sys:s.sys,cid:s.cid});
	s.request({url,data:s.values,success:function(res){
		if(res.msg){
			s.msg(res.msg,function(){
				if(res.ok===true&&link)s.link(link);
			});
		}else{
			if(res.ok===true&&link)s.link(link);
		}
	},method:'POST'});
}
s.navcolor=function(o){
	const s=this;
	//if(!o)o=s.storage('xg-nav-color');
	if(o){
		s.storage('xg-nav-color',o);
		uni.setNavigationBarColor(o);
	}
}
let datas;
s.datas=function(){
	const s=this;
	s.hooks('datas-before',s);
	if(!s.g.datas){
		const thid=s.c.thid||'';
		s.request({url:s.url('index/sets',{thid}),success:function(data){
			s.g.datas=data;
			s.g.hooks=s.extend(s.g.hooks,data.hooks);
			for(let i in data.config){
				s.c[i]=data.config[i];
			}
			initdatas();
		}});
	}else{
		initdatas();
	}
	s.reload=function(){
		s.g.datas={};
		s.request({url:s.url('index/sets',{thid}),success:function(data){
			s.g.datas=data;
			initdatas();
		}});
	}
	function initdatas(){
		uni.setNavigationBarTitle({title:s.c.site_name});
		s.navcolor({frontColor:s.c.title_color,backgroundColor:s.c.theme_color});
		let hooks=s.g.hooks.datas;
		let pagename=s.options.pagename||'index';
		let pages=s.g.datas.pages;
		let page=pages[pagename];
		if(page){
			if(page.data){
				if(page.data.bg_color){
					s.o('pagebg',page.data.bg_color);
				}
				if(page.data.need_login>0){
					s.login_page=page.data.login_page;
					s.checklogin(1);
				}else{
					s.checklogin();
				}
			}
		}

		let blocks=s.obj2arr(s.g.datas.blocks[pagename]);
		s.blocks=blocks;
		if(page.type=='cont'){
			let param={thid:s.g.datas.thid,sys:s.sys,cid:s.cid,id:s.id};
			let name=s.id;
			let file=s.cachepath(name,s.sys+'-'+s.model,s.sys);
			s.request({url:s.url(file,param),success:function(data){
				s.cont=data;
				if(data.title)uni.setNavigationBarTitle({title:data.title});
				// #ifdef MP-BAIDU
				// s.setbdinfo();
				// #endif
				for(let i in blocks){
					let block=blocks[i];
					getblock(block,data[s.cidname]);
				}
			},dataType:'json'});
		}else if(page.type=='cate'){
			if(s.cates&&s.cates[s.cid])uni.setNavigationBarTitle({title:s.cates[s.cid].title});
			s.getconts();
		}
		for(let i in blocks){
			let block=blocks[i];
			getblock(block,s.cid);
		}
		s.hooks('datas-after',s);
	}
	function getblock(block,cid){
		if(!['cate-box','slide','img-nav'].includes(block.block))return;
		let cids=[];
		if(block.source=='allcate'){
			var name=block.bid+'-0';
		}else if(block.source=='recom'){
			var name=block.recom;
		}else if(block.source=='curcate'&&cid){
			var name=block.bid+'-'+cid;
		}else if(block.source=='custom'){
			let cidname=s.cidnames[block.sys];
			let idname=s.cidnames[block.sys];
			//const custom=[];
			for(let i in block.cateids){
				let datai=block.cateids[i];
				/* if(datai[idname]){
					custom.push(datai);
				}else */if(datai[cidname]){
					cids.push(datai[cidname]);
				}
			}
			//block.custom=custom;
			if(cids.length)name=block.bid+'-'+cids.join(',');
		}
		if(!name)return;
		let bid=block.bid,
			source=block.source,
			sys=block.sys||s.sys,
			file=s.cachepath(name,source,sys),
			param=s.extend({thid:s.g.datas.thid,bid:bid,sys:sys,type:block.source,recom:block.recom,count:block.show_count},s.urldata(s.options));
		s.request({url:s.url(file,param),success:function(data){
			for(let i in s.blocks){
				if(s.blocks[i].bid==bid){
					if(s.blocks[i].source=='recom'){
						s.blocks[i].list=data;
					}else{
						s.blocks[i]=data;
					}
				}
			}
		},dataType:'json'});
	}
}

s.cachefile=function(name,type,param,callback,sys){
	const s=this;
	sys=sys||s.sys;
	let file=s.cachepath(name,type,sys);
	s.request({url:s.url(file,param),success:function(data){
		if(s.isfun(callback))callback(data)
	},dataType:'json'});
}
s.getconts=function(data){
	const s=this;
	if(s.conts_reqed)return;
	s.conts_reqed=true;
	const cid=s.cid;
	let param=s.urldata(s.options);
	data=data||{};
	let page=s.options.page||1;
	if(s.page&&s.page>1)page=s.page;
	let pagesize=(s.block&&s.block.pagesize)||10;
	let count=s.count||0;
	let name=cid+'-'+page+'-'+pagesize+'-'+count;
	let sys=s.sys||'xg';
	param['cid']=cid;
	param['page']=page;
	param['sys']=sys;
	param['pagesize']=pagesize;
	param['total']=count;
	let file=s.cachepath(name,'contents',sys);
	if(s.keywords){
		param['keywords']=s.keywords;
		file='/app/index/data';
	}
	if(s.pagedata&&s.pagedata.need_login>1){
		param.needuid=1;
		// 服务器端判断用户id
		// if(!param.contuid){
		// 	s.o('userinfo',function(){
		// 		param.contuid=s.user&&s.user.userid;
		// 		request();
		// 	});
		// 	return;
		// }
		// param.contuid=s.user&&s.user.userid;
	}
	request();
	function request(){
		s.request({url:s.url(file,param),data:data,success:function(data){
			s.conts=s.conts||[];
			data=s.bottominfo(data.list||data.data||data);
			if(data){
				for(let i in data){
					const datai=data[i];
					if(datai.img)datai.img=s.fileurl(datai.img);
					if(datai.pic)datai.pic=s.fileurl(datai.pic);
					s.conts.push(datai);
				}
			}
		},dataType:'json'});
	};
}
s.dataurl=function(){
	const s=this;
	if(!s.block.data_url)return;
	let url=s.block.data_url;
	url=s.blockinfo(url);
	if(!url)return;
	s.o('xg-emit',function(data){
		setTimeout(function(){
			let url=s.block.data_url;
			url=s.blockinfo(url);
			if(url!==s.data_requested_url){
				request(url);
			}
		},10);
	})
	request(url);
	function request(url){
		const regex = /sys=([^&]+)/;
		const match = url.match(regex);
		if(match)s.block.sys=match[1];
		s.data_requested_url=url;
		let data={};
		if(s.pagedata&&s.pagedata.need_login>1){
			data.needuid=1;
			data.contuid=s.user&&s.user.userid;
		}
		s.request({url:s.url(url,data),success:function(res){
			if(s.isarr(res)){
				s.conts=res;
			}else if(res.tid){
				s.cont=res;
			}else if(res.cont){
				s.cont=res.cont;
			}else if(res.conts){
				s.conts=res.conts;
			}else if(res[s.cidname] && res[s.idname]){
				s.cont=res;
			}else if(res[s.cidname]){
				s.conts=res;
			}
		},dataType:'json'});
	}
}

s.wxmobile=function(e){
	const s=this;
	s.wx.mobile(e,s);
}
export default s;