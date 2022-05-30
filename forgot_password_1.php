<?php

require_once("./database.php");
require_once("./init.php");

if (isset($_SESSION['verified'])){
    unset($_SESSION['verified']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        isset($_POST['username']) &&
        isset($_POST['s_question']) &&
        isset($_POST['s_answer'])) 
    {
        $query = $conn->prepare("SELECT * FROM users WHERE username = ?;");
        $query->execute([$_POST['username']]);
        $user = $query->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            if (
                strtolower($user['s_question']) == strtolower($_POST['s_question']) &&
                strtolower($user['s_answer']) == strtolower($_POST['s_answer']))
            {
                $_SESSION['verified'] = true;
                $_SESSION['v_user_id'] = $user['id'];
                header("Location: ./forgot_password_2.php?" . "verified=true");
            }

            else{
                array_push($errors, "Question and Answer didn't match!");
            }
            
        } else {
            array_push($errors, "User not found!");
        }
    }
    $_SESSION['errors'] = $errors;
}
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
        Forgot Password
        </div>

        <!-- ALERT MESSAGE -->
        <div class="alert">
            <div><b> error go here </b></div>
            </div>

            
        <div class="arrange">
        <form action="" method="POST">
        Username <br>
        <input type="text" class="forinput"> <br>
        Security Question <br>
        <select name="s_question" id="" class="forinput">
            <option value="sample">sample</option>
            <option value="sample">sample</option>
            <option value="sample">sample</option>
        </select> <br>
        Security Answer <br>
        <input type="text" class="forinput"> <br>

        <input type="submit" name="submit" value="SUBMIT" class="forinputsubmit">
        </form>
        </div>
    </div>
</body>
</html>