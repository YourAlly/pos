<?php

require_once("./database.php");
require_once("./init.php");

if (isset($_SESSION['user_id'])) {
    array_push($errors, "You're already logged in!");
    $_SESSION['errors'] = $errors;
    header("Location: ./index.php");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['submit'] == "login") {

        // Login
        if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['g-recaptcha-response'])) {

            $secretKey = "6LcSRE0aAAAAAI1jKXJr-gMB3M08d4-sRsZmeCp8";
            $ip = $_SERVER['REMOTE_ADDR'];
            // post request to server
            $url = 'https://www.google.com/recaptcha/api/siteverify?secret=' . urlencode($secretKey) .  '&response=' . urlencode($_POST['g-recaptcha-response']);
            $response = file_get_contents($url);
            $responseKeys = json_decode($response, true);
            // should return JSON with success as true
            if ($responseKeys["success"]) {
                $query = $conn->prepare("SELECT * FROM users WHERE username = ?;");
                $query->execute([$_POST['username']]);
                $user = $query->fetch(PDO::FETCH_ASSOC);
                if ($user) {
                    if (password_verify($_POST['password'], $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        array_push($messages, "Logged in!");
                        $_SESSION['messages'] = $messages;
                        header("Location: ./index.php");
                    } else {
                        array_push($errors, "Username and password didn't match!");
                    }
                } else {
                    array_push($errors, "User not found!");
                }
            } else {
                array_push($errors, "Captcha failure!");
            }
        } else {
            array_push($errors, "All fields must be checked and filled!");
        }
    } else if ($_POST['submit'] == "register") {

        // Register
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
                $password = password_hash($_POST['password1'], PASSWORD_DEFAULT);
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $s_question = $_POST['s_question'];
                $s_answer = $_POST['s_answer'];

                try {
                    $query = $conn->prepare("INSERT INTO users (username, password)
                        VALUES (:username, :password);");
                    $query->execute(array(
                        'username' => $username,
                        'password' => $password
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

                    array_push($messages, "You are now registered!");
                    $_SESSION['messages'] = $messages;
                    header("Location: ./index.php");
                } catch (Exception $e) {
                    array_push($errors, "Username taken!");
                }
            }
        }
    }
    $_SESSION['errors'] = $errors;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

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
                            <li><a href="#">Account</a></li>
                            <li><a href="./logout.php">Logout</a></li>
                        <?php } else { ?>
                            <li><a href="./login_register_form.php">Log In</a></li>
                        <?php } ?>
                    </ul>
                </nav>
                <a href="./cart.php"><img src="image/cart2.png" width="30px" height="30px"></a>
            </div>
        </div>
    </div>


    <!--login/register-->
    <?php if (isset($_SESSION['messages'])) {
        foreach ($_SESSION['messages'] as $message) { ?>
            <div class="message-portion">
                <p><?php echo $message; ?></p>
            </div>
    <?php }
        unset($_SESSION['messages']);
    } ?>

    <div class="account-page">
        <div class="container">
            <div class="row">
                <div class="col-2">
                    <img src="image/homebg2.png" width="100%">
                </div>
                <div class="col-2">
                    <div class="form-container">

                        <div class="form-btn">
                            <span onclick="login()">Login</span>
                            <span onclick="register()">Register</span>
                            <hr id="Indicator">
                        </div>
                        <?php if (isset($_SESSION['errors'])) {
                            foreach ($_SESSION['errors'] as $error) { ?>
                                <div class="error-portion">
                                    <p><?php echo $error; ?></p>
                                </div>
                        <?php }
                            unset($_SESSION['errors']);
                        } ?>



                        <form id="LoginForm" method="POST">
                            <input type="text" name="username" placeholder="Username">
                            <input type="password" name="password" placeholder="Password">
                            <div class="g-recaptcha" data-sitekey="6LcSRE0aAAAAAG74wDTkrxm4mesBqCNJr5PzsLEp"></div>

                            <button type="submit" name="submit" value="login" class="btn">Login</button>
                            <a href="">Forgot Password?</a>
                        </form>
                        <form id="RegForm" method="POST">
                            <input type="text" name="first_name" placeholder="First Name">
                            <input type="text" name="last_name" placeholder="Last Name">
                            <input type="text" name="username" placeholder="Username">
                            <input type="password" name="password1" placeholder="Password">
                            <input type="password" name="password2" placeholder="Confirm Password">
                            <label>Secret Question</label>
                            <select name="s_question">
                                <option value="1">What is your favorite color?</option>
                                <option value="2">What is your favorite food?</option>
                                <option value="3">How old is your mom?</option>
                            </select>
                            <input type="text" name="s_answer" placeholder="Secret Answer">
                            <button type="submit" name="submit" value="register" class="btn">Register</button>
                        </form>
                    </div>
                </div>
            </div>
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
    <!--JS Toggle Form-->
    <script>
        var LoginForm = document.getElementById("LoginForm");
        var RegForm = document.getElementById("RegForm");
        var Indicator = document.getElementById("Indicator");


        function register() {
            RegForm.style.transform = "translateX(0px)";
            LoginForm.style.transform = "translateX(0px)";
            Indicator.style.transform = "translateX(150px)";
        }

        function login() {

            RegForm.style.transform = "translateX(300px)";
            LoginForm.style.transform = "translateX(300px)";
            Indicator.style.transform = "translateX(0px)";

        }

        document.addEventListener('DOMContentLoaded', () => {
            login();
        })
    </script>

</body>

</html>