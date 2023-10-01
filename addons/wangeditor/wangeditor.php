<?php
namespace addons\wangeditor;
class wangeditor{
	public function create($that){
		$this->that=$that;
		if($that->type=='editor')$this->editor();
		if($that->type=='base_editor')$this->editor();
	}
	protected function editor(){
		$that=$this->that;
		$name=$that->name;
		$name2=$that->name2();
		$html.='<div class="xg-form-item-'.$name2.' xg-form-item-'.$name.'">';
		$html.=$that->label();
		$this->base_editor();
		$html.=$that->html;
		$html.='</div>';
		$html.='<script>window.xg_form_items["'.$name.'"]="'.$name2.'";</script>';
		$that->html=$html;
		return $that;
	}
	protected function base_editor(){
		$that=$this->that;
		$name=$that->name;
		$value=$that->value;
		$name2=$that->name2();
		$url=xg_url('file/upload',['urlname'=>'path']);
		$value=str_replace('&','&amp;',$value);
		$html="<div class=\"xg-mt-4\"></div>
		<style>#editor-text-$name2{border-radius:0!important;min-height:300px!important;}.w-e-text-container{min-height:300px!important;}.w-e-scroll{min-height:300px!important;}.w-e-scroll > div{min-height:300px!important;}</style>
		<link href=\"https://unpkg.com/@wangeditor/editor@latest/dist/css/style.css\" rel=\"stylesheet\">
		<script src=\"https://unpkg.com/@wangeditor/editor@latest/dist/index.js\"></script>
		<script src=\"".XG_STATIC."static/addons/wangeditor/wangeditor.js\"></script>
		<div class=\"editor-root editor-root-$name2\">
			<div id=\"editor-bar-$name2\"></div>
			<textarea name=\"$name\" id=\"editor-text-$name2\" class=\"xg-hide\">$value</textarea>
			<div id=\"editor-$name2\"></div>
		</div>
		<script>wangeditor(`$name2`);</script>";
		$that->html=$html;
		return $that;
	}
}
