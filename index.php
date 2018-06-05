<?php
require 'bootstrap.php';

$container = new \HybridLogin\Container();
$controller = new \HybridLogin\Controller\Controller($container, $_REQUEST['route'] ?? null);
$controller->run($_REQUEST['action'] ?? null);
exit;
