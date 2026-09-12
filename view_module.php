<?php
include 'db.php';
session_start();
$module_name = $_GET['name'];
$user_id = $_SESSION['user_id'];

// Handle Registration
if (isset($_POST['confirm_select'])) {
    mysqli_query($conn, "INSERT INTO student_modules (user_id, module_name) VALUES ('$user_id', '$module_name')");
    echo "<script>alert('Module Added Successfully!'); window.location='dashboard.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $module_name; ?> | UNI-TUIT</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; background: #eee; }
        .box { background: white; padding: 40px; border-radius: 15px; text-align: center; max-width: 500px; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .price { font-size: 24px; color: #1e3a8a; font-weight: bold; margin: 20px 0; }
        .btn-add { background: #22c55e; color: white; border: none; padding: 15px 30px; border-radius: 5px; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>

<div class="box">
    <h1>Module: <?php echo $module_name; ?></h1>
    <p>This course provides a comprehensive guide to understanding <strong><?php echo $module_name; ?></strong>. We guarantee quality teaching and hands-on practicals for every student.</p>
    
    <div class="price">Tuition Fee: 15,000 Tsh</div>

    <form method="POST">
        <button type="submit" name="confirm_select" class="btn-add">Select this Subject</button>
    </form>
    <br>
    <a href="dashboard.php">Go Back</a>
</div>

</body>
</html>