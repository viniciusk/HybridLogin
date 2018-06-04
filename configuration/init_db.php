<?php
require '../vendor/autoload.php';

use configuration\DB;

/*
 * SETTINGS!
 */
$databaseName = DB::DATABASE;
$databaseUser = DB::USER;
$databasePassword = DB::PASSWORD;
$databaseHost = DB::HOST;

/*
 * CREATE THE DATABASE
 */
$pdoDatabase = new PDO('mysql:host='.$databaseHost, $databaseUser, $databasePassword);
$pdoDatabase->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdoDatabase->exec('CREATE DATABASE IF NOT EXISTS ' . $databaseName);

/*
 * CREATE THE TABLE
 */
$pdo = new PDO('mysql:host='.$databaseHost.';dbname='.$databaseName, $databaseUser, $databasePassword);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// initialize the table
$pdo->exec('DROP TABLE IF EXISTS `user`;');

$pdo->exec('CREATE TABLE `user` (
 `uuid` bigint(20) NOT NULL,
 `email` varchar(320) COLLATE utf8mb4_unicode_ci NOT NULL,
 `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
 `active` tinyint NOT NULL DEFAULT 0,
 PRIMARY KEY (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');

echo "Example database created!\n";
