<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>UNI-TUIT | Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>








/* --- Re-balanced Hero Styling --- */


.hero-content {
    max-width: 800px;
    text-align: center;
}



/* CTA Button */
.hero-btn {
    display: inline-block;
    padding: 14px 40px;
    background: var(--accent);
    color: white;
    text-decoration: none;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    text-transform: uppercase;
    font-size: 14px;
    letter-spacing: 1px;
}

.hero-btn:hover {
    background: #e68a00;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(245, 158, 11, 0.4);
}

/* Subtle Floating Animation */
@keyframes floating {
    0% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
    100% { transform: translateY(0px); }
}

/* Mobile Adjustments */
@media (max-width: 768px) {
    .hero-title { font-size: 1.8rem; }
    .hero-visual img { width: 140px; }
    .hero-subtitle { font-size: 1rem; }
}







        :root { --primary: #1e3a8a; --accent: #f59e0b; }
        body { font-family: 'Poppins', sans-serif; margin: 0; background: #f4f7f6; }
        
        /* Header & Menu */
        header { background: white; padding: 15px 5%; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000; }
        .logo { font-size: 24px; font-weight: bold; color: var(--primary); }
        .menu-btn { background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; }
        
        #navMenu { display: none; position: absolute; top: 70px; right: 5%; background: white; box-shadow: 0 5px 15px rgba(0,0,0,0.2); border-radius: 8px; width: 200px; text-align: center; }
        #navMenu a { display: block; padding: 15px; color: var(--primary); text-decoration: none; border-bottom: 1px solid #eee; }

        /* Hero & About */
        .hero { height: 60vh; background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('image1.jgp'); background-size: cover; display: flex; align-items: center; justify-content: center; color: white; text-align: center; }
        .about { padding: 50px 10%; text-align: center; background: white; }

        /* Modules */
        .module-list { padding: 50px 10%; display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); text-align: center; cursor: pointer; }
        .card img { width: 100%; height: 150px; object-fit: cover; }
        .card h3 { padding: 15px; margin: 0; }

        /* Modals & Forms */
        .modal { display: none; position: fixed; z-index: 2000; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 30px; border-radius: 10px; width: 400px; position: relative; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn-submit { width: 100%; background: var(--primary); color: white; border: none; padding: 12px; cursor: pointer; border-radius: 5px; }









        /* --- Advanced Split Hero Design --- */
.hero {
    min-height: 90vh;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.9) 0%, rgba(30, 58, 138, 0.8) 100%), 
                url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=1500&q=80');
    background-size: cover;
    background-position: center;
    display: flex;
    align-items: center;
    padding: 80px 10%;
    overflow: hidden;
}

.hero-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    gap: 50px;
}

/* --- Text Styling (Left Side) --- */
.hero-text {
    flex: 1;
    text-align: left;
    z-index: 2;
}

.hero-title {
    font-size: 3.2rem;
    color: #ffffff;
    font-weight: 800;
    line-height: 1.1;
    margin-bottom: 20px;
}

.hero-title span {
    color: var(--accent); /* Highlight the "University Success" */
}

.hero-subtitle {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 35px;
    max-width: 500px;
}

/* --- Image Styling (Right Side) --- */
.hero-visual {
    flex: 1;
    display: flex;
    justify-content: center;
    position: relative;
    z-index: 1;
}

.hero-visual img {
    width: 100%;
    max-width: 550px; /* Your desired large size */
    height: auto;
    filter: drop-shadow(0 20px 50px rgba(0,0,0,0.5));
    animation: floating 4s ease-in-out infinite;
}

/* The glow behind the image for attraction */
.image-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 300px;
    height: 300px;
    background: var(--accent);
    filter: blur(150px);
    opacity: 0.2;
    z-index: -1;
}

/* --- Buttons --- */
.hero-actions { display: flex; gap: 15px; }

.hero-btn {
    padding: 15px 35px;
    background: var(--accent);
    color: white;
    text-decoration: none;
    font-weight: 700;
    border-radius: 8px;
    transition: 0.3s;
}

.hero-btn-outline {
    padding: 15px 35px;
    border: 2px solid white;
    color: white;
    text-decoration: none;
    font-weight: 700;
    border-radius: 8px;
    transition: 0.3s;
}

.hero-btn-outline:hover { background: white; color: var(--primary); }

@keyframes floating {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(2deg); }
}

/* --- MOBILE RESPONSIVENESS (The critical part) --- */
@media (max-width: 992px) {
    .hero { padding: 120px 5% 50px 5%; text-align: center; height: auto; }
    
    .hero-container {
        flex-direction: column-reverse; /* Put text on top of image on mobile */
        gap: 40px;
    }

    .hero-text { text-align: center; }
    .hero-title { font-size: 2.2rem; }
    .hero-subtitle { margin: 0 auto 30px auto; }
    .hero-actions { justify-content: center; }

    .hero-visual img {
        max-width: 300px; /* Shrink the 600px image so it fits the phone screen */
    }
}
    </style>
</head>
<body>

<header>
    <div class="logo">UNI-TUIT</div>
    <button class="menu-btn" onclick="toggleMenu()">MENU <i class="fas fa-bars"></i></button>
    <div id="navMenu">
    <a href="index.php">Home</a>
    <a href="#" onclick="openModal('loginModal')">Register / Login</a>
    <a href="#about">About Us</a>
        <a href="services.php" style="color: var(--accent); font-weight: bold;">Other Services</a> <!-- NEW -->

    <a href="#contact">Contact Us</a>
</div>
</header>















<section class="hero">
    <div class="hero-container">
        <!-- Text Side -->
        <div class="hero-text">
            <h1 class="hero-title">Quality Tuition for <br><span>University Success</span></h1>
            <p class="hero-subtitle">Master your modules with the best tutors in the country. We guarantee quality and academic excellence.</p>
            <div class="hero-actions">
                <a href="#modules" class="hero-btn">Explore Modules</a>
                <a href="services.php" class="hero-btn-outline">Our Services</a>
            </div>
        </div>

        <!-- Large Image Side -->
        <div class="hero-visual">
            <img src="lecturer.png" alt="UNI-TUIT Excellence">
            <div class="image-glow"></div> <!-- Adds attraction -->
        </div>
    </div>
</section>












<h2 style="text-align:center;">Module List</h2>
<section class="module-list">
    <?php 
    $mods = ["Database System", "Function of Single Varriable"];
       $img = ["db.jpg", "os.jpg", "network.jpg", "web.jpg", "function.jpg"];
    foreach($mods as $key => $m) {
        $i=$img[$key];
        echo "<div class='card' onclick=\"alert('Please Login or Register first to continue!'); openModal('loginModal')\">
                <img src='$i'>
                <h3>$m</h3>
              </div>";
    }
    ?>
</section>

<!-- LOGIN MODAL -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span style="float:right; cursor:pointer;" onclick="closeModal('loginModal')">&times;</span>
        <h2 style="color:var(--primary)">Login</h2>
        <form action="auth.php" method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login" class="btn-submit">Login</button>
        </form>
        <p style="text-align:center; margin-top:15px;">New here? <span style="color:var(--accent); cursor:pointer; font-weight:bold;" onclick="closeModal('loginModal'); openModal('regModal')">Register Now</span></p>
    </div>
</div>

<!-- REGISTER MODAL -->
<div id="regModal" class="modal">
    <div class="modal-content">
        <span style="float:right; cursor:pointer;" onclick="closeModal('regModal')">&times;</span>
        <h2 style="color:var(--primary)">Create Account</h2>
        <form action="auth.php" method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="phone" placeholder="Phone (e.g. 07XXXXXXXX)" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="program" placeholder="Program Name" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register" class="btn-submit">Register</button>
        </form>
    </div>
</div>







<!-- Other Services Preview Section -->
<section style="padding: 60px 10%; background: #1e3a8a; color: white; text-align: center;">
    <h2 style="font-size: 2.5rem; margin-bottom: 10px;">Beyond Tuition...</h2>
    <p style="margin-bottom: 30px; opacity: 0.9;">Discover professional tech solutions provided by our parent company.</p>
    <a href="services.php" style="background: #f59e0b; color: white; padding: 15px 35px; border-radius: 30px; text-decoration: none; font-weight: bold; display: inline-block; transition: 0.3s;">
        Explore Our Other Services <i class="fas fa-arrow-right"></i>
    </a>
</section>












<footer id="contact" style="background:#222; color:white; padding:40px; text-align:center; margin-top:50px;">
    <p>Email: fransiscofrednand24@gmail.com | WhatsApp: +255 760 987 261</p>
    <p>&copy; 2026 UNI-TUIT System</p>
</footer>

<script>
    function toggleMenu() { 
        var m = document.getElementById('navMenu');
        m.style.display = (m.style.display === 'block') ? 'none' : 'block';
    }
    function openModal(id) { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }
</script>
</body>
</html>