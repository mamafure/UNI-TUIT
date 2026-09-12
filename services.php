<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professional Services | UNI-TUIT Group</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --accent: #f59e0b;
            --dark: #0f172a;
            --light: #f8fafc;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
        body { background-color: var(--light); color: var(--dark); line-height: 1.6; }

        /* Hero Section */
        .service-hero {
            height: 50vh;
            background: linear-gradient(rgba(30, 58, 138, 0.85), rgba(30, 58, 138, 0.85)), 
                        url('https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&w=1350&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 0 20px;
        }
        .service-hero h1 { font-size: 3rem; margin-bottom: 10px; }
        .service-hero p { font-size: 1.2rem; max-width: 700px; margin: 0 auto; opacity: 0.9; }

        .container { max-width: 1100px; margin: -50px auto 50px; padding: 0 20px; }

        /* Service Cards */
        .service-card {
            background: white;
            border-radius: 20px;
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 40px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }
        .service-card:hover { transform: translateY(-5px); }

        .service-img { flex: 1; min-width: 300px; min-height: 350px; background-size: cover; background-position: center; }
        
        .service-content { flex: 1.2; padding: 50px; min-width: 300px; }
        .service-content h2 { color: var(--primary); font-size: 2rem; margin-bottom: 20px; position: relative; }
        .service-content h2::after { content: ''; display: block; width: 50px; height: 4px; background: var(--accent); margin-top: 10px; }
        
        .service-content p { color: #475569; font-size: 1.05rem; margin-bottom: 20px; }
        
        .feature-list { list-style: none; margin-bottom: 30px; }
        .feature-list li { margin-bottom: 10px; display: flex; align-items: center; color: #1e293b; font-weight: 500; }
        .feature-list li i { color: var(--accent); margin-right: 15px; font-size: 1.2rem; }

        .btn-inquire {
            background: var(--primary);
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
            transition: 0.3s;
        }
        .btn-inquire:hover { background: var(--accent); }

        /* Footer Copy */
        .footer-cta { text-align: center; padding: 60px 20px; background: white; border-top: 1px solid #e2e8f0; }
        .footer-cta h3 { color: var(--primary); margin-bottom: 15px; }

        @media (max-width: 768px) {
            .service-content { padding: 30px; }
            .service-hero h1 { font-size: 2rem; }
        }
    </style>
</head>
<body>

    <!-- Hero -->
    <section class="service-hero">
        <div>
            <h1>Our Expert Services</h1>
            <p>Empowering your business and personal growth with cutting-edge technology solutions and professional training.</p>
        </div>
    </section>

    <div class="container">
        <!-- Service 1: Computer Maintenance -->
        <div class="service-card">
            <div class="service-img" style="background-image: url('t_shooter.jpg');"></div>
            <div class="service-content">
                <h2>Computer Maintenance & Repair</h2>
                <p>Is your hardware slowing you down? Our certified technicians provide top-tier diagnostic and repair services to keep your productivity at its peak.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i> Advanced Hardware Troubleshooting</li>
                    <li><i class="fas fa-check-circle"></i> Black & Blue screen problem</li>
                    <li><i class="fas fa-check-circle"></i> OS installation</li>
                    <li><i class="fas fa-check-circle"></i> applications & other software problems</li>
                </ul>
            </div>
        </div>

        <!-- Service 2: Microsoft Training -->
        <div class="service-card" style="flex-direction: row-reverse;">
            <div class="service-img" style="background-image: url('office.jpg');"></div>
            <div class="service-content">
                <h2>Microsoft Office Training</h2>
                <p>Become a workplace pro. We offer hands-on training for Microsoft tools that are essential for every student and professional in the modern world.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i> Advanced Excel (Data Analysis & Macros)</li>
                    <li><i class="fas fa-check-circle"></i> Professional Word & Report Writing</li>
                    <li><i class="fas fa-check-circle"></i> Impactful PowerPoint Presentations</li>
                    <li><i class="fas fa-check-circle"></i> Access for best database management</li>
                </ul>
            </div>
        </div>

        <!-- Service 3: Web & System Creation -->
        <div class="service-card">
            <div class="service-img" style="background-image: url('website.jpg');"></div>
            <div class="service-content">
                <h2>Website & System Creation</h2>
                <p>Take your business or idea online. We design and develop custom websites and management systems tailored to your specific needs.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-check-circle"></i> Custom School & Hospital Systems</li>
                    <li><i class="fas fa-check-circle"></i> Professional E-commerce Websites</li>
                    <li><i class="fas fa-check-circle"></i> Mobile-Responsive Design (UI/UX)</li>
                    <li><i class="fas fa-check-circle"></i> Full Deployment & Maintenance</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Final CTA -->
    <section class="footer-cta">
        <h3>Need a Custom Solution?</h3>
        <p>Our team is ready to help you navigate your technical challenges.</p>
        <br>
        <a href="index.php" style="text-decoration: none; color: var(--primary); font-weight: bold;"><i class="fas fa-arrow-left"></i> Back to UNI-TUIT Home</a>
    </section>

</body>
</html>