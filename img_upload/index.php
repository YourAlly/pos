<?php 
require_once 'functions.php';

require_once("../init.php");

// dummy
$id = 1;

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=shop;', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if (!isset($_SESSION['user_id'])) {
    array_push($errors, "You must be logged in to access this page!");

    $_SESSION['errors'] = $errors;
    header("Location: ./index.php");
}

$query = $pdo->prepare("SELECT * FROM users WHERE id = ?;");
$query->execute([$_SESSION['user_id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user['id'] != $_SESSION['user_id'] && $user['is_admin'] != 1) {
    array_push($errors, "Insufficient Priveleges!");

    $_SESSION['errors'] = $errors;
    header("Location: ./index.php");
}


//dummy db sample with only two columns id and img_url
$statement = $pdo->prepare('SELECT * FROM profiles WHERE user_id = :id');
$statement->bindValue(':id', $_SESSION['user_id']);
$statement->execute();
$sample = $statement->fetch(PDO::FETCH_ASSOC);

//dummy lmao
$errors = [];
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $image = $_FILES['img_url'] ?? null;
    $imagePath = '';

    if(!is_dir('images')){
        mkdir('images');
    }

    if (!($_FILES['img_url']['size'] == 0 && $_FILES['cover_image']['error'] == 0)) {
        if ($image && $image['tmp_name']) {
            $updateStatement = "";
            if ($sample['img_url']) {
                unlink($sample['img_url']);
                $updateStatement = $sample['img_url'];
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
            unlink($sample['img_url']);
            $updateStatement = $sample['img_url'];
            rmdir(substr($updateStatement, 0, 15));
        }
    } else {
        $imagePath = $sample['img_url'];
    }
    if(empty($errors)){
        $statement = $pdo->prepare("UPDATE profiles SET img_url = :img_url WHERE user_id = :id" );
        $statement->bindValue(':img_url', $imagePath);
        $statement->bindValue(':id', $_SESSION['user_id']);

        $statement->execute();
        header('Location: ../profile_page.php');
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./app.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Image Upload</title>
</head>
<body>
<div class="header-1">
    <h1>Edit Profile</h1>
    </div>

    <div class="container">
        <div class="header">
        Change Profile
        </div>
        <div class="arrange">
        <?php if($sample['img_url']):?>
        <img src="<?php echo $sample['img_url'] ?>" class="product-image-view">
        <?php endif;?>
        <form method="POST" enctype="multipart/form-data">
            <input type="file" name="img_url"/>
            <br>
        <input type="submit" name="submit" value="UPDATE" class='forinputsubmit'>
        </form>
        </div>
</div>
</body>
</html>