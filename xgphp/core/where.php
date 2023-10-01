<?php
/**
 * XGPHP 轻量级PHP框架
 * @link http://xgphp.xg3.cn
 * @version 1.0.0
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @author 讯高科技 <xungaokeji@qq.com>
*/
namespace xg;
class where{
	protected $where='';
	protected $alias=null;
	protected $pk='id';
	public function where_or($p1,$p2=null,$p3=null){
		return $this->where($p1,$p2,$p3,'or');
	}
	public function between($p1,$p2,$p3,$join='and'){
		$this->where("`$p1` between $p2 and $p3",null,null,$join);
	}
	public function not_between($p1,$p2,$p3,$join='and'){
		$this->where("`$p1` not between $p2 and $p3",null,null,$join);
	}
	public function pk($pk){
		$this->pk=$pk;
		return $this;
	}
	public function build(){
		if(!$this->isempty())return ' where '.$this->where;
		return '';
	}
	public function get(){
		return xg_stripslashes($this->where);
	}
	public function reset(){
		$this->alias=null;
		$this->where='';
	}
	public function alias($alias){
		$this->alias=$alias;
		return $this;
	}
	public function join($name,$join,$value,$prefix=null){
		if(is_null($prefix))$prefix=$this->alias;
		$name=str_replace('`','',$name);
		if(strpos($name,'.')===false){
			if($prefix){
				$name="`$prefix`.`$name`";
			}else{
				$name="`$name`";
			}
		}else{
			$arr=xg_arr($name,'.');
			return $this->join($arr[1],$join,$value,$arr[0]);
		}
		if(is_array($value) or $join=='in'){
			return "$name in (".xg_str(array_map(function($v){return (is_numeric($v)?$v:"'$v'");},$value)).")";
		}elseif(is_numeric($value)){
			return "$name $join $value";
		}elseif(strpos($value,'`')!==false){
			return "$name $join $value";
		}else{
			return "$name $join '$value'";
		}
	}
	public function isempty(){
		return !$this->where;
	}
	public function where($p1,$p2=null,$p3=null,$join='and'){
		//if(is_array($p1))$p1=xg_addslashes($p1);
		if($p2)$p2=xg_addslashes($p2);
		if($p3)$p3=xg_addslashes($p3);
		$sym=array('neq'=>'!=','eq'=>'=','egt'=>'>=','gt'=>'>','elt'=>'<=','lt'=>'<');
		if(xg_isfun($p1)){
			$where=$p1(new self())->alias($this->alias)->get();
			if($where)$where="($where)";
		}elseif(is_object($p1)){
			if(get_class($p1)=='xg\where'){
				$where=$p1->alias($this->alias)->get();
			}
		}elseif(is_array($p1)){
			$obj=new self();
			if(is_array($p1[0])){
				foreach($p1 as $v1){
					$obj->where($v1[0],trim($v1[1]),$v1[2]);
				}
			}elseif(is_string(array_keys($p1)[0])){
				foreach($p1 as $k1=>$v1){
					if(is_array($v1)){
						$obj->where($k1,'in',$v1);
					}else{
						$obj->where($k1,'=',$v1);
					}
				}
			}elseif(count($p1)==3){// or count($p1)==2
				$obj->where($p1[0],trim($p1[1]),$p1[2]);
			}
			if($where=$obj->get())$where="(".$where.")";
		}elseif(is_string($p1) and strpos($p1,'|')>0){
			$this->where(function($obj)use($p1,$p2,$p3){
				$arr=xg_arr($p1,'|');
				foreach($arr as $v){
					$obj->where_or($v,$p2,$p3);
				}
				return $obj;
			});
			return $this;
		}elseif(is_numeric($p1)){
			$this->where($this->pk,'=',$p1);
			return $this;
		}elseif(is_array($p2)){
			$where=$this->join($p1,'in',$p2);
		}elseif(!is_null($p3)){
			if(trim($p2)=='in'){
				$where=$this->join($p1,'in',xg_arr($p3));
			}else{
				$where=$this->join($p1,($sym[$p2]?$sym[$p2]:$p2),$p3);
			}
		}elseif(!is_null($p2)){
			$where=$this->join($p1,'=',$p2);;
		}elseif(is_string($p1)){
			$where=$p1;
		}
		if($where){
			if($this->where){
				$this->where="$this->where $join $where";
			}else{
				$this->where=$where;
			}
		}
		return $this;
	}
}
?>