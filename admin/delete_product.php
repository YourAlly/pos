<?php 
$pdo = new PDO('mysql:host=localhost; port=3306;dbname=shop',  'root', '');
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

$id = $_POST['id'] ?? null;
if (!$id) {
    array_push($errors, "An ID is required!");
    $_SESSION['errors'] = $errors;
    header('Location: ./index.php');
    exit;
}

$deleteStatement = $pdo->prepare('SELECT img_url FROM products WHERE id = :id');
$deleteStatement->bindValue(':id', $id);
$deleteStatement->execute();
$images = $deleteStatement->fetchAll(PDO::FETCH_ASSOC);
$deleteImage = "";
foreach($images as $image){
    echo $image['img_url'];
    $deleteImage = implode($image);
    unlink($deleteImage);
}

rmdir(substr($deleteImage, 0, 15));

$statement = $pdo->prepare('DELETE FROM products WHERE id = :id');
$statement->bindValue(':id', $id);
$statement->execute();


header('Location: index.php');

?>