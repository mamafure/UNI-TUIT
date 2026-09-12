

<?php 
include 'db.php'; 
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$u_id = $_SESSION['user_id'];

// 1. Define all available subjects in your system
$all_system_modules = ["Database System", "OS", "Networking", "Web Programming", "Calculus"];
$max_modules = count($all_system_modules);

// 2. Fetch the user's current selections
$query = "SELECT * FROM registrations WHERE user_id = '$u_id'";
$result = mysqli_query($conn, $query);

$selected_subjects = [];
$total_fee = 0;
$draft_count = 0;
$pending_count = 0;
$registered_count = 0;
$user_total_count = 0;

while($row = mysqli_fetch_assoc($result)) {
    $selected_subjects[] = $row;
    $total_fee += $row['fee'];
    $user_total_count++;
    
    if($row['status'] == 'Draft') $draft_count++;
    if($row['status'] == 'Pending') $pending_count++;
    if($row['status'] == 'Registered') $registered_count++;
}

$btn_main_text = ($user_total_count > 0) ? "+ Add Another Subject" : "Select Your Subjects";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Learning Dashboard | UNI-TUIT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #1e3a8a; --accent: #f59e0b; --success: #10b981; }
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        
        .header-card { background: var(--primary); color: white; padding: 30px; border-radius: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .header-card h1 { margin: 0; font-size: 24px; }
        
        .summary-card { background: white; padding: 30px; border-radius: 20px; margin-top: 30px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .subject-item { display: flex; justify-content: space-between; padding: 15px 0; border-bottom: 1px solid #eee; }
        
        /* Status Tags */
        .badge { padding: 5px 12px; border-radius: 15px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .badge-draft { background: #e2e8f0; color: #475569; }
        .badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #f59e0b; }
        .badge-registered { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }

        /* Buttons */
        .btn-submit { width: 100%; background: var(--accent); color: white; border: none; padding: 18px; border-radius: 12px; font-size: 18px; font-weight: bold; cursor: pointer; margin-top: 20px; transition: 0.3s; }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4); }
        
        .btn-browse { display: block; text-align: center; margin-top: 25px; color: var(--primary); font-weight: 600; text-decoration: none; font-size: 15px; }
        .btn-browse:hover { text-decoration: underline; }

        /* Celebratory Message Box */
        .congrats-box { background: #ecfdf5; border: 2px solid #10b981; padding: 25px; border-radius: 15px; text-align: center; margin-top: 20px; animation: fadeIn 1s ease; }
        .congrats-box i { font-size: 40px; color: #10b981; margin-bottom: 10px; }
        .congrats-box h2 { color: #065f46; margin: 0; }
        .congrats-box p { color: #065f46; margin: 5px 0 0 0; opacity: 0.8; }

        /* Waiting Message Box */
        .waiting-box { text-align: center; padding: 20px; color: #92400e; background: #fffbeb; border-radius: 15px; border: 1px dashed #f59e0b; margin-top: 20px; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="container">
    <div class="header-card">
        <div>
            <p style="opacity: 0.8; margin: 0;">Student Portal</p>
            <h1>Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        </div>
        <a href="logout.php" style="color: white; font-size: 20px;"><i class="fas fa-sign-out-alt">log out</i></a>
    </div>

    <div class="summary-card">
        <h3><i class="fas fa-list-check"></i> My Tuition Modules</h3>
        
        <?php if($user_total_count == 0): ?>
            <div style="text-align: center; padding: 40px 0;">
                <i class="fas fa-folder-open" style="font-size: 50px; color: #cbd5e1; margin-bottom: 15px;"></i>
                <p style="color: #64748b;">Your registration list is currently empty.</p>
            </div>
        <?php else: ?>
            <?php foreach($selected_subjects as $sub): ?>
                <div class="subject-item">
                    <div>
                        <strong style="font-size: 16px;"><?php echo $sub['module_name']; ?></strong>
                        <p style="margin: 0; font-size: 12px; color: #94a3b8;">Price: 5,000 Tsh</p>
                    </div>
                    <span class="badge badge-<?php echo strtolower($sub['status']); ?>">
                        <?php echo $sub['status']; ?>
                    </span>
                </div>
            <?php endforeach; ?>

            <div style="text-align: right; font-size: 20px; font-weight: bold; padding: 20px 0; color: var(--primary);">
                Total: <?php echo number_format($total_fee); ?> Tsh
            </div>
        <?php endif; ?>

        <!-- Logic for Messages and Buttons -->
          <?php 
        if ($draft_count > 0) {
            echo '<button class="btn-submit" onclick="confirmSubmission()">SUBMIT FOR REGISTRATION</button>';
        } 
        elseif ($pending_count > 0 && $registered_count < $user_total_count) {
            echo '<div class="waiting-box">  All your selections have been submitted. Please wait for the Admin to confirm your registration.</div>';
        } 
        elseif ($registered_count > 0 && $registered_count == $user_total_count) {
            echo '<div class="congrats-box">
                    <i class="fas fa-award"></i>
                    <h2>Congratulations!</h2>
                    <p>You are now a verified student of <strong>UNI-TUIT</strong>. Excellence awaits you!</p>
                  </div>';
        }
        ?>

        <!-- SMART BUTTON LOGIC: Hide button if user has all subjects -->
        <?php if ($user_total_count < 2): ?>
            <a href="subject_list.php" class="btn-browse">
                <i class="fas fa-plus"></i> <?php echo $btn_main_text; ?>
            </a>
        <?php else: ?>
            <div style="text-align: center; margin-top: 30px; padding: 15px; border-top: 1px solid #eee; color: #94a3b8;">
                <i class="fas fa-check-double"></i> 
                <span style="font-size: 13px;">You have explored all available modules in our current catalog.</span>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmSubmission() {
    let count = <?php echo $draft_count; ?>;
    let total = "<?php echo number_format($total_fee); ?>";
    
    let msg = `🌟 Brilliant Choices, <?php echo $_SESSION['username']; ?>!\n\nYou have ${count} module(s) ready for submission.\nTotal Tuition Investment: ${total} Tsh.\n\nWould you like to send these to the Admin for final approval?`;
    
    if (confirm(msg)) {
        window.location.href = "process_submission.php";
    }
}
</script>

</body>
</html>