<?php

require_once("./database.php");
require_once("./init.php");

if (isset($_SESSION['cart'])) {
    unset($_SESSION['cart']);
    array_push($messages, "The cart is now deleted");
    $_SESSION['messages'] = $messages;
    header("Location: ./cart.php");
} else {
    array_push($errors, "No current existing cart");
    $_SESSION['errors'] = $errors;
    header("Location: ./cart.php");
}
