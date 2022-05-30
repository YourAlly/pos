<?php

$s_name = "localhost";
$username = "root";
$password = "";
$database = "shop";

try {
    $conn = new PDO(
        "mysql:host=$s_name;dbname=$database",
        $username,
        $password
    );
} catch (PDOException $err) {
    echo "Error: " . $err->getMessage();
}
