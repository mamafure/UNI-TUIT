<?php
include 'db.php';

if (isset($_POST['admin_login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    // Search for user with this email AND role 'admin'
    $query = "SELECT * FROM users WHERE email='$email' AND role='admin'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        // Verify the hashed password
        if (password_verify($password, $user['password'])) {
            // Set Admin Sessions
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = 'admin';

            header("Location: admin.php");
            exit();
        } else {
            echo "<script>alert('Invalid Admin Password!'); window.location='admin_login.php';</script>";
        }
    } else {
        // No admin found with that email
        echo "<script>alert('Access Denied: You are not authorized as an Admin.'); window.location='admin_login.php';</script>";
    }
}
?>