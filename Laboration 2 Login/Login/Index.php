<?php

require_once("HTMLView.php");
require_once("src/LoginController.php");

session_start();


$lc = new LoginController();

$htmlBody = $lc->doLogin();

$view = new HTMLView();
$view->echoHTML($htmlBody);
var_dump($_POST);