<?php
include 'db.php';
include 'flash.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$u_id = $_SESSION['user_id'];

/**
 * Update logic:
 * We change the status from 'Draft' to 'Pending'.
 * This tells the Admin that the student has officially clicked "Submit".
 */
$sql = "UPDATE registrations SET status = 'Pending' WHERE user_id = '$u_id' AND status = 'Draft'";

if(mysqli_query($conn, $sql)) {
    flash_set('success', 'Your application has been submitted successfully. Please wait for Admin confirmation.');
} else {
    error_log('process_submission.php update failed: ' . mysqli_error($conn));
    flash_set('error', 'Something went wrong submitting your application. Please try again.');
}
header("Location: home.php");
exit();
?>