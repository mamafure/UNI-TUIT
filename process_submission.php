<?php
include 'db.php';

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
    // Success! Redirect with a JavaScript alert
    echo "<script>
            alert('Congratulations! Your application has been submitted successfully. Please wait for Admin confirmation.');
            window.location.href = 'home.php';
          </script>";
} else {
    // Error handling
    echo "Error updating records: " . mysqli_error($conn);
}
?>