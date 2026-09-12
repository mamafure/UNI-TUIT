<?php 
include 'db.php'; 

// ACCESS CONTROL
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Define the core modules
$modules = ["Database System", "OS", "Networking", "Web Programming", "Calculus"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Subject Reports | Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary: #1e3a8a; --bg: #f1f5f9; }
        body { font-family: 'Poppins', sans-serif; background: var(--bg); margin: 0; display: flex; }
        
        .sidebar { width: 250px; background: var(--primary); height: 100vh; color: white; padding: 20px; position: fixed; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 12px; margin: 5px 0; border-radius: 8px; }
        .sidebar a:hover { background: rgba(255,255,255,0.1); }

        .main { margin-left: 250px; width: 100%; padding: 40px; }
        
        .subject-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .subject-card { 
            background: white; padding: 30px; border-radius: 15px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); text-align: center;
            border-top: 5px solid var(--primary);
        }
        .subject-card i { font-size: 40px; color: #cbd5e1; margin-bottom: 15px; }
        .subject-card h3 { margin: 10px 0; color: #1e293b; }
        
        .count-box { font-size: 32px; font-weight: bold; color: #1e3a8a; margin: 10px 0; }
        .count-label { color: #64748b; font-size: 14px; margin-bottom: 20px; }
        
        .btn-details { 
            background: #f59e0b; color: white; text-decoration: none; 
            padding: 10px 20px; border-radius: 8px; font-weight: bold; font-size: 13px;
            display: inline-block; transition: 0.3s;
        }
        .btn-details:hover { background: #d97706; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>UNI-TUIT Admin</h2>
    <a href="admin.php"><i class="fas fa-users"></i> Student List</a>
    <a href="admin_subjects.php" style="background:rgba(255,255,255,0.1)"><i class="fas fa-book"></i> Subject Reports</a>
    <a href="services.php"><i class="fas fa-tools"></i> Services</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<div class="main">
    <h1>Subject Registration Statistics</h1>
    <p>Monitor how many students are enrolled in each module.</p>

    <div class="subject-grid">
        <?php foreach($modules as $m): 
            // Query to count ONLY 'Registered' students for this module
            $count_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM registrations WHERE module_name = '$m' AND status = 'Registered'");
            $data = mysqli_fetch_assoc($count_query);
            $total = $data['total'];
        ?>
            <div class="subject-card">
                <i class="fas fa-graduation-cap"></i>
                <h3><?php echo $m; ?></h3>
                <div class="count-box"><?php echo $total; ?></div>
                <div class="count-label">Confirmed Students</div>
                <a href="admin_subject_details.php?name=<?php echo urlencode($m); ?>" class="btn-details">
                    View Student List
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>