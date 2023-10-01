/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
(function(win,doc,$){
String.prototype.xg=function(a,b){this[a]=b;};
win.xg_form_items=win.xg_form_items||{};
var xg=function(c){
	var XG=function(){}
	var curjs=doc.currentScript.src;
	XG.prototype.c=$.extend({
		debug:1,
		dir:curjs.substring(0,curjs.lastIndexOf('/')+1)
	},c);
	XG.prototype.msgbox=function(o){
		var s=this;
		if(o)o.ook=o.ok;
		if(o&&o.ok===1)o.ok='确定';
		if(o&&o.ok===true){
			o.ook=true;
			delete o.ok;
		}
		if(o&&o.ok===-1){
			o.ok='确定';
		}
		if(o&&(o.cancel===1||o.cancel===true))o.cancel='取消';
		o=$.extend({},{ok:'',cancel:'',icon:'meh',drag:true,title:'',time:0,cclose:true,anim:'xg-anim-in',color:'',msg:'',width:'',auto:true,max:17,min:12},xg.isobj(o)?o:{});
		if(o.time&&o.time<100)o.time*=1000;
		if(o.msg){
			o.cont='<div class="xg-msg-cont-2 xg-color"><div class="xg-msg-cont-3" style="'+(o.color?'color:'+o.color+';':'')+'">'+(o.msg)+'</div></div>';
		}else if(xg.isstr(o.cont)){
			o.cont='<div class="xg-msg-cont-1 xg-color">'+o.cont+'</div>';
		}
		if(o.auto){
			if(o.max&&!o.width)o.min=xg.px(Math.min(xg.emsize(o.cont)[0]+7,o.max),'em');
		}
		if(o.min)o.min=xg.px(xg.isnan(o.min)?o.min:o.min+'em');
		win.xg_msg_ids=win.xg_msg_ids||[];
		var id=win.xg_msg_ids.length;
		win.xg_msg_ids.push(id);
		o.class=o.class||'';
		if(o.btn||o.ok||o.cancel||o.btns)o.class+=' xg-btn-msg-box';
		if(o.title)o.class+=' xg-tit-msg-box';
		var html='';
		html+='<div class="xg-msg" id="xg-msg-'+id+'">';
		if(o.shade)html+='<div class="xg-fix-bg"></div>';
		html+='<div class="xg-fix-center xg-msg-center">';// style="'+(o.owidth&&o.oheight?'transform:translate(-'+xg.px(o.owidth/2)+',-'+xg.px(Math.ceil(o.oheight/2))+')':'')+'"
		html+='<div class="xg-msg-box xg-bg-white'+o.class+' '+o.anim+'" style="'+(o.max?'max-width:'+o.max:'')+';'+(o.min?'min-width:'+o.min:'')+';'+(o.width?'width:'+o.width+';':'')+(o.height?'height:'+o.height:'')+';">';
		html+='<div class="xg-msg-outer">';
		if(o.title){
			if(xg.isarr(o.title)){
				html+='<div class="xg-msg-tit xg-bg xg-pd-0"><ul class="xg-tab-title xg-theme-border">';
				for(var i in o.title){
					html+='<li'+(i==0?' class="xg-this"':'')+' xg-id="'+i+'">'+o.title[i]+'</li>';
				}
				html+='</ul></div>';
			}else{
				html+='<div class="xg-msg-tit xg-bg'+(o.drag?' xg-msg-drag':'')+'" style="text-align:'+(o.titalign?o.titalign:'center')+'">'+o.title+'</div>';
			}
		}
		html+='<div class="xg-msg-inner xg-msg-cont-border'+(o.title?'':' xg-msg-cont-border-top')+(o.border?' xg-theme-border':'')+'">';
		html+='<div class="xg-msg-cont'+(o.icon?' xg-msg-icon-cont':'')+'">';
		if(o.icon)html+='<div class="xg-msg-icon xg-color xg-icon xg-icon-'+o.icon+'" style="'+(o.color?'color:'+o.color+'!important;':'')+'"></div>';
		if(xg.isarr(o.cont)){
			html+='<ul class="xg-tab-content">';
			for(var i in o.cont){
				html+='<li'+(i==0?' class="xg-this"':'')+' xg-id="'+i+'">'+o.cont[i]+'</li>';
			}
			html+='</ul>';
		}else{
			html+=o.cont;
		}
		html+='</div>';
		if(o.ok||o.cancel||o.btns)html+='<div class="xg-msg-btns'+(o.btnalign?' xg-msg-btn-'+o.btnalign:'')+'">';
		if(o.ok)html+='<div class="xg-msg-ok xg-msg-btn xg-bg">'+o.ok+'</div>';
		if(o.cancel)html+='<div class="xg-msg-cancel xg-msg-btn xg-bg">'+o.cancel+'</div>';
		if(o.ok||o.cancel||o.btns)html+='</div>';
		if(o.close)html+='<div class="xg-pos-a xg-msg-close"><div class="xg-pos-r xg-close'+(o.title?' xg-close-2':'')+'"></div></div>';
		html+='</div>';
		html+='</div>';
		html+='</div>';
		html+='</div>';
		html+='</div>';
		$('body').append(html);
		if(o.btns){
			for(let n in o.btns){
				$('<div/>').html(n).addClass('xg-msg-btn').addClass('xg-bg').appendTo('#xg-msg-'+id+' .xg-msg-btns').click(function(){
					var rt=o.btns[n]();
					if(rt!==false)remove()
				});
			}
		}
		var $box=$('#xg-msg-'+id+' .xg-msg-box');
		setTimeout(function(){
			if(o.cclose){
				$(doc).one('click',function(){remove();});
			}
			size();
		},10);
		$(window).resize(function(){
			size();
		});
		if($('#xg-msg-'+id).find('.xg-msg-drag').length){
			var moveing,init={};
			$('#xg-msg-'+id).find('.xg-msg-drag').mousedown(function(e){
				moveing=true;
				let event=e.originalEvent;
				let dom=$('#xg-msg-'+id).find('.xg-msg-center');
				let left=dom.offset().left,top=dom.offset().top;
				init={x:event.clientX,y:event.clientY,left:left,top:top};
				e.preventDefault();
				$(doc).mousemove(function(e){
					if(moveing){
						let event=e.originalEvent;
						let x=event.clientX,y=event.clientY;
						let dom=$('#xg-msg-'+id).find('.xg-msg-center');
						let left=region(init.left+(x-init.x-$(win).scrollLeft()),0,$(win).width()-dom.width());
						let top=region(init.top+(y-init.y-$(win).scrollTop()),0,$(win).height()-dom.height());
						let css={transform:'translate(0,0)',left:left,top:top};
						dom.css(css);
						e.preventDefault();
					}
				}).mouseup(function(){
					moveing=false;
				});
			});
			function region(o,min,max){
				o<min?o=min:o>max?o=max:'';
				return o;
			}
		}
		if(o.ok){
			$('#xg-msg-'+id).find('.xg-msg-ok').click(function(){
				var close=true;
				if(xg.isfun(o.onok)){
					var close=o.onok();
				}else if(xg.isfun(o.callback)){
					var close=o.callback();
				}
				if(close===undefined||close===true)remove();
			});
		}
		if(o.cancel){
			$('#xg-msg-'+id).find('.xg-msg-cancel').click(function(){
				var close=true;
				if(xg.isfun(o.oncancel)){
					var close=o.oncancel();
				}else if(xg.isfun(o.callback)){
					var close=o.callback();
				}
				if(close===undefined||close===true)remove();
			});
		}
		if(o.close){
			$('#xg-msg-'+id).find('.xg-msg-close').click(function(){
				var close=true;
				if(xg.isfun(o.onclose)){
					var close=o.onclose();
				}else if(xg.isfun(o.callback)){
					var close=o.callback();
				}
				if(close===undefined||close===true)remove();
			});
		}
		$('#xg-msg-'+id).css({zIndex:9999999+1+id});
		if(o.ook!==-1){
			if(o.wait===0){
				okfun();
				setTimeout(function(){
					remove();
				},o.time);
			}else if(!xg.isnan(o.time)&&o.time>0){
				setTimeout(function(){
					okfun(1);
				},o.time);
			}
		}
		function okfun(rm){
			if(o.onok){
				if(xg.isfun(o.onok)){
					var close=o.onok();
				}else if(typeof o.onok=='string'){
					location=o.onok;
				}
				if((close===undefined||close===true)&&rm)remove();
			}else if(xg.isfun(o.callback)){
				var close=o.callback();
				if((close===undefined||close===true)&&rm)remove();
			}else if(o.callback==='onok'){
				$('#xg-msg-'+id).find('.xg-ok-btn').click();
			}else if(o.callback==='oncancel'){
				$('#xg-msg-'+id).find('.xg-ok-cancel-btn').click();
			}else if(o.callback==='onclose'){
				$('#xg-msg-'+id).find('.xg-ok-close-btn').click();
			}else if(typeof o.callback==='string'){
				location=o.callback;
				if(rm)remove();
			}else{
				if(rm)remove();
			}
		}
		function remove(){
			var animtime=$('#xg-msg-'+id+' .xg-msg-box').css('animation-duration');
			animtime=animtime?animtime.replace('s',0):'1';
			if(animtime.substr(0,1)=='.')animtime='0'+animtime;
			animtime*=1000;
			$('#xg-msg-'+id+' .xg-msg-box')
			.removeClass(o.anim?o.ainm:'xg-anim-in')
			.addClass((o.anim=='xg-anim-in')?'xg-anim-out':'xg-anim-1-1')
			.bind('animationend',function(){removeid(id);});;
			setTimeout(function(){removeid(id)},animtime);
		}
		function removeid(id){
			$('#xg-msg-'+id).remove();
			delete(window.xg_msg_ids[id]);
		}
		function size(){
			if($(window).width()<o.owidth){
				$box.width($(window).width());
			}else{
				$box.width(o.owidth);
			}
			var h=(o.oheight%2)?o.oheight-1:o.oheight;
			if($(window).height()<h){
				let h2=($(window).height()%2)?$(window).height()-1:$(window).height();
				$box.height(h2);
			}else{
				$box.height(h);
			}
		}
		return id;
	}
	XG.prototype.close=function(id){
		if(id!==undefined){
			removeid(id);
		}else{
			for(var id in window.xg_msg_ids){
				removeid(id);
			}
		}
		function removeid(id){
			$('#xg-msg-'+id+' .xg-msg-box').removeClass('xg-anim-1-0').addClass('xg-anim-1-1');
			setTimeout(function(){
				$('#xg-msg-'+id).remove();
				delete(window.xg_msg_ids[id]);
			},100);
		}
	}
	XG.prototype.def=function(m,f){
		var s=this;
		if(xg.isfun(f)){
			s[m]=f();
		}else{
			s[m]=f;
		}
	}
	XG.prototype.mod=function(m,f){
		var s=this;
		s.mcallbacks=s.mcallbacks||{};
		s.mcallbacks[m]=s.mcallbacks[m]||[];
		if(!s[m]){
			if(xg.isfun(f))s.mcallbacks[m].push(f);
			xg.loadjs(s.c.dir+'modules/'+m+'.js',function(){
				for(var i in s.mcallbacks[m]){
					s.mcallbacks[m][i]();
				}
				s.mcallbacks[m]=[];
			});
		}else{
			f(2);
		}
	}
	XG.prototype.o=function(c){
		var s=this;
		s.c=c;
		return s;
	}
	XG.prototype.msg=function(){
		var arg=arguments,o={};
		for(let i in arg){
			if(xg.isobj(arg[i])){
				o=$.extend({},o,arg[i]);
			}else if(xg.isnum(arg[i])){
				o.time=arg[i];
			}else if(xg.isfun(arg[i])){
				o.onok=arg[i];
			}else if(xg.isstr(arg[i])){
				o.msg=arg[i];
			}
		}
		if(!o.msg)return;
		if(!o.time)o.time=3;
		if(xg.isnum(o.code)){
			if(o.code<0){
				xg.err(o);
			}else if(o.code===0){
				xg.ok(o);
			}
			return;
		}
		if(o.ok===-1){
			xg.err(o);
			return;
		}else if(o.ok===true){
			xg.ok(o);
			return;
		}
		xg.msgbox(o);
	}
	XG.prototype.ok=function(m,a,b){
		let o={};
		if(xg.isobj(m)){
			o=$.extend(o,{icon:'smile',color:'#093'},m);
		}else{
			o={icon:'smile',color:'#093',time:xg.isnum(a)?a:2,msg:m,onok:(xg.isfun(a)?a:b)};
		}
		this.msgbox(o);
	}
	XG.prototype.err=function(m,f){
		let o={};
		if(xg.isobj(m)){
			o=$.extend(o,{ok:1,icon:'frown',title:'提示信息',cclose:false},m);
		}else{
			o={ok:1,icon:'frown',msg:m,title:'提示信息',onok:f,cclose:false};
		}
		this.msgbox(o);
	}
	XG.prototype.confirm=function(m,f1,f2){
		if(xg.isobj(m)){
			o=$.extend(o,{ok:1,cancel:1,icon:'question',title:'确认信息',cclose:false,btnalign:'right',titalign:'left'},m);
		}else{
			o={ok:1,cancel:1,icon:'question',msg:m,title:'确认信息',onok:f1,oncancel:f2,cclose:false,btnalign:'right',titalign:'left'};
		}
		this.msgbox(o);
	}
	XG.prototype.winop=function(arg){
		var s=this,o={};
		o.icon=o.auto='';
		o.titalign='left';
		o.btnalign='right';
		o.close=true;
		o.border=true;
		o.cclose=false;
		o.anim='xg-anim-1-0';
		for(var i in arg){
			let a=arg[i];
			if(xg.isstr(a))o.title=a;
			if(xg.isfun(a)){
				if(!xg.isfun(o.onok)){
					o.onok=a;
				}else if(!xg.isfun(o.oncancel)){
					o.oncancel=a;
				}else if(!xg.isfun(o.oncallback)){
					o.oncallback=a;
				}
			}
			if(a===1){
				if(!o.ok){
					o.ok=1;
				}else{
					o.cancel=1;
				}
			}else if(xg.isnum(a)){
				if(!xg.isnum(o.width)){
					o.width=a;
				}else{
					o.height=a;
				}
			}
			if(xg.isobj(a)){
				if(a.isxgurl){
					o.src=a;
				}else{
					o=$.extend({},o,a);
				}
			}
		}
		o.title=xg.isunde(o.title)?'信息':o.title;
		o.max=xg.px(o.max);
		if(!xg.isnan(o.width))o.owidth=o.width;
		if(!xg.isnan(o.height))o.oheight=o.height;
		o.width=xg.px(o.width);
		o.height=xg.px(o.height);
		return o;
	}
	XG.prototype.win=function(){
		return this.msgbox(this.winop([].slice.call(arguments,0)));
	}
	XG.prototype.iframe=function(){
		if(!xg.isnan(arguments[0])){
			var ifr=$('#xg-msg-'+arguments[0]).find('iframe');
			if(ifr.length>1){
				var arr=[];
				for(var i=0;i<ifr.length;i++){
					arr[i]=ifr[i].contentWindow;
				}
				return arr;
			}else if(ifr.length==1){
				return ifr[0].contentWindow;
			}
			return null;
		}
		var o=this.winop(arguments);
		if(xg.isobj(o.cont)&&!o.cont.isxgurl){
			var titles=[];
			var conts=[];
			for(var title in o.cont){
				titles.push(title);
				if(o.cont[title]&&o.cont[title].isxgurl){
					conts.push('<iframe src="'+xg.urladd(o.cont[title],'xg-iframe',1)+'" style="width:100%;height:calc(100% - 2px);"></iframe>');
				}else{
					conts.push(o.cont[title]);
				}
			}
			var o=$.extend({},o,{title:titles,cont:conts});
			var id=this.msgbox(o);
			xg.tab('xg-msg-tab-box-'+id).bind('#xg-msg-'+id+' .xg-tab-title','#xg-msg-'+id+' .xg-tab-content');
		}else{
			var o=$.extend({},o,{cont:'<iframe src="'+xg.urladd(o.src,'xg-iframe',1)+'" style="width:100%;height:calc(100% - 2px);"></iframe>'});
			var id=this.msgbox(o);
		}
		return id;
	}
	XG.prototype.color=function(){
		var fi=0,o={},tit,value,f,f2,f3;
		for(let i in arguments){
			let arg=arguments[i];
			if(xg.isobj(arg)){
				o=$.extend({},o,arg);
			}
			if(xg.isstr(arg)){
				if(i==0){
					tit=arg;
				}else{
					value=arg;
				}
			}
			if(xg.isfun(arg)){
				if(fi==0)f=arg;
				if(fi==1)f2=arg;
				if(fi==2)f3=arg;
				fi++;
			}
		}
		var id=0;
		value=o.value?o.value:value;
		var data={
			title:(tit||o.title),
			cont:'<div class="xg-flex xg-mt-5 xg-form xg-pl-1 xg-pr-1"><div class="xg-color-swatch"><input class="xg-color-input" type="color" value="'+(value?value:'')+'" /></div><input class="xg-input xg-msg-input" type="text" /><input class="xg-color-clear xg-btn" type="button" value="清除"></div>',
			titalign:'left',
			width:'18em',
			ok:1,
			cancel:1,
			oncancel:(f2||o.oncancel),
			oncallback:(f3||o.oncallback),
			onok:function(){
				return(f||o.onok)($('#xg-msg-'+id).find('.xg-msg-input').val());
			},
			btns:o.btns,
			cclose:false
		};
		var id=xg.input(data);
		$('#xg-msg-'+id).find('.xg-color-input').change(function(){$('#xg-msg-'+id).find('.xg-msg-input').val($(this).val());});
		$('#xg-msg-'+id).find('.xg-msg-input').change(function(){$('#xg-msg-'+id).find('.xg-color-input').val($(this).val());});
		$('#xg-msg-'+id).find('.xg-color-clear').click(function(){$('#xg-msg-'+id).find('.xg-msg-input').val('').change();});
		return id;
	}
	XG.prototype.input=function(){
		var fi=0,o={},tit,value,f,f2,f3;
		for(let i in arguments){
			let arg=arguments[i];
			if(xg.isobj(arg)){
				o=$.extend({},o,arg);
			}
			if(xg.isstr(arg)){
				if(i==0){
					tit=arg;
				}else{
					value=arg;
				}
			}
			if(xg.isfun(arg)){
				if(fi==0)f=arg;
				if(fi==1)f2=arg;
				if(fi==2)f3=arg;
				fi++;
			}
		}
		var id=0;
		value=o.value?o.value:value;
		var id=this.win(
			$.extend({},{
				title:(tit||o.title),
				cont:(o.cont||'<input class="xg-msg-input" type="text" value="'+(value?value:'')+'" />'),
				titalign:'left',
				width:'18em',
				ok:1,
				cancel:1,
				oncancel:(f2||o.oncancel),
				oncallback:(f3||o.oncallback),
				onok:function(){
					return(f||o.onok)($('#xg-msg-'+id).find('.xg-msg-input').val());
				},
				btns:o.btns,
				cclose:false
			})
		);
		$('#xg-msg-'+id).find('.xg-msg-input').focus().keydown(function(e){
			if((e.keyCode||e.which||e.charCode)==13){
				e.preventDefault();e.stopPropagation();
				$('#xg-msg-'+id).find('.xg-msg-ok').click();
				$(this).focus();
			}
		});
		return id;
	}
	XG.prototype.log=function(){
		var s=this;
		if(xg.device().os=='mac'){
			return;
		}
		var info=(new Error()).stack.split('\n')[2];
		var info=(/(.*)at\s+(.*)\s+\((.*):(\d*):(\d*)\)/i.exec(info)||/(.*)at\s+()(.*):(\d*):(\d*)/i.exec(info)).slice(3);info.length--;
		var arr=[].slice.call(arguments,0);arr.push(info.join(':'));
		if(s.c.debug)console.log(arr);
	}
	XG.prototype.error=function(){
		if(xg.device().os=='mac'){
			console.error(arguments);
			return;
		}
		var info=(new Error()).stack.split('\n')[2];
		var info=(/(.*)at\s+(.*)\s+\((.*):(\d*):(\d*)\)/i.exec(info)||/(.*)at\s+()(.*):(\d*):(\d*)/i.exec(info)).slice(3);info.length--;
		var arr=[].slice.call(arguments,0);arr.push(info.join(':'));
		console.error(arr);
	}
	return new XG();
};
var xg=xg();
xg.iswin=function(v){return v&&(v.window===v);}
xg.isstr=function(v){return xg.type(v)=='string';}
xg.isobj=function(v){return xg.type(v)=='object';}
xg.iseobj=function(v){if(xg.isobj(v)){for(var i in v){return false;}return true;}return false;}
xg.isarr=function(v){return xg.type(v)=='array';}
xg.isfun=function(v){return xg.type(v)=='function';}
xg.isnum=function(v){return xg.type(v)=='number';}
xg.isnan=function(v){return isNaN(v);}
xg.isbool=function(v){return xg.type(v)=='boolean';}
xg.istrue=function(v){return v===1||v==='1'||v===true;}
xg.isfalse=function(v){return v===0||v==='0'||v===false;}
xg.isunde=function(v){return xg.type(v)=='undefined';}
xg.isnull=function(v){return v===null;}
xg.isfile=function(v){return xg.type(v)=='file';}
xg.type=function(v){
	if(v===null)return 'null';
	if(v instanceof win.File||v instanceof win.Blob)return 'file';
	var type=typeof(v);
	if(type=='object')return((Array.isArray||function(v){return Object.prototype.toString.call(v)==='[object Array]';})(v)?'array':'object');
	return type;
}
xg.obj2arr=function(v){var r=[];for(var i in v){r.push(v[i]);}return r;}
xg.int=function(n){
	n=n.replace(/[^0-9\-]/g,'')
	return xg.isnan(n)?0:parseInt(n);
}
xg.float=function(n){
	n=n.replace(/[^0-9\.\-]/g,'');
	return xg.isnan(n)?0:parseFloat(n);
}
xg.jsloaded=[];
xg.loadjs=function(v,f){
	if(!xg.isfun(f))f=xg.noop;
	if(xg.jsloaded.indexOf(v)>-1)return f(2);
	if($('script[src^="'+v+'"]').length)return;
	var timer=setInterval(function(){
		var loaded=xg.jsloaded.indexOf(v)>-1;
		i++;
		if(loaded){
			clearInterval(timer);
			f(2);
			return;
		}
		if(i>100){
			doc.head.removeChild(elem);
			clearInterval(timer);
			f(0);
		}
	},50);
	var elem=doc.createElement('script');
	elem.src=xg.uniqurl(v);
	elem.async=true;
	elem.onload=function(){xg.jsloaded.push(v);clearInterval(timer);f(1);}
	elem.onerror=function(e){xg.jsloaded.push(v);clearInterval(timer);}
	var i=0;
	doc.head.appendChild(elem);
}
xg.sha1=function(s){var hexcase=0;var b64pad="";
function hex_sha1(s){return rstr2hex(rstr_sha1(str2rstr_utf8(s)));}
function rstr_sha1(s){return binb2rstr(binb_sha1(rstr2binb(s),s.length*8));}
function rstr2hex(input){try{hexcase} catch(e){hexcase=0;}var hex_tab=hexcase?"0123456789ABCDEF":"0123456789abcdef";var output="";var x;for(var i=0;i<input.length;i++){x=input.charCodeAt(i);output+=hex_tab.charAt((x>>>4)&0x0F)+hex_tab.charAt(x&0x0F);}return output;}
function str2rstr_utf8(input){var output="";var i=-1;var x,y;while(++i<input.length){x=input.charCodeAt(i);y=i+1<input.length?input.charCodeAt(i+1):0;if(0xD800<=x &&x<=0xDBFF &&0xDC00<=y &&y<=0xDFFF){x=0x10000+((x&0x03FF)<< 10)+(y&0x03FF);i++;}if(x<=0x7F)output+=String.fromCharCode(x);else if(x<=0x7FF)output+=String.fromCharCode(0xC0|((x>>>6)&0x1F),0x80|(x&0x3F));else if(x<=0xFFFF)output+=String.fromCharCode(0xE0|((x>>>12)&0x0F),0x80|((x>>>6)&0x3F),0x80|(x&0x3F));else if(x<=0x1FFFFF)output+=String.fromCharCode(0xF0|((x>>>18)&0x07),0x80|((x>>>12)&0x3F),0x80|((x>>>6)&0x3F),0x80|(x&0x3F));}return output;}
function rstr2binb(input){var output=Array(input.length>>2);for(var i=0;i<output.length;i++)output[i]=0;for(var i=0;i<input.length*8;i+=8)output[i>>5]|=(input.charCodeAt(i/8)&0xFF)<<(24-i%32);return output;}
function binb2rstr(input){var output="";for(var i=0;i<input.length*32;i+=8)output+=String.fromCharCode((input[i>>5]>>>(24-i%32))&0xFF);return output;}
function binb_sha1(x,len){x[len>>5]|=0x80<<(24-len%32);x[((len+64>>9)<< 4)+15]=len;var w=Array(80);var a=1732584193;var b=-271733879;var c=-1732584194;var d=271733878;var e=-1009589776;for(var i=0;i<x.length;i+=16){var olda=a;var oldb=b;var oldc=c;var oldd=d;var olde=e;for(var j=0;j<80;j++){if(j<16)w[j]=x[i+j];else w[j]=bit_rol(w[j-3]^w[j-8]^w[j-14]^w[j-16],1);var t=safe_add(safe_add(bit_rol(a,5),sha1_ft(j,b,c,d)),safe_add(safe_add(e,w[j]),sha1_kt(j)));e=d;d=c;c=bit_rol(b,30);b=a;a=t;}a=safe_add(a,olda);b=safe_add(b,oldb);c=safe_add(c,oldc);d=safe_add(d,oldd);e=safe_add(e,olde);}return Array(a,b,c,d,e);}
function sha1_ft(t,b,c,d){if(t<20)return(b&c)|((~b)&d);if(t<40)return b^c^d;if(t<60)return(b&c)|(b&d)|(c&d);return b^c^d;}
function sha1_kt(t){return(t<20)?1518500249 :(t<40)?1859775393 :(t<60)?-1894007588 :-899497514;}
function safe_add(x,y){var lsw=(x&0xFFFF)+(y&0xFFFF);var msw=(x>>16)+(y>>16)+(lsw>>16);return(msw<< 16)|(lsw&0xFFFF);}
function bit_rol(num,cnt){return(num<< cnt)|(num>>>(32-cnt));}return hex_sha1(s);}
xg.index=function(a,v,k){
	var index=-1;
	if(xg.isarr(a)){
		for(var i in a){
			if(xg.isstr(k)){
				if(k.indexOf(',')>-1){
					var k=k.split(',');
					var c=0;
					for(var j in k){
						if(a[i][k[j]]==v)c++;
					}
					if(c==k.length)index=i;
				}else{
					if(a[i][k]==v[k])index=i;
				}
			}else{
				if(a[i]==v)index=i;
			}
			if(index!=-1)break;
		}
	}
	return index;
}
xg.obj={
	bykey:function(o,k){
		var n={};
		for(i in o){
			if(k.indexOf(i)>-1){
				n[i]=o[i];
			}
		}
		return n;
	}
};
xg.push=function(a,v,k){
	if(xg.isstr(a)){
		if(a.substr(0,1)=='['){
			a=JSON.parse(a);
		}else{
			a=a.split(',');
		}
	}
	if(xg.index(a,v,k)==-1)a.push(v);
	return a;
}
xg.delete=function(a,v,k){
	if(xg.isstr(a)){
		if(a.substr(0,1)=='['){
			a=JSON.parse(a);
		}else{
			a=a.split(',');
		}
	}
	var index;
	while((index=xg.index(a,v,k))>-1)a.splice(index,1);
	return a;
}
xg.swapitem=function(arr,from,to){
	arr[from]=arr.splice(to,1,arr[from])[0];
	return arr;
}
xg.moveitem=function(arr,from,to){
	arr.splice(to,0,arr.splice(from,1)[0]);
	return arr;
}

xg.strpad=function(len,str){
	if(!str)str=' ';
	return new Array(len).join(str);
}

xg.funfirst=function($s,fun){
	xg.funmove($s,fun,1);
}
xg.funmove=function($s,fun,first){
	$($s).each(function(){
		var arr=$._data($(this).get(0)).events.click;
		for(var i in arr){
			if(arr[i].handler===fun){
				arr=xg.moveitem(arr,i,(first?0:arr.length-1));
				break;
			}
		}
		$._data($(this).get(0)).events.click=arr;
	});
}
xg.funlast=function($s,fun){
	xg.funmove($s,fun,0);
}
xg.subbtn=function(e){
	var e=e.originalEvent||e;
	var result=new String($(e.submitter).is('button')?$(e.submitter).text():$(e.submitter).val());
	result.e=e;
	result.btn=e.submitter;
	result.$=$(e.submitter);
	return result;
};
xg.pointobj=function(o,k){
	return eval('o'+k.split('.').map(function(v){return '['+(xg.isnum(v)?v:"'"+v+"'")+']';}).join(''))
};
xg.loadcss=function(v){
	if($('link[href^="'+v+'"]').length)return;
	$('<link/>').attr('href',xg.uniqurl(v)).attr('rel','stylesheet').appendTo('head');
}
xg.px=function(v,u){
	var v=xg.isstr(v)?v.toLowerCase():v;
	if(xg.isstr(v)&&(v.substr(-1)=='%'||v.substr(-2)=='px'||v.substr(-2)=='em'||v.substr(-3)=='rem'))return v;
	if(xg.isnum(v)||!xg.isnan(v))return v+(u?u:'px');
	return '';
}
xg.zero=function(n){
	if(n<10)return '0'+n;
	return n;
}
xg.time=function(){
	return new Date().getTime();
}
xg.date=function(type){
	var arr=[];
	arr.push(new Date().getFullYear());
	arr.push(new Date().getMonth()+1);
	arr.push(new Date().getDate());
	arr.push(new Date().getHours());
	arr.push(new Date().getMinutes());
	arr.push(new Date().getSeconds());
	if(type==0){
		return arr[0]+'-'+xg.zero(arr[1])+'-'+xg.zero(arr[2])+' '+xg.zero(arr[3])+':'+xg.zero(arr[4])+':'+xg.zero(arr[5]);
	}else if(type==1){
		return arr[0]+'-'+xg.zero(arr[1])+'-'+xg.zero(arr[2]);
	}else if(type==2){
		return xg.zero(arr[3])+':'+xg.zero(arr[4])+':'+xg.zero(arr[5]);
	}
	return arr;
}
xg.cssvar=function($s,c,v){
	if(xg.isunde(v)){
		return $($s)[0].style.getPropertyValue(c);
	}else{
		$($s)[0].style.setProperty(c,v);
		return $($s);
	}
}
xg.attr=function($s){
	const dom=$($s)[0];
	const attr=dom.attributes;
	const result={};
	for(let i in attr){
		result[attr[i].name]=attr[i].value;
	}
	return result;
}
xg.realcss=function($s,n){
	const style=getComputedStyle($($s)[0]);
	return style.getPropertyValue(n);
}
xg.class=function($s,c){
	$($s).addClass(c);
}
xg.emsize=function(v){
	var elem=$('<div style="width:0;overflow:hidden;"><div style="width:999999999999px;"><div style="float:left;width:auto;word-break:keep-all;white-space:nowrap;font-size:50px!important;" class="xg-em-size">'+v+'</div></div></div>').appendTo('body');
	var rt=[elem.find('.xg-em-size').width()/50,elem.find('.xg-em-size').height()/50];
	elem.remove();
	return rt;
}
xg.reall=xg.replaceall=function(str,from,to){
	while(str&&str.indexOf(from)>-1)str=str.replace(from,to);return str;
}
xg.bytelen=function(str){
	var byteLen = 0;
	for (var i = 0; i < str.length; i++) {
		if ((/[\x00-\xgf]/g).test(str.charAt(i)))
			byteLen += 1;
		else
			byteLen += 2;
	}
	return byteLen;
}
xg.device=function(){
	var agent=navigator.userAgent.toLowerCase(),
	os=(function(){
		if(/windows/.test(agent)){
			return 'windows';
		}else if(/linux/.test(agent)){
			return 'linux';
		}else if(/iphone|ipod|ipad|ios/.test(agent)){
			return 'ios';
		}else if(/mac/.test(agent)){
			return 'mac';
		}
	})(),
	android=/android/.test(agent),
	weixin=/micromessenger/.test(agent),
	ios=os==='ios',
	mobile=android||ios;
	return{agent:agent,os:os,android:android,ios:ios,mobile:mobile,weixin:weixin};
}
xg.newdiv=xg.div=function(c){return $('<div/>').addClass(c||'').appendTo('body');};
xg.newli=xg.li=function(c){return $('<li/>').addClass(c||'').appendTo('body');};
xg.newul=xg.ul=function(c){return $('<ul/>').addClass(c||'').appendTo('body');};
xg.newbtn=function(a,b,c){return $('<button/>').attr('type','button').html(a).addClass(xg.isstr(b)?b:'').addClass('xg-btn').click(xg.isfun(b)?b:c);};
xg.divshadow=function(t){
	var html='';
	html+='<div class="xg-div-shadow xg-div-shadow-t"></div>';
	html+='<div class="xg-div-shadow xg-div-shadow-r"></div>';
	html+='<div class="xg-div-shadow xg-div-shadow-b"></div>';
	html+='<div class="xg-div-shadow xg-div-shadow-l"></div>';
	return html;
}
xg.loading=function(a,b){
	if(a!==0){
		if(!$('.xg-loading').length){
			if(xg.isstr(a)){
				var a=xg.newdiv('xg-loading-tips xg-fix-center xg-border xg-theme-border xg-bg-white xg-radius-5 xg-pd-3 xg-lh-1').html(a);
			}else{
				var div=xg.newdiv('xg-loading-dot'+(a===2?'-2':'')+' xg-fix-center');
				for(var c=((a===2)?5:3);c>0;c--){
					div.append(xg.newdiv('xg-bg'));
				}
				var a=div;
			}
			return xg.newdiv('xg-loading').append(b?'':xg.newdiv().addClass('xg-fix-bg')).append(a).appendTo('body');
		}
	}else{
		$('.xg-loading').remove();
	}
}
xg.copy=function(text){
	if(typeof navigator.clipboard!='undefined'){
		try{
			(async()=>{
				await navigator.clipboard.writeText(text).then(function(){
					xg.ok('复制成功');
				}),function(){
					xg.msg('复制失败');
				};
			})();
		}catch(e){
			xg.msg('复制失败');
		}
	}else{
		$('<input type="text" readonly="readonly" id="xg-copy-input" value="" style="width:1px;height:1px;opacity:0;"/>').appendTo('body');
        try{
        	$('#xg-copy-input').val(text);
        	$('#xg-copy-input').focus();
        	$('#xg-copy-input').select();
    		var ok=document.execCommand('copy',false,null);
    		$('#xg-copy-input').remove();
    		if(ok){
    			xg.ok('复制成功');
    		}else{
    			xg.msg('复制失败');
    		}
    	}catch(e){
    		xg.msg('复制失败');
    	}
	}
};
win.xg_copytext=xg.copy;
xg.parseurl=function(url){
	var url=url||location.toString();
	var a=document.createElement('a');
	a.href=url;
	return{
		source:url,
		protocol:a.protocol.replace(':',''),
		host:a.hostname,
		port:a.port,
		query:a.search||'',
		params:(function(){
			var ret={},seg=a.search.replace(/^\?/,'').split('&'),len=seg.length,i=0,s;
			for(;i<len;i++){
				if(!seg[i])continue;
				s=seg[i].split('=');
				ret[s[0]]=s[1];
			}
			return ret;
		})(),
		file:(a.pathname.match(/\/([^\/?#]+)$/i) || [,''])[1],
		hash:a.hash.replace('#',''),
		path:a.pathname.replace(/^([^\/])/,'/$1'),
		relative:(a.href.match(/tps?:\/\/[^\/]+(.+)/) || [,''])[1],
		segments:a.pathname.replace(/^\//,'').split('/')
	};
}
xg.formvals=function($s){
	let inputs=$($s).find('input[name],textarea[name],select[name],button[name]');
	let res={};
	inputs.each(function(){
		var $this=$(this);
		var name=$this.attr('name');
		var pattern=/\[(.*?)\]/g;
		var match=pattern.exec(name);
		if(match){
			let keys=[];
			while(match!=null){
				let key=match[1];
				keys.push(key);
				match=pattern.exec(name);
			}
			if(name.indexOf('[')>0)keys.unshift(name.substr(0,name.indexOf('[')));
			var obj=res;
			for(let i=0;i<keys.length;i++){
				let key=keys[i];
				let next=keys[i+1];
				if(key){
					if(next===''){
						obj[key]=obj[key]||[];
						obj=obj[key];
					}else if(next===undefined){
						obj[key]=$this;
					}else{
						obj[key]=obj[key]||{};
						obj=obj[key];
					}
				}else if(key===''){
					if(xg.isarr(obj)){
						obj.push($this);
					}
				}
			}
		}else{
			res[name]=$this;
		}
	});
	return res;
}
xg.addon=function(u,p,app){
	var addon=u.split('://')[0];
	var arr=u.split('://')[1].split('/');
	p=$.extend({addon:addon,controller:arr[0],action:arr[1]},p);
	return xg.url((app?'/'+app+'/':'')+'addons/execute',p);
}
xg.rurl=function(u,p,app){
	let url=xg.url(u,p,app);
	const root=xginfo.root||'/';
	url=url.substr(root.length-1);
	return url;
}
xg.url=function(u,p,app){
	var m,c,a;
	var pa={};
	var xginfo=win.xginfo||{};
	var root=xginfo.root||'/';
	var route=xginfo.route||0;
	if(xg.isnull(u)||xg.isunde(u)){
		u=location.href.toString();
	}else if(xg.isobj(u)&&!u.isxgurl){
		var p=u;
		var u='';
	}
	if(u=='/')return root;
	if(u.indexOf('/')===0)u=u.substr(1);
	if(u.indexOf('?')>-1){
		var ua1=u.split('?');
		u=ua1[0];
		var qps=ua1[1].split('&');
		for(var i in qps){
			var qps2=qps[i].split('=');
			pa[qps2[0]]=qps2[1];
		}
	}
	var match=/([a-zA-Z0-9\-_]+)@([a-zA-Z0-9\-_]+).?([a-zA-Z0-9\-_]*)/.exec(u);
	if(match){
		let sys=match[1];
		let ctl=match[2];
		let act=match[3];
		let params=$.extend({sys:sys,controller:ctl,action:act},p);
		return xg.url('/'+(app?app:xginfo.app)+'/sys/execute',params);
	}
	var ua2=u.split('/');
	if(ua2.length==3){
		m=ua2[0];
		c=ua2[1];
		a=ua2[2];
	}else if(ua2.length==2){
		c=ua2[0];
		a=ua2[1];
	}else if(ua2.length==1){
		a=ua2[0];
	}
	if(typeof p=='string'){
		if(p.substr(0,1)=='?')p=p.substr(1);
		ps=p.split('&');
		for(var i in ps){
			var ps2=ps[i].split('=');
			pa[ps2[0]]=ps2[1];
		}
	}else if(typeof p=='object'){
		for(var i in p){
			pa[i]=p[i];
		}
	}
	var pa1=[];
	for(var i in pa){
		pa1.push(i+'='+pa[i]);
	}
	p=pa1.join('&');
	var get=getparams();
	if(!m)m=xginfo.app||'';
	if(!c)c=xginfo.controller||'';
	if(!a)a=xginfo.action||'';
	var pa2=[];
	if(p)pa2.push(p);
	if(route){
		var rt=new String(root+m+'/'+c+'/'+a+'.html'+(pa2.length>0?('?'+pa2.join('&')):''));
	}else{
		var rt=new String(root+'?xgapp='+m+'&xgctl='+c+'&xgact='+a+'&'+pa2.join('&'));
	}
	rt.xg('isxgurl',true);
	return rt;
	function getparams(){
		var a=document.createElement('a');
		a.href=location;
		var ret={},
			seg=a.search.replace(/^\?/,'').split('&'),
			len=seg.length,i=0,s;
		for(;i<len;i++){
			if(!seg[i]){continue;}
			s=seg[i].split('=');
			ret[s[0]]=s[1];
		}
		return ret;
	}
}
xg.query2arr=function(data){
	if(xg.isunde(data)){
		var l=location.href.toString();
		var data=l.indexOf('?')>-1?l.substr(l.indexOf('?')+1):'';
	}
	var arr=data.split('&');
	var newres={};
	for(var i in arr){
		var arr2=arr[i].split('=');
		if(arr2.length==2&&arr2[0]){
			var name=decodeURIComponent(xg.reall(arr2[0],'+',' '));
			var value=decodeURIComponent(xg.reall(arr2[1],'+',' '));
			if(/\[.*?\]/.test(name)){
				var arr3=name.split(/([\[\]]{1})/);
				name=arr3.shift();
				value=dores(arr3,value,newres[name]);
			}
			var tmpres={};
			newres[name]=value;
		}
	}
	return newres;
	function iswords(v){
		return /^[^\[\]]+$/.test(v);
	}
	function dores(n,v,r){
		var v1={};
		var v2=[];
		var n2=[];
		var i=0;
		for(;i<n.length;i++){
			if(n[i])n2.push(n[i]);
		}
		i=0;
		n=n2;
		if(n.length==0){
			return v;
		}else if(iswords(n[i])){
			if(typeof r=='undefined')r={};
			r[n[i]]=dores(n.slice(1),v,r[n[i]]);
			return r;
		}else if(n[i]=='['&&n[i+1]==']'){
			if(typeof n[i+2]=='undefined'){
				if(typeof r=='undefined')r=[];
				r.push(dores(n.slice(1),v,r[r.length-1]));
				return r;
			}else if((n[i+2]&&n[i+2]=='['&&(((n[i+3]&&(iswords(n[i+3])||n[i+3]==']')))))){
				if(typeof r=='undefined')r=[];
				r.push(dores(n.slice(1),v));
				return r;
			}
		}
		return dores(n.slice(1),v,r);
	}
}
xg.query2obj=xg.query2arr;
xg.obj2query=function(data){
	var query='';
	for(let k in data){
		let v=data[k];
		if(xg.isarr(v)){
			for(let i in v){
				if(xg.isobj(v[i])){
					for(let j in v[i]){
						query += (query?'&':'') + encodeURIComponent(k)+'['+i+']['+j+']' + '=' + encodeURIComponent(v[i][j]);
					}
				}else{
					query += (query?'&':'') + encodeURIComponent(k)+'[]' + '=' + encodeURIComponent(v[i]);
				}
			}
		}else if(xg.isobj(v)){
			for(let i in v){
				query += (query?'&':'') + encodeURIComponent(k)+'['+i+']' + '=' + encodeURIComponent(v[i]);
			}
		}else{
			query += (query?'&':'') + encodeURIComponent(k) + '=' + encodeURIComponent(v);
		}
	};
	return query;
}
xg.randstr=function(l,t){
	var i,e,b='abcdefghijklmnopqrstuvwxyz',c='',l=l||10;
	if(xg.isnum(t)){
		if(t==0)b='123456789';
		if(t>0)b+='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if(t>1)b+='0123456789';
	}
	for(i=0;i<l;i++){
		c+=b.charAt(Math.floor(Math.random()*b.length));
	}
	return c;
}
xg.urladd=function(a,b,v,t){
	if(xg.isobj(a)&&!a.isxgurl){
		var u=xg.url();
		var rt=u;
		for(var i in a){
			rt=xg.urladd(rt,i,a[i],b);
		}
	}else{
		var u=a;
		var add='';
		if(t===1){
			u=u.replace(new RegExp(b+'=.*?([\&]+|$)','g'),'');
		}
		if(b&&v&&(t||!xg.query2arr(u)[b]))add=b+'='+v;
		var rt=u+(u.indexOf('?')>-1?(u.substr(-1)=='&'?'':'&'):'?')+add;
	}
	if(u.isxgurl){
		var rt=new String(rt);
		rt.isxgurl=true;
	}
	return rt;
}
xg.uniqurl=function(v){
	var v=v.replace(/[&\?]?xg\-random=[^&]*/g,'');
	return xg.urladd(v,'xg-random',Math.random());
}
xg.ismobile=function(v){
	return /^1[3|4|5|6|7|8|9]{1}[\d]{9}$/.test(v.replace('+86',''));
}
xg.isemail=function(v){
	return /^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]{2,10}){1,2}$/.test(v);
}
xg.isdomain=function(v){
	if(/[^a-zA-Z0-9\.\-]+/.test(v))return false;
	if(!/[\.]+/.test(v))return false;
	if(/[\.]{2,}/.test(v))return false;
	if(/^[\.]+/.test(v))return false;
	if(/[\.]+$/.test(v))return false;
	if(/[\-]{3,}/.test(v))return false;
	if(/^[\-]+/.test(v))return false;
	if(/[\-]+$/.test(v))return false;
	return true;
}
xg.reurl=function(url,istop) {
	var target=self;
	if(istop)target=top;
	if(xg.isfun(history.replaceState)){
		target.history.replaceState({xg:'xg'},'',url);
	}
}
xg.zoom=function($s,o){
	var s={};
	s.sc=1;
	s.el=$($s);
	s.p=o.parent?$(o.parent):$(s.el).parent();
	s.pw=s.p.width();
	s.ph=s.p.height();
	if(s.el.css('position')=='absolute'){
		s.ln='left';
		s.tn='top';
		if(s.el.css('marginLeft')){
			let ml=xg.float(s.el.css('marginLeft'));
			let pl=xg.float(s.el.css('left'));
			s.el.css({left:ml+pl,marginLeft:0});
		}
		if(s.el.css('marginTop')){
			let mt=xg.float(s.el.css('marginTop'));
			let pt=xg.float(s.el.css('top'));
			s.el.css({top:mt+pt,marginTop:0});
		}
	}else{
		s.ln='marginLeft';
		s.tn='marginTop';
	}
	s.zoom=function(sc){
		sc=s.sc=s.sc*(sc+1);
		if(sc<s.minsc)sc=s.minsc;
		if(sc>s.maxsc)sc=s.maxsc;
		let l=xg.float(s.el.css(s.ln));
		let t=xg.float(s.el.css(s.tn));
		let w=s.el.width();
		let h=s.el.height();
		if(w*sc<s.pw)sc=s.pw/w;
		if(h*sc<s.ph)sc=s.ph/h;
		l=(w*sc-w)/2;
		t=(h*sc-h)/2;
		if(l>0)l=0;
		if(t>0)t=0;
		let nw=w*sc;
		let nh=h*sc;
		if(l+nw<s.pw)l=s.pw-nw;
		if(t+nh<s.ph)t=s.ph-nh;
		let css={};
		css[s.ln]=l;
		css[s.tn]=t;
		css['width']=w*sc;
		css['height']=h*sc;
		s.el.css(css);
	}
	return s;
}
xg.wheel=function($s,o){
	var el=$($s);
	el.off("mousewheel.xg-wheel").on("mousewheel.xg-wheel",function(e){
		e.preventDefault();
		var delta=e.originalEvent.wheelDelta||e.originalEvent.detail;
		if(o.zoom&&o.zoom.$s){
			var zoom=xg.zoom(o.zoom.$s,o.zoom);
			if(delta>0){
				zoom.zoom(0.1);
			}else{
				zoom.zoom(-0.1);
			}
		}
	});
	return xg;
}
xg.touch=function($s,o){
	var elem=$($s);
	var touch=('ontouchend' in document);
	o=$.extend({},{vdis:5,hdis:0,du:500,pdef:!1,stop:!1},(o||{}));
	function start(ev){
		var e=exy(ev).e;
		var time=xg.time();
		var initX=0;
		var initY=0;
		var startX=0;
		var startY=0;
		var diffX=0;
		var diffY=0;
		var endX=0;
		var endY=0;
		var startX=exy(ev).x;
		var startY=exy(ev).y;
		initX=startX;
		initY=startY;
		if(xg.isfun(o.start))o.start(rt(e,startX,startY),startX,startY);
		$(doc).unbind(event('move')).bind(event('move'),move);
		$(doc).one(event('end'),end);
		if(!touch){
			elem.one('click',click);
		}
		function move(ev){
			var e=exy(ev).e;
			var now=xg.time();
			endX=exy(ev).x;
			endY=exy(ev).y;
			diffX=endX-startX;
			diffY=endY-startY;
			if(now-time<o.du&&Math.abs(diffX)<o.hdis&&Math.abs(diffY)<o.vdis)return;
			if(o.pdef||!touch)e.preventDefault();
			if(o.stop)e.stopPropagation();
			if(xg.isfun(o.move))o.move(rt(e),diffX,diffY);
			startX=endX;
			startY=endY;
		}
		function end(ev){
			var e=exy(ev).e;
			diffX=initX-endX;
			diffY=initY-endY;
			if(xg.isfun(o.end))o.end(rt(e,diffX,diffY),diffX,diffY);
			$(doc).unbind(event('move'))
		}
		function click(ev){
			var r=true;
			if(xg.isfun(o.click))r=o.click(rt(exy(ev).e,exy(ev).x,exy(ev).y),exy(ev).x,exy(ev).y);
			return r;
		}
		function exy(ev){
			var e=ev.originalEvent||ev;
			var touch=(e.changedTouches||e.touches)?(e.changedTouches||e.touches)[0]:e;
			var x=touch.clientX;
			var y=touch.clientY;
			return {e:e,x:x,y:y};
		}
		function rt(e,x,y){
			return {
				e:e,
				x:x,y:y,
				start:{x:startX,y:startY},
				diff:{x:diffX,y:diffY},
				end:{x:endX,y:endY},
				init:{x:initX,y:initY}
			};
		}
	}
	function event(e){
		if(e=='start'){
			return touch?'touchstart.xg-touch':'mousedown.xg-touch';
		}else if(e=='move'){
			return touch?'touchmove.xg-touch':'mousemove.xg-touch';
		}
		return touch?'touchend.xg-touch':'mouseup.xg-touch';
	}
	elem.bind(event('start'),start);
	return xg;
}
var drag_ids=[];
xg.drag=function(){
	var target,id,draging;
	var id=drag_ids.length;
	drag_ids++;
	var o={
		s:'.xg-drag',
		item:'.xg-drag-item',
		btn:'.xg-drag-btn',
		layerclass:'xg-drag-layer',
		curclass:'xg-drag-cur',
		start:null,
		end:null
	};
	for(var i in arguments){
		let a=arguments[i];
		if(xg.isstr(a)){
			o.s=a;
		}else if(xg.isfun(a)){
			if(xg.isnull(o.start)){
				o.start=a;
			}else if(xg.isnull(o.end)){
				o.end=a;
			}
		}else if(a instanceof jQuery){
			o.s=a;
		}else if(xg.isobj(a)){
			o=$.extend({},o,a);
		}
	}
	if(!o.s||!$(o.s).length)return;
	$(o.s).find(o.item).each(function(){
		if($(this).find(o.btn).length){
			$(this).find(o.btn).unbind('mousedown.xgdrag',drag).bind('mousedown.xgdrag',drag);
		}else{
			$(this).attr('draggable','true');
			drag();
		}
	}).mouseleave(function(){
		if(!draging)$(o.s).find(o.item).removeAttr('draggable');
	});
	function drag(){
			draging=true;
			$(o.s).find(o.item).attr('draggable','true');
			$(doc).unbind('dragend.xgdrag').bind('dragend.xgdrag',function(e){
				var e=e.originalEvent;
				if(xg.isfun(o.end))o.end(target);
				$(o.s).find('.'+o.layerclass).remove();
				$(o.s).find('.'+o.curclass).removeClass(o.curclass);
			}).unbind('dragover.xgdrag').bind('dragover.xgdrag',function(e){
				var e=e.originalEvent;
				e.preventDefault();
				var dom=itemdom(e);
				$(o.s).find('.'+o.layerclass).remove();
				if(dom.attr('draggable')){
					if(dom.is(o.item+':last')){
						var y=e.clientY;
						if(dom.offset().top+dom.height()/2<y){
							dom.addClass(o.curclass).after(xg.newdiv(o.layerclass));
						}else{
							dom.addClass(o.curclass).before(xg.newdiv(o.layerclass));
						}
					}else{
						dom.addClass(o.curclass).before(xg.newdiv(o.layerclass));
					}
				}
			}).unbind('dragenter.xgdrag').bind('dragenter.xgdrag',function(e){
				var $that=$(this);
			}).unbind('dragleave.xgdrag').bind('dragleave.xgdrag',function(e){
				var e=e.originalEvent;
				var dom=itemdom(e);
			}).unbind('drop.xgdrag').bind('drop.xgdrag',function(e){
				var e=e.originalEvent;
				e.preventDefault();
				var id=e.dataTransfer.getData('text');
				var dom=itemdom(e);
				target=dom.attr('xg-drag-id');
				if(dom.attr('draggable'))$(o.s).find('.'+o.layerclass).before($('[xg-drag-id="'+id+'"]'));
			}).unbind('dragstart.xgdrag').bind('dragstart.xgdrag',function(e){
				var e=e.originalEvent;
				target=undefined;
				if($(e.target).attr('draggable')){
					id=$(e.target).attr('xg-drag-id');
					e.dataTransfer.setData('text',id);
					setTimeout(function(){
						if(xg.isfun(o.start))o.start(id);
					},10);
				}
			}).unbind('mouseup.xgdrag').bind('mouseup',function(){draging=false;});
	}
	function itemdom(e){
		if(!e||!e.target)return $();
		var dom=$(e.target);
		if(dom.hasClass('.'+o.layerclass)){
			dom=dom.next(o.item);
			if(!dom.length)dom=dom.prev(o.item);
		}else if(dom.closest(o.item).length){
			dom=dom.closest(o.item);
		}else if(dom.parents(o.item).length){
			dom=dom.parents(o.item);
		}
		return dom;
	}
	return $(o.s);
}
xg.lazy=function(dom,add){
	if(!$(dom).is('img')&&!$(dom).is('iframe')){
		xg.lazy($(dom).find('img,iframe'),add);
	}else{
		$(dom).each(function(){
			var src=$(dom).attr('xg-src');
			if(src){
				if(!$(this).attr('xg-lazy')){
					if(add)src=xg.urladd(src,'xg-random',Math.random());
					$(this).attr('src',src).attr('xg-lazy',1);
				}
			}
		});
	}
	return $(dom);
}
xg.unique=function(arr){
	var array=[];
	if(xg.isarr(arr)){
		for(var i=0;i<arr.length;i++){
			if(array.indexOf(arr[i])===-1){
				array.push(arr[i]);
			}
		}
	}
	return array;
}
xg.storage=function(key,value){
	if(xg.isunde(value)){
		return JSON.parse(localStorage.getItem(key));
	}
	if(xg.isnull(value)){
		return localStorage.removeItem(key);
	}
	return localStorage.setItem(key,JSON.stringify(value));
}

xg.noop=function(){};
win.xg=xg;
})(window,document,jQuery);

(function(win,doc,$){
function nav(){
	$('.xg-nav-toggler').unbind('click.nav').bind('click.nav',function(){
		$('.xg-nav').toggleClass('xg-nav-show');
	});
	$('.xg-nav-item-toggler').unbind('click.nav').bind('click.nav',function(){
		$(this).toggleClass('xg-nav-item-toggler-2').next().toggleClass('xg-n-hide');
	});
}
function vcode(){
	var v=$('.xg-vcode');
	v.each(function(){
		var index=v.index($(this));
		var e=$(this).parents('form').find('input.xg-vcode-email');
		var m=$(this).parents('form').find('input.xg-vcode-mobile');
		$(this).unbind('click.vcode').bind('click.vcode',function(){
			var account=null;
			if($(this).hasClass('xg-vcode-mobile')){
				if(!xg.ismobile(m.val())){
					xg.msg('请填写正确的手机号',2);
					return false;
				}else{
					var account=m.val();
				}
			}else if($(this).hasClass('xg-vcode-email')){
				if(!xg.isemail(e.val())){
					xg.msg('请填写正确的电子邮箱',2);
					return false;
				}else{
					var account=e.val();
				}
			}else if(xg.ismobile(m.val())){
				var account=m.val();
			}else if(xg.isemail(e.val())){
				var account=e.val();
			}else{
				xg.msg('请填写正确的手机号或电子邮箱',2);
				return false;
			}
			get(account,$(this).attr('xg-vcode-type'),index);
		});
	});
	var sent=false;
	function time(t,index){
		t--;
		if(t<=0){
			sent=false;
			v.eq(index).html('获取');
		}else{
			sent=true;
			v.eq(index).html(t);
			setTimeout(function(){time(t,index);},1000);
		}
	};
	function get(account,type,index){
		var w;
		xg.ajax.get(xg.url('base/imgcode'),{t:Math.random()},function(html){
			w=xg.win({
				title:'请输入图形验证码',
				cont:html,
				cclose:false,
				btns:{'确定':onok,'取消':function(){}}
			});
			$('#xg-msg-'+w).find('input').focus().keydown(function(e){
				if((e.keyCode||e.which||e.charCode)==13){
					onok();
				}
			});
		});
		function onok(){
			var imgcode=$('input[name="imgcode"]').val();
			if(!imgcode){
				xg.msg('请填写图形验证码',2);
				return false;
			}
			var get={act:"vcode",account:account,imgcode:imgcode,t:Math.random()};
			if(type==1)get["getpassword"]=1;
			if(type==2)get["authaccount"]=1;
			xg.ajax.get(xg.url("base/vcode"),get,function(data){
				if(data.ok===true){
					time(60,index);
					xg.close(w);
				}else if(data.ok<0){
					xg.err(data.msg);
				}else{
					$('#img-code-img').click();
					xg.msg(data.msg,2);
				}
			},"json");
			return false;
		}
	}
}
function clearinput(){
	$('[xg-clear-input]').unbind('click.clearinput').bind('click.clearinput',function(){
		$('[name="'+$(this).attr('xg-clear-input')+'"]').val('').change();
	});
}
function checkall(){
	$('.xg-checkall').unbind('click.checkall').bind('click.checkall',function(){
		var sel='.xg-checkbox';
		if($(this).attr('xg-checkall-sel-name'))sel=$(this).attr('xg-checkall-sel-name');
		if(this.checked){
			$(this.form).find(sel).each(function(){this.checked=true;});
		}else{
			$(this.form).find(sel).each(function(){this.checked=false;});
		}
	});
}
function device(){
	if(xg.device().mobile){
		$('body').addClass('xg-mobile');
	}else{
		$('body').removeClass('xg-mobile');
	}
	if(xg.device().android){
		$('body').addClass('xg-android');
	}else{
		$('body').removeClass('xg-android');
	}
	if(xg.device().ios){
		$('body').addClass('xg-ios');
	}else{
		$('body').removeClass('xg-ios');
	}
}
$(document).ready(function(){
	device();
	vcode();
	checkall();
	clearinput();
	nav();
	$(doc).on('DOMNodeInserted',function(){
		vcode();
		checkall();
		clearinput();
		nav();
	});
});
})(window,document,jQuery);


(function(win,doc,$){
xg.def('hooks',function(){
	return function(name,key,fun){
	if(name=='run')return xg.hooks;
	if(xg.isfun(key)){
		fun=key;
		key=name;
	}
	if(!xg.isobj(xg.hooks[name]))xg.hooks[name]={};
	xg.hooks[name][key]=fun;
	return xg.hooks;
};
});
xg.hooks.run=function(name,data){
	if(!xg.isobj(xg.hooks[name])){
		xg.hooks[name]={};
	}
	var result={};
	for(let key in xg.hooks[name]){
		if(xg.isfun(xg.hooks[name][key])){
			let args=[];
			for(let i=1;i<arguments.length;i++){
				args.push(arguments[i]);
			}
			let rt=xg.hooks[name][key].apply(null,args);
			if(!xg.isunde(rt)){
				return rt;
			}
		}
	}
	return result;
}
})(window,document,jQuery);


(function(win,doc,$){
xg.def('table',function(){
	function datalink(){
		const name=$(this).attr('xg-table-data-link');
		const link=$(this).attr('href');
		if(name&&link){
			win.xg_table_list[name].reload(link);
			return false;
		}
	}
	return function(sel,o){
		function TB(sel,o){
			const s=this;
			s.o=o||{};
			s.t=$(sel);
			if(s.o.name)win.xg_table_list[s.o.name]=s;
			$('[xg-table-data-link]').unbind('click.xg-table-data-link').bind('click.xg-table-data-link',datalink);
		};
		TB.prototype.done=function(callback){
			const s=this;
			s.o=callback;
			return s;
		};
		TB.prototype.reload=function(url){
			const s=this;
			if(!url)url=xg.url();
			s.load(url,1);
		};
		TB.prototype.page=function(page,pagesize){
			const s=this;
		};
		TB.prototype.name=function(name){
			const s=this;
			win.xg_table_list[name]=s;
			return s;
		};
		TB.prototype.pagehtml=function(html){
			const s=this;
			const id='table-page-'+xg.randstr();
			s.t.append(`<tr xg-table-create="1"><td colspan="100" id="${id}">${html}</td></tr>`);
			$('#'+id).find('a').click(function(){
				const url=$(this).attr('href');
				s.reload(url);
				xg.reurl(url);
				return false;
			});
		};
		TB.prototype.load=function(url,isreload){
			const s=this;
			if(isreload!==1||!s.t.find('[xg-table-create]').length)s.t.find('.xg-table-loading').show();
			s.t.find('.xg-table-nors').hide();
			xg.ajax.get(url,function(data){
				if(s.t.find('.xg-table-loading').length)s.t.find('.xg-table-loading').hide();
				if(data.ok===false||data.ok<0){
					xg.err(data.msg);
					return;
				}
				s.create(data.data,isreload);
				if(data.pagehtml)s.pagehtml(data.pagehtml);
				$('[xg-table-data-link]').unbind('click.xg-table-data-link').bind('click.xg-table-data-link',datalink);
				$('.xg-checkall').prop('checked',false);
			});
			return s;
		};
		TB.prototype.create=function(data,isreload){
			const s=this;
			var keys=s.keys||s.o.keys;
			if(keys){
				$.each(keys,function(name,title){
					var $tr=$('<tr></tr>').attr('xg-table-create',1);
					$tr.append($('<th></th>').html(title));
					s.t.append($tr);
				});	
			}else{
				var keys=[];
				s.t.find('th').each(function(){
					var $th=$(this);
					if($th.attr('xg-table-item-order')){
						let input=xg.parseurl().params;
						let order='desc';
						if(input){
							if(input.orderby==$th.attr('xg-table-item-order') && input.order=='desc'){
								order='asc';
							}
						}
						let $link=$(`<a class="table-order-link xg-inblock xg-ml-1 xg-icon ${order=='desc'?'xg-icon-arrow-down':'xg-icon-arrow-up'} xg-em-08" href="${xg.url(null,{orderby:$th.attr('xg-table-item-order'),order:order})}"></a>`)
						.click(function(){
							const url=$(this).attr('href');
							s.reload(url);
							xg.reurl(url);
							return false;
						});
						$th.find('.table-order-link').remove();
						$th.append($link);
					}
					if($th.attr('xg-table-item-key')){
						let data={type:'string',key:$th.attr('xg-table-item-key')};
						if($th.attr('xg-table-link-class'))data['link_class']=$th.attr('xg-table-link-class');
						if($th.attr('xg-table-link')){
							data['link']=$th.attr('xg-table-link');
						}else if($th.attr('xg-table-blank-link')){
							data['link']=$th.attr('xg-table-blank-link');
							data['blank']=1;
						}else if($th.attr('xg-table-admin-tab-link')){
							data['link']=$th.attr('xg-table-admin-tab-link');
							data['admin_tab']=1;
							if($th.attr('xg-table-admin-tab-name'))data['admin_tab_name']=$th.attr('xg-table-admin-tab-name');
							if($th.attr('xg-table-admin-tab-title'))data['admin_tab_name']=$th.attr('xg-table-admin-tab-title');
						}
						keys.push(data);
					}else if($th.attr('xg-table-checkbox')){
						keys.push({type:'checkbox',key:$th.attr('xg-table-checkbox')});
					}else if($th.attr('xg-table-bool')){
						keys.push({type:'bool',key:$th.attr('xg-table-bool')});
					}else if($th.attr('xg-table-img')){
						keys.push({type:'img',key:$th.attr('xg-table-img')});
					}else if($th.attr('xg-table-switch')){
						keys.push({type:'switch',data:$th.attr('xg-table-switch-data'),key:$th.attr('xg-table-switch')});
					}
				});
			}
			data=xg.obj2arr(data);
			if(data&&data.length){
				s.t.find('[xg-table-create]').attr('xg-table-create-old',1);
				var rt=xg.hooks.run('table-before-create',{data,keys});
				$.each(data,function(index,item){
					var $tr=$('<tr></tr>').attr('xg-table-create',1);
					if(s.o.tr2)$tr.addClass('tr2').attr('tr2',1);
					for(var i in keys){
						if(keys[i].type=='string'){
							let html=item[keys[i].key];
							if(keys[i].link){
								let link=item[keys[i].link]||keys[i].link;
								html=$(`<a href="${link}">${html}</a>`);
								if(keys[i].admin_tab)html.addClass('xg-admin-tab-link');
								if(keys[i].link_class)html.addClass(keys[i].link_class);
								if(keys[i].admin_tab_name)html.attr('xg-tab-title',item[keys[i].admin_tab_name]||keys[i].admin_tab_name);
								if(keys[i].blank)html.attr('target','_blank');
							}
							$tr.append($('<td></td>').append(html));
						}
						if(keys[i].type=='checkbox'){
							$tr.append($('<td></td>').html('<input class="xg-checkbox" type="checkbox" name="'+keys[i]['key']+'[]" value="'+item[keys[i]['key']]+'">'));
						}
						if(keys[i].type=='bool'){
							$tr.append($('<td></td>').html(item[keys[i]['key']]>0?'<span class="xg-green">是</span>':'<span class="xg-red">否</span>'));
						}
						if(keys[i].type=='img'){
							$tr.append($('<td></td>').html(`<img width="80" src="${item[keys[i]['key']]}">`));
						}
						if(keys[i].type=='switch'){
							let switchdata=[];
							if(keys[i].data){
								switchdata=keys[i].data.split(',');
							}
							const urldata={switch:keys[i].key};
							for(let i2 in switchdata){
								urldata[switchdata[i2]]=item[switchdata[i2]];
							}
							$tr.append($('<td></td>').html(`<a href="${xg.url('xswitch',urldata)}" class="xg-icon xg-switch" xg-status="${item[keys[i].key]?1:0}"></a>`));
						}
						s.t.append($tr);
					}
				});
				s.t.find('[xg-table-create-old]').hide().remove();
				if(xg.isfun(s.o.created)){
					s.o.created();
				}
				var rt=xg.hooks.run('table-after-create',{data,keys});
			}else{
				s.t.find('[xg-table-create]').hide().remove();
				s.t.find('.xg-table-nors').show();
			}
			if(xg.isfun(s.o))s.o();
			return s;
		};
		return new TB(sel,o);
	}
});
xg.table.get=function(name){
	return win.xg_table_list[name];
}
win.xg_table_list={};
function table(){//TODO
	$('.xg-table td,.xg-table th').each(function(){
		if(!$(this).children().first().is('div'))$(this).html('<div>'+$(this).html()+'</div>');
		if(xg.device().mobile&&$(this).attr('width'))$(this).css({width:xg.px($(this).attr('width'))});
	});
}
})(window,document,jQuery);


(function(win,doc,$){
var image={};
image.create=function(src,callback){
	var img = new Image();
	img.src = src;
	return image.load(img,callback);
}
image.size=function(src,callback){
	return image.create(src,function(img){
		return callback({width:img.width,height:img.height});
	});
}
image.resize=function($s,o){
	o=$.extend({},{scale:false,srcname:''},xg.isobj(o)?o:{scale:o});
	if(!$s)$s=$(this);
	$($s).each(function(index,element){
		var $this=$(this);
		if($this.is('img')){
			if($this.attr('mode')){
				var mode = $this.attr('mode');
				switch (mode) {
					case 'widthFix':
					$this.css({
						'width': '100%',
						'height': 'auto',
						'object-fit': 'cover',
						'object-position': 'top left'
					});
					break;
				case 'heightFix':
					$this.css({
						'width': 'auto',
						'height': '100%',
						'object-fit': 'cover',
						'object-position': 'top left'
					});
					break;
				case 'aspectFit':
					$this.css({
						'max-width': '100%',
						'max-height': '100%',
						'object-fit': 'contain',
						'object-position': 'center'
					});
					break;
				case 'aspectFill':
					$this.css({
						'width': '100%',
						'height': '100%',
						'object-fit': 'cover',
						'object-position': 'center'
					});
					break;
				case 'scaleToFill':
					$this.css({
						'width': '100%',
						'height': '100%',
						'object-fit': 'fill',
						'object-position': 'center'
					});
					break;
				default:
					$this.css({
						'width': 'auto',
						'height': 'auto',
						'object-fit': 'contain',
						'object-position': 'center'
					});
					break;
				}
				$this.attr('xg-img-resize','mode');
			}else if(!$this.attr('xg-img-resize')){
				$this.attr('xg-img-resize',o.scale?'scale':'cut');
				var $w=$this.width();
				var $h=$this.height();
				if(o.srcname){
					var src=$this.attr(o.srcname);
					if(src){
						image.load(src,function(){
							var nowsrc=$this.attr('src');
							if(nowsrc==src){
								image.create(src,function(img){resize($this,$w,$h,img.width,img.height);});
							}
						});
					}else{
						$this.removeAttr('xg-img-resize');
					}
				}else{
					var src=$this.attr('src');
					if(src){
						image.create(src,function(img){resize($this,$w,$h,img.width,img.height);});
					}else{
						$this.removeAttr('xg-img-resize');
					}
				}
			}
		}else{
			$($s).find('img').each(function(){
				if(!$(this).attr('xg-img-resize'))xg.img.resize($(this),o);
			});
		}
	});
	function resize($this,$w,$h,w,h){
		var wp=w/$w;
		var hp=h/$h;
		var css={};
		var abs=$this.css('position')=='absolute';
		if((o.scale&&wp>hp)||(!o.scale&&wp<hp)){
			var newwidth=w/wp;
			var newheight=h/wp;
			css[abs?'top':'marginTop']=($h-newheight)/2;
		}else{
			var newwidth=w/hp;
			var newheight=h/hp;
			css[abs?'left':'marginLeft']=($w-newwidth)/2;
		}
		$this.width(newwidth).height(newheight).css(css);
	}
}
$.fn.imgsize=image.resize;
image.load=function(img,callback){
	if(!xg.isfun(callback))callback=xg.noop;
	$(img).bind('load',function(){callback($(img)[0]);});
	if($(img)[0].complete){
		callback($(img)[0]);
	}
	return $(img);
}
xg.def('img',function(){
	return image;
});
})(window,document,jQuery);


(function(win,doc,$){
xg.def('tab',function(){
	var T=function(name){
		var s=this;
		win.xg_tab_list=win.xg_tab_list||{};
		win.xg_tab_list[name]=s;
		s.o={};
	}
	T.prototype.i=0
	T.prototype.add=function(title,content){
		var s=this,i=s.i;
		s.title.append($('<li xg-id="'+i+'"></li>').append(title).append('<div class="xg-tab-title-close"><a class="xg-close"></a></div>'));
		s.content.append($('<li xg-id="'+i+'"></li>').append(content));
		bind(s,i);
		s.i++;
		s.more();
		if(xg.isfun(s.o.add))s.o.add(i,s.title.children('[xg-id="'+i+'"]'),s.content.children('[xg-id="'+i+'"]'));
		return i;
	}
	T.prototype.option=function(o,v){
		var s=this;
		if(xg.isobj(o)){
			s.o=$.extend({},s.o,o);
		}else{
			s.o[o]=v;
		}
	}
	T.prototype.onshow=function(f){
		var s=this;
		s.o.show=f;
		return s;
	}
	T.prototype.more=function(){
		var s=this;
		if(getmore(s).length==0){
			s.morebtn.hide();
			if(s.morepop)s.morepop.remove();
		}else{
			s.morebtn.show();
		}
		if(xg.isfun(s.o.more))s.o.more(getmore(s).length>0);
	}
	T.prototype.remove=function(i){
		var s=this;
		var isthis=s.title.children('[xg-id="'+i+'"]').hasClass('xg-this');
		s.title.children('[xg-id="'+i+'"]').remove();
		s.content.children('[xg-id="'+i+'"]').remove();
		if(isthis)s.show(s.title.children('li').last().attr('xg-id'));
		if(xg.isfun(s.o.remove))s.o.remove(i,isthis);
		s.more();
		return s;
	}
	T.prototype.show=function(i){
		var s=this;
		if(xg.isunde(i)){
			if(s.title.children('li.xg-this').length>0){
				return;
			}
		}
		if(i)s.events[i].show();
		s.title.children('li').removeClass('xg-this').filter('[xg-id='+i+']').addClass('xg-this');
		s.content.children('li').removeClass('xg-this').filter('[xg-id='+i+']').addClass('xg-this');
		if(xg.isfun(s.o.show))s.o.show(i,s.title.children('[xg-id="'+i+'"]'),s.content.children('[xg-id="'+i+'"]'));
		return s;
	}
	T.prototype.iframe=function(i,t){
		var s=this;
		var iframe=s.content.children('li[xg-id="'+i+'"]').find('iframe');
		if(iframe.length){
			if(t==1)return iframe;
			if(t==2)return iframe[0];
			return iframe[0].contentWindow;
		}
	}
	T.prototype.events=[];
	T.prototype.event=function(i){
		var s=this,e=s.events[i];
		if(!e){
			var e=new event();
			s.events[i]=e;
		}
		return e;
	}
	T.prototype.bind=function(title,content,more){
		var s=this;
		s.title=$(title);
		s.content=$(content);
		s.morebtn=$(more);
		s.show();
		s.title.children('li').each(function(){
			bind(s,$(this).attr('xg-id'));
			s.i=xg.int($(this).attr('xg-id'))+1;
		});
		s.morebtn.click(function(){
			var more=getmore(s);
			s.morepop=xg.pop().ref($(this),more,{alignleft:1,alignbottom:1,class:'xg-tab-more-title'}).up();
		});
		return s;
	}
	function event(){}
	event.prototype.events={show:[]};
	event.prototype.onshow=function(f){
		this.events.show.push(f);
	}
	event.prototype.show=function(){
		for(var i in this.events.show){
			this.events.show[i]();
		}
	}
	function getmore(s){
		let more=$();
		s.title.children('li').each(function(){
			if($(this).offset().left+$(this).width()>s.title.offset().left+s.title.width()){
				more.push($(this).clone(true)[0]);
			}
		});
		more.find('.xg-close').click(function(e){
			e.stopPropagation();
			var id=$(this).parent().attr('xg-id');
			s.remove(id);
			$(this).closest('li').remove();
			//s.morepop.remove();
		});
		if(more.length)more=xg.newdiv('xg-tab-title xg-tab-show-close').append(more);
		return more;
	}
	function bind(s,i){
		s.event(i);
		s.title.children('[xg-id="'+i+'"]').mousedown(function(e){
			e.tabid=i;
			e.tabtit=s.title.children('[xg-id="'+i+'"]');
			e.tabcont=s.content.children('[xg-id="'+i+'"]');
			if((e.button===0||e.button===1)&&xg.isfun(s.o.mouse))var rt=s.o.mouse(e);
			e.preventDefault();
		}).contextmenu(function(e){
			e=e||{};
			e.button=2;
			e.tabid=i;
			e.tabtit=s.title.children('[xg-id="'+i+'"]');
			e.tabcont=s.content.children('[xg-id="'+i+'"]');
			setTimeout(function(){if(s.morepop)s.morepop.remove();},10);
			if(xg.isfun(s.o.mouse))return s.o.mouse(e);
		}).click(function(e){
			s.show(i);
			setTimeout(function(){if(s.morepop)s.morepop.remove();},10);
			e=e||{};
			e.button=0;
			e.tabid=i;
			e.tabtit=s.title.children('[xg-id="'+i+'"]');
			e.tabcont=s.content.children('[xg-id="'+i+'"]');
			if(xg.isfun(s.o.mouse))return s.o.mouse(e);
		}).dblclick(function(e){
			e=e||{};
			e.button=3;
			e.tabid=i;
			e.tabtit=s.title.children('[xg-id="'+i+'"]');
			e.tabcont=s.content.children('[xg-id="'+i+'"]');
			if(xg.isfun(s.o.mouse))return s.o.mouse(e);
		}).find('.xg-close').click(function(){
			s.remove(i);
			s.more();
		});
	}
	return function(name){
		if(win.xg_tab_list&&win.xg_tab_list[name])return win.xg_tab_list[name];
		return new T(name);
	};
});
xg.tab.get=function(name){
	if(!win.xg_tab_list)return;
	var s=win.xg_tab_list[name];
	if(!s)xg.error('没有此名称的标签卡');
	return s;
}
xg.tab.mouse={left:0,middle:1,right:2,double:3};
})(window,document,jQuery);


(function(win,doc,$){
xg.ajax={};
$.each(['get','post'],function(i,method){
	xg.ajax[method]=function(url,data,callback,type,error){
		if(xg.isfun(data)){
			type=type||callback;
			callback=data;
			data=undefined;
		}
		if(!xg.isfun(error)&&xg.isfun(callback)){
			var error=function(a,b,c){
				callback(a.responseJSON||a.responseText,b,c);
			}
		}
		return $.ajax({url:url,type:method,dataType:type,data:data,success:callback,error:error,complete:function(){}});
	};
});
$(document).ready(function(){
	function switchf(){
		$('.xg-switch').unbind('click.xg-switch').bind('click.xg-switch',function(){
			var target,that=this;
			if(xg.hooks.run('switch',that)===false)return false;
			if(xg.hooks.run('switch-before',that)===false)return false;
			if((target=$(that).attr('xg-url'))||(target=$(that).attr('href'))){
				var status=$(that).attr('xg-status');
				var newstatus=(status>0?0:1);
				xg.ajax.get(target,{status:newstatus,t:Math.random()},function(data){
					if(xg.hooks.run('switch-after',that,data)===false)return false;
					if(data.ok){
						$(that).attr('xg-status',data.status);
						if(data.msg)xg.ok(data.msg);
					}else if(data.msg){
						xg.msg(data.msg,3);
					}
				},'json');
			}
			return false;
		});
		$('.xg-ajax-get-status').unbind('click.xg-switch').bind('click.xg-switch',function(){
			var target,that=this;
			if((target=$(that).attr('xg-url'))||(target=$(that).attr('href'))){
				xg.ajax.get(target,{t:Math.random()},function(data){
					if(data.status===0||data.status===-1){
						$(that).text('启用').removeClass('xg-a-status-1').addClass('xg-a-status-0');
					}else{
						$(that).text('停用').removeClass('xg-a-status-0').addClass('xg-a-status-1');
					}
					xg.msg(data);
				},'json');
			}
			return false;
		});
		$('.xg-ajax-submit button[type="submit"],.xg-ajax-submit input[type="submit"]').unbind('click.xg-submit').bind('click.xg-submit',function(){
			$this=$(this);
			window.xg_submit_no_refresh=false;
			if($this.hasClass('xg-not-refresh')){
				window.xg_submit_no_refresh=true;
			}
		});
		$('.xg-ajax-submit').unbind('submit.xg-submit').bind('submit.xg-submit',function(){
			var that=this;
			if($(that).attr('xg-confirm')){
				xg.confirm($(that).attr('xg-confirm'),function(){
					submit();
				});
			}else{
				submit();
			}
			function submit(action){
				if(!action)action=$(that).attr('action')||xg.url();
				if($(that).hasClass('xg-show-loading'))xg.loading();
				xg.ajax.post(action,$(that).serialize(),function(data){
					if(data.confirm){
						xg.confirm(data.confirm,function(){submit(confirm_yes(action,data));});
						return;
					}
					if($(that).hasClass('xg-show-loading'))xg.loading(0);
					if(xg.hooks.run('ajax-submited',data)===false)return false;
					xg.msg(data,function(){
						if(data.goto){
							if(!window.xg_submit_no_refresh)location.href=data.goto;
						}else if(data.ok===true){
							if($(that).hasClass('xg-ajax-parent-reload')){
								parent.location.reload();
							}else if(!$(that).hasClass('xg-ajax-not-reload')&&!window.xg_submit_no_refresh){
								location.reload();
							}
						}
					});
				},'json');
			}
			return false;
		});
		$('.xg-ajax-get').unbind('click.xg-ajax-get').bind('click.xg-ajax-get',function(){
			var target,that=this;
			xg.confirm(($(that).attr('xg-confirm')?$(that).attr('xg-confirm'):'确定要执行此操作吗？'),function(){
				if((target=$(that).attr('xg-url'))||(target=$(that).attr('href'))){
					get(target);
				}
			});
			function get(target){
				xg.ajax.get(target,{t:Math.random()},function(data){
					if(data.confirm){
						xg.confirm(data.confirm,function(){get(confirm_yes(target,data));});
						return;
					}
					if(xg.hooks.run('ajax-geted',that,data)===false)return false;
					if($(that).attr('xg-table-reload')){
						const table=xg.table.get($(that).attr('xg-table-reload'));
						if(table&&data.ok===true){
							if(data.msg)xg.ok(data.msg);
							table.reload();
							return false;
						}
					}
					xg.msg(data,function(){
						if(xg.hooks.run('ajax-geted',that,data)===false)return false;
						if($(that).hasClass('xg-ajax-reload')){
							location.reload();
						}else{
							if(data.goto){
								if(data.wait===0){
									get(data.goto)
								}else{
									location.href=data.goto;
								}
							}else{
								if(data.ok===true){
									if(data.action=='delete'||data.action=='softdelete'||data.action=='restore'){
										$(that).closest('tr').remove();
									}else if(!$(that).hasClass('xg-ajax-not-reload')){
										location.reload();
									}
								}
							}
						}
					});
				},'json');
			}
			return false;
		});
		$('.xg-btn-action').unbind('click.xg-btn-action').bind('click.xg-btn-action',function(){
			var that=this;
			if(xg.hooks.run('btn-action',that)===false)return false;
			if(xg.hooks.run('btn-action-before',that)===false)return false;
			if($(that).hasClass('xg-need-select')&&$(that.form).find('.xg-checkbox:checked').length==0){
				xg.msg('请先选择信息',3);
				return false;
			}
			$(that.form).attr('action',$(that).attr('action'));
			var msg=$(that).attr('xg-confirm');
			if(!msg)msg='';
			if(msg){
				xg.confirm(msg,function(){
					submit();
				});
			}else{
				submit();
			}
			return false;
			function submit(action){
				if(!action)action=$(that).attr('xg-action');
				xg.ajax.post(action,$(that.form).serialize(),function(data){
					if(data.confirm){
						xg.confirm(data.confirm,function(){submit(confirm_yes(action,data));});
						return;
					}
					if($(that).attr('xg-table-reload')){
						const table=xg.table.get($(that).attr('xg-table-reload'));
						if(table&&data.ok===true){
							if(data.msg)xg.ok(data.msg);
							table.reload();
							return false;
						}
					}
					xg.msg(data,function(){
						if($(that).attr('xg-btn-table-reload')){
							if(data.ok===false){
								
							}else if(data.ok===true){
								if(data.goto){
									submit(data.goto);
								}else{
									let table=xg.table.get($(that).attr('xg-btn-table-reload'));
									if(data.msg)xg.ok(data.msg);
									if(table)table.reload();
								}
							}
						}else if($(that).hasClass('xg-ajax-reload')){
							location.reload();
						}else{
							if(data.goto){
								location.href=data.goto;
							}else{
								if(data.ok===true){
									if($(that).hasClass('xg-ajax-not-reload')){
										if($(that).hasClass('xg-uncheck-checked')){
											$('.xg-checkbox').prop('checked',false);
										}
									}else{
										location.reload();
									}
								}
							}
						}
					});
				},'json');
			}
		});
	}
	switchf();
	$(doc).on('DOMNodeInserted',switchf);
});
function confirm_yes(url,data){
	url=xg.urladd(url,'xg-yes',data['confirm'],1);
	for(var i in data['confirmed']){
		url=xg.urladd(url,'xg-confirmed[]',data['confirmed'][i],2);
	}
	return url;
}
})(window,document,jQuery);


(function(win,doc,$){
function calculateOffset(element) {
	var position = { left: 0, top: 0 };
	if (element.css('position') === 'relative') {
		return position;
	}
	if (element.css('position') === 'fixed') {
		return position;
	}
	if (element.length) {
		position = element.offset();
		//position.left += parseInt(element.css('margin-left'), 10);
		//position.top += parseInt(element.css('margin-top'), 10);
		// var parent = element.parent();
		// if(parent.hasClass('blocks'))parent=parent.parent().parent();
		// if (parent.length && !parent.is('body')) {
		// 	var parentPosition = calculateOffset(parent);
		// 	position.left += parentPosition.left;
		// 	position.top += parentPosition.top;
		// }
	}

	return position;
}
xg.def('pop',function(){
	const P=function(){
		const s=this;
		s.o={};
		for(var i in arguments){
			let a=arguments[i];
			if(xg.isstr(a)){
				s.o.html=a;
			}else if(a instanceof jQuery){
				s.o.html=a;
			}else if(xg.isobj(a)){
				s.o=$.extend({},s.o,a);
			}
		}
		if(!xg.iseobj(s.o))this.up();
	};
	P.prototype.up=function(){
		const s=this;
		for(var i in arguments){
			let a=arguments[i];
			if(xg.isstr(a)){
				s.o.html=a;
			}else if(a instanceof jQuery){
				s.o.html=a;
			}else if(xg.isobj(a)){
				s.o=$.extend({},s.o,a);
			}
		}
		win.xg_popup_ids=win.xg_popup_ids||[];
		var id=win.xg_popup_ids.length;
		s.id=id;
		win.xg_popup_ids.push(id);
		css=xg.obj.bykey(s.o,['width','height','left','top','maxWidth','maxHeight','minWidth','minHeight']);
		s.class=s.class||'';
		s.d=xg.newdiv('xg-popup xg-popup-'+id).addClass(s.o.class).css(css).append(s.o.html).mouseenter(function(){
			s.enter=true;
		}).mouseleave(function(){
			s.enter=false;
		});
		$('body').append(s.d);
		return s;
	};
	P.prototype.show=function(){
		var s=this;
		s.d.show();
		return s;
	};
	P.prototype.close=function(){
		var s=this;
		s.d.hide();
		return s;
	};
	P.prototype.hide=function(){
		var s=this;
		s.d.hide();
		return s;
	};
	P.prototype.ref=function($s){
		var s=this;
		s.o={};
		for(var i in arguments){
			if(i===0)continue;
			let a=arguments[i];
			if(a instanceof jQuery){
				s.o.html=a;
			}else if(xg.isstr(a)){
				s.o.html=a;
			}else if(xg.isobj(a)){
				s.o=$.extend({},s.o,a);
			}
		}
		function recount(){
			getcss();
			var css=xg.obj.bykey(s.o,['width','height','left','top','maxWidth','maxHeight','minWidth','minHeight']);
			s.d.css(css);
		}
		$(document).ready(function(){
			recount();
			setInterval(function(){
				if(s.d.is(':visible')){
					recount();
				}
			},20);
			$(win).resize(recount);
		});
		function getcss(){
			var offset=calculateOffset($($s));
			var x = offset.left;
			var y = offset.top;
			var l=x+$($s).outerWidth();
			var t=y+$($s).outerHeight();
			if(s.o.alignright)s.o.left=l;
			if(s.o.alignbottom)s.o.top=t;
			if(s.o.same)s.o.samew=s.o.sameh=s.o.alignleft=s.o.aligntop=1;
			if(s.o.minw)s.o.minWidth=$($s).outerWidth();
			if(s.o.minh)s.o.minHeight=$($s).outerHeight();
			if(s.o.maxw)s.o.maxWidth=$($s).outerWidth();
			if(s.o.maxh)s.o.maxHeight=$($s).outerHeight();
			if(s.o.samew)s.o.width=$($s).outerWidth();
			if(s.o.sameh)s.o.height=$($s).outerHeight();
			if(s.o.alignleft)s.o.left=x;
			if(s.o.aligntop)s.o.top=y;
		}
		return s.up(s.o);
	};
	P.prototype.remove=function(){
		var s=this;
		s.d.remove();
		return s;
	};
	var rt=function(){
		return function(o){
			return new P(o);
		};
	};
	return rt();
});
})(window,document,jQuery);


(function(win,doc,$){
	xg.iframe_link_fun=function(){
		var $this=$(this);
		var title=$this.attr('xg-iframe-title')||$this.text();
		var area=$this.attr('xg-iframe-area')||$this.attr('xg-iframe-size');
		let width=550,height=400;
		if(area){
			area=area.split(',');
			if(area[0]>0)width=area[0];
			if(area[1]>0)height=area[1];
		}
		var url=$this.attr('xg-iframe-url')||$this.attr('href');
		let target=win;
		if(parent!==self&&parent.pagelock)target=parent;
		target.xg.iframe({src:url,title,width,height});
		return false;
	};
	function iframe_fun(){
		$('.xg-iframe-link').unbind('click.xg-iframe-link').bind('click.xg-iframe-link',xg.iframe_link_fun);
	}
	iframe_fun();
	$(doc).on('DOMNodeInserted',iframe_fun);
})(window,document,jQuery);


(function(win,doc,$){
xg.def('admin',function(){
$(document).ready(function(){
	xg.admin_tab_fun=function(){
		var $this=$(this);
		var title=$this.attr('xg-tab-title')||$this.text();
		var url=$this.attr('xg-tab-url')||$this.attr('href');
		var autoclose=$this.attr('xg-tab-auto-close');
		top.admin_tab(title,url,autoclose?1:0);
		return false;
	};
	if(xg.device().mobile){
		init_admin_mobile();
	}else{
		init_admin_pc();
	}
	init_admin_menu_toggle();
});
function init_admin_mobile(){
	$('.xg-admin-iframe').attr('src',xg.url('main/index'));
	$('.xg-admin-tab-link').each(function(){
		var link=$(this).attr('href');
		$(this).attr('href',url(link,{ismobile:1}));
	}).unbind('click.xg-admin-menu-show').bind('click.xg-admin-menu-show',function(){
		$('.xg-admin').removeClass('xg-admin-menu-show');
		return true;
	});
};
function init_admin_menu_toggle(){
	$('.xg-admin-menu-toggle').unbind('click.xg-admin-menu').bind('click.xg-admin-menu',function(){
		$('.xg-admin').toggleClass('xg-admin-menu-show');
	});
};
function init_admin_pc(){
	function tab_fun(){
		$('.xg-admin-tab-link').unbind('click.xg-admin-tab').bind('click.xg-admin-tab',xg.admin_tab_fun);
	}
	tab_fun();
	$(doc).on('DOMNodeInserted',tab_fun);
	$('.xg-admin-menu dt').click(function(){
		var $this=$(this);
		var opened=$this.closest('li').hasClass('xg-open');
		$('.xg-admin-menu li').removeClass('xg-open');
		if(!opened){
			$this.parents('li').addClass('xg-open');
			if($this.closest('li').attr('xg-admin-menu-group'))xg.storage('xg-admin-menu-group',$this.closest('li').attr('xg-admin-menu-group'));
		}else{
			xg.storage('xg-admin-menu-group',null);
		}
		return false;
	});
	var opened=xg.storage('xg-admin-menu-group');
	if(opened){
		$('.xg-admin-menu li').removeClass('xg-open');
		$('[xg-admin-menu-group="'+xg.storage('xg-admin-menu-group')+'"]').addClass('xg-open');
	}
	if($('#xg-admin-tab-content').length==0)return;
	xg.mod('tab',function(){
		var tab=xg.tab('xg-admin').bind('#xg-admin-tab-title','#xg-admin-tab-content','#xg-admin-tab-more').option({
			remove:function(id){
				window.admin_tab_count=xg.tab.get('xg-admin').title.children('li').length;
				if(window.admin_tab_count<=0){
					show_index_page();
				}
			},show:function(id){
			},more:function(o){
			},mouse:function(e){
				if(e.button===xg.tab.mouse.double){
					xg.tab.get('xg-admin').remove(e.tabid);
					xg.tab.get('xg-admin').show();
					return false;
				}else if(e.button===xg.tab.mouse.middle){
					xg.tab.get('xg-admin').show(e.tabid);
					let dom=e.tabcont.find('iframe');
					if(dom)dom.attr('src',xg.uniqurl(dom.attr('src')));
					return false;
				}else if(e.button===xg.tab.mouse.right){
					let stuck=xg.storage('xg-admin-tab-stuck')||{};
					let name=$(e.tabtit).text();
					let link=$(e.tabcont).find('iframe').attr('src');
					$0=xg.li('xg-pd-1 xg-hover-black-1 xg-radius-2').html('关闭标签').click(function(){
						xg.tab.get('xg-admin').remove(e.tabid);
					})
					$1=xg.li('xg-pd-1 xg-hover-black-1 xg-radius-2').html('刷新标签').click(function(){
						xg.tab.get('xg-admin').show(e.tabid);
						if($(e.tabcont).find('iframe').length&&$(e.tabcont).find('iframe')[0].contentWindow){
							$(e.tabcont).find('iframe')[0].contentWindow.location.reload();
						}
					})
					let $2;
					if(name!='管理首页'){
						if(stuck[name]&&stuck[name]==link){
							$2=xg.li('xg-pd-1 xg-hover-black-1 xg-radius-2').html('取消固定').click(function(){
								delete stuck[name];
								xg.storage('xg-admin-tab-stuck',stuck);
							})
						}else{
							$2=xg.li('xg-pd-1 xg-hover-black-1 xg-radius-2').html('固定标签').click(function(){
								if(stuck[name]&&stuck[name]==link)return;
								stuck[name]=link;
								xg.storage('xg-admin-tab-stuck',stuck);
							})
						}
					}
					$3=xg.li('xg-pd-1 xg-hover-black-1 xg-radius-2').html('关闭左侧').click(function(){
						for(let i=0;i<e.tabid;i++){
							xg.tab.get('xg-admin').remove(i);
						}
						xg.tab.get('xg-admin').show();
					});
					$4=xg.li('xg-pd-1 xg-hover-black-1 xg-radius-2').html('关闭右侧').click(function(){
						let last=xg.int(e.tabtit.siblings().addBack().last().attr('xg-id'));
						for(let i=e.tabid+1;i<=last;i++){
							xg.tab.get('xg-admin').remove(i);
						}
						xg.tab.get('xg-admin').show();
					});
					$5=xg.li('xg-pd-1 xg-hover-black-1 xg-radius-2').html('关闭其他').click(function(){
						let last=xg.int(e.tabtit.siblings().addBack().last().attr('xg-id'));
						for(let i=0;i<=last;i++){
							if(i!=e.tabid)xg.tab.get('xg-admin').remove(i);
						}
						xg.tab.get('xg-admin').show();
					});
					$6=xg.li('xg-pd-1 xg-hover-black-1 xg-radius-2').html('关闭所有').click(function(){
						let last=xg.int(e.tabtit.siblings().addBack().last().attr('xg-id'));
						for(let i=0;i<=last;i++){
							xg.tab.get('xg-admin').remove(i);
						}
						xg.tab.get('xg-admin').show();
					});
					let menu=xg.ul().append($0,$1,$2,$3,$4,$5,$6);
					menu.css({cursor:'pointer'});
					let pop=xg.pop().up({left:e.clientX-5,top:e.clientY-5,class:'xg-admin-tab-menu xg-bg-white xg-radius-3 xg-pd-1 xg-border-c',html:menu});
					pop.d.mouseleave(function(s){
						setTimeout(function(){
							if(!pop.enter&&xg.isfun(pop.close))pop.close();
						},100);
					});
					$(doc).one('click',function(){pop.remove();});
					return false;
				}
			}
		});
		
		let stuck=xg.storage('xg-admin-tab-stuck')||{};
		show_index_page();
		for(let name in stuck){
			admin_tab(name,stuck[name]);
		}
		function show_index_page(){
			admin_tab('管理首页',xg.url('main/index'));
		}
	});
}
function admin_tab(title,url,autoclose){
	if(xg.isnan(window.admin_tab_count))window.admin_tab_count=0;
	var tab=xg.tab.get('xg-admin');
	var id=tab.add(title,'<iframe class="xg-admin-tab-iframe" width="100%" height="100%" src="'+url+'"></iframe>');
	tab.show(id);
	if(autoclose){
		var w=tab.iframe(id);
		w.xg_ajax_submited_hooks=w.xg_ajax_submited_hooks||{};
		if(w&&w.xg){
			w.xg.hooks('ajax-submited','xg-admin-tab-auto-close',function(data){
				if(data.ok){
					xg.ok(data.msg,function(){
						tab.remove(id);
					});
					return false;
				}
			});
		}
	}
	window.admin_tab_count=xg.tab.get('xg-admin').title.children('li').length;
}
win.admin_tab=admin_tab;
});
})(window,document,jQuery);