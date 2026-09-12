<?php 
include 'db.php'; 

// Check if user is logged in
if(!isset($_SESSION['user_id'])) { 
    header("Location: index.php"); 
    exit();
}

$m_name = $_GET['name'];
$u_id = $_SESSION['user_id'];

// 1. DATA ARRAY: Information for each specific subject
$subject_data = [
    "Database System" => [
        "intro" => "Master the art of data management. In today's world, data is the new oil, and knowing how to store, retrieve, and secure it is a vital skill for any IT professional.",
        "topics" => ["Entity Relationship Diagrams (ERD)", "Advanced SQL Queries", "Database Normalization (1NF, 2NF, 3NF)", "Transaction Management & Concurrency Control"],
        "icon" => "fa-database",
        "color" => "#2563eb",
       "watermark" => "db.jpg" // Database icon

    ],
    "OS" => [
        "intro" => "Go behind the scenes of computing. Learn how Operating Systems manage hardware resources and provide a platform for application software to run smoothly.",
        "topics" => ["Process Scheduling & Threads", "Memory Management & Virtual Memory", "Deadlock Detection and Prevention", "File Systems and Disk Management"],
        "icon" => "fa-microchip",
        "color" => "#dc2626",
        "watermark" => "os.jpg" 
    ],
    "Networking" => [
        "intro" => "The world is connected! Understand the protocols and technologies that allow computers to communicate across the globe, from local cables to the vast internet.",
        "topics" => ["OSI and TCP/IP Models", "IP Addressing and Subnetting", "Routing and Switching Protocols", "Network Security and Firewalls"],
        "icon" => "fa-network-wired",
        "color" => "#059669",
        "watermark" => "network.jpg" 
    ],
    "Web Programming" => [
        "intro" => "Build the modern web. From beautiful user interfaces to powerful server-side logic, this module prepares you to create full-stack web applications.",
        "topics" => ["Responsive Design with HTML5 & CSS3", "JavaScript & DOM Manipulation", "PHP Backend & MySQL Integration", "Web Security Best Practices"],
        "icon" => "fa-code",
        "color" => "#7c3aed",
        "watermark" => "web.jpg" 
    ],
    "Function of Single Varriable" => [
        "intro" => "The mathematics of change. Calculus is essential for engineering, physics, and computer science. We make complex derivatives and integrals easy to understand.",
        "topics" => ["Limits and Continuity", "Rules of Differentiation", "Applications of Integrals", "Infinite Sequences and Series"],
        "icon" => "fa-square-root-variable",
        "color" => "#ea580c",
        "watermark" => "function.jpg" 
    ]
];

// Fallback if the subject name doesn't exist in our array
if (!array_key_exists($m_name, $subject_data)) {
    die("Subject not found.");
}

$info = $subject_data[$m_name];

// 2. HANDLE SUBMISSION
if (isset($_POST['add'])) {
    // Check if user already registered for this specific module
    $check = mysqli_query($conn, "SELECT * FROM registrations WHERE user_id='$u_id' AND module_name='$m_name'");
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('You have already added this subject to your list!'); window.location='home.php';</script>";
    } else {
        mysqli_query($conn, "INSERT INTO registrations (user_id, module_name,fee,status) VALUES ('$u_id', '$m_name',5000,'Draft')");
        echo "<script>alert('Successful! $m_name has been added to your tuition list.'); window.location='home.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $m_name; ?> | UNI-TUIT Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>

        :root { --subject-color: <?php echo $info['color']; ?>; }
        body { font-family: 'Poppins', sans-serif; background: #f8fafc; margin: 0; }


 /* NEW: The Watermark Style */
        body::before {
            content: "";
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            background-image: url('<?php echo $info['watermark']; ?>');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            opacity: 0.04; /* Very subtle so it doesn't disturb text */
            z-index: -1; /* Keeps it behind everything */
            pointer-events: none;
            filter: grayscale(100%);
        }








        .nav-back { padding: 20px; background: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .nav-back a { text-decoration: none; color: #555; font-weight: bold; }

        .container { max-width: 800px; margin: 40px auto; background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        
        /* Subject Header */
        .subject-header { background: var(--subject-color); color: white; padding: 50px 30px; text-align: center; }
        .subject-header i { font-size: 60px; margin-bottom: 20px; }
        .subject-header h1 { margin: 0; font-size: 36px; text-transform: uppercase; letter-spacing: 2px; }

        .content { padding: 40px; }
        .intro-text { font-size: 18px; line-height: 1.6; color: #444; margin-bottom: 30px; border-left: 5px solid var(--subject-color); padding-left: 20px; }
        
        .learning-box { background: #f9fafb; padding: 25px; border-radius: 15px; margin-bottom: 30px; }
        .learning-box h3 { margin-top: 0; color: #333; }
        .learning-box ul { list-style: none; padding: 0; }
        .learning-box li { padding: 10px 0; border-bottom: 1px solid #eee; display: flex; align-items: center; }
        .learning-box li i { color: var(--subject-color); margin-right: 15px; }

        .fee-section { text-align: center; padding: 20px; border: 2px dashed var(--subject-color); border-radius: 15px; margin-bottom: 30px; }
        .fee-amount { font-size: 28px; font-weight: bold; color: var(--subject-color); }

        .btn-add { 
            width: 100%; background: var(--subject-color); color: white; border: none; 
            padding: 20px; font-size: 20px; font-weight: bold; cursor: pointer; 
            border-radius: 10px; transition: 0.3s;
        }
        .btn-add:hover { filter: brightness(1.2); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.2); }
    </style>
</head>
<body>

<div class="nav-back">
    <div class="container" style="box-shadow:none; margin:0 auto; max-width:900px;">
        <a href="home.php"><i class="fas fa-arrow-left"></i> Back to Module List</a>
    </div>
</div>

<div class="container">
    <!-- Visual Header -->
    <div class="subject-header">
        <i class="fas <?php echo $info['icon']; ?>"></i>
        <h1><?php echo $m_name; ?></h1>
    </div>

    <div class="content">
        <!-- General View -->
        <h3>General Overview</h3>
        <p class="intro-text"><?php echo $info['intro']; ?></p>

        <!-- What will be learnt -->
        <div class="learning-box">
            <h3>What you will learn:</h3>
            <ul>
                <?php foreach($info['topics'] as $topic): ?>
                    <li><i class="fas fa-check-circle"></i> <?php echo $topic; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Pricing -->
        <div class="fee-section">
            <span>Tuition Registration Fee</span>
            <div class="fee-amount">5,000 Tsh</div>
            <p style="font-size: 12px; color: #777;">Payable via M-Pesa / TigoPesa after confirmation</p>
        </div>

        <!-- Submission -->
        <form method="POST">
            <button type="submit" name="add" class="btn-add">
                SELECT THIS SUBJECT <i class="fas fa-plus-circle"></i>
            </button>
        </form>
    </div>
</div>

<footer style="text-align: center; padding: 20px; color: #888; font-size: 14px;">
    UNI-TUIT Platform &copy; 2026 - Quality University Tuition
</footer>

</body>
</html>