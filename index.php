<?php
if (!empty($_REQUEST['sessionId'])) {
    session_id($_REQUEST['sessionId']);
}
session_start();
require 'vendor/autoload.php';

$container = new \HybridLogin\Container();
$controller = new \HybridLogin\Controller\Controller($container, $_REQUEST['route'] ?? null);
$controller->run($_REQUEST['action'] ?? null);
exit;
