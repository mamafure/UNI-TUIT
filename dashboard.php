<?php
include 'db.php';
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: index.php"); }

$user_id = $_SESSION['user_id'];

// Get user's current selections
$my_modules = mysqli_query($conn, "SELECT module_name FROM student_modules WHERE user_id = '$user_id'");
$selected_list = [];
while($row = mysqli_fetch_assoc($my_modules)) {
    $selected_list[] = $row['module_name'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | UNI-TUIT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; background: #f4f4f4; padding: 20px; }
        .header { background: #1e3a8a; color: white; padding: 20px; display: flex; justify-content: space-between; border-radius: 10px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px; }
        .card { background: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .btn { background: #f59e0b; color: white; padding: 10px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px; }
        .status { color: green; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>

<div class="header">
    <h2>Welcome, <?php echo $_SESSION['username']; ?>!</h2>
    <a href="logout.php" style="color: white;">Logout</a>
</div>

<h3>Available Modules (Fee: 5,000 Tsh each)</h3>
<div class="grid">
    <?php
    $modules = ["Database System", "OS", "Networking", "Web Programming", "Calculus"];
    foreach ($modules as $m) {
        echo "<div class='card'>";
        echo "<h3>$m</h3>";
        if (in_array($m, $selected_list)) {
            echo "<p class='status'><i class='fas fa-check-circle'></i> Already Selected</p>";
        } else {
            echo "<a href='view_module.php?name=$m' class='btn'>View & Register</a>";
        }
        echo "</div>";
    }
    ?>
</div>

</body>
</html>