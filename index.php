<?php
require 'bootstrap.php';

$container = new \HybridLogin\Container();
$controller = new \HybridLogin\Controller\Controller($container, $_REQUEST ?? null);
$controller->run();
exit;
