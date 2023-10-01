/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
(function(win,doc,$){
win.xg.def('editor',function(){
	xg.loadcss(xg.c.dir+'modules/editor/editor.css');
	return function(sl,c){
		win.xg_editor_ids=win.xg_editor_ids||[];
		var s;
		var E=function(sl,o){
			var id=win.xg_editor_ids.length;
			s=this;
			s.id=id;
			win.xg_editor_ids.push(id);
			s.sl=sl;
			s.setname(sl,$(sl),'xg-editor-'+id,id);
			s.o=$.extend(true,{},{
				upload:{url:'./upload.php',multi:1}
			},o);
			s.t=$(sl).addClass('xg-editor-text').addClass('xg-hide');
			s.e=s.t.wrap('<div/>').parent().addClass('xg-fix-r').addClass('xg-editor').addClass('xg-theme-border');
			s.el=xg.newdiv('xg-hide').appendTo(s.e);
			s.b=xg.newdiv('xg-editor-topbar xg-clear xg-theme-border');
			s.bs=xg.newdiv('xg-editor-topbar-static').append(s.b);
			s.i=xg.newdiv('xg-editor-info xg-fr').appendTo(s.b).append('<div class="xg-pd-2">共有<span class="word-count">0</span>个字</div>');
			s.e.prepend(s.t);
			s.bs.prependTo(s.e);
			s.d=$('<div/>').addClass('xg-editor-dom').html(s.t.val()).attr('contenteditable',true);
			
			
			
			
			s.o.upload.done=function(r){
				if(r.ok!==true){
					xg.err(r.msg);
				}else if(r.imgurl){
					s.d.focus();
					s.range().resume();
					doc.execCommand('InsertImage',true,r.imgurl);
					s.imgoption();
					s.format(1);
				}
			}
			var btns=s.btns;
			for(let i in btns){
				let btni=btns[i];
				let dom=xg.newdiv('xg-editor-btns xg-clear xg-theme-bg xg-theme-border');
				for(let n in btni){
					let $btn=$('<div/>')
					.addClass('xg-editor-btn xg-theme-border')
					.append(xg.newdiv('xg-icon xg-theme-color xg-theme-border xg-icon-'+btni[n]['icon']))
					.appendTo(dom)
					.attr('title',btni[n]['title']);
					if(xg.isfun(btni[n]['mouseenter'])){
						let fun=function(){
							if(s.source)return;
							s.d.focus();
							s.range().save();
							var rt=btni[n]['mouseenter'](s,$btn);
							return rt;
						};
						$btn.mouseenter(fun);
					}
					if(xg.isfun(btni[n]['mouseleave'])){
						$btn.mouseenter(btni[n]['mouseleave'])
						if(xg.isfun(btni[n]['mouseleave'])){
							let fun=function(){
								var rt=btni[n]['mouseleave'](s,$btn);
								return rt;
							};
							$btn.mouseleave(fun);
						}
					}
					if(xg.isfun(btni[n]['click'])){
						let fun=function(){
							if(s.source&&btni[n]['icon']!='html')return;
							s.range().resume();
							var rt=btni[n]['click'](s,$btn);
							s.format(1);
							return rt;
						};
						$btn.mousedown(function(){
							s.d.focus();
							s.range().save();
						}).click(fun);
					}
					if(btni[n]['exec']){
						let fun=function(){
							if(s.source)return;
							s.d.focus();
							s.range().resume();
							doc.execCommand(btni[n]['exec']);
							s.format(1);
						}
						$btn.mousedown(function(){
							s.d.focus();
							s.range().save();
						}).click(fun);
					}
				}
				dom.appendTo(s.b);
			}
			s.t.before(s.d.wrap('<div/>').parent().addClass('xg-editor-dom-parent'));
			if(s.o.autofocus){
				s.d.focus();
				s.range().save();
			}
			s.d.one('mousedown',function(){
				s.range().save();
			});
			s.keepin();
			s.bind();
			s.format(1);
			$(s.t).closest('form').submit(function(){
				s.beautify();
			});
		}
		E.prototype.imgoption=function(){
			s.d.find('img').unbind('dblclick.xg-editor').bind('dblclick.xg-editor',function(e){
				var t=$(this);
				s.actionimg=t;
				xg.ajax.get(xg.c.dir+'modules/editor/imgoption.html',{r:Math.random()},function(html){
					html=html.replace(/(editor\-name)/g,'xg-editor-'+s.id);
					s.img_option_win=xg.win({cont:html,shade:1},538,373);
				},'json');
			});
		}
		E.prototype.bind=function(){
			s.d.unbind().bind('paste',function(e){
				var data=e.originalEvent.clipboardData;
				if(data&&data.items&&data.items.length>0){
					s.range().save();
					item=data.items[0];
					if(item.kind=='string'&&item.type=='text/plain'){
						var str=data.getData('text/html');
						var reg=/<img.*?src="(.*?)".*?[\/]?>/gim;
						while(e=reg.exec(str)){
							let url=e[1];
							xg.upload(s.o.upload).data(function(){return s.o.upload.data;}).data('isimg',1).download(url,function(data){
								if(data.ok!==true){
									xg.err(data.msg);
								}else if(data.imgurl){
									s.d.focus();
									s.range().resume();
									s.d.find('img[src="'+url+'"]').attr('src',data.imgurl);
									s.format(1);
								}
							});
						}
					}else if(item.kind=='string'&&item.type=='text/html'){
						item.getAsString(function(str){
							var e=/<\!\-\-StartFragment\-\-><img.*?src="(.*?)".*?[\/]?><\!\-\-EndFragment\-\->/.exec(str);
							if(e&&e[1]){
								let url=e[1];
								xg.upload(s.o.upload).data(function(){return s.o.upload.data;}).data('isimg',1).download(url,function(data){
									if(data.ok!==true){
										xg.err(data.msg);
									}else if(data.imgurl){
										s.d.focus();
										s.range().resume();
										s.d.find('img[src="'+url+'"]').attr('src',data.imgurl);
										s.format(1);
									}
								});
							}
						});
					}else{
						var file=item.getAsFile();
						if(xg.isfile(file)){
							xg.mod('upload',function(){xg.upload(s.o.upload).data(function(){return s.o.upload.data;}).data('isimg',1).setfile(file).upload();});
							e.preventDefault();
						};
					}
				}
				s.format(1);
			})
			.keydown(function(e){
				e.stopPropagation();
				var command='';
				let code=e.keyCode||e.which||e.charCode;
				const range=s.range().get();
				if(code==13){
					if($(range.ancestor).parents('pre').length){
						e.preventDefault();
						let br='<br>';
						if(s.range().next())br='<br><br>';
						let node=range.range.createContextualFragment(br);
						range.range.insertNode(node);
						let next=range.selection.anchorNode.nextSibling;
						if(next.nextSibling)next=next.nextSibling;
						range.range.setStart(next,0);
						range.range.setEnd(next,0);
						s.range().resume(range.range);
						range.range.collapse();
						s.range().save();
						s.save();
						s.format();
						return false;
					}
				}else if(code==9){
					if(e.shiftKey){
						s.indent(0);
					}else{
						s.indent(1);
					}
					e.preventDefault();
				}else if(e.ctrlKey){
					if(code==86){
						return;
					}
					if(code==83){
						e.preventDefault();
						s.draft.save();
						return;
					}
					var command={66:'bold',73:'italic',85:'underline',90:'undo',89:'redo',76:'justifyleft',82:'justifyright'}[code];
				}else{
				}
				if(command){doc.execCommand(command);e.preventDefault();}
			}).mousedown(function(){
				const range=s.range().get();
				if($(range.ancestor).parents('pre').length){
					if(range.end.offset>=2){
						range.range.setEnd(range.end.container,range.end.offset-2);
						range.range.collapse(false);
						s.range().resume(range.range);
					}
				}
			});
			s.format();
			s.words();
			return s;
		}
		E.prototype.keepin=function(){
			var width,fixed;
			$(win).scroll(recount).resize(resize);
			resize();
			function resize(){
				width=s.e.width();
				recount();
			}
			function recount(){
				var offset=s.b.offset();
				if(s.bs.offset().top>$(win).scrollTop()){
					fixed=false;
					s.b.css({position:'',left:'auto',top:'auto',width:'auto'});
				}else{
					fixed=true;
					s.b.css({position:'fixed',left:offset.left,top:0,width:width})
				}
			}
			return s;
		}
		E.prototype.setname=function(name){
			win.xg_editor_list=win.xg_editor_list||{};
			for(var i in arguments){
				win.xg_editor_list[arguments[i]]=s;
			}
			return s;
		}
		E.prototype.indent=function(add){
			s.range().save();
			var doms=s.range().all();
			$(doms).each(function(){
				var dom=$(this);
				if(!dom.is('p'))dom=dom.parents('p');
				if(dom.length){
					if(add){
						dom.css({textIndent:'2rem'});
					}else{
						dom.css({textIndent:''});
					}
				}
			});
			s.format(1);
			return s;
		}
		E.prototype.upload={
			data:function(){
				var arg=arguments;
				if(xg.isobj(arg[0])){
					s.o.upload.data=$.extend({},s.o.upload.data,arg[0]);
				}else{
					s.o.upload.data[arg[0]]=arg[1];
				}
				return s;
			},
		}
		E.prototype.win=function(o,f,type){
			if(s.source)return;
			s.range().save();
			var btns=o.btns||f&&f.btns;
			if(btns)$.each(btns,function(n,f){btns[n]=function(v){
				s.range().resume();
				return f(s,v);
			}});
			if(type=='input'){
				xg.input({
					title:(o.title||o),
					onok:function(v){s.range().resume();return (xg.isfun(f)?f:(f.onok||xg.noop))(v)},
					oncancel:function(){s.range().resume();},
					oncallback:function(){s.range().resume();},
					btns:btns
				});
			}
			if(type=='color'){
				xg.color({
					title:(o.title||o),
					onok:function(v){s.range().resume();return (xg.isfun(f)?f:(f.onok||xg.noop))(v)},
					oncancel:function(){s.range().resume();},
					oncallback:function(){s.range().resume();},
					btns:btns
				});
			}
			return s;
		}
		E.prototype.content=function(c){
			if(xg.isunde(c))return s.d.html();
			s.d.html(c);
			s.format(1);
			return s;
		}
		E.prototype.words=function(){
				var c=s.d.text().replace(/[\r\n\s]+/g,'').length;
				s.i.find('.word-count').html(c);
		}
		E.prototype.save=function(){
			if(s.source)return;
			var c=s.d.html();
			s.t.val(c);
			return s;
		}
		E.prototype.source=false;
		E.prototype.format=function(immediate){
			if(s.format_timer)clearTimeout(s.format_timer);
			if(immediate){
				start();
			}else{
				s.format_timer=setTimeout(start,500);
			}
			return s;
			function start(){
				s.d.contents().each(function(){
					if($(this).parents('pre').length==0){
						if(this.nodeType==3){
							if(this.textContent){
								if($(this).parents('p,li,pre,td,th,tr').length==0){
									$(this).wrap('<p></p>');
								}
							}
						}else{
							format(this,0);
						}
					}
				});
				s.save();
				s.bind();
				s.imgoption();
			}
			function format(t,level){
				var tag=t.tagName.toLowerCase();
				var $t=$(t);
				if(tag=='div'){
					let node=$t.contents();
					if(!node.length)node=$('<br>');
					$t.replaceWith($('<p></p>').append(node));
				}else if(tag=='strong'){
					$t.replaceWith($('<b></b>').append($t.contents()));
				}else if(tag=='font'){
					let bg=$t.css('background');
					let color=$t.attr('color')||$t.css('color');
					let css={};
					if(color)css.color=color;
					if(bg)css.backgroundColor=bg;
					$t.replaceWith($('<span></span>').css(css).append($t.contents()));
				}else if(tag=='img'){
					let p;
					if($t.parent().is('p')){
						p=$t.parent();
					}else if($t.parent().parent().is('p')){
						p=$t.parent().parent();
					}
					if(p&&p.css('text-indent')){
						p.css({textIndent:''});
					}
				}
				if(tag=='span'){
					if(t.attributes.length==0){
						$t.replaceWith($t.contents());
					}
				}
				if(tag=='br'){
					if($t.parent('p').contents().length>1){
						$t.remove();
					}else if($t.parent('.xg-editor-dom').length){
						$t.wrap('<p></p>');
					}
				}
				$t.children().each(function(){
					format(this,level+1);
				});
			}
		}
		E.prototype.beautify=function(){
			s.d.html(s.d.html().replace(/>[\s]*</g,'><'));
			s.d.children().each(function(){
				beautify(this,0);
			});
			function beautify(t,level){
				var tag=t.tagName.toLowerCase();
				var $t=$(t);
				if($t.length){
					if(['p','span','b','u','i','a','img','table','thead','tbody','tr','td','th','ul','ol','li','pre','br','font','video'].indexOf(tag)==-1){
						$t.replaceWith($t.contents());
					}
				}
				if(['span'].indexOf(tag)>-1){
					if($t.contents().length==1){
						if($t.children().is('span')||$t.children().is('font')){
							let bg=$t.children().css('background');
							let color=$t.children().attr('color')||$t.children().css('color');
							let css={};
							if(color)css.color=color;
							if(bg)css.backgroundColor=bg;
							$t.css(css).append($t.children().contents());
						}else if(!$t.children().is('a')&&!$t.children().is('img')){
							$t.append($t.children().contents());
						}
					}
				}
				if(['span','b','u','i'].indexOf(tag)>-1){
					if(!$t.html().replace(/[\s]+/g,'')){
						$t.remove();
					}
				}
				if(tag=='p'){
					if(!$t.html().replace(/[\s]+/g,'')){
						$t.remove();
					}else{
						if($t.parent('li,td,th').length){
							$t.replaceWith($t.contents());
						}
					}
				}
				if(['p','table','thead','tbody','tr','td','th','ul','ol','li','pre'].indexOf(tag)>-1){
					$t.after('\r\n');
				}
				if(['table','thead','tbody','tr','ul','ol'].indexOf(tag)>-1){
					$t.append(xg.strpad(level+1,'	')).prepend('\r\n');
				}
				if(['table','thead','tbody','tr','td','th','ul','ol','li','pre'].indexOf(tag)>-1){
					$t.before(xg.strpad(level+1,'	'));
				}
				$t.children().each(function(){
					beautify(this,level+1);
				});
			}
			s.save();
			return s;
		}
		E.prototype.range=function(){
			var s;
			var R=function(){s=this;},d,sel=win.getSelection();
			var range;
			var e=this;
			R.prototype.save=function(){
				var r=s.get();
				e.range.data=r;
			}
			R.prototype.set=function(container,start,end){
				var range=doc.createRange();
				range.setStart(container,start);
				range.setEnd(container,end);
				sel.addRange(range);
				s.range=range;
				return s;
			}
			R.prototype.modify=function(dom){
				dom=$(dom);
				dom.append(s.range.extractContents());
				s.range.insertNode(dom[0]);
				e.format(1);
			}
			R.prototype.start=function(){
				return e.range.data.start;
			}
			R.prototype.end=function(){
				return e.range.data.end;
			}
			R.prototype.container=function(){
				return e.range.data.container;
			}
			R.prototype.all=function(){
				var doms=$();
				var start=s.start().container.parentNode;
				var end=s.end().container.parentNode;
				var between=s.between();
				doms.push(start);
				between.each(function(){
					doms.push($(this)[0]);
				});
				doms.push(end);
				return doms;
			}
			R.prototype.between=function(){
				var r=e.range.data;
				var ancestor=r.range.commonAncestorContainer;
				var start=r.container.start.parentNode;
				var end=r.container.end.parentNode;
				var doms=$();
				if(ancestor){
					$(ancestor).children().each(function(){
						var a=this.compareDocumentPosition(start);
						var b=this.compareDocumentPosition(end);
						if((a==2&&b==4)||(a==4&&b==2)){
							doms.push(this);
						}
					});
				}
				return doms;
			}
			R.prototype.resume=function(range){
				try{
					sel.removeAllRanges();
				}catch(e){
					doc.body.createTextRange().select();
					doc.selection.empty();
				}
				if(!range)range=e.range.data;
				var newrange=doc.createRange();
				if(!range)return;
				if(range.start){
					if(range.start.container)newrange.setStart(range.start.container,range.start.offset);
					if(range.end.container)newrange.setEnd(range.end.container,range.end.offset);
					sel.addRange(newrange);
				}else{
					sel.addRange(range);
				}
			}
			R.prototype.next=function(e){
				if(!e)e=s.get().end.container;
				var node=e.nextSibling;
				if (node&&node.nodeType!=1)node=s.next(node);
				return node;
			}
			R.prototype.get=function(){
				var d={
					container:{start:'',end:''},
					start:{container:'',offset:''},
					end:{container:'',offset:''},
					range:''
				}
				if(sel.getRangeAt&&sel.rangeCount){
					var r=sel.getRangeAt(0);
					d.container.start=d.start.container=r.startContainer;
					d.container.end=d.end.container=r.endContainer;
					d.start.offset=r.startOffset;
					d.end.offset=r.endOffset;
					d.ancestor=r.commonAncestorContainer;
					d.selection=sel;
					d.range=r;
				}
				return d;
			}
			return new R();
		}
		E.prototype.range.data=null;
		E.prototype.draft={};
		E.prototype.draft.show=function(){
			var list=xg.storage('xg-editor-draft-list')||[];
			if(!list.length){
				xg.msg('没有存储的草稿',2);
				return;
			}
			list=list.reverse();
			var html='';
			html+='<ul class="xg-clear" style="width:420px;padding:0.25rem">';
			for(var i in list){
				html+='<li style="display:block;float:left;border:solid 1px #f5f5f5;margin:0.25rem;padding:0.5rem;border-radius:0.3rem;line-height:1rem;cursor:pointer;" storage-name="'+list[i]+'"><span class="xg-fr xg-close xg-ml-5"></span>'+list[i]+'</li>';
			}
			html+='</ul>';
			var id=xg.win({cont:html,title:'草稿记录'});
			$('#xg-msg-'+id).find('ul').height($('#xg-msg-'+id).find('ul').height());
			$('#xg-msg-'+id).find('li').click(function(){
				var that=$(this);
				xg.confirm('确定要还原此草稿吗？<br>现有内容将会被覆盖',function(){
					s.content(xg.storage(that.attr('storage-name')));
					$('#xg-msg-'+id).remove();
				});
			}).find('.xg-close').click(function(e){
				var that=$(this);
				var name=$(this).parent('li').attr('storage-name');
				var list=xg.storage('xg-editor-draft-list')||[];
				xg.delete(list,name);
				xg.storage(name,null);
				xg.storage('xg-editor-draft-list',list);
				$(this).parent('li').remove();
				e.stopImmediatePropagation();
				if(!list.length)$('#xg-msg-'+id).remove();
			});
		}
		E.prototype.draft.save=function(msg){
			var cont=s.content();
			if(!cont.replace(/[\r\n\s]*/g,'')){
				if(msg!==0)xg.msg('内容为空',2);
				return s;
			}
			var name=xg.date(0);
			xg.storage(name,cont);
			var list=xg.storage('xg-editor-draft-list')||[];
			if(list.indexOf(name)>-1)return;
			list.push(name);
			if(list.length>20){
				var last=list.shift();
				xg.storage(last,null);
			}
			xg.storage('xg-editor-draft-list',list);
			if(msg!==0)xg.ok('保存成功');
			return s;
		}
		E.prototype.createtable=function(data){
			var html='';
			html+=`<table class="${data.classname?'xg-table'+data.classname:'xg-table'}" width="${data.width?data.width:'100%'}" height="${data.height?data.height:''}">`;
			if(data.thead){
				html+=`<thead>`;
				html+=`<tr>`;
				for(let j=0;j<data.col;j++){
					html+=`<th></th>`;
				}
				html+=`</tr>`;
				html+=`</thead>`;
			}
			html+=`<tbody>`;
			for(let i=0;i<data.row;i++){
				html+=`<tr>`;
				for(let j=0;j<data.col;j++){
					html+=`<td></td>`;
				}
				html+=`</tr>`;
			}
			html+=`</tbody>`;
			html+=`</table>`;
			return html;
		}
		E.prototype.colors=[
			'#ffffff', '#000000', '#eeece1', '#1f497d', '#4f81bd', '#c0504d', '#9bbb59', '#8064a2', '#4bacc6', '#f79646',
			'#f2f2f2', '#979797', '#ddd9c3', '#c6d9f0', '#dbe5f1', '#f2dcdb', '#ebf1dd', '#e5e0ec', '#dbeef3', '#fdeada',
			'#d8d8d8', '#595959', '#c4bd97', '#8db3e2', '#b8cce4', '#e5b9b7', '#d7e3bc', '#ccc1d9', '#b7dde8', '#fbd5b5',
			'#bfbfbf', '#3f3f3f', '#938953', '#548dd4', '#95b3d7', '#d99694', '#c3d69b', '#b2a2c7', '#92cddc', '#fac08f',
			'#a5a5a5', '#262626', '#494429', '#17365d', '#366092', '#953734', '#76923c', '#5f497a', '#31859b', '#e36c09',
			'#7f7f7f', '#0c0c0c', '#1d1b10', '#0f243e', '#244061', '#632423', '#4f6128', '#3f3151', '#205867', '#974806',
			'#c00000', '#ff0000', '#ffc000', '#ffff00', '#92d050', '#00b050', '#00b0f0', '#0070c0', '#002060', '#7030a0'
		];
		E.prototype.btns=[
			[
				{icon:'html',title:'查看源码',click:function(s){
					if(!s.source){
						s.format(1);
						s.beautify();
						s.source=true;
						var val=s.t.val();
						s.t.removeClass('xg-hide').val(val);
						s.d.addClass('xg-hide');
						s.b.addClass('xg-editor-source');
					}else{
						s.source=false;
						s.t.addClass('xg-hide');
						s.d.removeClass('xg-hide').html(s.t.val()).focus();
						s.range().resume();
						s.b.removeClass('xg-editor-source');
					}
				}}
			],
			[
				{icon:'table',title:'插入表格',mouseenter:function(s,d){
					if(s.tbpop){
						if(!s.source)s.tbpop.show();
						return;
					}
					d.mousemove(function(){
						if(!s.source)s.tbpop.show();
					});
					let div=xg.newdiv('xg-editor-table-pop');
					let mousetimer=0;
					for(let i=0;i<7;i++){
						let tr=xg.newdiv('xg-editor-table-tr').mouseleave(function(){
							$('.xg-editor-table-td').removeClass('xg-editor-table-bg');
						});
						for(let j=0;j<10;j++){
							let td=xg.newdiv('xg-editor-table-td xg-editor-table-td-'+i+'-'+j).mouseover(function(){
								$('.xg-editor-table-td').removeClass('xg-editor-table-bg');
								for(let i2=0;i2<=i;i2++){
									for(let j2=0;j2<=j;j2++){
										$('.xg-editor-table-td-'+i2+'-'+j2).addClass('xg-editor-table-bg').unbind('mousedown').bind('mousedown',function(){
											s.d.focus();
											s.range().resume();
											var html=s.createtable({row:i2+1,col:j2+1});
											document.execCommand('InsertHtml',true,html);
											s.save();
											s.format(1);
											pop.close();
										});
									}
								}
							});
							tr.append(td);
						}
						div.append(tr);
					}
					div.append(xg.newdiv('xg-editor-table-more').append($('<button class="xg-btn xg-btn-s xg-w-100">更多设置项</button>').click(function(){
						xg.ajax.get(xg.c.dir+'modules/editor/table.html',{r:Math.random()},function(html){
							html=html.replace(/(editor-name)/g,'xg-editor-'+s.id);
							html=html.replace(/(table-id)/g,'table-'+s.id);
							var winid=xg.win({cont:html,ok:1,onok:function(){
								s.d.focus();
								s.range().resume();
								window['table-'+s.id+'-fun']();
								s.save();
								s.format(1);
								xg.close(winid);
								return false;
							}},520,400,'插入表格');
						},'json');
					})));
					var pop=xg.pop().ref('.xg-editor .xg-icon-table',div,{alignright:1,alignbottom:1});
					pop.d.mouseleave(function(){
						pop.close();
					})
					$(doc).click(function(){pop.close();});
					s.tbpop=pop;

				},mouseleave:function(s){
					setTimeout(function(){
						let pop=s.tbpop||{};
						if(!pop.enter&&xg.isfun(pop.close))pop.close();
					},100);
				}},
				{icon:'code',title:'插入代码块',click:function(s){
					var html='<textarea class="code-text-'+s.id+'" style="border:none;padding:10px;;width:100%;height:100%;border-bottom:solid 1px #ddd;box-sizing:border-box;"></textarea>';
					var winid=xg.win({cont:html,ok:1,onok:function(){
						s.d.focus();
						s.range().resume();
						var code=$('.code-text-'+s.id).val();
						code=code.replace(/>/g,'&gt;').replace(/</g,'&lt;');
						var text=`<pre class="xg-code">${code}</pre>`;
						document.execCommand('InsertHtml',true,text);
						s.save();
						s.format(1);
						xg.close(winid);
						return false;
					}},520,400,'插入代码块');
				}},
				{icon:'picture',title:'上传图片',mouseenter:function(s,d){
					xg.ajax.get(xg.c.dir+'modules/editor/uppic.html',{r:Math.random()},function(html){
						if(s.uipop){
							if(!s.source)s.uipop.show();
							return;
						}
						d.mousemove(function(){
							if(!s.source)s.uipop.show();
						});
						var pop=xg.pop().ref('.xg-editor .xg-icon-picture',html.replace(/(editor\-name)/g,'xg-editor-'+s.id),{alignright:1,alignbottom:1});
						pop.d.mouseleave(function(){
							pop.close();
						})
						$(doc).click(function(){pop.close();});
						s.uipop=pop;
					},'json');
				},mouseleave:function(s){
					setTimeout(function(){
						let pop=s.uipop||{};
						if(!pop.enter&&xg.isfun(pop.close))pop.close();
					},100);
				}},
			],
			[
				{icon:'ol',title:'插入数字列表',click:function(s){
						s.d.focus();
						s.range().resume();
						var text=`<ol><li>&nbsp;</li></ol>`;
						document.execCommand('InsertHtml',true,text);
						s.save();
						s.format(1);
						return false;
				}},
				{icon:'ul',title:'插入普通列表',click:function(s){
						s.d.focus();
						s.range().resume();
						var text=`<ul><li>&nbsp;</li></ul>`;
						document.execCommand('InsertHtml',true,text);
						s.save();
						s.format(1);
						return false;
				}},
			],
			[
				{icon:'magic',title:'清除格式',click:function(s){
					s.d.find('*').not('table,td,th').removeAttr('style width height id');
					s.d.find('table,td,th').removeAttr('style id');
					s.format(1);
				}},
				{icon:'save',title:'保存草稿',function(s){
					s.draft.save();
				}},
				{icon:'turn-left',title:'恢复草稿',click:function(s){
					s.draft.show();
				}}
			],
			[
				{icon:'align-left',title:'左对齐',exec:'justifyleft'},
				{icon:'align-center',title:'居中对齐',exec:'justifycenter'},
				{icon:'align-right',title:'右对齐',exec:'justifyright'},
				{icon:'align-justify',title:'两侧对齐',exec:'justifyfull'}
			],
			[
				{icon:'undo',title:'撤回',exec:'undo'},
				{icon:'redo',title:'重做',exec:'redo'}
			],
			[
				{icon:'copy',title:'复制',exec:'copy'},
				{icon:'cut',title:'剪切',exec:'cut'},
				{icon:'trash',title:'删除',exec:'delete'}
			],
			[
				{icon:'bold',title:'粗体',exec:'bold'},
				{icon:'italic',title:'斜体',exec:'italic'},
				{icon:'underline',title:'下划线',exec:'underline'}
			],
			[
				{icon:'indent-right',title:'缩进',click:function(s){
					s.indent(1);
				}},
				{icon:'indent-left',title:'取消缩进',click:function(s){
					s.indent(0);
				}}
			],
			[
				{icon:'forecolor',title:'文本颜色',mouseenter:function(s,d){
					if(s.fcpop){
						if(!s.source)s.fcpop.show();
						return;
					}
					d.mousemove(function(){
						if(!s.source)s.fcpop.show();
					});
					let div=xg.newdiv('xg-editor-color-pop');
					let mousetimer=0;
					for(let i=1;i<=7;i++){
						let tr=xg.newdiv('xg-editor-color-tr').mouseleave(function(){
							$('.xg-editor-color-td').removeClass('xg-editor-color-bg');
						});
						for(let j=1;j<=10;j++){
							let td=xg.newdiv('xg-editor-color-td xg-editor-color-td-'+i+'-'+j).css({backgroundColor:s.colors[i*j]}).click(function(){
								s.d.focus();
								s.range().resume();
								doc.execCommand('forecolor',false,s.colors[i*j]);
								s.save();
								s.format(1);
								pop.close();
							});
							tr.append(td);
						}
						div.append(tr);
					}
					div.append(xg.newdiv('xg-editor-color-more').append($('<button class="xg-btn xg-btn-s xg-w-100">自定义颜色</button>').click(function(){
						s.win('文本颜色',function(v){
							if(!v){xg.msg('请填写颜色',1);return false;}
							doc.execCommand('forecolor',false,v);
						},'color');
					})));
					var pop=xg.pop().ref('.xg-editor .xg-icon-forecolor',div,{alignright:1,alignbottom:1});
					pop.d.mouseleave(function(){
						pop.close();
					})
					$(doc).click(function(){pop.close();});
					s.fcpop=pop;
				},mouseleave:function(s){
					setTimeout(function(){
						let pop=s.fcpop||{};
						if(!pop.enter&&xg.isfun(pop.close))pop.close();
					},100);
				}},
				{icon:'backcolor',title:'背景颜色',mouseenter:function(s,d){
					if(s.bgpop){
						if(!s.source)s.bgpop.show();
						return;
					}
					d.mousemove(function(){
						if(!s.source)s.bgpop.show();
					});
					let div=xg.newdiv('xg-editor-color-pop');
					let mousetimer=0;
					for(let i=1;i<=7;i++){
						let tr=xg.newdiv('xg-editor-color-tr').mouseleave(function(){
							$('.xg-editor-color-td').removeClass('xg-editor-color-bg');
						});
						for(let j=1;j<=10;j++){
							let td=xg.newdiv('xg-editor-color-td xg-editor-color-td-'+i+'-'+j).css({backgroundColor:s.colors[i*j]}).click(function(){
								s.d.focus();
								s.range().resume();
								doc.execCommand('backcolor',false,s.colors[i*j]);
								s.save();
								s.format(1);
								pop.close();
							});
							tr.append(td);
						}
						div.append(tr);
					}
					div.append(xg.newdiv('xg-editor-color-more').append($('<button class="xg-btn xg-btn-s xg-w-100">自定义颜色</button>').click(function(){
						s.win('背景颜色',function(v){
							if(!v){xg.msg('请填写颜色',1);return false;}
							doc.execCommand('backcolor',false,v);
						},'color');
					})));
					var pop=xg.pop().ref('.xg-editor .xg-icon-backcolor',div,{alignright:1,alignbottom:1});
					pop.d.mouseleave(function(){
						pop.close();
					})
					$(doc).click(function(){pop.close();});
					s.bgpop=pop;
				},mouseleave:function(s){
					setTimeout(function(){
						let pop=s.bgpop||{};
						if(!pop.enter&&xg.isfun(pop.close))pop.close();
					},100);
				}},
				{icon:'link',title:'超链接',click:function(s){
					return s.win('超链接',{onok:function(v){
						s.d.focus();
						s.range().resume();
						if(v){
							var start=s.range().start();
							var end=s.range().end();
							var between=s.range().between();
							s.range().set(start.container,start.offset,start.container.textContent.length).modify($('<a></a>').attr('href',v));
							if(start.container!=end.container){
								between.each(function(){
									$(this).append($('<a></a>').attr('href',v).append($(this).contents()));
								});
								s.range().set(end.container,0,end.offset).modify($('<a></a>').attr('href',v));
							}
						}else{
							doc.execCommand('unlink',false);
						}
					}},'input');
				}}
			]
		];
		return new E(sl,c);
	};
});

xg.editor.get=function(sl){
	if(xg.isunde(sl)){
		let arr=[];
		for(let i in win.xg_editor_list){
			if(!arr.includes(win.xg_editor_list[i])){
				arr.push(win.xg_editor_list[i]);
			}
		}
		return arr;
	}
	var s=win.xg_editor_list[sl];
	if(!s)console.log('没有此名称的编辑器');
	return s;
}
})(window,document,jQuery);