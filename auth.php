<?php
include 'db.php';
include 'flash.php';

// REGISTER
if (isset($_POST['register'])) {
    $user = $_POST['username'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $prog = $_POST['program'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validation
    if (!preg_match('/^(06|07)[0-9]{8}$/', $phone)) {
        flash_set('error', 'Phone must be 10 digits starting with 06 or 07.');
        header("Location: index.php?auth=register");
        exit();
    } else {
        $sql = "INSERT INTO users (username, phone, email, password, program) VALUES ('$user', '$phone', '$email', '$pass', '$prog')";
        if (mysqli_query($conn, $sql)) {
            flash_set('success', 'Account created! Please log in to continue.');
            header("Location: index.php?auth=login");
            exit();
        } else {
            flash_set('error', 'That email is already registered. Try logging in instead.');
            header("Location: index.php?auth=register");
            exit();
        }
    }
}

// LOGIN
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $row = mysqli_fetch_assoc($res);

    if ($row && password_verify($pass, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        flash_set('success', 'Welcome back, ' . $row['username'] . '!');
        header("Location: home.php");
        exit();
    } else {
        flash_set('error', 'Wrong email or password.');
        header("Location: index.php?auth=login");
        exit();
    }
}
?>