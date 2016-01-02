<?php
require_once('easybitcoin/easybitcoin.php');
class bitcoinapi {

	function init($port, $user, $password) {
		$this->bitcoin = new Bitcoin($user, $password, 'localhost', $port);
	}

	function getBalance() {
		return $this->bitcoin->getbalance();
	}
	function sendMoney($params) {
		$ret = new stdClass;
		$ret->success=$this->bitcoin->sendtoaddress($params[0], $params[1]);
		$ret->error=$this->bitcoin->error;
		$ret->status=$this->bitcoin->status;
		return $ret;
	}
}
?>
