<?php
// Starter Faucet header page.
// This page is part of the template, modify as you please, you may also want to modify footer.php.
$loader = new loader();
$template = $loader->load('template');
$config = $loader->load('configuration');
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link href="assets/style.css" rel="stylesheet" type="text/css"/>
<title><?php
$curtitle = $template->getTitle(true);
echo $config->faucet_name().' - '.$curtitle; 
?></title>
</head>
<body>
<div class="title">
<h1><a href="index.php"><?php echo $config->faucet_name(); ?></a></h1>
</div>
<div id="content" class="content">
