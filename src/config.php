<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include "vendor/autoload.php";

$client_id = "[snip]";
$client_secret = "[snip]";

use kamermans\OAuth2\Persistence\FileTokenPersistence;
$token_storage = new FileTokenPersistence('../token.txt');
$redirect_uri = 'http://localhost/auth.php';

$challenge = isset($_SESSION['challenge']) ? $_SESSION['challenge'] : null;