<?php

require_once("./database.php");
require_once("./init.php");

if (!isset($_SESSION['user_id'])) {
    array_push($errors, "You must be logged in to access this!");
    $_SESSION['errors'] = $errors;
    header("Location: ./index.php");
}

$query = $conn->prepare("SELECT * FROM transactions WHERE user_id = ?;");
$query->execute(array($_SESSION['user_id']));
$transactions = $query->fetchAll(PDO::FETCH_ASSOC);

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

    <div class="mainhistory-container">
        <div class="label-container">
            <h1>Purchase History</h1>
        </div>
        <div class="history-container">
            <form action="orderdetails.html">
                <table class="oldorder">
                    <tr>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>

                    <?php foreach ($transactions as $transaction) { ?>
                        <tr>
                            <td><?php echo $transaction['id']; ?></td>
                            <td><?php echo $transaction['transact_date']; ?></td>
                            <td>
                                <form action="./order_details.php?transact_id=<?php echo $transaction['id']; ?>">
                                    <button type="submit" class="btnOD">View Order Details</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>

                </table>
            </form>

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