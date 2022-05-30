<?php

require_once("./database.php");
require_once('./init.php');

if (!isset($_SESSION['user_id'])) {
    array_push($errors, "You must be logged in to access this page!");
}

$query = $conn->prepare("SELECT * FROM users WHERE id = ?;");
$query->execute([$_SESSION['user_id']]);
$user = $query->fetch(PDO::FETCH_ASSOC);

if ($user['is_admin'] != 1){
    array_push($errors, "Insufficient Privileges!");
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

            $query = $conn->prepare("INSERT INTO users (username, password, is_admin)
                                VALUES (:username, :password, :is_admin);");
            $query->execute(array(
                'username' => $username,
                'password' => $password,
                'is_admin' => 1
            ));
            $uid = $conn->lastInsertId();

            $query2 = $conn->prepare("INSERT INTO users (user_id, first_name, last_name
            s_question, s_answer) VALUES (:user_id, :first_name, :last_name, :s_question,
            :s_answer);");

            $query2->execute();
        }
    }
    $_SESSION['errors'] = $errors;
}
