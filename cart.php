<?php

require_once("./database.php");
require_once('./init.php');

$products = array();
$is_shopping = false;
$total = 0;

if (isset($_SESSION['cart'])) {
    $is_shopping = true;
    foreach (array_keys($_SESSION['cart']) as $key) {
        $query = $conn->prepare("SELECT * FROM products WHERE id = ?;");
        $query->execute(array($key));
        $product = $query->fetch(PDO::FETCH_ASSOC);

        array_push($products, $product);
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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
                                    <a href="Profile.html">Profile</a>
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

    <!---Product details,  adding to cart--->
    <?php if (isset($_SESSION['messages'])) {
        foreach ($_SESSION['messages'] as $message) { ?>
            <div class="message-portion">
                <p><?php echo $message; ?></p>
            </div>
    <?php }
        unset($_SESSION['messages']);
    } ?>

    <?php if (isset($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) { ?>
            <div class="error-portion">
                <p><?php echo $error; ?></p>
            </div>
    <?php }
        unset($_SESSION['errors']);
    } ?>

    <div class="small-container cart-page">
        <div class="table-container">
            <table>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>

                <?php if ($is_shopping) {
                    foreach ($products as $product) { ?>
                        <tr>
                            <td>
                                <div class="cart-info">
                                    <img src="./admin/<?php echo $product['img_url'] ?>" width="20%">
                                    <div>
                                        <p><?php echo $product['name'] ?></p>
                                        <small>Price: <?php echo $product['price'] ?><br></small>
                                        <a href="./remove_from_cart.php?product_id=<?php echo $product['id'] ?>">Remove</a>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $_SESSION['cart'][$product['id']]; ?></td>
                            <td>
                                <?php
                                $subtotal = $product['price'] * $_SESSION['cart'][$product['id']];
                                echo $subtotal;
                                $total += $subtotal;
                                ?>

                            </td>
                        </tr>
                <?php }
                } ?>
            </table>
        </div>
        <div class="table2-container">
            <div class="total-price">
                <table>
                    <tr>
                        <td>Total</td>
                        <td><?php echo $total; ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <a href="./delete_cart_confirm.php" class="btnP">Delete Cart</a>
        <a href="./purchase.php" class="btnP">Purchase</a>
    </div>

    </form>


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