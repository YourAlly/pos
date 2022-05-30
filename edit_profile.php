<?php

require_once("./database.php");
require_once('./init.php');

if (!isset($_SESSION['user_id'])) {
    array_push($errors, "You must be logged in to access this page!");

    $_SESSION['errors'] = $errors;
    header("Location: ./index.php");
}

$query = $conn->prepare("SELECT * FROM users WHERE id = ?;");
$query->execute([$_SESSION['user_id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user['id'] != $_SESSION['user_id'] && $user['is_admin'] != 1) {
    array_push($errors, "Insufficient Priveleges!");

    $_SESSION['errors'] = $errors;
    header("Location: ./index.php");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Change password
    if ($_POST['submit'] === "change_password") {

        // Trashy Validation
        if ($_POST['password1'] != $_POST['password2']) {
            array_push($errors, "Both password fields must be equal!");
        }
        if (!password_verify($_POST['verification'], $user['password'])) {
            array_push($errors, "The password for verification didn't match!");
        }

        // If it gets past the trashy validation
        if (empty($errors)) {
            $hash = password_hash($_POST['password1'], PASSWORD_DEFAULT);
            $change_pass = $conn->prepare("UPDATE users SET password = :hash
                WHERE id = :user_id");
            $change_pass->execute(array(
                'hash' => $hash,
                'user_id' => $user['id']
            ));
            array_push($messages, "Password Changed!");
            $_SESSION['messages'] = $messages;
            header("Location: ./profile_page.php");
        }
    } else if ($_POST['submit'] === "change_name") {

        if (empty($_POST['first_name']) || empty($_POST['last_name'])) {
            array_push($errors, "Both name fields are required!");
        }
        if (!password_verify($_POST['verification'], $user['password'])) {
            array_push($errors, "The password for verification didn't match!");
        }

        // If it gets past the trashy validation
        if (empty($errors)) {
            $change_pass = $conn->prepare("UPDATE profiles SET first_name = :first_name,
                last_name = :last_name WHERE user_id = :user_id");
            $change_pass->execute(array(
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'user_id' => $user['id']
            ));

            array_push($messages, "Name Changed!");
            $_SESSION['messages'] = $messages;
            header("Location: ./profile_page.php");
        }
    } else if ($_POST['submit'] === "change_address") {

        if (empty($_POST['address'])) {
            array_push($errors, "Both name fields are required!");
        }
        if (!password_verify($_POST['verification'], $user['password'])) {
            array_push($errors, "The password for verification didn't match!");
        }

        // If it gets past the trashy validation
        if (empty($errors)) {
            $change_pass = $conn->prepare("UPDATE profiles SET address = :address
                WHERE user_id = :user_id");
            $change_pass->execute(array(
                'address' => $_POST['address'],
                'user_id' => $user['id']
            ));

            array_push($messages, "Address Changed!");
            $_SESSION['messages'] = $messages;
            header("Location: ./profile_page.php");
        }
    } else if ($_POST['submit'] === "change_s_question") {

        if (empty($_POST['s_answer'])) {
            array_push($errors, "The answer is required!");
        }
        if (!password_verify($_POST['verification'], $user['password'])) {
            array_push($errors, "The password for verification didn't match!");
        }

        if (empty($errors)) {
            $change_pass = $conn->prepare("UPDATE profiles SET s_question = :s_question,
                s_answer = :s_answer WHERE user_id = :user_id;");
            $change_pass->execute(array(
                's_question' => $_POST['s_question'],
                's_answer' => $_POST['s_answer'],
                'user_id' => $_SESSION['user_id']
            ));

            array_push($messages, "Security Question and Answer Changed!");
            $_SESSION['messages'] = $messages;
            header("Location: ./profile_page.php");
        }
    }
}
$_SESSION['errors'] = $errors;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="app.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Edit Profile</title>
</head>

<body>
    <div class="header-1">
        <h1>Edit Profile</h1>
    </div>

    <div class="container">
        <div class="header">
            Change Password
        </div>
        <div class="arrange">
            <form method="POST">
                Current Password <br>
                <input type="password" name="verification" class="forinput"> <br>
                New Password <br>
                <input type="password" name="password1" class="forinput"> <br>
                Confirm newPassword <br>
                <input type="password" name="password2" class="forinput"> <br>
                <input type="submit" name="submit" value="change_password" class="forinputsubmit">
            </form>
        </div>

        <div class="header">
            Change Name
        </div>
        <div class="arrange">
            <form method="POST">
                Current Password <br>
                <input type="password" name="verification" class="forinput"> <br>
                First Name <br>
                <input type="text" name="first_name" class="forinput"> <br>
                Last Name <br>
                <input type="text" name="last_name" class="forinput"> <br>
                <input type="submit" name="submit" value="change_name" class="forinputsubmit">
            </form>
        </div>

        <div class="header">
            Change Address
        </div>
        <div class="arrange">
            <form method="POST">
                Current Password <br>
                <input type="password" name="verification" class="forinput"> <br>
                New Address <br>
                <input type="text" name="address" class="forinput"> <br>
                <input type="submit" name="submit" value="change_address" class="forinputsubmit">
            </form>
        </div>

        <div class="header">
            Change Security Question
        </div>
        <div class="arrange">
            <form method="POST">
                Current Password <br>
                <input type="password" name="verification" class="forinput"> <br>
                New Security Question <br>
                <select name="s_question" class="forinput">
                    <option value="1">What is your favorite color?</option>
                    <option value="2">What is your favorite food?</option>
                    <option value="3">How old is your mom?</option>
                </select> <br>
                New Security Answer <br>
                <input type="text" name="s_answer" class="forinput"> <br>
                <input type="submit" name="submit" value="change_s_question" class="forinputsubmit">
            </form>
        </div>
    </div>



</body>

</html>