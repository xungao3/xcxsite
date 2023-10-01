<?php

function xg_install_get_sql_arr($filename){
	$cont=file_get_contents(xg_install_file_path($filename));
	$cont=str_replace("\r","\n",$cont);
	$cont=str_replace("\n\n","\n",$cont);
	$sqla=xg_arr($cont,";\n");
	return $sqla;
}

function xg_install_file_path($name){
	return XG_APPS.'/install/files/'.$name;
}
function xg_install_get_version() {
	return xg_cont(XG_DATA.'/version');
}
function xg_install_check_env() {
	$items = array(
		'os' => array('操作系统', '不限制', '类Unix', PHP_OS, 'success'),
		'php' => array('PHP版本', '5.6', '7.1+', PHP_VERSION, 'success'),
		'upload' => array('附件上传最大支持', '20M+', '20M+', '未知', 'success'),
		'post' => array('post最大支持', '20M+', '20M+', '未知', 'success'),
		'gd' => array('GD库', '2.0', '2.0+', '未知', 'success'),
		'disk' => array('磁盘空间', '35M', '不限制', '未知', 'success'),
	);

	//PHP环境检测
	if ($items['php'][3] < $items['php'][1]) {
		$items['php'][4] = 'error';
		xg_cookie('xg-check-env','fail');
	}

	//附件上传检测
	if (@ini_get('file_uploads')) {
		$items['upload'][3] = ini_get('upload_max_filesize');
	}

	$items['post'][3] = ini_get('post_max_size');

	//GD库检测
	$tmp = function_exists('gd_info') ? gd_info() : array();
	if (empty($tmp['GD Version'])) {
		$items['gd'][3] = '未安装';
		$items['gd'][4] = 'error';
		xg_cookie('xg-check-env','fail');
	} else {
		$items['gd'][3] = $tmp['GD Version'];
	}
	unset($tmp);

	//磁盘空间检测
	if (function_exists('disk_free_space')) {
		$items['disk'][3] = floor(disk_free_space('./') / (1024 * 1024)) . 'M';
	}

	return $items;
}

/**
 * 目录，文件读写检测
 * @return array 检测数据
 */
function xg_install_check_dirfile() {
	$items = array(
		array('dir', '可写', 'success', XG_PUBLIC, '可写'),
		array('file', '可写', 'success', XG_CONFIG.'/app.php', '可写'),
		array('dir', '可写', 'success', XG_RUNTIME, '可写'),
	);
	foreach ($items as $key=>$val) {
		if ($items[$key][0] == 'dir') {
			$item = $items[$key][3] = rtrim($items[$key][3], '/') . '/';
		}

		if ('可读' == $items[$key][1]) {
			if (!@is_readable($item)) {
				$items[$key][1] = '不可读';
				$items[$key][2] = 'error';
				xg_cookie('xg-check-env','fail');
			}else{
				$items[$key][1] = '可读';
			}
		} else if ('dir' == $items[$key][0]) {
			if (!@is_writable($item)) {
				if (is_dir($item)) {
					$items[$key][1] = '可读';
					$items[$key][2] = 'error';
					xg_cookie('xg-check-env','fail');
				} else {
					$items[$key][1] = '不存在';
					$items[$key][2] = 'error';
					xg_cookie('xg-check-env','fail');
				}
			}
		} else {
			if (@file_exists($item)) {
				if (!is_writable($item)) {
					$items[$key][1] = '不可写';
					$items[$key][2] = 'error';
					xg_cookie('xg-check-env','fail');
				}
			} else {
				if (!@is_writable(dirname($item))) {
					$items[$key][1] = '不存在';
					$items[$key][2] = 'error';
					xg_cookie('xg-check-env','fail');
				}
			}
		}
	}
	return $items;
}
function xg_install_check_func() {
	$items = array(
		array('PDO','支持','success','类'),
		array('file_get_contents', '支持', 'success', '函数'),
		array('mb_strlen', '支持', 'success', '函数'),
		array('mb_substr', '支持', 'success', '函数'),
		array('mb_convert_encoding', '支持', 'success', '函数'),
	);

	foreach ($items as $key=>$val) {
		if (('类' == $items[$key][3] && !class_exists($items[$key][0]))
			|| ('模块' == $items[$key][3] && !extension_loaded($items[$key][0]))
			|| ('函数' == $items[$key][3] && !function_exists($items[$key][0]))
		) {
			$items[$key][1] = '不支持';
			$items[$key][2] = 'error';
			xg_cookie('xg-check-env','fail');
		}
	}

	return $items;
}
function xg_install_write_config($config) {
	if (is_array($config)){
		$cont=xg_cont(xg_install_file_path('database.php'));
		foreach($config as $k=>$v){
			$cont=str_replace("[!{$k}!]",str_replace("'","\\'",$v),$cont);
		}
		if (!xg_cont(XG_CONFIG.'/database.php',$cont)) {
			xg_error('database配置文件写入失败');
		}
		return true;
	}
	xg_error('设置信息错误');
}
function xg_install_write_admin_path($adminpath) {
	$cont="<?php
return [
	'map'=>['$adminpath'=>'admin'],
	'rename'=>[]
];
?>";
	if (!xg_cont(XG_CONFIG.'/app.php',$cont)) {
		xg_error('app配置文件写入失败');
	}
	return true;
}
?>