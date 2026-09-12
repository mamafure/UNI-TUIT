<?php
include 'db.php';

if (isset($_GET['id'])) {
    $reg_id = $_GET['id'];
    $user_id = $_GET['user_id']; // Get the user_id to redirect back

    $sql = "UPDATE registrations SET status = 'Registered' WHERE id = '$reg_id'";

    if (mysqli_query($conn, $sql)) {
        // Redirect back to the specific student details page
        header("Location: admin_student_details.php?user_id=$user_id");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>