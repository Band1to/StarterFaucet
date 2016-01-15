<?php
require_once('functions/loader.php');
require_once('validator/address_validator.php');

class Main {

	function __construct() {
		$loader = new loader();
		$this->api = $loader->load('selectapi');
		$this->template = $loader->load('template');
		$this->config = $loader->load('configuration');
		$this->log = $loader->load('log');
		$this->_message = '';
		$this->_hasMessage = false;
		if (isset($_GET['next'])) {
			if ($this->validate()) {
				$useraddr = $_POST['address'];
				$amount = $this->config->faucet_amount();
				$this->sendMoney($useraddr, $amount);
			}
		}
	}

	function getTitle() {
		return $this->config->faucet_name();
	}
	function coinName() {
		return $this->config->coin_name();
	}
	function coinCode() {
		return $this->config->coin_code();;
	}
	function faucetAmount() {
		return $this->config->faucet_amount();
	}
	function faucetTime() {
		$ts = $this->config->wait_period();
		$h = intval($ts/3600);
		$m = intval($ts/60) % 60;
		$s = intval($ts) % 60;
		$ret = "";
		if ($h==1) $ret .= "$h hora ";
		elseif ($h) $ret .= "$h horas ";
		if ($m==1) $ret .= "$m minuto ";
		elseif ($m) $ret .= "$m minutos ";
		if ($s==1) $ret .= "$s segundo";
		elseif ($s) $ret .= "$s segundos";
		return $ret;
	}
	function getBalance() {
		if (!isset($this->balance)) {
			$this->balance = $this->api->getBalance();
		}
		return $this->balance;
	}
	function useRecaptcha() {
		return $this->config->enable_captcha();
	}
	function recaptchaPublic() {
		return $this->config->recaptcha_public_key();
	}
	function hasMessage() {
		return $this->_hasMessage;
	}
	function getMessage() {
		return $this->_message;
	}

	function getAddress() {
		if (!isset($this->useraddr)) {
			$this->useraddr = $_POST['address'];
		}
		return $this->useraddr;
	}

	function isChecked() {
		if (!isset($this->checked)) {	
			$this->checked = !empty($_POST['terms']);
		}
		return $this->checked;
	}

	function getChecked() {
		if ($this->isChecked()) {
			return ' checked="checked" ';
		}
		return '';
	}

	function clearForm() {
		$this->useraddr = '';
		$this->checked = false;
		// força a reatualizar  osaldo
		unset($this->balance);
	}

	function getQuotedAddress() {
		return htmlspecialchars($this->getAddress(), ENT_QUOTES);
	}

	// all teh work is here;
	// has collateral effects on both $this->_message and $this->_hasMessage
	function validate() {
		$continue = false;
		$msg = '';
		$useraddr = $this->getAddress();
		$terms = $_POST['terms'];
		$continue = true;
		$msg = '';
		if (empty($terms)) {
			$continue = false;
			//$msg = 'Please agree to the terms of service.';
			$msg = 'Você precisa aceitar os termos de uso.';
		} elseif (empty($useraddr)) {
			$continue = false;
			//$msg = 'Please agree fill the address.';
			$msg = 'Preencha seu endereço ' . $this->config->coin_name() . '.';
		} elseif ($this->config->enable_captcha()) {
			require_once('recaptcha/autoload.php');
			$secret = $this->config->recaptcha_private_key();
			$recaptcha = new \ReCaptcha\ReCaptcha($secret);
			$continue = false;
			//$msg = 'The captcha is incorrect, please try again.';
			$msg = 'O Captcha foi preenchido incorretamente, tente novamente.';
			if(isset($_POST['g-recaptcha-response'])) {
				$captcha=$_POST['g-recaptcha-response'];
				$remoteIp=$_SERVER['REMOTE_ADDR'];
				$resp = $recaptcha->verify($captcha, $remoteIp);
				if ($resp->isSuccess()) {
					$continue = true;
					$msg = '';
				} else {
					$msg .= ' Error: ' . implode(", ", $resp->getErrorCodes());
				}
			}
		}
		if ($continue && !checkAddress($useraddr, dechex(30))) { // dilmacoin specific, move this to config
			$continue = false;
			$msg = 'Preencha um endereço ' . $this->config->coin_name() . ' válido.';
		}
		if ($continue) {
			$amount = $this->config->faucet_amount();
			if ($this->getBalance() < $amount) {
				$continue = false;
				$msg = 'Não há fundos suficientes na Faucet, tente novamente mais tarde.';
			}
		}
		if ($continue && !$this->log->checkIP()) {
			$continue = false;
			$msg = 'Você já usou o faucet. Aguarde ' . $this->config->wait_period() . ' segundos para tentar novamente.';
		}
 
		$this->_hasMessage = !$continue;
		$this->_message = $msg;
		return $continue;
	}
	function sendMoney($useraddr, $amount) {
		$send = $this->api->sendMoney($useraddr, $amount);
		if ($send->success) {
			$sent = $this->log->getLog('sent');
			// This updates the log to show how much is sent.
			$this->log->saveLog('sent', $sent + $amount);
			// Update the log to put the wait period in place.
			$ret = $this->log->logIP();
			// Unset the variables to clear the form.
			$this->clearForm();
			$msg = 'Sucesso, o depósito deve aparecer na sua carteira em breve. (txid ' . $send->success . ').';
		} else {
			$msg = 'Não foi possivel enviar para sua carteira. Código de erro: ' . $send->error;
		}
		
		$this->_hasMessage = true;
		$this->_message = $msg;

	}
}
?>
