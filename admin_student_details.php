<?php 
include 'db.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header("Location: admin_login.php"); exit(); }

$user_id = $_GET['user_id'];

// Get student personal details
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$student = mysqli_fetch_assoc($user_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Details: <?php echo $student['username']; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f1f5f9; padding: 40px; }
        .container { max-width: 900px; margin: auto; }
        .back-btn { text-decoration: none; color: #1e3a8a; font-weight: bold; margin-bottom: 20px; display: inline-block; }
        
        .profile-card { background: white; padding: 30px; border-radius: 15px; display: flex; gap: 30px; align-items: center; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .profile-card i { font-size: 50px; color: #cbd5e1; }
        
        .subject-table { background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; }
        td { padding: 15px; border-top: 1px solid #f1f5f9; }

        .status { padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-Pending { background: #fef3c7; color: #92400e; }
        .status-Registered { background: #d1fae5; color: #065f46; }

        .btn-approve { background: #10b981; color: white; padding: 6px 12px; border-radius: 5px; text-decoration: none; font-size: 12px; }
    </style>
</head>
<body>

<div class="container">
    <a href="admin.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Student List</a>

    <div class="profile-card">
        <i class="fas fa-user-circle"></i>
        <div>
            <h1 style="margin:0;"><?php echo $student['username']; ?></h1>
            <p style="margin:5px 0; color:#64748b;">
                <i class="fas fa-envelope"></i> <?php echo $student['email']; ?> | 
                <i class="fas fa-phone"></i> <?php echo $student['phone']; ?>
            </p>
            <p style="margin:0; font-weight:bold; color:#1e3a8a;">Program: <?php echo $student['program']; ?></p>
        </div>
    </div>

    <h3>Selected Modules</h3>
    <div class="subject-table">
        <table>
            <thead>
                <tr>
                    <th>Module Name</th>
                    <th>Fee</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $reg_query = mysqli_query($conn, "SELECT * FROM registrations WHERE user_id = '$user_id'");
                while($row = mysqli_fetch_assoc($reg_query)) {
                    echo "<tr>";
                    echo "<td><strong>{$row['module_name']}</strong></td>";
                    echo "<td>5,000 Tsh</td>";
                    echo "<td><span class='status status-{$row['status']}'>{$row['status']}</span></td>";
                    echo "<td>";
                    if($row['status'] == 'Pending') {
                        // Pass both reg_id and user_id to redirect back properly
                        echo "<a href='approve.php?id={$row['id']}&user_id=$user_id' class='btn-approve'>Approve</a>";
                    } else {
                        echo "<i class='fas fa-check-circle' style='color:#10b981;'></i> Confirmed";
                    }
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>