<?php 
include 'db.php'; 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$subject_name = $_GET['name'];

// Query to get details of all students registered for THIS subject
$query = "SELECT users.username, users.email, users.phone, users.program 
          FROM registrations 
          JOIN users ON registrations.user_id = users.id 
          WHERE registrations.module_name = '$subject_name' AND registrations.status = 'Registered'
          ORDER BY users.username ASC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class List: <?php echo $subject_name; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f1f5f9; padding: 40px; margin: 0; }
        .container { max-width: 900px; margin: auto; }
        .header { background: #1e3a8a; color: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; position: relative; }
        .back-btn { color: white; text-decoration: none; font-size: 14px; position: absolute; top: 10px; left: 20px; opacity: 0.8; }
        
        .card { background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 15px; text-align: left; color: #64748b; font-size: 13px; text-transform: uppercase; }
        td { padding: 15px; border-top: 1px solid #f1f5f9; font-size: 14px; }
        tr:hover { background: #fdfdfd; }
        
        .empty-state { padding: 40px; text-align: center; color: #94a3b8; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <a href="admin_subjects.php" class="back-btn"><i class="fas fa-arrow-left"></i> Back to Subjects</a>
        <h1 style="margin: 10px 0 0 0;"><?php echo $subject_name; ?></h1>
        <p style="margin: 5px 0 0 0; opacity: 0.9;">Official Class List (Confirmed Students)</p>
    </div>

    <div class="card">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Program</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><strong><?php echo $row['username']; ?></strong></td>
                            <td><?php echo $row['email']; ?></td>
                            <td><?php echo $row['phone']; ?></td>
                            <td><?php echo $row['program']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users-slash" style="font-size: 50px; margin-bottom: 15px;"></i>
                <p>No students have been officially registered for this module yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>