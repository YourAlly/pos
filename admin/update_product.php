<?php
require_once 'functions.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=shop', 'root', '');
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

$statement = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);


$name = '';
$description = '';
$category = '';
$price = '';
$quantity = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'];
    $description = $_POST['description'];
    $category = $_POST['category'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if (!trim($name)) {
        $errors[] = 'Product NAME is REQUIRED';
    }
    if (!trim($category)) {
        $errors[] = 'Product CATEGORY is REQUIRED';
    }
    if (!trim($price)) {
        $errors[] = 'Product PRICE is REQUIRED';
    }
    if (!trim($quantity)) {
        $errors[] = 'Product QUANTITY is REQUIRED';
    }

    $image = $_FILES['img_url'] ?? null;
    $imagePath = '';

    if (!is_dir('images')) {
        mkdir('images');
    }


    if (!($_FILES['img_url']['size'] == 0 && $_FILES['cover_image']['error'] == 0)) {
        if ($image && $image['tmp_name']) {
            $updateStatement = "";
            if ($product['img_url']) {
                unlink($product['img_url']);
                $updateStatement = $product['img_url'];
                echo substr($updateStatement, 7, 8);
                $imagePath = 'images/' . substr($updateStatement, 7, 8) . '/' . $image['name'];
                move_uploaded_file($image['tmp_name'], $imagePath);
            } else {
                $updateStatement = randomString(8);
                $imagePath = 'images/' . $updateStatement . '/' . $image['name'];
                mkdir(dirname($imagePath));
                move_uploaded_file($image['tmp_name'], $imagePath);
            }
        } else {
            unlink($product['img_url']);
            $updateStatement = $product['img_url'];
            rmdir(substr($updateStatement, 0, 15));
        }
    } else {
        $imagePath = $product['img_url'];
    }

    if (empty($errors)) {


        $statement = $pdo->prepare("UPDATE products SET name = :name, img_url = :img_url, 
                                    description = :description, category = :category, price = :price, quantity = :quantity
                                    WHERE id = :id");
        $statement->bindValue(':name', $name);
        $statement->bindValue(':img_url', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':category', $category);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':quantity', $quantity);
        $statement->bindValue(':id', $id);

        $statement->execute();
        header('Location: index.php');
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Update Product</title>
</head>

<body>
    <div class="container">
        <div class="header">

            <div class="col-2">
                <h1>Update Product: <span class="shit"> <?php echo $product['name'] ?> </span> </h1>
            </div>
            <a href="index.php"><button type="button" class="forinputsubmit">View Products</button></a>
        </div>
        <!-- ALERT MESSAGE -->
        <?php if (!empty($errors)) : ?>
            <div class="alert">
                <?php foreach ($errors as $error) : ?>
                    <div><b> <?php echo $error ?> </b></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <!-- FORM -->
        <div class="myform">
            <form action="" method="POST" enctype="multipart/form-data">
                <h4>Update Name</h4>
                <input type="text" name="name" id="" placeholder="Enter product name.." class="forinput" value="<?php echo $product['name'] ?>"> <br>

                <?php if ($product['img_url']) : ?>
                    <img src="<?php echo $product['img_url'] ?>" class="product-image-view">
                <?php endif; ?>
                <h4>Sample Image</h4>
                <input type="file" name="img_url" id="" placeholder="Enter product image.." class="forinput"> <br>

                <h4>Product Description</h4>
                <textarea placeholder="Enter product description.." name="description" placeholder="Enter product description.." class="forinput"><?php echo $product['description'] ?></textarea>

                <h4>Category
                    <select id="singleSelect" onchange="singleSelectChangeValue()" class="forselect">
                        <option value="CAT">CAT</option>
                        <option value="FOOD">FOOD</option>
                        <option value="TOY">TOY</option>
                    </select>
                </h4>
                <input type="text" name="category" id="category" class="forinput" placeholder="Select category.." value="<?php echo $product['category'] ?>" readonly>


                <h4>Product Price</h4>
                <input type="number" step=".01" name="price" id="" placeholder="Enter product price.." class="forinput" value="<?php echo $product['price'] ?>"> <br>

                <h4>Product Quantity</h4>
                <input type="number" name="quantity" id="" placeholder="Enter product quantity.." class="forinput" value="<?php echo $product['quantity'] ?>"> <br>

                <input type="submit" name="submit" class="update" value="Update Product">
            </form>
        </div>
    </div>
    <script>
        function singleSelectChangeValue() {
            var selObj = document.getElementById("singleSelect");
            var selValue = selObj.options[selObj.selectedIndex].value;
            document.getElementById("category").value = selValue;
        }
    </script>
</body>

</html>