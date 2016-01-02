<?php
// Starter Faucet index page.
// This is currently the only page, the post request for the coins have been put into this page as well as the form.
// However this page is a little messy coded, maybe could be cleaned up just a little.
require_once('functions/loader.php');
require_once('validator/address_validator.php');
$loader = new loader();
$api = $loader->load('selectapi');
$template = $loader->load('template');
$config = $loader->load('configuration');
$log = $loader->load('log');
$balance = $api->getBalance();
if (isset($_GET['next'])) {
	$useraddr = $_POST['address'];
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
		$msg = 'Preencha seu endereço ' . $config->coin_name() . '.';
	} elseif ($config->enable_captcha()) {
		require_once('recaptcha/autoload.php');
		$secret = $config->recaptcha_private_key();
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
		//$msg = 'Please fill a valid ' . $config->coin_name() . ' address.';
		$msg = 'Preencha um endereço ' . $config->coin_name() . ' válido.';
	} 
	if ($continue) {
		$amount = $config->faucet_amount();
		if ($balance < $amount) {
			$continue = false;
			//$msg = 'There are currently not enough funds in the faucet.';
			$msg = 'Não há fundos suficientes na Faucet, tente novamente mais tarde.';
		}
	}
	if ($continue && !$log->checkIP()) {
		$continue = false;
		//$msg = 'Please wait ' . $config->wait_period() . ' seconds to request more funds.';
		$msg = 'Você já usou o faucet. Aguarde ' . $config->wait_period() . ' segundos para tentar novamente.';
	}
	if ($continue) {
		$send = $api->sendMoney($useraddr, $amount);
		if ($send->success) {
			$sent = $log->getLog('sent');
			// This updates the log to show how much is sent.
			$log->saveLog('sent', $sent + $amount);
			// Update the log to put the wait period in place.
			$ret = $log->logIP();
			// Unset the variables to clear the form.
			unset($useraddr);
			unset($amount);
			//$msg = 'Successful, you should see the funds in your wallet shortly. txid = ' . $send->success;
			$msg = 'Sucesso, o depósito deve aparecer na sua carteira em breve. (txid ' . $send->success . ').';
		} else {
			$continue = false;
			//$msg = 'Your funds were unable to be sent: ' . $send->error;
			$msg = 'Não foi possivel enviar para sua carteira. Código de erro: ' . $send->error;
		}
	}
}
$template->header();
if (isset($msg)) {
	echo '<div class="errormsg">'.$msg.'</div>';
}
echo '<form action="index.php?next" method="post">
<table style="width:75%">';
if ($config->show_balance()) {
	echo '<tr><td align="right">Saldo em '.$config->coin_name().':</td>';
	if (empty($balance) || is_nan($balance)) {
		$balance = 'Unknown';
	}
	echo '<td>'.$balance.' '.$config->coin_code().'</td>';
} else {
	echo '<td></td>';
}
if (!empty($_POST['terms'])) {
	$checked = ' checked="checked"';
}
echo '</tr>
<tr>
<td align="right">Seu endereço '.$config->coin_name().':</td>
<td><input type="text" name="address" maxlength="100" style="width:300px" value="'.htmlspecialchars($useraddr, ENT_QUOTES).'"/></td>
</tr>
<tr>
<td align="right">Termos de Uso</td>
<td><input id="terms" type="checkbox" name="terms"'.$checked.'/><label for="terms">Eu concordo com os <a href="terms.php" target="_blank">Termos de Uso</a> do serviço</label></td>
</tr>';
if ($config->enable_captcha()) {
	//require_once('functions/recaptchalib.php');
	echo '<tr>
	<td></td>
	<td>
	<script src="https://www.google.com/recaptcha/api.js?hl=pt-BR" async defer></script>
      <div class="g-recaptcha" data-sitekey="' . $config->recaptcha_public_key() . '"></div>
	</td></tr>
	';
	//<td>'.recaptcha_get_html($config->recaptcha_public_key()).'</td>
	//</tr>';
}
echo '<tr>
<td></td>
<td><input type="submit" value="Enviar '.$config->coin_name().'"/></td>
</tr>
</form>
</table>';
$template->footer();
?>
