<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class form{
	public $type=null;
	public $name='';
	public $title='';
	public $value=null;
	public $tip='';
	public $html='';
	public $options=[];
	public $values=[];
	public $placeholder=null;
	public $multi=0;
	public $btns=[];
	public $rmbtns=[];
	public $filter=[];
	public $default=null;
	public static $inited=false;
 	public function __construct($name='',$title='',$value=null,$values=[]){
		$this->name=$name;
		$this->title=$title;
		$this->value=$value;
		$this->values=$values;
	}
	protected function options($a=[],$b=null){
		if($b){
			$this->options[$a]=$b;
		}else{
			$this->options=$a;
		}
		return $this;
	}
	protected function def($val){
		$this->default=$val;
		return $this;
	}
	protected function placeholder($placeholder){
		$this->placeholder=$placeholder;
		return $this;
	}
	protected function name2($name=''){
		if(!$name)$name=$this->name;
		return substr(md5($name),0,10);
	}
	protected function tip($tip){
		$this->tip=$tip;
		return $this;
	}
	protected function helpid($helpid){
		$this->helpid=$helpid;
		return $this;
	}
	protected function values($values=[]){
		$this->values=$values;
		return $this;
	}
	protected function value($value){
		$this->value=$value;
		return $this;
	}
	protected function multi($v){
		$this->multi=$v;
		return $this;
	}
	protected function help_link($helpid=0){
		$site=xg_config('site.off_site_url');
		$site=$site?$site:'http://e.xg3.cn';
		return '<a href="'.$site.'help/'.$helpid.'.html" class="fa fa-question-circle" target="_blank"></a>';
	}
	protected function label($title=null,$tip=null,$helpid=null){
		if(!$title)$title=$this->title;
		if(!$tip)$tip=$this->tip;
		if(!$helpid)$helpid=$this->helpid;
		if($helpid){
			$helparr=xg_arr($helpid);
			$helphtml='';
			foreach($helparr as $v){
				$helphtml.=' '.$this->help_link($v);
			}
		}
		return '<label class="xg-form-label">'.$title.' '.$helphtml.($tip?'<br>':'').'<span class="xg-form-item-tip">'.($tip?''.$tip.'':'').'</span></label>';
	}
	protected function wraphtml($htm=null){
		$name=$this->name;
		$title=$this->title;
		$options=$this->options;
		$tip=$this->tip;
		$helpid=$this->helpid;
		$name2=$this->name2();
		$html='';
		$html.='<div class="xg-form-item-'.$name2.' xg-form-item-'.$name.'">';
		$html.=$this->label();
		$html.='<div class="xg-form-item'.($this->item_class?' '.$this->item_class:'').((is_array($options) and $options['item_class'])?' '.$options['item_class']:'').'">';
		$html.=$htm?$htm:$this->html;
		$html.='</div>';
		$html.='</div>';
		if(!self::$inited){
			self::$inited=true;
			$html.='<script>window.xg_form_items=window.xg_form_items||{};</script>';
		}
		$html.='<script>window.xg_form_items["'.$name.'"]="'.$name2.'";</script>';
		return $html;
	}
	protected function wrap(){
		$this->html=$this->wraphtml();
		return $this;
	}
	protected function rmbtn($btns=[]){
		$btns=xg_arr($btns);
		$this->rmbtn=xg_merge($this->rmbtn,$btns);
		return $this;
	}
	protected function btn($btns=[]){
		$this->btns=[];
		if($btns)$this->addbtn($btns);
		return $this;
	}
	protected function addbtn($btns=[]){
		$name=$this->name;
		$title=$this->title;
		$value=$this->value;
		$values=$this->values?$this->values:[];
		$options=$this->options;
		$data=xg_jsonstr($options['data']);
		$name2=$this->name2();
		$btns=xg_arr($btns);
		foreach($btns as $k=>$v){
			if($v=='selectimg'){
				$this->btns['selectimg']="<script>
				function select_image_{$name2}(){
					var data=window.updatas||{};
					data.isimg=1;
					data.count=1;
					var id=xg.iframe({src:xg.url('file/select',data),width:960,height:600,ok:1,cancel:1,onok:function(){
						var selected=xg.iframe(id).selected;
						if(selected&&selected.length){
							$('input[name=\"{$name}\"]').val(selected[0]);
						}
					}});
				}</script><a title=\"选择图片\" href=\"javascript:window.select_image_{$name2}();\" class=\"xg-btn xg-icon xg-icon-picture\"></a>";
			}
			if($v=='selectfile'){
				if($options['data']){
					$data=xg_jsonstr($options['data']);
					if(!$data)$data='{}';
					$this->btns['selectfile']="<script>
					function select_file_{$name2}(){
						var data=window.updatas||{$data};
						data.count=1;
						var id=xg.iframe({src:xg.url('file/select',data),width:960,height:600,ok:1,cancel:1,onok:function(){
							var selected=xg.iframe(id).selected;
							if(selected&&selected.length){
								$('input[name=\"{$name}\"]').val(selected[0]);
							}
						}});
					}</script><a title=\"选择文件\" href=\"javascript:window.select_file_{$name2}();\" class=\"xg-btn xg-icon xg-icon-file-zip\"></a>";
				}
			}
			if($v=='selecticon'){
				$this->btns['selecticon']="<script>
				function select_file_{$name2}(){
					var id=xg.iframe(xg.url('icon/icon'),650,470,'选择图标');xg.iframe(id).target_win=window;xg.iframe(id).target_input='.xg-form-item-{$name2} .xg-form-item input';xg.iframe(id).icon_win_id=id;
				}</script><a title=\"选择图标\" href=\"javascript:window.select_file_{$name2}();\" class=\"xg-btn xg-icon xg-icon-smile\"></a>";
			}
			if($v=='uploadbtn'){
				$btnname='上传'.($options['type']=='video'?'视频':($options['type']=='image'?'图片':'文件'));
				$this->btns['uploadbtn']="<button title=\"$btnname\" class=\"xg-btn xg-icon xg-icon-upload\" id=\"upload-btn-{$name2}\" type=\"button\" name=\"{$name2}_btn\"></button> 	";
			}
			if($v=='clear'){
				$this->btns['clear']="<input class=\"xg-btn\" type=\"button\" value=\"清除\" xg-clear-input=\"$name\" >";
			}
		}
		return $this;
	}
	protected function filter($filter=[]){
		$this->filter=$filter;
		return $this;
	}
	protected function get_items($filter=[]){
		if(!$filter)$filter=$this->filter;
		if($filter)$filter=xg_arr($filter);
		$get=xg_input('get.');
		unset($get['xg']);
		foreach($get as $k=>$v){
			if(in_array($k,$filter))unset($get[$k]);
		}
		$html='';
		foreach($get as $k=>$v){
			$html=$html.xg_form($k,$v)->hidden();
		}
		$this->html=$html;
		return $this;
	}
	protected function button(){
		if(!$this->type)$this->type=__FUNCTION__;
		if(!$submit)$submit=$this->name;
		if(!$submit)$submit='提交';
		if(!$classname)$classname=$this->title;
		$html='';
		$html.='<div class="xg-form-item-btn">';
		if(is_array($submit)){
			foreach($submit as $name=>$type){
				if($type=='html'){
					$html.=$name;
				}elseif($type!='submit' and $type!='button' and $type!='reset'){
					$link=$type;
					$html.='<a href="'.$link.'" class="xg-btn">'.$name.'</a>';
				}else{
					$html.='<input type="'.$type.'" class="xg-btn" value="'.$name.'" />';
				}
			}
		}else{
			$html.='<input type="submit" class="xg-btn'.($classname?' '.$classname:'').'" value="'.$submit.'" />';
		}
		$html.='</div>';
		$this->html=$html;
		return $this;
	}
	protected function hidden(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value?$this->value:$this->title;
		$this->html='<input type="hidden" name="'.$name.'" value="'.str_replace('"','&quot;',$value).'" />';
		return $this;
	}
	protected function text(){
		if(!$this->type)$this->type=__FUNCTION__;
		//$this->addbtn('clear');
		return $this->wrap($this->base_text());
	}
	protected function base_text(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$options=$this->options;
		$html='';
		$html.='<input class="xg-input '.($options['classname']?' '.$options['classname']:'').'" type="text" name="'.$name.'" value="'.str_replace('"','&quot;',$value).'" placeholder="'.$this->placeholder.'" '.$this->attrs().' /> <!--xg-btns-replace-->';
		$this->html=$html;
		return $this;
	}
	protected function number(){
		if(!$this->type)$this->type=__FUNCTION__;
		//$this->addbtn('clear');
		return $this->wrap($this->base_number());
	}
	protected function base_number(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$options=$this->options;
		$html='';
		$html.='<input class="xg-input '.($options['classname']?' '.$options['classname']:'').'" type="text" name="'.$name.'" value="'.str_replace('"','&quot;',$value).'" '.$this->attrs().' /> <!--xg-btns-replace-->';
		$this->html=$html;
		return $this;
	}
	protected function color(){
		if(!$this->type)$this->type=__FUNCTION__;
		//$this->addbtn('clear');
		return $this->wrap($this->base_color());
	}
	protected function base_color(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$options=$this->options;
		$name2=$this->name2();
		$html='';
		$html.='<div class="xg-color-swatch"><input class="xg-color-'.$name2.'" type="color" value="'.str_replace('"','&quot;',$value).'" /></div><input class="xg-input xg-color-input xg-color-input-'.$name2.' '.($options['classname']?' '.$options['classname']:'').'" type="text" name="'.$name.'" value="'.str_replace('"','&quot;',$value).'" '.$this->attrs().' /> <!--xg-btns-replace-->';
		$html.='<script>$(".xg-color-'.$name2.'").change(function(){$(".xg-color-input-'.$name2.'").val($(this).val());});$(".xg-color-input-'.$name2.'").change(function(){$(".xg-color-'.$name2.'").val($(this).val());});</script>';
		$this->html=$html;
		return $this;
	}
	protected function icon(){
		if(!$this->type)$this->type=__FUNCTION__;
		$this->addbtn(['selecticon']);
		return $this->wrap($this->base_icon());
	}
	protected function base_icon(){
		if(!$this->type)$this->type=__FUNCTION__;
		if(!$option['allowupload']){
			$this->rmbtn(['uploadbtn','selectimg','selectfile']);
		}
		return $this->base_image();
	}
	protected function image(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_image());
	}
	protected function base_image(){
		if(!$this->type)$this->type=__FUNCTION__;
		$this->options['type']='image';
		if(!$this->value)$this->value=$this->option['fileurl'];
		return $this->base_upload();
	}
	protected function upload(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_upload());
	}
	protected function base_upload(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$options=$this->options;
		$name2=$this->name2();
		$html='';
		$html.='<input class="xg-input xg-value-input'.($options['classname']?' '.$options['classname']:'').'" id="upload-input-'.$name2.'" type="text" name="'.$name.'" value="'.str_replace('"','&quot;',$value).'" '.$this->attrs().' /> <!--xg-btns-replace-->';
		$this->html=$html.$this->uploadjs();
		$this->addbtn(['uploadbtn']);
		if($options['type']=='image'){
			$this->addbtn(['selectimg']);
		}else{
			$this->addbtn(['selectfile']);
		}
		return $this;
	}
	protected function uploadjs($name=null,$options=[]){
		if(!$name)$name=$this->name;
		$name2=$this->name2();
		if(!$options)$options=$this->options;
		$uploadurl=$options['uploadurl'];
		if(!$uploadurl)$uploadurl=xg_url('file/upload');
		$max=(int)$options['max'];
		if($options['type'] and !$options['data']['type'])$options['data']['type']=$options['type'];
		$data=xg_jsonstr($options['data']);
		if(!$data)$data='null';
		if($options['input']){
			$input_selector=$options['input'];
		}else{
			$input_selector=".xg-form-item-{$name2} .xg-value-input";
		}
		$html.="<script>xg.mod('upload',function(){
		xg.upload({
			url:'{$uploadurl}',
			max:{$max},
			data:$data,
			done:function(data){
				if(data.ok===true){
					var input=$('{$input_selector}');
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
		.bind('#upload-btn-{$name2}')
		.paste('#upload-input-{$name2}')
		.setname('#upload-input-{$name2}','#upload-btn-{$name2}',\$('#upload-input-{$name2}'),\$('#upload-btn-{$name2}'),'{$name}','{$name2}');
	});</script>";
		return $html;
	}
	protected function btn_group(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_btn_group());
	}
	protected function base_btn_group(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$values=$this->values?$this->values:[];
		$name2=$this->name2();
		$html='';
		foreach($values as $k=>$v){
			$html.='<label class="'.($k==$value?'xg-btn-group-checked':'').'"><input value="'.str_replace('"','&quot;',$k).'"'.(($k==$value)?' checked':'').' name="'.$name.'" type="radio" />'.$v.'</label>';
		}
		$html.='<script>$(".xg-form-item-'.$name2.'").find("label").click(function(){$(this).parent(".xg-form-item").find("label").removeClass("xg-btn-group-checked");$(this).addClass("xg-btn-group-checked")});</script>';
		$this->item_class='xg-btn-group';
		$this->html=$html;
		return $this;
	}
	protected function bool(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_bool());
	}
	protected function base_bool(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$options=$this->options;
		if(is_array($options)){
			if($options['text']){
				$text=$options['text'];
			}elseif($options[0]){
				$text=$options[0];
			}
		}elseif(is_string($options)){
			$text=$options;
			$this->options=[];
		}
		if(strpos($text,'|')!==false){
			$arr=xg_arr($text,'|');
			$text=$arr[0];
		}
		$name2=$this->name2();
		$html='';
		$html.='<label class="xg-label-checkbox"><input type="checkbox" '.($value?' checked':'').' name="'.$name.'"  value="1" '.$this->attrs().'>'.$text.'</label>';
		$this->html=$html;
		return $this;
	}
	protected function password(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_password());
	}
	protected function base_password(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$name2=$this->name2();
		$html='';
		$html.='<input class="xg-input" type="password" name="'.$name.'" value="'.str_replace('"','&quot;',$value).'" '.$this->attrs().' />';
		$this->html=$html;
		return $this;
	}
	protected function textarea(){
		return $this->wrap($this->base_textarea());
	}
	protected function base_textarea(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$name2=$this->name2();
		$html='';
		$html.='<textarea name="'.$name.'" '.$this->attrs().'>'.$value.'</textarea>';
		$this->html=$html;
		return $this;
	}
	protected function editor(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$html.='<div class="xg-form-item-'.$name2.' xg-form-item-'.$name.'">';
		$html.=$this->label();
		$this->base_editor();
		$html.=$this->html;
		$html.='</div>';
		$html.='<script>window.xg_form_items["'.$name.'"]="'.$name2.'";</script>';
		$this->html=$html;
		return $this;
	}
	protected function base_editor(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$name2=$this->name2();
		$value=str_replace('&','&amp;',$value);
		$html='';
		$html.='<div class="xg-form-item-editor"><textarea class="xg-editor-'.$name2.'" autofocus name="'.$name.'" '.$this->attrs().'>'.$value.'</textarea></div>';
		$html.='<script>';
		$html.='xg.mod("editor",function(){';
		$html.='	xg.editor(".xg-editor-'.$name2.'",{';
		$html.='		upload:{url:"'.xg_url('file/upload',array('iseditor'=>'1')).'"}';
		$html.='	}).setname("'.$name.'","'.$name.'");';
		$html.='});';
		$html.='</script>';
		$this->html=$html;
		return $this;
	}
	protected function date(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_date());
	}
	protected function attrs(){
		$data=$this->options['attrs'];
		if($data and is_array($data)){
			$attrs=[];
			foreach($data as $k=>$v){
				$attrs[]=$k.'="'.$v.'"';
			}
			return ' '.xg_str($attrs,' ').' ';
		}
	}
	protected function base_date(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$name2=$this->name2();
		$html='';
		$html.='<input class="xg-input" type="date" name="'.$name.'" value="'.$value.'" id="date_'.$name2.'" '.$this->attrs().' />';
		$this->html=$html;
		return $this;
	}
	protected function datetime(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_datetime());
	}
	protected function base_datetime(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$name2=$this->name2();
		$html='';
		if(is_numeric($value)){
			$value=date('Y-m-d H:i:s',$value);
		}
		$html.='<input class="xg-input '.$this->options['classname'].'" type="datetime-local" name="'.$name.'" value="'.str_replace(' ','T',$value).'" id="datetime_'.$name2.'" '.$this->attrs().' />';
		$this->html=$html;
		return $this;
	}
	protected function radio(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_radio());
	}
	protected function base_radio(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$title=$this->title;
		$value=!is_null($this->value)?$this->value:$this->default;
		$values=$this->values?$this->values:[];
		$options=$this->options;
		$name2=$this->name2();
		$html='';
		if(gettype($values)=='string')$values=$this->extra($values);
		foreach($values as $k=>$v){
			$html.='<label class="xg-label-radio"><input value="'.str_replace('"','&quot;',$k).'"'.($k==$value?' checked':'').' name="'.$name.'" '.(($options['disallow'] and xg_in_array($k,$options['disallow']))?' disabled ':'').' type="radio" '.$this->attrs().' />'.$v.'</label>';
		}
		$this->item_class='xg-flex-wrap';
		$this->html=$html;
		return $this;
	}
	protected function checkbox(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_checkbox());
	}
	protected function base_checkbox(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$title=$this->title;
		$value=$this->value;
		if(is_string($value)){
			if(substr($value,0,1)=='[' and substr($value,-1)==']'){
				$value=xg_jsonarr($value);
			}elseif(substr($value,0,1)=='{' and substr($value,-1)=='}'){
				$value=xg_array_values(xg_jsonarr($value));
			}else{
				$value=xg_arr($value);
			}
		}
		$values=$this->values?$this->values:[];
		$options=$this->options;
		$name2=$this->name2();
		$html='';
		if(gettype($values)=='string')$values=$this->extra($values);
		foreach($values as $k=>$v){
			$html.='<label class="xg-label-checkbox"><input value="'.str_replace('"','&quot;',$k).'"'.((xg_in_array($k,$value) or $k==$value)?' checked':'').' name="'.$name.'" '.(($options['disallow'] and xg_in_array($k,$options['disallow']))?' disabled ':'').' type="checkbox" '.$this->attrs().' />'.$v.'</label>';
		}
		$this->item_class='xg-flex-wrap';
		$this->html=$html;
		return $this;
	}
	protected function region(){
		if(!$this->type)$this->type=__FUNCTION__;
		return $this->wrap($this->base_region());
	}
	protected function base_region(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$values=$this->values?$this->values:[];
		$options=$this->options;
		$name2=$this->name2();
		$html=xg_view(true)->fetch(XG_PHP.'/view/region',['options'=>$options,'value'=>$value,'name'=>$name,'name2'=>$name2]);
		$this->html=$html;
		return $this;
	}
	protected function select(){
		return $this->wrap($this->base_select());
	}
	protected function base_select(){
		if(!$this->type)$this->type=__FUNCTION__;
		$name=$this->name;
		$value=$this->value;
		$values=$this->values?$this->values:[];
		$options=$this->options;
		$name2=$this->name2();
		if(is_array($values) and $values['']){
			$placeholder=0;
		}elseif($this->placeholder){
			$placeholder=$this->placeholder;
		}else{
			$placeholder='请选择';
		}
		$html.='<select class="'.$options['classname'].'" name="'.$name.'" '.($this->multi?' multiple="multiple" style="max-width:500px; min-height:150px;"':'').' '.$this->attrs().'>';
		if($placeholder)$html.='<option value="">'.$placeholder.'</option>';
		if(gettype($values)=='string')$values=$this->extra($values);
		foreach($values as $k=>$v){
			if(is_array($v)){
				$valuekey=$options['valuekey']?$options['valuekey']:'value';
				$titlekey=$options['titlekey']?$options['titlekey']:'title';
				$classname=$v['classname'];
				$titlei=$v[$titlekey];
				$valuei=$v[$valuekey];
			}else{
				$titlei=$v;
				$valuei=$k;
			}
			$html.='<option class="'.$classname.'" value="'.str_replace('"','&quot;',$valuei).'"'.($valuei==$value&&$value!==null&&$value!==''?' selected':'').'>'.$titlei.'</option>';
		}
		$html.='</select>';
		$this->html=$html;
		return $this;
	}
	protected function extra($str){
		$values=[];
		$arr=xg_line_arr($str);
		foreach($arr as $line){
			$linea=xg_arr($line,'=');
			$name=$linea[1]?$linea[1]:$linea[0];
			$value=$linea[0];
			$values[$value]=$name;
		}
		return $values;
	}
	public function __call($method,$args){
		$args2=(array)$args;
		array_unshift($args2,$this);
		$result=xg_hooks($method.'-before','form')->args($args2)->last();
		if(!$result)$result=call_user_func_array([$this,$method],$args);
		$result=xg_hooks($method.'-after','form')->def($result)->args($args2)->last();
		return $result;
	}
	public function get(){
		return $this->__tostring();
	}
	public function __tostring(){
		xg_hooks('create','form')->run($this);
		$html=$this->html;
		if(!$html)$html=$this->base_text();
		foreach($this->btns as $k=>$v){
			if(!xg_in_array($k,$this->rmbtn))$html=str_replace('<!--xg-btns-replace-->',$v.' <!--xg-btns-replace-->',$html);
		}
		if(is_object($html) and $html instanceof self){
			$html=$html->html;
		}
		return $html;
	}
}
?>