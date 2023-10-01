<?php
namespace apps\install\controller;

class index extends base {
	public function init(){
		parent::init();
		if(file_exists(XG_DATA.'/installed'))xg_error('系统已经安装');
	}
	public function index(){
		$this->display();
	}
	public function step1(){
		xg_cookie('xg-check-env','ok');
		$env = xg_install_check_env();
		$dirfile = xg_install_check_dirfile();
		$func = xg_install_check_func();
		$this->assign('env', $env);
		$this->assign('dirfile', $dirfile);
		$this->assign('func', $func);
		$this->display();
	}
	public function step2(){
		if(xg_cookie('xg-check-env')!='ok')xg_error('环境检测没有通过，请调整环境后重试');
		if(XG_POST){
			$post=xg_input('post.');
			if(!$post['in-other-sys']){
				if(!$post['host']){
					xg_error('请填写数据库地址');
				}
				if(!$post['name']){
					xg_error('请填写数据库名称');
				}
				if(!$post['user']){
					xg_error('请填写数据库用户名');
				}
				if(!$post['pass']){
					xg_error('请填写数据库密码');
				}
				if(!$post['prefix']){
					xg_error('请填写数据库表前缀');
				}
				if(!$post['port']){
					xg_error('请填写数据库端口');
				}
			}
			if(!$post['adminname']){
				xg_error('请填写管理员用户名');
			}
			if(!$post['adminpass']){
				xg_error('请填写管理员密码');
			}
			if($post['adminpass']!=$post['adminpass2']){
				xg_error('确认密码不正确');
			}
			if(!$post['adminpath']){
				xg_error('请填写后台管理路径');
			}
			if(preg_match('/[^a-zA-Z0-9]/',$post['adminpath'])){
				xg_error('后台管理路径不正确');
			}
			if(!$post['in-other-sys']){
				if(!xg_db()->test($post))xg_error('数据库连接错误');
			}
			xg_install_write_config($post);
			xg_cookie('admin-name',$post['adminname']);
			xg_cookie('admin-pass',$post['adminpass']);
			xg_cookie('admin-path',$post['adminpath']);
			xg_success('设置成功',xg_url('index/step3'));
		}
		$this->display();
	}
	public function step3(){
		$runtime_dir=XG_RUNTIME;
		xg_deldir($runtime_dir.'/cache/');
		xg_deldir($runtime_dir.'/db/');
		if(!xg_cookie('admin-name') or !xg_cookie('admin-pass') or !xg_cookie('admin-path'))xg_error('管理员信息不能为空');
		if(XG_POST){
			$post=xg_input('post.');
			if($post['step2']=='sql'){
				$sqli=$post['sqli'];
				$filename='install.sql';
				$sqla=xg_install_get_sql_arr($filename);
				$sql=$sqla[$sqli];
				$sql=str_replace(array("\r","\n"),'',$sql);
				$sql=str_ireplace('xg_db_table_',XG_TBPRE,$sql);
				if(!$sql)xg_success('sqlok');
				if(substr($sql,0,12)=='CREATE TABLE'){
					$name = preg_replace("/[^`]+`(\w+)`.*/s", "\\1", $sql);
					$msg = "创建数据表 {$name} ";
				}elseif(substr($sql, 0, 11) == 'ALTER TABLE'){
					$name = preg_replace("/[^`]+`(\w+)`.*/s", "\\1", $sql);
					$msg = "修改数据表 {$name} ";
				}elseif (substr($sql, 0, 11) == 'INSERT INTO'){
					$name = preg_replace("/[^`]+`(\w+)`.*/s", "\\1", $sql);
					$msg = "写入表数据 {$name} ";
				}
				try{
					xg_db()->exec($sql);
					$ok='成功';
				}catch(Exception $e){
					$ok='失败';
				}
				if($msg)xg_success($msg.$ok);
				xg_success('');
			}elseif($post['step2']=='config'){
				
				$data=xg_jsonarr(xg_cont(xg_install_file_path('config.json')));
				foreach($data as $k=>$v){
					if($v['name']=='hashid_key' or $v['name']=='main_key'){
						$data[$k]['value']=xg_randstr(10);
					}
				}
				xg_model('config')->insert($data);
				
				$siddata=xg_http_json('http://e.xg3.cn/esite/get/applysid?domain='.xg('domain'));
				if($siddata){
					$sid=$siddata['sid'];
					xg_model('config')->where('name','service-sid')->update(['value'=>$sid]);
				}
				
				$data=xg_jsonarr(xg_cont(xg_install_file_path('app-list.json')));
				xg_model('app_list')->insert($data);
				
				$data=xg_jsonarr(xg_cont(xg_install_file_path('admin-auth.json')));
				xg_model('admin_auth')->insert($data);
				
				$data=xg_jsonarr(xg_cont(xg_install_file_path('region.json')));
				xg_model('region')->insert($data);
				
				$data=xg_jsonarr(xg_cont(xg_install_file_path('models.json')));
				foreach($data as $model){
					xg_load_model($model);
				}
				
				$msg = "添加设置项 成功";
				xg_success($msg);
			}elseif($post['step2']=='admin'){
				$username=xg_cookie('admin-name');
				$password=xg_cookie('admin-pass');
				$salt=xg_randstr(6);
				$md5password=md5(md5($password).$salt);
				xg_model('admin')->insert(array('username'=>$username,'password'=>$md5password,'salt'=>$salt,'status'=>1,'groupid'=>1));
				xg_install_write_admin_path(xg_cookie('admin-path'));
				$version=xg_install_get_version();
				xg_cont(XG_DATA.'/installed',$version);
				xg_success('创建管理员 成功');
			}
		}
		$this->display();
	}
}