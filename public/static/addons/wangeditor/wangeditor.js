function wangbeautify(html,callback){
	const $html=$(xg.div().append(html));
	$html.children().each(function(){
		beautify(this,0);
	});
	callback($html.html());
	$html.remove();
	function beautify(t,level){
		var tag=t.tagName.toLowerCase();
		var $t=$(t);
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
}
function wangeditor(name){
	const E = window.wangEditor
	const LANG = 'zh-CN'
	E.i18nChangeLanguage(LANG)
	
	const editor = E.createEditor({
		selector: `#editor-${name}`,
		html: $(`#editor-text-${name}`).val(),
		config: {
			MENU_CONF: {
				uploadImage: {
					server:xg.addon('wangeditor://upload/image'),
					fieldName: 'file',
				},
				uploadVideo: {
					server:xg.addon('wangeditor://upload/video'),
					fieldName: 'file',
				}
			},
			onChange(editor) {
				const html=editor.getHtml();
				$(`#editor-text-${name}`).val(html);
			},
		}
	});
	E.createToolbar({
		editor,
		selector: `#editor-bar-${name}`,
		config: {}
	});
	$(`#editor-bar-${name} .w-e-toolbar`).append(`<div class="w-e-bar-item"><button type="button" class="wang-source-${name}">源码</button></div>`);
	$(`.wang-source-${name}`).click(function(){
		if($(this).html()=='源码'){
			wangbeautify(editor.getHtml(),function(html){
				$(`#editor-text-${name}`).val(html).removeClass('xg-hide');
				$(`#editor-${name}`).addClass('xg-hide');
			});
			$(this).html('返回');
		}else{
			const editorHtml=$(`#editor-text-${name}`).val();
			editorHtm=editorHtml.replace(/&lt;/ig,'<').replace(/&gt;/ig,'>');
			editor.setHtml(editorHtml);
			$(`#editor-text-${name}`).addClass('xg-hide');
			$(`#editor-${name}`).removeClass('xg-hide');
			$(this).html('源码');
		}
	});
}