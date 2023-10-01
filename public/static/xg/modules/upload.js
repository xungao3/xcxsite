/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
(function(win,doc,$){
xg.def('upload',function(o){
	return function(o){
		var s,id;
		var U=function(o){
			s=this;
			s.files=[];
			s.o=$.extend({},{
				name:'file',
				error:function(msg,index,file){xg.err('上传发生错误['+index+']['+file.name+']'+(msg?'['+msg+']':'')+'');},
				choose:function(file,start){},
				done:function(data,index,file){},
				start:function(index,file){},
				allDone:function(){},
				progress:function(){},
				callback:null,
				thread:1,
				data:{},
				multi:false
			},o);
			return s;
		}
		U.prototype.setname=function(){
			win.xg_upload_list=win.xg_upload_list||{};
			for(var i in arguments){
				win.xg_upload_list[arguments[i]]=s;
			}
			return s;
		}
		U.prototype.done=function(fun){
			s.o.done=fun;
			return s;
		}
		U.prototype.data=function(){
			arg=arguments;
			if(xg.isobj(arg[0])){
				s.o.data=$.extend({},s.o.data,arg[0]);
			}else if(xg.isfun(arg[0])){
				s.o.data=$.extend({},s.o.data,arg[0]());
			}else{
				s.o.data[arg[0]]=arg[1];
			}
			return s;
		};
		U.prototype.index=0;
		U.prototype.okcount=0;
		U.prototype.errcount=0;
		U.prototype.filechange=function(s){
			return function(){
				var newfiles=$(this).prop("files");
				var filelength=s.files.length;
				for(var i in newfiles){
					if(typeof newfiles[i]=='object'){
						var filesize=newfiles[i].size;
						if(!s.o.max || filesize<s.o.max){
							s.files.push(newfiles[i]);
						}else{
							xg.err(newfiles[i].name+'上传文件超过大小('+Math.round(s.o['max']/1024,2)+'KB)');
						}
					}
				}
				var newfiles2=[];
				for(var i=0;i<newfiles.length;i++){
					if(typeof newfiles[i]=='object'){
						newfiles2[i+filelength]=newfiles[i];
					}
				}
				s.o.choose(newfiles2,filelength);
				var domax=Math.max(Math.min(s.files.length,s.o.thread),1);
				if(xg.isfun(s.o.callback)){
					s.callback();
				}else{
					for(var i=0;i<domax;i++){
						s.upload(s.index);
					}
				}
				$(this).remove();
			}
		}
		U.prototype.paste=function(sl,data,fun){
			if(!$(sl).length){
				xg.error('没有找到此节点['+sl+']');
				return;
			}
			if(!$(sl).is('input')){
				xg.error('所选节点必须是input');
				return;
			}
			s.o.done=xg.isfun(s.o.done)?s.o.done:function(data){
				if($(sl).val()){
					var val=$(sl).val().split(',');
				}else{
					var val=[];
				}
				val.push(data.fileurl);
				$(sl).val(val.join(','));
			};
			$(sl).bind('paste',function(e){
				var data=e.originalEvent.clipboardData;
				if(data&&data.items&&data.items.length>0){
					var item=data.items[0];
					if(item.kind=='string'&&item.type=='text/html'){
						item.getAsString(function(str){
							var e=/<\!\-\-StartFragment\-\-><img.*?src="(.*?)".*?[\/]?><\!\-\-EndFragment\-\->/.exec(str);
							if(e&&e[1]){
								s.download(e[1]);
							}
						});
					}else{
						var file=item.getAsFile();
						if(xg.isfile(file)){
							s.setfile(file).upload();
							e.preventDefault();
						};
					}
				}
			})
			return s;
		}
		U.prototype.download=function(url,done){
			if(url.indexOf(location.protocol+'//'+location.host)===0)url=url.substring((location.protocol+'//'+location.host).length);
			var data={url:url};
			$.each(s.o.data,function(key,value){
				if(xg.isfun(value)){
					var val=value();
				}else{
					var val=value;
				}
				if(val)data[key]=val;
			});
			xg.ajax.get(s.o.url,data,function(data){
				if(!xg.isfun(done))done=s.o.done;
				done(data);
			},'json');
			return s;
		}
		U.prototype.bind=function(sl){
			if(!$(sl).length){
				xg.error('没有找到此节点['+sl+']');
				return;
			}
			if(!$('#xg-upload-elem').length)$('<div/>').attr('id','xg-upload-elem').appendTo('body');
			win.xg_upload_elem_ids=win.xg_upload_elem_ids||[];
			id=win.xg_upload_elem_ids.length;
			win.xg_upload_elem_ids.push(id);
			$(sl).click(function(){
				if(!$('#xg-upload-elem-'+id).length)$('<input/>').attr('type','file').attr('id','xg-upload-elem-'+id).appendTo('#xg-upload-elem').change(s.filechange(s));
				if(s.o.multi)$('#xg-upload-elem-'+id).attr('multiple',1);
				if(s.o.mime)$('#xg-upload-elem-'+id).attr('accept',s.o.mime);
				$('#xg-upload-elem-'+id).click();
			});
			return s;
		}
		U.prototype.setfile=function(file){
			s.files.push(file);
			return s;
		}
		U.prototype.callback=function(){
			var result=[];
			var count=0;
			for(let i in s.files){
				let file=s.files[i];
				let reader=new FileReader();
				if(/text/.test(file.type)||/json/.test(file.type)){
					reader.readAsText(file,'utf-8')
				}else{
					reader.readAsDataURL(file)
				}
				reader.onload=function(){
					result[i]=this.result;
				}
				reader.onloadend=function(){
					count++;
					if(count>=s.files.length){
						s.o.callback(result);
						$('#xg-upload-elem-'+id).remove();
					}
				}
			}
		}
		U.prototype.upload=function(nowindex){
			if(nowindex>=s.files.length){
				xg.loading(0);
				s.o.allDone();
				return;
			}
			if(xg.isunde(nowindex))nowindex=s.index;
			s.index++;
			var file=s.files[nowindex];
			if(!file)return;
			console.log('正在上传第'+(nowindex+1)+'个文件，共'+s.files.length+'个文件。');
			var formData=new FormData();
			if(s.o.data){
				$.each(s.o.data,function(key,value){
					if(xg.isfun(value)){
						var val=value();
					}else{
						var val=value;
					}
					if(val)formData.append(key,val);
				});
			}
			formData.append(s.o.name,file);
			var opts={
				url:s.o.url
				,type:'post'
				,data:formData
				,contentType:false 
				,processData:false
				,dataType:'json'
				,headers:s.o.headers || {}
				,success:function(data){
					s.okcount++;
					s.o.done(data,nowindex,file);
					s.upload(nowindex+1);
				}
				,error:function(data){
					s.errcount++;
					s.o.error(data.msg,nowindex,file);
					s.upload(nowindex+1);
				}
			};
			if(xg.isfun(s.o.progress)){
				opts.xhr=function(){
					var xhr=$.ajaxSettings.xhr();
					xhr.upload.addEventListener("progress",function(e){
						if(e.lengthComputable){
							var percent=Math.floor((e.loaded/e.total)*100);
							s.o.progress(percent,nowindex,file,e);
						}
					});
					return xhr;
				}
			}
			xg.loading();
			s.o.start(nowindex,file);
			$.ajax(opts);
		}
		return new U(o);
	};
});
xg.upload.get=function(name){
	if(xg.isunde(name)){
		let arr=[];
		for(let i in win.xg_upload_list){
			if(!arr.includes(win.xg_upload_list[i])){
				arr.push(win.xg_upload_list[i]);
			}
		}
		return arr;
	}
	return win.xg_upload_list[name];
}
})(window,document,jQuery);