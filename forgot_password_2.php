<?php

require_once("./database.php");
require_once("./init.php");

if (isset($_SESSION['verified']) && isset($_SESSION['v_user_id'])) {
    unset($_SESSION['verified']);
} else {
    array_push($errors, "You are not yet verified!");
    $_SESSION['errors'] = $errors;
    header("Location: ./forgot_password_1.php");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Trashy Validation
    if ($_POST['password1'] != $_POST['password2']) {
        array_push($errors, "Both password fields must be equal!");
    }

    // If it gets past the trashy validation
    if (empty($errors)) {
        $hash = password_hash($_POST['password1'], PASSWORD_DEFAULT);
        $change_pass = $conn->prepare("UPDATE users SET password = :hash
                WHERE id = :user_id");
        $change_pass->execute(array(
            'hash' => $hash,
            'user_id' => $_SESSION['v_user_id']
        ));

        unset($_SESSION['v_user_id']);
        array_push($messages, "Password Changed!");
        $_SESSION['messages'] = $messages;
        $header("Location: ./account.php");
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
    <link rel="stylesheet" href="forgot.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>Document</title>
</head>

<body>
    <div class="forgotpasscontainer">
        <div class="header">
            Create New Password
        </div>

        <!-- ALERT MESSAGE -->
        <div class="alert">
            <div><b> error go here </b></div>
        </div>

        <div class="arrange">
            <form action="" method="POST">
                New Password <br>
                <input type="text" class="forinput"> <br>
                Confirm Password <br>
                <input type="text" class="forinput"> <br>
                <input type="submit" name="submit" value="SUBMIT" class="forinputsubmit">
            </form>
        </div>
    </div>
</body>

</html>