<?php 
include 'db.php'; 
// Protect the page - only logged in users
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Modules | UNI-TUIT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; padding: 20px; }
        .container { max-width: 1000px; margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; }
       
        .card { 
            background: white; border-radius: 15px; overflow: hidden; 
            box-shadow: 0 10px 20px rgba(0,0,0,0.05); transition: 0.3s; 
            text-align: center; border: 1px solid #eee;
        }
        .card:hover { transform: translateY(-10px); box-shadow: 0 15px 30px rgba(0,0,0,0.1); }
        .card img { width: 100%; height: 160px; object-fit: cover; }
        .card-body { padding: 20px; }
        .card-body h3 { margin: 10px 0; color: #1e3a8a; }
        
        .btn-view { 
            display: inline-block; background: #1e3a8a; color: white; 
            padding: 10px 20px; text-decoration: none; border-radius: 5px; 
            font-weight: bold; margin-top: 10px;
        }
        .btn-back { text-decoration: none; color: #1e3a8a; font-weight: bold; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Explore Modules</h1>
        <a href="home.php" class="btn-back"><i class="fas fa-arrow-left"></i> Back to My List</a>
    </div>

    <div class="grid">
        <?php
        // Define our modules with images
        $modules = [
            ["name" => "Database System", "img" => "db.jpg"],
          
            ["name" => "Function of Single Varriable", "img" => "function.jpg"]
        ];

        foreach ($modules as $m) {
            echo "
            <div class='card'>
                <img src='{$m['img']}' alt='{$m['name']}'>
                <div class='card-body'>
                    <h3>{$m['name']}</h3>
                    <p style='color:#777; font-size:14px;'>Fee: 15,000 Tsh</p>
                    <a href='subject_view.php?name=".urlencode($m['name'])."' class='btn-view'>View Details</a>
                </div>
            </div>";
        }
        ?>
    </div>
</div>

</body>
</html>