<?php
include 'db.php';

// ACCESS CONTROL
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login");
    exit();
}

include 'admin_ui.php';

// Define the core modules
$modules = ["Database System", "OS", "Networking", "Web Programming", "Calculus"];
$icons = ["Database System" => "fa-database", "OS" => "fa-microchip", "Networking" => "fa-network-wired", "Web Programming" => "fa-code", "Calculus" => "fa-square-root-variable"];

admin_page_start('Subject Reports', 'subjects', [
    'heading' => 'Subject Registration Statistics',
    'sub' => 'Monitor how many students are confirmed in each module.',
]);
?>
    <div class="subject-grid">
        <?php foreach ($modules as $m):
            // Count ONLY 'Registered' students for this module
            $stmt = mysqli_prepare($conn, "SELECT COUNT(*) FROM registrations WHERE module_name = ? AND status = 'Registered'");
            mysqli_stmt_bind_param($stmt, 's', $m);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $total);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        ?>
            <div class="subject-card">
                <div class="ico"><i class="fas <?php echo $icons[$m] ?? 'fa-graduation-cap'; ?>"></i></div>
                <h3><?php echo admin_h($m); ?></h3>
                <div class="num"><?php echo (int)$total; ?></div>
                <div class="lbl">Confirmed students</div>
                <a href="admin_subject_details?name=<?php echo urlencode($m); ?>" class="btn btn-gold">View class list <i class="fas fa-arrow-right"></i></a>
            </div>
        <?php endforeach; ?>
    </div>
<?php admin_page_end();
