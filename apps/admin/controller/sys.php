<?php
namespace apps\admin\controller;
class sys extends base{
	use \xg\traits\sys;
	function install(){
		$name=xg_input('name');
		$config=xg_load_sys_config($name);
		$info=xg_model('sys')->where('name',$name)->json('database')->find();
		if(XG_POST){
			if($info){
				xg_error('此系统已经安装');
			}else{
				$post=xg_input('post.');
				$this->check($post);
				if($tables=$config['install']['tables']){
					foreach($tables as $table=>$sql){
						$sql=str_replace('[XG_TBPRE]',XG_TBPRE,$sql);
						if(!xg_table_exist($table))xg_db()->exec($sql);
					}
				}
				$this->copyfiles($name);
				$data=xg_merge($post,$config,array('name'=>$name,'title'=>$config['title'],'status'=>1,'time'=>XG_TIME));
				xg_model('sys')->json('database,config')->insert($data);
				xg_cache('sys',null);
				xg_success('安装成功',xg_url('syslist'));
			}
		}else{
			$info=[
				'database'=>[
					'host'=>'127.0.0.1',
					'port'=>'3306',
					'name'=>'ecms',
					'user'=>'root',
					'char'=>'utf8',
					'cache'=>1,
					//'prefix'=>xg_randstr(4).'_'
				]
			];
			$this->display(['info'=>$info]);
		}
	}
	function status(){
		$name=xg_input('name');
		$status=xg_model('sys')->where('name',$name)->value('status');
		xg_model('sys')->where('name',$name)->update(array('status'=>($status?0:1)));
		xg_cache('sys',null);
		xg_success(array('msg'=>'更新成功','type'=>'status','status'=>($status?0:1)));
	}
	function uninstall(){
		$name=xg_input('name');
		$config=xg_load_addon_config($name);
		xg_model('sys')->where('name',$name)->delete();
		xg_cache('sys',null);
		xg_success('卸载成功');
	}
	function copyfiles($name){
		$name=xg_input('name');
		xg_copy(XG_SYS.'/'.$name.'/static/',XG_PUBLIC.'/static/sys/'.$name.'/');
	}
	function check($post){
		if(!file_exists($post['path']))xg_error('提交的系统路径不存在<br>或无法访问');
		if(!is_dir($post['path']))xg_error('提交的系统路径错误');
		if(!$post['url'])xg_error('请填写系统访问地址');
		if(!xg_db()->test($post['database']))xg_error('数据库无法连接');
	}
	function reload(){
		$name=xg_input('name');
		$this->copyfiles($name);
		xg_success('重载成功');
	}
	function config(){
		$name=xg_input('name');
		$config=xg_load_sys_config($name);
		$info=xg_model('sys')->where('name',$name)->json('database')->find();
		if(XG_POST){
			if(!$info){
				xg_error('此系统未安装');
			}else{
				$post=xg_input('post.');
				$this->check($post);
				$data=xg_merge($post,$config,array('name'=>$name,'title'=>$config['title'],'status'=>1,'time'=>XG_TIME));
				xg_model('sys')->json('database,config')->update($data,$info['id']);
				xg_cache('sys',null);
				xg_success('修改成功',xg_url('syslist'));
			}
		}else{
			$this->display(['info'=>$info]);
		}
	}
	function syslist(){
		$dirs = glob(XG_PATH.'/sys/*',GLOB_ONLYDIR);
		$insed=xg_model('sys')->column('*','name');
		$list=array();
		foreach($dirs as $k=>$v){
			$name=basename($v);
			$config=xg_load_sys_config($name);
			if($config['name']!=$name)continue;
			$item=array(
				'name'=>$config['name'],
				'title'=>$config['title'],
			);
			if($insed[$name]){
				$item=$insed[$name];
				$item['status']=$insed[$name]['status'];
				$item['insed']=1;
			}
			$list[]=$item;
		}
		$this->display(['list'=>$list]);
	}
}
?>