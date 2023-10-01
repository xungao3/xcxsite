<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
function xg_form_item_btn($submit='',$classname=''){
	return xg_form($submit,$classname)->button();
}
function xg_form_get_items($filter=[]){
	return xg_form()->filter($filter)->get_items();
}
function xg_form_item_label($title='',$tip='',$helpid=0){
	return xg_form()->label($title,$tip,$helpid);
}
function xg_form_item_hidden($name,$value=''){
	return xg_form($name,$title,$value)->hidden();
}
function xg_form_item_text($name,$title,$value='',$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->text();
}
function xg_form_item_number($name,$title,$value='',$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->number();
}
function xg_form_item_color($name,$title,$value='',$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->color();
}
function xg_form_item_bool($name,$title,$text='开启"',$value='',$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($text)->bool();
}
function xg_form_item_icon($name,$title,$options=[],$value='',$tip='',$helpid=0){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->icon();
}
function xg_form_item_upload($name,$title,$options,$value='',$tip='',$helpid=0){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->upload();
}
function xg_form_item_image($name,$title,$option,$value='',$tip='',$helpid=0){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->image();
}
function xg_form_item_password($name,$title,$value='',$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->password();
}
function xg_form_item_editor($name,$title,$options=[],$value,$tip='',$helpid=0){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->editor();
}
function xg_form_item_textarea($name,$title,$value,$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->textarea();
}
function xg_form_item_datetime($name,$title,$value,$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->datetime();
}
function xg_form_item_date($name,$title,$value,$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value)->tip($tip)->helpid($helpid)->options($options)->date();
}
function xg_form_item_radio($name,$title,$values,$value,$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value,$values)->tip($tip)->helpid($helpid)->options($options)->radio();
}
function xg_form_item_checkbox($name,$title,$values,$value=[],$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value,$values)->tip($tip)->helpid($helpid)->options($options)->checkbox();
}
function xg_form_item_btn_group($name,$title,$values,$value='',$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value,$values)->tip($tip)->helpid($helpid)->options($options)->btn_group();
}
function xg_form_item_select($name,$title,$values,$value='',$tip='',$helpid=0,$options=[]){
	return xg_form($name,$title,$value,$values)->tip($tip)->helpid($helpid)->options($options)->select();
}
?>