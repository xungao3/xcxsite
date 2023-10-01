<?php
namespace apps\admin\controller;

class addons extends base{
	use \xg\traits\addons;
	function install(){
		$name=xg_input('name');
		$config=xg_load_addon_config($name);
		$insted=xg_model('addons')->where('name',$name)->value('id');
		if($insted){
			xg_error('此插件已经安装');
		}else{
			xg_model('addons')->insert(array('name'=>$name,'title'=>$config['title'],'type'=>$config['type'],'version'=>$config['version'],'hooks'=>xg_jsonstr($config['hooks']),'status'=>1,'addtime'=>XG_TIME));
			if($config['database']['tables']){
				$tables=$config['database']['tables'];
				foreach($tables as $table=>$sql){
					if(!xg_table_exist($table)){
						$sql=str_replace('xg_db_table_',XG_TBPRE,$sql);
						xg_db()->query($sql);
					}
				}
			}
			$this->copyfiles($name);
			xg_cache('hooks',null);
			xg_success('安装成功');
		}
	}
	function copyfiles($name){
		mkdir(XG_PUBLIC.'/static/addons/'.$name,0755,true);
		xg_deldir(XG_PUBLIC.'/static/addons/'.$name);
		xg_copydir(XG_PATH.'/addons/'.$name.'/files',XG_PUBLIC.'/static/addons/'.$name);
		xg_copydir(XG_PATH.'/addons/'.$name.'/static',XG_PUBLIC.'/static/addons/'.$name);
	}
	function save_config(){
		$name=xg_input('name');
		if($name){
			$data=[];
			$names=[];
			if(is_array($name)){
				foreach($name as $namei){
					$config=xg_addon_config($namei);
					$data[$namei]=$config;
					$names[$namei]=xg_load_addon_config($namei)['title'];
				}
				$savename='讯高小程序插件设置文件';
			}else{
				$config=xg_addon_config($name);
				$data[$name]=$config;
				$title=xg_load_addon_config($name)['title'];
				$names[$name]=$title;
				$savename='讯高小程序插件-'.$title.'-设置文件';
			}
			$data=['xg-saved-addon-config'=>['data'=>$data,'names'=>$names]];
			$cont=xg_jsonstr($data);
			xg_download($cont,$savename.'.json');
		}
		$list=xg_model('addons')->where('config','!=','')->column('title','name');
		$this->display(['list'=>$list]);
	}
	function config(){
		$name=xg_input('name');
		if(XG_POST){
			$data=xg_input('post.');
			xg_model('addons')->where('name',$name)->update(array('config'=>xg_jsonstr($data)));
			xg_cache('hooks',null);
			xg_success('保存成功',null);
		}
		$config=xg_load_addon_config($name,'config');
		$data=xg_jsonarr(xg_model('addons')->where('name',$name)->value('config'));
		$this->display(['config'=>$config,'data'=>$data,'name'=>$name]);
	}
	function reload(){
		$name=xg_input('name');
		$hooks=xg_load_addon_config($name,'hooks');
		xg_model('addons')->where('name',$name)->update(array('hooks'=>xg_jsonstr($hooks)));
		$this->copyfiles($name);
		xg_success(array('msg'=>'重载成功'));
	}
	function status(){
		$name=xg_input('name');
		$status=xg_model('addons')->where('name',$name)->value('status');
		xg_model('addons')->where('name',$name)->update(array('status'=>($status?0:1)));
		xg_cache('hooks',null);
		xg_success(array('msg'=>'更新成功','type'=>'status','status'=>($status?0:1)));
	}
	function uninstall(){
		$name=xg_input('name');
		$config=xg_load_addon_config($name);
		xg_model('addons')->where('name',$name)->delete();
		xg_cache('hooks',null);
		xg_deldir(XG_PUBLIC.'/static/addons/'.$name);
		xg_success('卸载成功');
	}
	function addonlist(){
		$dirs = glob(XG_PATH.'/addons/*',GLOB_ONLYDIR);
		$insed=xg_model('addons')->column('name,status','name');
		$list=array();
		foreach($dirs as $k=>$v){
			$name=basename($v);
			$config=xg_load_addon_config($name);
			if($config['name']!=$name)continue;
			$item=array(
				'name'=>$config['name'],
				'title'=>$config['title'],
				'developer'=>$config['developer'],
				'version'=>$config['version'],
				'config'=>$config['config'],
				'description'=>$config['description'],
				'apps'=>array_keys($config['hooks'])
			);
			if($insed[$name]){
				$item['status']=$insed[$name]['status'];
				$item['insed']=1;
			}
			$list[]=$item;
		}
		$this->assign('list',$list);
		return $this->display();
	}
}
?>