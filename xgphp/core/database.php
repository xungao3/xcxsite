<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class database{
	protected $conf=array();
	protected $sys=null;
	protected $name='';
	protected $names=[];
	protected $table='';
	protected $tables=[];
	protected $where=null;
	protected $limit='';
	protected $order='';
	protected $group=[];
	protected $time=[];
	protected $timeformat='Y-m-d H:i:s';
	protected $json=null;
	protected $fields=null;
	protected $without=null;
	protected $count=null;
	protected $master;
	protected $slave;
	protected $cache=null;
	protected $cachetime=0;
	protected $begin=false;
	protected $pk='id';
	protected $querytype=null;
	protected $join=[];
	protected $alias=null;
	protected $aliases=[];
	protected $getter=[];
	protected $hooks=['runhook','alias','join','time','pk','table','prefix','name','fields','field','where','where_or','between','not_between','order','group','limit','page','select','find','column','values','value','count','max','min','sum','abs','add','insert','save','update','delete','inc','dec','with','without','json','cache','begin','commit','roll_back','sys','conf','table_fields','query','exec','tables','names','multi_query','multi_fields','multi_conds'];
	protected $runhook=1;
	public function __construct($conf=null){
		if($this->sys and $this->sys!='xg'){
			$this->sys();
		}elseif($conf){
			if(!$conf=$this->conf($conf))xg_sys_error('database config error!');
			$this->sets($conf);
		}
		$this->where=new where();
	}
	public function runhook($runhook){
		$this->runhook=$runhook;
		return $this;
	}
	protected function sys($name=null){
		if($name)$this->sys=$name;
		if($conf=xg_config("sys.{$this->sys}.database")){
			if(!$conf=$this->conf($conf))xg_sys_error('database config error!');
			$this->sets($conf);
		}
		return $this;
	}
	protected function sets($name,$value=null){
		if(is_array($name)){
			$this->conf=xg_merge($this->conf,$name);
			return $this->conf;
		}elseif(!is_null($value)){
			$this->conf[$name]=$value;
			return $this;
		}elseif($name){
			return $this->conf[$name];
		}
	}
	protected function parse_alias($info,$alias=null){
		if(is_array($info)){
			if($info[0] and $info[1]){
				$name=$info[0];
				$alias=$info[1];
			}else{
				$name=array_keys($info)[0];
				$alias=$key.' as '.$info[$key];
			}
		}elseif(is_string($info)){
			if(stripos($info,' as ')!==false){
				$info=preg_split('/[ ]+(as|AS)[ ]+/',$info);
				if(is_numeric($info[0]) or (substr($info[0],0,1)=='"' and substr($info[0],-1)=='"') or (substr($info[0],0,1)=="'" and substr($info[0],-1)=="'")){
					$value=$info[0];
				}else{
					$name=$info[0];
				}
				$alias=$info[1];
			}elseif(strpos($info,' ')!==false){
				$info=preg_split('/[ ]+/',$info);
				$name=$info[0];
				$alias=$info[1];
			}else{
				$name=$info;
			}
		}
		if($name){
			$name=str_replace('`','',$name);
			if(strpos($name,'.')!==false){
				$arr=xg_arr($name,'.');
				$prefix=$arr[0];
				$name=$arr[1];
			}
		}
		return [$name,$alias,$prefix,$value];
	}
	protected function alias($name){
		$this->alias=$name;
		$this->where->alias($this->alias);//查询连环操作时 alias要放到where前边
		return $this;
	}
	protected function join($table,$condition=null,$type='inner'){
		$table=$this->parse_alias($table);
		if($condition){
			$where=new where();
			$condition=$where->where($condition)->get();
		}
		$this->join[]=['table'=>$table,'on'=>$condition,'type'=>$type];
		return $this;
	}
	protected function time($fields,$format=null){
		$this->time=xg_arr($fields);
		if($format)$this->timeformat=$format;
		return $this;
	}
	protected function pk($pk=null){
		if($pk){
			$this->pk=$pk;
			$this->where->pk($this->pk);
			return $this;
		}else{
			$pk=$this->table_info($table,$cache)['pk'];
			$this->pk=$pk;
			$this->where->pk($this->pk);
			return $pk;
		}
	}
	protected function table($table=null){
		if(!$table){
			if(!$this->table)$this->table=[$this->conf['prefix'].$this->name];
			return $this->table;
		}
		$this->table=$this->parse_alias($table);
		$this->where->pk($this->pk());
		return $this;
	}
	protected function tables($tables=null){
		if(!$tables){
			foreach($this->names as $name){
				$table=$this->conf['prefix'].$name;
				$this->tables[$table]=[$table];
			}
			return $this->tables;
		}
		$tables=xg_arr($tables);
		foreach($tables as $table){
			$this->tables[]=$this->parse_alias($table);
		}
		return $this;
	}
	protected function name($name=null){
		if(!$name)$name=$this->name;
		if(is_array($name)){
			return $this->table($name);
		}
		return $this->table($this->conf['prefix'].$name);
	}
	protected function names($names=null){
		$this->names=xg_arr($names);
		$this->tables();
		return $this;
	}
	protected function prefix($prefix){
		$this->sets('prefix',$prefix);
		if($this->name)return $this->name($this->name);
		return $this;
	}
	protected function fields($fields=null){
		if($fields=='*' or !$fields){
			$this->fields=$this->table_fields($this->table);
		}else{
			$this->fields=$fields;
		}
		return $this;
	}
	protected function field($fields=null){
		return $this->fields($fields);
	}
	protected function build_where(){
		$where=$this->where->build();
		$where=preg_replace('/[\s]+([`]?\[pk\][`]?)[\s]*/',' `'.$this->pk().'` ',$where);
		return $where;
	}
	protected function where($p1,$p2=null,$p3=null,$join='and'){
		if(is_numeric($p1)){
			$p2=$p1;
			$p1=$this->pk();
		}
		$this->where->where($p1,$p2,$p3,$join);
		return $this;
	}
	protected function where_or($p1,$p2=null,$p3=null){
		$this->where->where($p1,$p2,$p3,'or');
		return $this;
	}
	protected function between($p1,$p2,$p3,$join='and'){
		$this->where->between($p1,$p2,$p3,$join);
		return $this;
	}
	protected function not_between($p1,$p2,$p3,$join='and'){
		$this->where->not_between($p1,$p2,$p3,$join);
		return $this;
	}
	protected function order($order){
		$order=strtolower($order);
		if(!strpos($order,' asc') and !strpos($order,' desc')){
			$order=$order.' asc';
		}
		$this->order=$order;
		return $this;
	}
	protected function group($group){
		$arr=xg_arr($group);
		foreach($arr as $g){
			$g=strtolower($g);
			$this->group[]='`'.trim($g,' `').'`';
		}
		return $this;
	}
	protected function limit($a,$b=null){
		if(is_numeric($b)){
			$limit=$a.','.$b;
		}else{
			$limit=$a;
		}
		$this->limit=$limit;
		return $this;
	}
	protected function page($page,$pagesize=12){
		if(!$page)return $this;
		if(is_array($page)){
			if($page[0] and $page[1]){
				$pagesize=$page[1];
				$page=$page[0];
			}elseif($page['page'] and $page['pagesize']){
				$pagesize=$page['pagesize'];
				$page=$page['page'];
			}else{
				return $this;
			}
		}
		$this->limit((($page-1)*$pagesize).','.$pagesize);
		return $this;
	}
	protected function build_sql(){
		$fields=$this->build_fields();
		$where=$this->build_where();
		$limit=$this->limit?' limit '.$this->limit:'';
		$order=$this->order?' order by '.$this->order:'';
		$group=$this->group?' group by '.xg_str($this->group):'';
		$join='';
		if($this->join){
			foreach($this->join as $v){
				$join=" $v[type] join ".$this->join_table($v['table'])." ".($v['on']?" on $v[on] ":'');
			}
		}
		$table=$this->table;
		$table=$this->join_table($table,$this->alias);
		if(!$fields){
			$fields='*';
		}elseif($this->join){
			$fields=xg_arr($fields);
			foreach($fields as $k=>$f){
				if(strpos($f,'(')===false){
					$f=str_replace('`','',$f);
					if(strpos($f,'.')<=0){
						$f=trim($f,'.');
						$f='`'.$this->alias.'`.`'.$f.'`';
					}
				}
				$fields[$k]=$f;
			}
			$fields=xg_str($fields);
		}
		$sql="select $fields from $table $join $where $group $order $limit";
		return $sql;
	}
	protected function multi_fields($table){
		if(!$this->fields)xg_sys_error('multi-table query, table fields must be set.');
		if(is_string($table))$table=[$table];
		$this->fields=xg_arr($this->fields);
		if($this->sys and $this->sys!='xg')$sysprefix=$this->sys.'@';
		$fields=xg_fields($sysprefix.$table[0],$this->fields);
		$diff=array_diff($this->fields,$fields);
		foreach($fields as $k=>$f){
			if($as=$this->aliases[$f]){
				$fields[$k]="`$f` as `$as`";
			}
		}
		if($diff){
			foreach($diff as $f){
				if($as=$this->aliases[$f] and xg_in_array($f,$fields)){
					$fields[]="`$f` as `$as`";
				}elseif($as=$this->parse_alias($f) and $as[1] and xg_in_array($as[1],$fields)){
					if($as[2]){
						$fields[]="'$as[2]' as `$as[1]`";
					}else{
						$fields[]="`$as[0]` as `$as[1]`";
					}
				}else{
					$fields[]="'' as `$f`";
				}
			}
		}
		return $fields;
	}
	protected function multi_conds(){
		return [];
	}
	protected function multi_query(){
		$wheres=$this->multi_conds();
		if($this->limit)$limit=' limit '.$this->limit;
		if($this->order)$order=' order by '.$this->order;
		foreach($this->tables as $table){
			$fields=$this->multi_fields($table);
			foreach($fields as $k=>$f){
				if(strpos($f,'`')===false){
					$fields[$k]="`$f`";
				}else{
					$fields[$k]=$f;
				}
			}
			$fieldstr=xg_str($fields);
			$where=$wheres[$table[0]];
			if($where)$where=" and ($where) ";
			$sql.=($sql?' union ':'')." select {$fieldstr} from {$table[0]} where 1=1 $where ";
		}
		$where=$this->where->get();
		if($where)$where=" and ($where) ";
		return $this->query("select * from ($sql) as alldata where 1=1 $where $order {$limit}");
	}
	protected function multi_count(){
		$wheres=$this->multi_conds();
		$sql="select ( 0 ";
		$where=$this->where->get();
		foreach($this->tables as $table){
			$where2=$wheres[$table[0]];
			if(trim($where2))$where2=" and ($where2)";
			$sql.=" + (select count(*) from {$table[0]} where ($where) $where2) ";
		}
		$sql.=") as c";
		$count=$this->query($sql)[0]['c'];
		return intval($count);
	}
	protected function select($type='a'){
		$json=$this->json;
		$time=$this->time;
		$timeformat=$this->timeformat;
		if(!$this->querytype)$querytype=$this->querytype='select';
		if($this->cache){
			$cache=$this->cache;
			$cachetime=$this->cachetime;
			if(!is_null($result=xg_cache($this->cache)))return $result;
		}
		if($this->tables()){
			$result=$this->multi_query();
		}else{
			$sql=$this->build_sql();
			$result=$this->sql($sql);
			if(!$result)return false;
			$result=$result->fetchAll($this->fetch_type($type));;
		}
		if($result and $time){
			if(is_string($time))$time=xg_arr($time);
			foreach($time as $v){
				foreach($result as $key=>$row){
					if($row[$v]){
						$result[$key][$v]=date($this->timeformat,$row[$v]);
					}else{
						$result[$key][$v]='';
					}
				}
			}
		}
		if($result and $json){
			if(is_string($json))$json=xg_arr($json);
			foreach($json as $v){
				foreach($result as $key=>$row){
					$result[$key][$v]=xg_jsonarr($row[$v]);
				}
			}
		}
		if($cache and $result and $querytype=='select')xg_cache($cache,$result,$cachetime);
		return $result;
	}
	protected function find($a=null,$b='a'){
		$json=$this->json;
		$time=$this->time;
		if(!$this->querytype)$querytype=$this->querytype='find';
		if($this->cache){
			$cache=$this->cache;
			$cachetime=$this->cachetime;
			if(!is_null($result=xg_cache($this->cache)))return $result;
		}
		if(is_numeric($a)){
			$this->where($this->pk(),$a);
		}elseif(is_string($a)){
			$b=$a;
		}
		$sql=$this->build_sql();
		$result=$this->sql($sql);
		if(!$result)return false;
		$result=$result->fetch($this->fetch_type($b));
		if($result and $time){
			if(is_string($time))$time=xg_arr($time);
			foreach($time as $v){
				$result[$v]=date($this->timeformat,$result[$v]);
			}
		}
		if($result and $json){
			if(is_string($json))$json=xg_arr($json);
			foreach($json as $v){
				$result[$v]=xg_jsonarr($result[$v]);
			}
		}
		if($cache and $result and $querytype=='find')xg_cache($cache,$result,$cachetime);
		return $result;
	}
	protected function column($fields=null,$key=null){
		if(!$this->querytype)$querytype=$this->querytype='column';
		if($this->cache){
			$cache=$this->cache;
			$cachetime=$this->cachetime;
			if(!is_null($result=xg_cache($this->cache)))return $result;
		}
		if($this->tables()){
			if($fields=='*')$fields=null;
			if($fields){
				$this->fields($fields);
				$fields=xg_arr($fields);
			}else{
				$fields=xg_arr($this->fields);
			}
			if(!$key)$key=$fields[0];
			if(count($fields)==1)$rtfield=$fields[0];
			if(!xg_in_array($key,$fields) and !xg_in_array($key,$this->aliases)){
				$nokey=true;
				$fields[]=$key;
				$this->fields($fields);
			}
			$data=$this->multi_query();
		}else{
			if(!$key)$key=$this->pk();
			if($fields=='*'){
				$fields=$this->table_fields($this->table);
			}elseif($fields){
				$fields=xg_arr($fields);
				if(count($fields)==1)$rtfield=$fields[0];
				if(!xg_in_array($key,$fields) and !xg_in_array($key,$this->aliases)){
					$nokey=true;
					$fields[]=$key;
				}
			}
			if($fields)$this->fields($fields);
			$data=$this->select();
		}
		if(!$data)return false;
		$result=array();
		foreach($data as $v){
			if(!$rtfield and $nokey)unset($v[$key]);
			if($rtfield){
				$result[$v[$key]]=$v[$rtfield];
			}elseif($keyv=$v[$key]){
				$result[$keyv]=$v;
			}else{
				$result[]=$v;
			}
		}
		if($cache and $result and $querytype=='column')xg_cache($cache,$result,$cachetime);
		return $result;
	}
	protected function values($fields=null){
		if($fields)$this->fields($fields);
		$result=$this->select();
		if($result){
			foreach($result as $k=>$v){
				$result[$k]=current($v);
			}
		}
		return $result;
	}
	protected function value($field){
		if(!$this->querytype)$querytype=$this->querytype='value';
		if($this->cache){
			$cache=$this->cache;
			$cachetime=$this->cachetime;
			if(!is_null($result=xg_cache($this->cache)))return $result;
		}
		$this->fields($field);
		if(!$this->build_fields()){
			xg_sys_error('no such field `'.$field.'`!');
		}
		$result=$this->find();
		$result=$result?$result[$field]:false;
		if($cache and $result)xg_cache($cache,$result,$cachetime);
		return $result;
	}
	protected function count(){
		if(!$this->querytype)$querytype=$this->querytype='count';
		if(!is_null($this->count))return $this->count;
		if($this->tables()){
			return $this->multi_count();
		}else{
			$result=$this->fields('count(*)')->find('n');
			return $this->count=intval($result[0]);
		}
	}
	protected function max($f){
		if(!$this->querytype)$querytype=$this->querytype='max';
		return intval($this->fields('max(`'.$f.'`)')->find('n')[0]);
	}
	protected function min($f){
		if(!$this->querytype)$querytype=$this->querytype='min';
		return intval($this->fields('min(`'.$f.'`)')->find('n')[0]);
	}
	protected function sum($f){
		if(!$this->querytype)$querytype=$this->querytype='sum';
		return intval($this->fields('sum(`'.$f.'`)')->find('n')[0]);
	}
	protected function abs($f){
		if(!$this->querytype)$querytype=$this->querytype='abs';
		return intval($this->fields('abs(`'.$f.'`)')->find('n')[0]);
	}
	protected function avg($f){
		if(!$this->querytype)$querytype=$this->querytype='avg';
		return intval($this->fields('avg(`'.$f.'`)')->find('n')[0]);
	}
	protected function add($values=[]){
		return $this->insert($values);
	}
	protected function insert($values=[],$insfileds=null){
		$json=$this->json;
		$time=$this->time;
		$timeformat=$this->timeformat;
		if(!$this->querytype)$querytype=$this->querytype='insert';
		$allow=$this->table_fields();
		if(!$this->table)xg_sys_error('no table name!');
		//使用了绑定$values=xg_addslashes($values);
		if($time){
			if(is_string($time))$json=xg_arr($time);
			foreach($time as $v){
				if(!$values[$v])$values[$v]=XG_TIME;
			}
		}
		if($json){
			if(is_string($json))$json=xg_arr($json);
			foreach($json as $v){
				$values[$v]=xg_jsonstr($values[$v]);
			}
		}
		$fields=array();
		$data=$datas=array();
		$datan=0;
		if(is_array($values[0])){
			if($insfileds)$insfileds=xg_arr($insfileds);
			foreach($allow as $value){
				if(($insfileds and xg_in_array($value,$insfileds)) or (!$insfileds and array_key_exists($value,$values[0]))){
					$fields[]='`'.$value.'`';
				}
			}
			foreach($values as $value){
				$datai=[];
				foreach($fields as $field){
					$datan++;
					$datai[]=":data{$datan}";
					$datas[":data{$datan}"]=$value[trim($field,'`')].'';
				}
				$data[]=$datai;
			}
		}else{
			foreach($allow as $value){
				if(array_key_exists($value,$values)){
					$fields[]='`'.$value.'`';
					$datan++;
					$data[]=":data{$datan}";
					$datas[":data{$datan}"]=$values[$value].'';
				}
			}
		}
		if($data){
			if(is_array($data[0])){
				$arr=[];
				foreach($data as $datai){
					$arr[]='('.xg_str($datai).')';
				}
				$datastr='values '.xg_str($arr);
			}else{
				$datastr='values ('.xg_str($data).')';
			}
			$result=$this->sql('insert into '.$this->join_table($this->table).'('.implode(',',$fields).') '.$datastr,'master','exec',$datas);
			if($result)return $this->master->lastInsertId();
		}
		return false;
	}
	protected function save($values=[],$id=0){
		return $this->update($values,$id);
	}
	protected function update($values=[],$id=0){
		$json=$this->json;
		$time=$this->time;
		$timeformat=$this->timeformat;
		if(!$this->querytype)$querytype=$this->querytype='update';
		if($id)$this->where($this->pk(),$id);
		//使用了绑定$values=xg_addslashes($values);
		if($time){
			if(is_string($time))$json=xg_arr($time);
			foreach($time as $v){
				if(!$values[$v])$values[$v]=XG_TIME;
			}
		}
		$allow=$this->table_fields();
		if($json){
			if(is_string($json))$json=xg_arr($json);
			foreach($json as $v){
				$values[$v]=xg_jsonstr($values[$v]);
			}
		}
		if(!$this->table)xg_sys_error('no table name!');
		if($this->where->isempty())xg_sys_error('no update condition!');
		$data=$datas=array();
		$datai=0;
		foreach($allow as $value){
			if(array_key_exists($value,$values)){
				if(
					substr($values[$value],0,strlen($value)+2)=="`{$value}`" 
					and xg_in_array(substr($values[$value],strlen($value)+2,1),array("+","-","*","/"))
					and (
						is_numeric(substr($values[$value],strlen($value)+3))
						or (
							substr($values[$value],strlen($value)+3,1)=="`"
							and substr($values[$value],-1)=="`"
						)
					)
				){
					$data[]='`'.$value.'`'."=".$values[$value];
				}else{
					$datai++;
					$data[]="`$value`=:data{$datai}";
					$datas[":data{$datai}"]=$values[$value].'';
				}
			}
		}
		if($data){
			$where=$this->build_where();
			$limit=$this->limit?' limit '.$this->limit:'';
			$order=$this->order?' order by '.$this->order:'';
			return $this->sql('update '.$this->join_table($this->table).' set '.implode(',',$data).' '.$where.' '.$order.' '.$limit,'master','exec',$datas);
		}
		return false;
	}
	protected function delete($id=0){
		if(!$this->querytype)$querytype=$this->querytype='delete';
		if($id)$this->where($this->pk(),$id);
		$where=$this->build_where();
		$limit=$this->limit?' limit '.$this->limit:'';
		$order=$this->order?' order by '.$this->order:'';
		return $this->sql('delete from '.$this->join_table($this->table).' '.$where.' '.$order.' '.$limit,'master','exec');
	}
	protected function inc($fields,$num=1){
		if(!$this->querytype)$querytype=$this->querytype='inc';
		$fields=xg_arr($fields);
		$fields=array_combine($fields,array_map(function($v)use($num){return "`$v`+$num";},$fields));
		return $this->update($fields);
	}
	protected function dec($fields,$num=1){
		if(!$this->querytype)$querytype=$this->querytype='dec';
		$fields=xg_arr($fields);
		$fields=array_combine($fields,array_map(function($v)use($num){return "`$v`-$num";},$fields));
		return $this->update($fields);
	}
	protected function reset(){
		$this->where->reset();
		$this->limit='';
		$this->order='';
		$this->group=[];
		$this->time=[];
		$this->timeformat='Y-m-d H:i:s';
		$this->json=null;
		$this->fields=null;
		$this->without=null;
		$this->count=null;
		$this->cache=null;
		$this->cachetime=0;
		$this->querytype=null;
		$this->begin=false;
		$this->alias=null;
		$this->join=[];
	}
	protected function cache_path($table){
		$type=$this->conf['type'];
		$name=$this->conf['name'];
		mkdir(XG_RUNTIME.'/db/',0777,true);
		return XG_RUNTIME.'/db/'.$type.'.'.trim($name,'`').'.'.trim($table,'`').'.php';
	}
	protected function get_table_cache($table){
		return include $this->cache_path($table);
	}
	protected function save_table_cache($table,$data){
		return xg_fcont($this->cache_path($table),'<?php return json_decode(\''.json_encode($data).'\',true);?>');
	}
	protected function with($fields=null){
		$this->fields=xg_arr($this->fields);
		$fields=xg_arr($fields);
		$this->fields=array_unique(xg_merge($this->fields,$fields));
		return $this;
	}
	protected function without($fields=null){
		$this->without=$fields;
		return $this;
	}
	protected function json($fields=null){
		$this->json=$fields;
		return $this;
	}
	protected function build_fields($table=null,$fields=null){
		if(!$table){
			if(!$this->table and $this->name){
				$this->name();
			}
			$table=$this->table;
		}
		if(!$fields)$fields=$this->fields;
		if(is_string($fields) and (strpos($fields,'(')>-1)){
			if($fun=trim(substr($fields,0,strpos($fields,'('))) and(xg_in_array($fun,array('count','sum','avg','abs','max','min'))))return $fields;
			xg_sys_error('function not allow');
		}else{
			$allow=$this->table_fields($table);
			if($fields){
				$fields=xg_arr($fields);
			}else{
				$fields=$allow;
			}
			if($this->without){
				$without=$this->without;
				if(is_string($without))$without=xg_arr($without);
			}
			$data=[];
			foreach($fields as $k=>$v){
				if(!is_numeric($k)){
					$v=$this->parse_alias($k,$v);
				}elseif($this->aliases and $asi=$this->aliases[$v]){
					$v=[$v,$asi];
				}else{
					$v=$this->parse_alias($v);
				}
				if(!$v[3] and !xg_in_array($v[0],$allow))continue;
				if($without and xg_in_array($v[0],$without))continue;
				$data[]=$v;
			}
			if($this->join){
				foreach($this->join as $join){
					$allow=$this->table_fields($join['table']);
					foreach($fields as $k=>$v){
						if(!is_numeric($k)){
							$v=$this->parse_alias($k,$v);
						}else{
							$v=$this->parse_alias($v);
						}
						if(!xg_in_array($v[0],$allow))continue;
						if($without and xg_in_array($v[0],$without))continue;
						$data[]=$v;
					}
				}
			}
			$result=[];
			foreach($data as $v){
				$result[]=$this->join_alias($v);
			}
			return xg_str($result);
		}
	}
	protected function table_fields($table=null,$cache=null){
		if(is_array($table)){
			$table=$table[0];
		}elseif(strpos($table,' as ')){
			$table=preg_split('/[ ]+as[ ]+/',$table)[0];
		}
		$info=$this->table_info($table,$cache);
		return $info['fields'];
	}
	protected function join_table($info,$alias=null){
		return $this->join_alias($info,$alias,true);
	}
	protected function join_alias($info,$alias=null,$istable=false){
		if(is_array($info)){
			if($alias)$info[1]=$alias;
			if($istable and substr($info[0],0,strlen($this->conf['prefix']))!=$this->conf['prefix'])$info[0]=$this->conf['prefix'].$info[0];
			if($info[2])$result=$info[2].'.';
			$result=$result.($info[3]?$info[3]:'`'.$info[0].'`').($info[1]?' as `'.$info[1].'`':'');
		}else{
			if($istable and substr($info,0,strlen($this->conf['prefix']))!=$this->conf['prefix'])$info=$this->conf['prefix'].$info;
			if($alias)$info=$info.' as `'.$alias.'`';
			$result=$info;
		}
		return $result;
	}
	protected function table_info($table=null,$cache=null){
		if(!$table)$table=$this->table();
		$table=$this->join_table($table);
		if(is_null($cache))$cache=$this->conf['cache'];
		if($cache and $info=$this->get_table_cache($table))return $info;
		switch($this->conf['type']){
			case 'mysql':
				$tablename=trim($table,'`');
				if(!$this->query("SHOW TABLES LIKE '{$tablename}'"))return [];
				$sql="describe $table";
				break;
			case 'sqlite':
				$sql="pragma table($table)";
				break;
		}
		$info=array();
		$pk='';
		$result=$this->sql($sql);
		if(!$result)return false;
		while($row=$result->fetchObject()){
			if($row->Key=='PRI')$pk=$row->Field;
			$info[]=$row->Field;
		}
		$info=['fields'=>$info,'pk'=>$pk];
		if($cache and $info)$this->save_table_cache($table,$info);
		return $info;
	}
	protected function cache($name,$time=0){
		$this->cache=$name;
		$this->cachetime=($time===true?60*60:$time);
		return $this;
	}
	protected function query($sql,$type='a'){
		$result=$this->sql($sql);
		if($result)return $result->fetchAll($this->fetch_type($type));
		return false;
	}
	protected function exec($sql,$datas=[]){
		return $this->sql($sql,'master','exec',$datas);
	}
	protected function sql($sql,$conn='slave',$query='query',$datas=[]){
		xg_data_info('sql-a');
		if($conn=='slave' and $conf=$this->slave_conf()){
			$this->slave=$this->conn($conf);
		}else{
			$conn='master';
			if(!$this->master){
				$this->master=$this->conn($this->conf);
				if($this->conf['type']=='mysql'){
					$this->master->exec("SET sql_mode='NO_ENGINE_SUBSTITUTION'");
				}
			}
			if(!$this->begin and ($this->conf['type']=='sqlite' or ($this->conf['type']=='mysql' and $this->conf['trans']))){
				$this->begin();
			}
		}
		$csql=strtolower(trim($sql));
		if(stripos($csql,'describe')!==0 and stripos($csql,'pragma')!==0 and stripos($csql,'SHOW TABLES LIKE')!==0)$this->reset();
		if(!$this->$conn)return;
		try{
			if($datas){
				$stmt=$this->$conn->prepare($csql);
				foreach($datas as $key=>$data){
					$stmt->bindParam($key,$datas[$key]);
				}
				$result=$stmt->execute();
			}else{
				$result=$this->$conn->$query($sql);
			}
		}catch(\PDOException $e){
			$result=false;
			$errmsg=$e->getMessage();
		}
		xg_data_info('sql-b');
		if($result===false){
			$errs=$this->$conn->errorInfo();
			if($this->begin){
				$this->$conn->rollBack();
				$this->begin=false;
			}
			if($datas){
				foreach($datas as $key=>$data){
					$sql=str_replace($key,'"'.$datas[$key].'"',$sql);
				}
			}
			xg_slog('sql error '.$sql.' errinfo='.xg_jsonstr($errs).' errmsg='.xg_jsonstr($errmsg));
			xg_sys_error('query error!',(XG_DEBUG?$sql.'<br>':'').$errs[2]);
		}else{
			xg_slog('sql '.xg_data_info('sql-a','sql-b').' '.$sql.' '.xg_jsonstr($datas));
		}
		return $result;
	}
	protected function begin(){
		$this->master->beginTransaction();
		$this->begin = true;
		return $this;
	}
	protected function commit(){
		$this->master->commit();
		$this->begin = false;
		return $this;
	}
	protected function rollback(){
		return $this->roll_back();
	}
	protected function roll_back(){
		$this->master->rollBack();
		$this->begin = false;
		return $this;
	}
	protected function slave_conf(){
		if(!$this->conf['slave'])return false;
		if(!$conf=$this->conf($this->conf['slave']) and !$conf=$this->conf($this->conf['slave'][mt_rand(0,count($this->conf['slave'])-1)])){
			return false;
		}
		return $conf;
	}
	public function test($conf){
		$conf['port']=$conf['port']?$conf['port']:'3306';
		$conf['char']=$conf['char']?$conf['char']:'utf8';
		$dsn='mysql:host='.$conf['host'].';port='.$conf['port'].';dbname='.$conf['name'].';charset='.$conf['char'];
		try{
			$conn=new \PDO($dsn,$conf['user'],$conf['pass']);
			return true;
		}catch(\PDOException $e){
			xg_slog('connect MySQL database fail:',$e->getMessage());
			return false;
		}
	}
	protected function conn($conf){
		if($conf['type']=='sqlite' and !extension_loaded('pdo_sqlite')){
			xg_sys_error('pdo_sqlite database extension not detected!');
		}elseif($conf['type']=='mysql' and !extension_loaded('pdo_mysql')){
			xg_sys_error('pdo_mysql database extension not detected!');
		}
		switch($conf['type']){
			case'mysql':
				$dsn='mysql:host='.$conf['host'].';port='.$conf['port'].';dbname='.$conf['name'].';charset='.$conf['char'];
				try{
					$conn=new \PDO($dsn,$conf['user'],$conf['pass']);
				}catch(\PDOException $e){
					xg_sys_error('connect MySQL database fail:',$e->getMessage());
				}
			break;
			case'pdo_sqlite':
				$dsn='sqlite:'.XG_PATH.$conf['path'];
				try{
					$conn=new \PDO($dsn);
				}catch(\PDOException $e){
					xg_sys_error('connect SQLite database fail:',$e->getMessage());
				}
			break;
		}
		return $conn;
	}
	protected function conf($conf){
		if(!$conf['path'] and !$conf['name']){
			return false;
		}elseif($conf['type']=='sqlite'){
			if(!$conf['path'])xg_sys_error('database path not defined!');
		}else{
			if(!$conf['name'])xg_sys_error('database name not defined!');
			if(!$conf['user'])xg_sys_error('database user not defined!');
			if(!$conf['host'])xg_sys_error('database host not defined!');
			$conf['port']=$conf['port']?$conf['port']:'3306';
			$conf['type']=$conf['type']?$conf['type']:'mysql';
			$conf['char']=$conf['char']?$conf['char']:'utf8';
			$conf['pass']=$conf['pass']?$conf['pass']:'';
		}
		$conf['char']=$conf['char']?$conf['char']:'utf8';
		$conf['prefix']=!is_null($conf['prefix'])?$conf['prefix']:'';
		return $conf;
	}
	protected function fetch_type($type='a'){
		switch($type){
			case 'a':
				return \PDO::FETCH_ASSOC;
			case 'n':
				return \PDO::FETCH_NUM;
			case 'o':
				return \PDO::FETCH_OBJ;
			case 'b':
				return \PDO::FETCH_BOTH;
			default:
				return \PDO::FETCH_ASSOC;
		}
	}
	public function __get($name){
		/* todo if(in_array($name,$this->getter)) */if($name)return $this->$name;
		//if(property_exists($this,$name))xg_sys_error("database var $name disallow to get");
	}
	public function __call($method,$args){
		if(!xg_in_array($method,$this->hooks))xg_sys_error("database method $method disallow to call");
		if($this->runhook and defined('XG_LOADED'))xg_hooks($method.'-before','database')->args(xg_merge([$this],$args));
		if(!$this->name and !$this->names and !$this->table and !$this->tables){
			if(xg_in_array($method,['select','find','column','value','update','insert','save','add','delete','inc','dec'])){
				return null;
			}
			if(xg_in_array($method,['count','max','min','sum','abs'])){
				return 0;
			}
		}
		$result=call_user_func_array([$this,$method],$args);
		$args2=[$this,&$result];
		foreach($args as $v){
			$args2[]=$v;
		}
		if($this->runhook and defined('XG_LOADED'))xg_hooks($method.'-after','database')->args($args2);
		return $result;
	}
	public function __clone(){
		$this->where=clone $this->where;
	}
}
?>