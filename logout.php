<?php
    require_once("./init.php");
    $_SESSION['messages'] = array("Logged Out!");
    unset($_SESSION['user_id']);
    header("Location: ./index.php");
