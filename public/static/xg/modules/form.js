/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
(function(win,doc,$){
xg.def('form',function(){
	return function(name,title,value){
		win.xg_form_ids=win.xg_form_ids||[];
		const F=function(name,title,value){
			const s=this;
			const id=win.xg_form_ids.length;
			win.xg_form_ids.push(id);
			s.o={};
			s.id=id;
			s.name=name;
			s.title=title;
			s.value=value;
		};
		F.prototype.options=function(o){
			const s=this;
			s.o=$.extend({},s.o,o);
			return s;
		};
		F.prototype.base_text=function(){
			const s=this;
			return $(`<input class="xg-input" type="text" name="${s.name}" value="${s.value}" />`);
		}
		F.prototype.base_textarea=function(){
			const s=this;
			return $(`<textarea class="xg-input" name="${s.name}">${s.value}</textarea>`);
		}
		F.prototype.base_upload=function(){
			const s=this;
			const $dom=$(`<div id="upload-div-${s.id}" class="xg-upload-div xg-flex"><input class="xg-input xg-upload-input xg-flex-1" type="text" name="${s.name}" value="${s.value}" id="upload-input-${s.id}" /></div>`);
			const $btn=$(`<input class="xg-btn xg-ml-2" type="button" value="上传${s.o.type=='image'?'图片':(s.o.type=='video'?'视频':'文件')}" />`);
			xg.mod('upload',function(){
				const upload=xg.upload({
					url:s.o.uploadurl||s.o.url||xg.url('file/upload'),
					max:s.o.max||1024*1024,
					data:s.o.data||{},
					done:function(data){
						if(data.ok===true){
							var input=$(`#upload-input-${s.id}`);
							if(data.ourl){
								input.val(data.ourl).change();
							}else if(data.fileurl){
								input.val(data.fileurl).change();
							}else if(data.info&&data.info.url){
								input.val(data.info.url).change();
							}
							xg.ok('上传成功');
						}else{
							xg.err(data.msg);
						}
					}
					,progress:function(n){}
				})
				.paste($dom.find('.xg-upload-input'))
				.setname(`#upload-input-${s.id}`,'{$name}',s.id);
				if(s.o.show_upload_btn){
					$dom.append($btn);
					upload.bind($btn);
				}
			});
			return $dom;
		}
		return new F(name,title,value);
	}
});
})(window,document,jQuery);