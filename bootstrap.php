<?php
if (!empty($_REQUEST['sessionId'])) {
    session_id($_REQUEST['sessionId']);
}
session_start();
require 'vendor/autoload.php';
