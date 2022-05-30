<?php

require_once("./database.php");
require_once("./init.php");

if(isset($_GET['product_id']) && isset($_GET['amount'])){
    $cart = array();

    if(!isset($_SESSION['cart'])){
        $_SESSION['cart'] = array();
        
    } else {
        $cart = $_SESSION['cart'];
    }

    $query = $conn->prepare("SELECT * FROM products WHERE id = ?;");
    $query->execute(array($_GET['product_id']));
    $product = $query->fetch(PDO::FETCH_ASSOC);

    if($product && $_GET['amount'] > 0){

        // Check if the product already is added into the cart
        if(array_key_exists($_GET['product_id'], $cart)){

            // If yes, just add the amount
            $cart[$_GET['product_id']] += $_GET['amount'];
        }
        else {

            // If no, then add it to the array
            $cart += array($_GET['product_id'] => $_GET['amount']);
        }

        $_SESSION['cart'] = $cart;
        array_push($messages, "The item has been added to the cart");
        $_SESSION['messages'] = $messages;
        header("Location: ./products.php");
    }
    else{
        array_push($errors, "Invalid item id or quantity");
        $_SESSION['errors'] = $errors;
        header("Location: ./products.php");
    }
}
