<?php
namespace apps\weixin\controller;
class index extends \apps\base{
	function index(){
		if (!empty($_GET['echostr']) && !empty($_GET["signature"]) && !empty($_GET["nonce"])) {
			$appid = xg_input('get.appid');
			$signature = $_GET["signature"];
			$timestamp = $_GET["timestamp"];
			$nonce = $_GET["nonce"];
			$tmpArr = array('AbDeCa11d',$timestamp,$nonce);
			sort($tmpArr, SORT_STRING);
			$tmpStr = sha1(implode($tmpArr));
			if ($tmpStr == $signature) {
				echo $_GET["echostr"];
			}
			return;
		}
	}
}