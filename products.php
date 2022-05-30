<?php

require_once("./database.php");
require_once('./init.php');

$query = $conn->prepare("SELECT * from products WHERE quantity > 0;");
$query->execute();
$available_products = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Products</title>
    <link rel="stylesheet" href="style.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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

    <!---All products such as Featured prod and latest prod--->
    <?php if (isset($_SESSION['messages'])) {
        foreach ($_SESSION['messages'] as $message) { ?>
            <div class="message-portion">
                <p><?php echo $message ?></p>
            </div>
    <?php }
        unset($_SESSION['messages']);
    } ?>

    <?php if (isset($_SESSION['errors'])) {
        foreach ($_SESSION['errors'] as $error) { ?>
            <div class="error-portion">
                <p><?php echo $error ?></p>
            </div>
    <?php }
        unset($_SESSION['errors']);
    } ?>

    <?php if (!isset($_GET['product_id']) && !isset($_GET['s'])) { ?>
        <div class="small-container">
            <form class="search">
                <input list="s" placeholder="Search.." name="s" autocomplete="off">
                <datalist id="s">
                    <?php foreach ($available_products as $product) { ?>
                        <option value="<?php echo $product['name']; ?>">
                        <?php } ?>
                </datalist>
                <button type=" submit"><i class="fa fa-search"></i></button>
            </form>

            <div class="row">
                <?php foreach ($available_products as $product) { ?>

                    <div class="col-4">
                        <a href="?product_id=<?php echo $product['id'] ?>">
                            <img src="./admin/<?php echo $product['img_url'] ?>">
                            <h4><?php echo $product['name']; ?></h4>
                            <p><?php echo $product['price']; ?></p>
                        </a>
                    </div>

                <?php } ?>
            </div>

            <div class="page-btn">
                <span>Next</span>
                <span>Previous</span>
            </div>
        </div>
    <?php } else if (!isset($_GET['product_id']) && isset($_GET['s'])) {
        $search = $_GET['s'];

        $query = $conn->prepare('SELECT * FROM products WHERE name LIKE ?;');
        $query->execute(array("%" . $search . "%"));

        $searched_products = $query->fetchAll(PDO::FETCH_ASSOC);

    ?>
        <div class="small-container">
            <form class="search" action="./search.php">
                <input type="text" placeholder="Search.." name="search" autocomplete="off">
                <button type="submit"><i class="fa fa-search"></i></button>
            </form>

            <div class="row">
                <?php foreach ($searched_products as $product) { ?>

                    <div class="col-4">
                        <a href="?product_id=<?php echo $product['id'] ?>">
                            <img src="./admin/<?php echo $product['img_url'] ?>">
                            <h4><?php echo $product['name']; ?></h4>
                            <p>₱<?php echo $product['price']; ?></p>
                        </a>
                    </div>

                <?php } ?>
            </div>
        </div>

        <?php } else {

        $query = $conn->prepare("SELECT * from products WHERE id = ?;");
        $query->execute(array($_GET['product_id']));
        $product = $query->fetch(PDO::FETCH_ASSOC);

        if ($product) { ?>
            <div class="small-container single-product">
                <div class="row">
                    <div class="col-2">
                        <img src="./admin/<?php echo $product['img_url'] ?>" width="100%" id="ProdImg">
                    </div>
                    <div class="col-2">
                        <p><?php echo $product['category'] ?></p>
                        <h1><?php echo $product['name'] ?></h1>
                        <h4><?php echo $product['price'] ?></h4>
                        <h3>Product Details</h3>
                        <p><?php echo $product['description'] ?></p>
                        <form action="./add_to_cart.php">
                            <input type="number" name="amount" value="1" min="1" max="<?php echo $product['quantity'] ?>"> On Stock: <?php echo $product['quantity'] ?>
                            <button type="submit" class="btn" name="product_id" value="<?php echo $product['id'] ?>">Add to Cart</a>
                        </form>
                    </div>
                </div>
            </div>
    <?php } else {
            array_push($errors, "Product Not Found!");
            $_SESSION['errors'] = $errors;
            header("Location: ./products.php");
        }
    } ?>

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