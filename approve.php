<?php
include 'db.php';
include 'flash.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $reg_id = (int) $_GET['id'];
    $user_id = (int) ($_GET['user_id'] ?? 0); // Get the user_id to redirect back
    $action = ($_GET['action'] ?? 'approve') === 'reject' ? 'reject' : 'approve';
    $new_status = $action === 'reject' ? 'Rejected' : 'Registered';

    $stmt = mysqli_prepare($conn, "UPDATE registrations SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $new_status, $reg_id);

    // Destination to redirect back to, decided before we know success/failure
    $return = $_GET['return'] ?? '';
    $return_to = $return === 'pending'
        ? "admin_pending.php"
        : ($return === 'students'
            ? "admin.php?open=$user_id"
            : "admin_student_details.php?user_id=$user_id");

    if (mysqli_stmt_execute($stmt)) {
        flash_set(
            $action === 'reject' ? 'error' : 'success',
            $action === 'reject' ? 'Registration rejected.' : 'Registration approved.'
        );
    } else {
        error_log('approve.php update failed: ' . mysqli_error($conn));
        flash_set('error', 'Something went wrong updating that registration. Please try again.');
    }

    header("Location: $return_to");
    exit();
}
?>