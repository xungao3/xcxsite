<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg\traits;
trait formtype{
protected $type=[
	'tinyint'=>'小数值型(TINYINT)',
	'smallint'=>'中数值型(SMALLINT)',
	'int'=>'大数值型(INT)',
	'bigint'=>'超大数值型(BIGINT)',
	
	'float'=>'数值浮点型(FLOAT)',
	'double'=>'数值双精度型(DOUBLE)',
	
	'varchar'=>'字符型(VARCHAR)',
	'char'=>'定长字符型(CHAR)',
	
	'text'=>'小型字符型(TEXT)',
	'mediumtext'=>'中型字符型(MEDIUMTEXT)',
	'longtext'=>'大型字符型(LONGTEXT)'
];
protected $form=[
	'category'=>'分类',
	'status'=>'状态',
	
	'table'=>'表关联',
	'html'=>'HTML代码',
	
	'text'=>'文本框',
	'textarea'=>'多行文本框',
	'password'=>'密码框',
	
	'radio'=>'单选',
	'checkbox'=>'多选',
	
	'select'=>'选择框',
	
	'editor'=>'编辑器',
	
	'region'=>'地区',
	'city'=>'城市',
	'province'=>'省份',
	
	'regionid'=>'地区ID',
	'cityid'=>'城市ID',
	'provinceid'=>'省份ID',
	
	'datetime'=>'日期和时间',
	//'date'=>'日期',
	//'time'=>'时间',
	
	'image'=>'上传图片',
	'file'=>'上传文件',
	'video'=>'上传视频',
	
	'bool'=>'布尔值',
	'color'=>'颜色值'
];
}
?>