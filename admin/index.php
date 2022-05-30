<?php
$pdo = new PDO('mysql:host=localhost; port=3306; dbname=shop', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

require_once('../init.php');

if (!isset($_SESSION['user_id'])) {
    array_push($errors, "You must be logged in to access this page!");
    $_SESSION['errors'] = $errors;
    header("Location: ../login_register_form.php");
}

$query = $pdo->prepare("SELECT * FROM users WHERE id = ?;");
$query->execute([$_SESSION['user_id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user['is_admin'] != 1) {
    array_push($errors, "Insufficient Privileges!");
    $_SESSION['errors'] = $errors;
    header("Location: ../index.php");
}


$statement = $pdo->prepare('SELECT * FROM products ORDER BY date_added DESC');
$statement->execute();
$products = $statement->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Admin</title>
</head>

<body>
    <div class="container">
        <div class="header">


            <div class="col-2">
                <h1>Admin</h1>
            </div>
            <a href="add_product.php"><button type="button" class='forinputsubmit'>Create Product</button></a>
            <a href="add_admin.php"><button type="button" class='forinputsubmit'>Create New Admin</button></a>
        </div>
        <br>
        <div class="arrangetable">
            <div class="col-2">
                <h1>Products List</h1>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $i => $product) : ?>
                        <tr>
                            <th><?php echo $i + 1 ?></th>
                            <td>
                                <?php if ($product['img_url']) : ?>
                                    <img src="<?php echo $product['img_url'] ?>" alt="<?php echo $product['name'] ?>" class="product-img">
                                <?php endif; ?>
                            </td>
                            <td><?php echo $product['name'] ?></td>
                            <td><?php echo $product['description'] ?></td>
                            <td><?php echo $product['category'] ?></td>
                            <td><?php echo $product['price'] ?></td>
                            <td><?php echo $product['quantity'] ?></td>
                            <td>
                                <a href="update_product.php?id=<?php echo $product['id'] ?>"><button type="submit" class="edit">EDIT</button></a>
                                <form method="post" action="delete_product.php" style="display: inline-block">
                                    <input type="hidden" name="id" value="<?php echo $product['id'] ?>" />
                                    <button type="submit" class="delete">DELETE</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>