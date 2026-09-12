<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login | UNI-TUIT</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #0f172a; /* Dark professional blue */
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }
        .login-box i {
            font-size: 50px;
            color: #1e3a8a;
            margin-bottom: 20px;
        }
        .login-box h2 {
            margin-bottom: 30px;
            color: #1e3a8a;
            font-weight: 600;
        }
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #64748b;
            font-size: 14px;
        }
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-sizing: border-box;
            outline: none;
        }
        .form-group input:focus {
            border-color: #1e3a8a;
        }
        .btn-login {
            width: 100%;
            background: #1e3a8a;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-login:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }
        .footer-link {
            display: block;
            margin-top: 20px;
            color: #64748b;
            text-decoration: none;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="login-box">
    <i class="fas fa-user-shield"></i>
    <h2>Admin Portal</h2>
    
    <form action="admin_auth.php" method="POST">
        <div class="form-group">
            <label>Administrator Email</label>
            <input type="email" name="email" placeholder="email@unituit.com" required>
        </div>
        <div class="form-group">
            <label>Secret Password</label>
            <input type="password" name="password" placeholder="••••••••" required>
        </div>
        <button type="submit" name="admin_login" class="btn-login">Secure Login</button>
    </form>

    <a href="index.php" class="footer-link"><i class="fas fa-arrow-left"></i> Back to Main Website</a>
</div>

</body>
</html>