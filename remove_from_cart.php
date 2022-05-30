<?php
require_once("./database.php");
require_once("./init.php");

if (isset($_GET['product_id'])) {

    if (isset($_SESSION['cart'][$_GET['product_id']])) {
        unset($_SESSION['cart'][$_GET['product_id']]);
        array_push($messages, "The item has been removed from the cart");
        $_SESSION['messages'] = $messages;
        header("Location: ./index.php");
    } else {
        array_push($errors, "Item not found in cart");
        $_SESSION['errors'] = $errors;
        header("Location: ./index.php");
    }
}
