<?php

if (empty($_POST)) {
    echo("I'm ready!");
    exit;
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
header("Access-Control-Allow-Origin: $origin");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

require "./helpers/globalScope.php";
require "./helpers/cacheLib.php";

error_log(var_export($_POST, true));

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);
session_start();

$result = [
    "status" => null,
    "data" => null
];

if (!isset($_SESSION['loggedIn'])) {
    $_SESSION = [
        "userId" => -1,
        "loggedIn" => false,
        "userName" => "Guest",
        "role" => "unknown"
    ];
}

if ($_POST["data"] ?? false) {
    $_POST["data"] = json_decode($_POST["data"], true);
} else {
    $_POST["data"] = [];
}

if ($_POST["actor"] ?? false) {
    $actorPath = __DIR__ . "/actors/" . $_POST["actor"] . ".php";
    if (file_exists($actorPath)) {
        $result = include $actorPath;
    } else {
        $result["status"] = "error";
        $result["data"] = "unknown actor";
    }
}

error_log(var_export($result, true));
echo(json_encode($result));
session_write_close();