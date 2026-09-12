<?php 
include 'db.php'; 

// ACCESS CONTROL: Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | Student List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #1e3a8a; --bg: #f1f5f9; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        /* Sidebar */
        .sidebar { width: 250px; background: var(--primary); height: 100vh; color: white; padding: 20px; position: fixed; }
        .sidebar h2 { border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; margin: 5px 0; border-radius: 8px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); }

        /* Main Content */
        .main { margin-left: 250px; width: 100%; padding: 40px; }
        .card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; padding: 15px; background: #f8fafc; color: #64748b; text-transform: uppercase; font-size: 13px; }
        td { padding: 15px; border-bottom: 1px solid #f1f5f9; }
        
        .btn-view { background: var(--primary); color: white; padding: 8px 15px; border-radius: 6px; text-decoration: none; font-size: 13px; }
        .subject-count { background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 10px; font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>UNI-TUIT Admin</h2>
    <a href="admin.php"><i class="fas fa-users"></i> Student List</a>
    <a href="admin_subjects.php"><i class="fas fa-book"></i> Subject Reports</a> <!-- NEW TAB -->
    <a href="services.php"><i class="fas fa-tools"></i> Services</a>
    <a href="logout.php" style="margin-top: 50px;"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
    <h1>Student Registrations</h1>
    <p>Below is the list of unique students who have applied for modules.</p>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Email & Contact</th>
                    <th>Program</th>
                    <th>Subjects Chosen</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch unique users who have at least one registration
                $sql = "SELECT users.id, users.username, users.email, users.phone, users.program, 
                        COUNT(registrations.id) as total_mods 
                        FROM users 
                        JOIN registrations ON users.id = registrations.user_id 
                        GROUP BY users.id 
                        ORDER BY users.username ASC";
                
                $result = mysqli_query($conn, $sql);
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td><strong>{$row['username']}</strong></td>";
                    echo "<td>{$row['email']}<br><small>{$row['phone']}</small></td>";
                    echo "<td>{$row['program']}</td>";
                    echo "<td><span class='subject-count'>{$row['total_mods']} Modules</span></td>";
                    echo "<td><a href='admin_student_details.php?user_id={$row['id']}' class='btn-view'>View Submissions</a></td>";
                    echo "</tr>";
                }
                

                ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>