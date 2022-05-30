<?php
require_once 'functions.php';


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



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (
        isset($_POST['username']) &&
        isset($_POST['password1']) &&
        isset($_POST['password2']) &&
        isset($_POST['first_name']) &&
        isset($_POST['last_name']) &&
        isset($_POST['s_question']) &&
        isset($_POST['s_answer'])
    ) {

        if (empty($_POST['username']) || empty($_POST['password1'])) {
            array_push($errors, "Username and password are required!");
        }
        if ($_POST['password1'] != $_POST['password2']) {
            array_push($errors, "Both password fields must be equal!");
        }
        if (empty($_POST['first_name']) || empty($_POST['last_name'])) {
            array_push($errors, "Both name fields are required!");
        }
        if (empty($_POST['s_answer']) || empty($_POST['s_question'])) {
            array_push($errors, "Security question and answer are required!");
        }

        if (empty($errors)) {
            $username = $_POST['username'];
            $password = $_POST['password1'];
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $s_question = $_POST['s_question'];
            $s_answer = $_POST['s_answer'];

            try {
                $query = $conn->prepare("INSERT INTO users (username, password, is_admin)
                                VALUES (:username, :password, :is_admin);");
                $query->execute(array(
                    'username' => $username,
                    'password' => $password,
                    'is_admin' => 1
                ));
                $uid = $conn->lastInsertId();
                $query2 = $conn->prepare("INSERT INTO profiles (user_id, first_name, last_name,
                        s_question, s_answer) VALUES (:user_id, :first_name, :last_name, :s_question, :s_answer);");

                $query2->execute(array(
                    'user_id' => $uid,
                    'first_name' =>  $first_name,
                    'last_name' =>  $last_name,
                    's_question' => $s_question,
                    's_answer' => $s_answer
                ));

                array_push($messages, "Admin registered!");
                $_SESSION['messages'] = $messages;
                header("Location: ./");
            } catch (Exception $e) {
                array_push($errors, "Username taken!");
            }
        }
    }
}

$_SESSION['errors'] = $errors;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style2.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Add Admin</title>
</head>

<body>
    <div class="container">
        <div class="header">

            <div class="col-2">
                <h1>Add Admin</h1>
            </div>
            <a href="./"><button type="button" class="forinputsubmit">Admin</button></a>
        </div>
        <!-- ALERT MESSAGE -->
        <?php if (!isset($_SESSION['errors'])) : ?>
            <div class="alert">
                <?php foreach ($_SESSION['errors'] as $error) : ?>
                    <div><b> <?php echo $error ?> </b></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <!-- FORM -->
        <div class="myform">
            <form method="POST" enctype="multipart/form-data">
                <h4>First Name</h4>
                <input type="text" name="first_name" placeholder="Enter first name.." class="forinput"> <br>
                <h4>Last Name</h4>
                <input type="text" name="last_name" placeholder="Enter last name.." class="forinput"> <br>
                <h4>Username</h4>
                <input type="text" name="username" placeholder="Enter username.." class="forinput"> <br>
                <h4>Password</h4>
                <input type="password" name="password1" placeholder="Enter password.." class="forinput"> <br>
                <h4>Confirm Password</h4>
                <input type="password" name="password2" placeholder="Enter confirm password.." class="forinput"> <br>

                <h4>Security Question</h4>
                <select name="s_question" id="" class="forinput">
                    <option value="1">sample1</option>
                    <option value="2">sample2</option>
                    <option value="3">sample3</option>
                </select> <br>

                <h4>Security Question Answer</h4>
                <input type="text" name="s_answer" placeholder="Enter answer.." class="forinput"> <br>

                <input type="submit" name="submit" class="forinputsubmit" value="Add Product">
            </form>
        </div>
    </div>

    <script src="thing.js"></script>
</body>

</html>