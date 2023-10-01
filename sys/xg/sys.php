<?php
namespace sys\xg;
class sys extends \xg\sys implements \xg\face\sys{
	protected $sys='xg';
	public function __construct(){
		parent::__construct();
		$this->uid=xg_login();
	}
	use \xg\traits\file;
	use \sys\xg\traits\app;
	use \sys\xg\traits\data;
	use \sys\xg\traits\user;
	use \sys\xg\traits\content;
	use \sys\xg\traits\comment;
}
?>