<?php
include 'db.php';
include 'flash.php';

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

            header("Location: admin");
            exit();
        } else {
            flash_set('error', 'Invalid admin password.');
            header("Location: admin_login");
            exit();
        }
    } else {
        // No admin found with that email
        flash_set('error', 'Access denied: you are not authorized as an admin.');
        header("Location: admin_login");
        exit();
    }
}
?>