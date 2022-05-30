<?php

require_once("./database.php");
require_once("./init.php");

if (!isset($_SESSION['user_id'])) {
    array_push($errors, "You must be logged in to complete the purchase!");
    $_SESSION['errors'] = $errors;
    header("Location: ./cart.php");
}

if (!isset($_SESSION['cart'])) {
    array_push($errors, "You must have an existing cart to complete the purchase!");
    $_SESSION['errors'] = $errors;
    header("Location: ./cart.php");
} else if (empty($_SESSION['cart'])) {
    array_push($errors, "You cart must have items to complete the purchase!");
    $_SESSION['errors'] = $errors;
    header("Location: ./cart.php");
} else {

    // Validations
    $query_profile = $conn->prepare("SELECT * FROM profiles WHERE user_id = ?;");
    $query_profile->execute([$_SESSION['user_id']]);
    $profile = $query_profile->fetch(PDO::FETCH_ASSOC);

    if (empty($profile['address'])) {
        array_push($errors, "You must have an address attached to your account complete the purchase!");
        $_SESSION['errors'] = $errors;
        header("Location: ./cart.php");
    }


    // Checks if the product's available
    $available = true;

    foreach (array_keys($_SESSION['cart']) as $key) {
        // Fetch the items
        $query_item = $conn->prepare("SELECT * FROM products WHERE id = ?;");
        $query_item->execute(array($key));
        $item = $query_item->fetch(PDO::FETCH_ASSOC);

        if (!$item) {
            $available = false;
            continue;
        }

        if ($_SESSION['cart'][$key] > $item['quantity']) {
            array_push($errors, "Not Enough Stocks for! " . $item['name']);
        }
    }

    if (!$available) {
        array_push($errors, "Product does not exist!");
        $_SESSION['errors'] = $errors;
        header("Location: ./cart.php");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)) {

        // Creation of rows
        $query = $conn->prepare("INSERT INTO transactions (user_id) VALUES (?);");
        $query->execute(array($_SESSION['user_id']));
        $transact_id = $conn->lastInsertId();

        foreach (array_keys($_SESSION['cart']) as $key) {
            $query = $conn->prepare("INSERT INTO transactions_products (transact_id, product_id, amount)
            VALUES (:transact_id, :product_id, :amount);");
            $query->execute(array(
                'transact_id' => $transact_id,
                'product_id' => $key,
                'amount' => $_SESSION['cart'][$key]
            ));

            $reduce = $conn->prepare("UPDATE products SET quantity = quantity - :amount_bought WHERE id = :id;");
            $reduce->execute(array(
                'amount_bought' => $_SESSION['cart'][$key],
                'id' => $key
            ));
        }

        unset($_SESSION['cart']);
        array_push($messages, "Purchase Successful!");
        $_SESSION['messages'] = $messages;
        header("Location: ./index.php");
    } else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $_SESSION['errors'] = $errors;
        header("Location: ./cart.php");
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete Purchase</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="header">
        <div class="container">
            <div class="navbar">
                <div class="logo">
                    <a href="./index.php"><img src="https://via.placeholder.com/300" width="125px"></a>
                </div>
                <nav>
                    <ul id="MenuItems">
                        <li><a href="./index.php">Home</a></li>
                        <li><a href="./products.php">Products</a></li>
                        <?php if (isset($_SESSION['user_id'])) { ?>
                            <div class="dropdown">
                                <li><a class="dropbtn">Account</a></li>
                                <div class="dropdown-content">
                                    <a href="./profile_page.php">Profile</a>
                                    <a href="purchase_history.php">Purchase History</a>
                                    <a href="./logout.php">Logout</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <li><a href="./login_register_form.php">Log In</a></li>
                        <?php } ?>
                    </ul>
                </nav>
                <a href="./cart.php"><img src="image/cart2.png" width="30px" height="30px"></a>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) { ?>
            <div class="error-portion">
                <p><?php echo $error ?></p>
            </div>
    <?php }
        unset($_SESSION['errors']);
    } ?>

    <div class="Purchasemain-container">
        <div class="Address-container">
            <h1>Delivery Address</h1>
            <table class="address">
                <tr>
                    <td><b>Name: <?php echo $profile['first_name'] . " " . $profile['last_name']; ?></b></td>
                    <td>Address: <?php echo $profile['address']; ?></td>
                </tr>
            </table>
        </div>
        <div class="order-container">
            <table class="order">
                <tr>
                    <th>Product Ordered</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Sub-Total</th>

                </tr>
                <tr>
                    <td></td>
                </tr>
                <?php
                $total = 0;
                foreach (array_keys($_SESSION['cart']) as $key) {
                    $query = $conn->prepare("SELECT * FROM products WHERE id = ?;");
                    $query->execute(array($key));
                    $product = $query->fetch(PDO::FETCH_ASSOC); ?>

                    <tr>
                        <td>
                            <div class="cart-info">
                                <img src="./admin/<?php echo $product['img_url']; ?>" width="30%">
                                <div>
                                    <p><?php echo $product['name']; ?></p>
                                    <small><br></small>
                                </div>
                            </div>
                        </td>
                        <td>₱<?php echo $product['price']; ?></td>
                        <td><?php echo $_SESSION['cart'][$key]; ?></td>
                        <td><?php
                            $subtotal = $product['price'] * $_SESSION['cart'][$key];
                            $total += $subtotal;
                            echo $subtotal;
                            ?></td>
                    </tr>

                <?php } ?>

                <tr id="separator">
                    <td></td>
                    <td></td>
                    <td>
                        <p><b>Standard Local</b></p>
                        <p id="deliv">Standard delivery</p>
                    </td>
                    <td>₱1,000</td>
                </tr>
                <tr id="totalprice">
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <p id="ord-tot">Order Total: ₱<?php echo $total; ?></p>
                    </td>
                </tr>
            </table>
        </div>
        <div class="purchase-container">
            <table>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <p id="merch-tot">Shipping Total: </p>
                    </td>
                    <td>₱1,000</td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>
                        <p id="merch-tot">Total Payment: </p>
                    </td>
                    <td>₱<?php echo $total + 1000; ?></td>
                </tr>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <form method="POST">
                        <td><button type="submit" name="submit" value="purchase" class="btn">Purchase</button></td>
                    </form>
                </tr>
            </table>
        </div>

    </div>



    <!--Footer-->
    <div class="footer">
        <div class="container">
            <div class="row">
                <div class="footer-col-3">
                    <img src="https://via.placeholder.com/300" width="125px">
                    <p>
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras ex nisi, elementum et tincidunt viverra, viverra nec massa. Donec metus lorem, dapibus in magna ut, ultricies imperdiet nisi. Aenean et semper tellus. Donec commodo tellus nisi, sit amet varius nulla dictum in. Vestibulum porttitor metus non gravida dictum.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!--Cart-->


    <!--JS-->
    <script>
        var MenuItems = document.getElementById("MenuItems");

        MenuItems.style.maxHeight = "0px";

        function menutoggle() {
            if (MenuItems.style.maxHeight == "0px") {
                MenuItems.style.maxHeight = "220px";
            } else {
                MenuItems.style.maxHeight = "0px";
            }

        }
    </script>


</body>

</html>