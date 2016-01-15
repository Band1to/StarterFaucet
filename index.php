<?php
require_once('main.php');
require_once('ad-rotator.php');
$main = new Main;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title><?= $main->getTitle() ?></title>
<meta name="ProgId" content="FrontPage.Editor.Document" />
<link rel="stylesheet" type="text/css" href="css/main.css" />
</head>

<body bgcolor="#242640">

<div align="center">
<div id="content" style="width: 728px; border:10px red; position: relative;">
<div id="ad-lateral" style="position: absolute; width: 120px; left: -130px; ">
	<?= rotate_left() ?>
</div>
<div id="ad-lateral" style="position: absolute; width: 120px; right: -130px; ">
	<?= rotate_right() ?>
</div>
<div id="ad-leaderboard">
	<?= rotate_top() ?>
</div>
<form action="index.php?next" method="post">
	<table width="728" border="1" class="Table" id="table1">

		<tr>
			<td style="font-family: 'Trebuchet MS', Verdana, Arial, sans-serif; font-size: 14px; color: #282828">
			<a href="." style="text-decoration: none;">
			<div class="title"><?= $main->getTitle() ?></div>
			</td>
			<td width="0" height="100"></td>
		</tr>
<?php if ($main->hasMessage()) { ?>
		<tr><td>
			<div class="errormsg"><?= $main->getMessage() ?></div>
		</td></tr>
<?php } ?>
		<tr>
			<td class="divider">
			<div class="content">
				<label for="balance">Saldo em <?= $main->coinName() ?></label>
				<span id="balance"><?= $main->getBalance() ?> <?= $main->coinCode() ?></span>
			</div>
			<br>
			<div class="content">
				<label for="address" style="display: block;">Seu endereço <?= $main->coinName() ?></label>
				<input type="text" name="address" maxlength="100" style="width:300px" value="<?= $main->getQuotedAddress() ?>" style="display: block;"/>
				<br />
			</div>
			<br>
			<div class="content">
				<label for="terms" style="display: block;">Termos de uso</label>
				<input id="terms" type="checkbox" name="terms"<?= $main->getChecked() ?>>Eu concordo com os <a href="terms.php" target="_blank">Termos de Uso</a> do serviço</input>
			</div>
			<br>
			<div class="undashed">
				<script src="https://www.google.com/recaptcha/api.js?hl=pt-BR" async defer></script>
				<div class="g-recaptcha" data-sitekey="<?=  $main->recaptchaPublic() ?>"></div>
			</div>
			<br>
			<div class="content">
				<input type="submit" value="Enviar Dilmacoin"/>
				(<?= $main->faucetAmount() ?> <?= $main->coinCode() ?> a cada <?= $main->faucetTime() ?>)
			</div>
			</td>
		</tr>
		<tr>
			<td></td>

		</tr>
		<tr>
			<td style="font-family: 'Trebuchet MS', Verdana, Arial, sans-serif; font-size: 14px; color: #282828">
			</td>
			<td height="2"></td>
		</tr>
		
		
		<tr>
			<td colspan="4" class="creds" bgcolor="#242640">Copyright © 2005 | All Rights Reserved  </td>
			<td height="21" width="0"></td>
		</tr>
	</table>
</form>
</div>
</div>

</body>

</html>
