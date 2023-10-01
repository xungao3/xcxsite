<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
function xg_drop_column($table,$column){
	if(substr($table,0,strlen(XG_TBPRE))!=XG_TBPRE)$table=XG_TBPRE.$table;
	if($notnull)$null='NOT NULL';
	$result=xg_db()->exec("ALTER TABLE `$table` DROP COLUMN `$column`;");
	xg_remove_db_cache($table);
	return $result;
}
function xg_remove_db_cache($table){
	$name=xg_config('database.name');
	$type=xg_config('database.type');
	@unlink(XG_RUNTIME.'/db/'.$type.'.'.trim($name,'`').'.'.trim($table,'`').'.php');
}
function xg_rename_column($table,$old,$new,$comment=''){
	if(substr($table,0,strlen(XG_TBPRE))!=XG_TBPRE)$table=XG_TBPRE.$table;
	$oldinfo=xg_field_info($table,$old);
	$type=$oldinfo['Type'];
	if($oldinfo['Null']=='NO')$null='NOT NULL';
	if($comment)$comment="COMMENT '$comment'";
	$result=xg_db()->exec("ALTER TABLE `$table` CHANGE COLUMN `$old` `$new` $type $null $comment;");
	xg_remove_db_cache($table);
	return $result;
}
function xg_change_column($table,$column,$type,$length=0,$comment='',$notnull=true){
	if(substr($table,0,strlen(XG_TBPRE))!=XG_TBPRE)$table=XG_TBPRE.$table;
	if($length)$type.="($length)";
	if($notnull)$null='NOT NULL';
	if($comment)$comment="COMMENT '$comment'";
	$result=xg_db()->exec("ALTER TABLE `$table` MODIFY COLUMN `$column` $type $null $comment;");
	xg_remove_db_cache($table);
	return $result;
}
function xg_add_column($table,$column,$type,$length=0,$comment='',$notnull=true){
	if(substr($table,0,strlen(XG_TBPRE))!=XG_TBPRE)$table=XG_TBPRE.$table;
	if($length)$type.="($length)";
	if($notnull)$null='NOT NULL';
	if($comment)$comment="COMMENT '$comment'";
	$result=xg_db()->exec("ALTER TABLE `$table` ADD COLUMN `$column` $type $null $comment;");
	xg_remove_db_cache($table);
	return $result;
}
function xg_field_info($table,$field){
	return xg_fields_info($table)[$field];
}
function xg_fields_info($table){
	if(substr($table,0,strlen(XG_TBPRE))!=XG_TBPRE)$table=XG_TBPRE.$table;
	$info=xg_db()->query("describe $table");
	$data=[];
	foreach($info as $v){
		$data[$v['Field']]=$v;
	}
	return $data;
}
function xg_fields($table,$fields=''){
	static $data=array();
	$tbpre=XG_TBPRE;
	if(($pos=strpos($table,'@'))!==false){
		$sys=substr($table,0,$pos);
		$systable=substr($table,$pos+1);
		$tbpre=xg_config("sys.{$sys}.db.prefix");
	}
	if(!$systable and substr($table,0,strlen($tbpre))!=$tbpre)$table=$tbpre.$table;
	if($systable and substr($systable,0,strlen($tbpre))!=$tbpre)$systable=$tbpre.$systable;
	if(!$data[$table]){
		$db=xg_db();
		if($sys)$db->sys($sys);
		$data[$table]=$db->table_fields($systable?$systable:$table);
	}
	$arr=$data[$table];
	if($fields){
		$fields=xg_arr($fields);
		foreach($fields as $k=>$v){
			if(!$v or !xg_in_array($v,$arr)){
				unset($fields[$k]);
			}
		}
		return $fields;
	}
	return $arr;
}
function xg_drop_table($table){
	if(substr($table,0,strlen(XG_TBPRE))!=XG_TBPRE)$table=XG_TBPRE.$table;
	$result=xg_db()->exec("DROP TABLE IF EXISTS `{$table}`;");
	xg_remove_db_cache($table);
	return $result;
}
function xg_rename_table($old,$new){
	if(substr($new,0,strlen(XG_TBPRE))!=XG_TBPRE)$new=XG_TBPRE.$new;
	if(substr($old,0,strlen(XG_TBPRE))!=XG_TBPRE)$old=XG_TBPRE.$old;
	$result=xg_db()->exec("RENAME TABLE $old TO `{$new}`;");
	xg_remove_db_cache($old);
	xg_remove_db_cache($new);
	return $result;
}
function xg_table_exist($table){
	if(substr($table,0,strlen(XG_TBPRE))!=XG_TBPRE)$table=XG_TBPRE.$table;
	static $list=array();
	if(!$list[$table]){
		$list[$table]=xg_db()->query("SHOW TABLES LIKE '{$table}'");
	}
	return $list[$table];
}
function xg_table_state($table){
	if(substr($table,0,strlen(XG_TBPRE))!=XG_TBPRE)$table=XG_TBPRE.$table;
	$result=xg_db()->query("SHOW CREATE TABLE `{$table}`");
	$result=$result[0]['Create Table'];
	return $result;
}
?>