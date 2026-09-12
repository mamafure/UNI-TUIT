<?php
include 'db.php';

// REGISTER
if (isset($_POST['register'])) {
    $user = $_POST['username'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $prog = $_POST['program'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Validation
    if (!preg_match('/^(06|07)[0-9]{8}$/', $phone)) {
        echo "<script>alert('Phone must be 10 digits starting with 06 or 07'); window.location='index.php';</script>";
    } else {
        $sql = "INSERT INTO users (username, phone, email, password, program) VALUES ('$user', '$phone', '$email', '$pass', '$prog')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Success! Please Login.'); window.location='index.php';</script>";
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
        header("Location: home.php");
    } else {
        echo "<script>alert('Wrong email or password'); window.location='index.php';</script>";
    }
}
?>