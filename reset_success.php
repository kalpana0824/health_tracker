<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset Successful - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        .success-container { 
            max-width: 400px; 
            margin: 100px auto; 
            padding: 30px; 
            background-color: white;
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        .success-icon {
            font-size: 60px;
            color: #28a745;
            margin-bottom: 20px;
        }
        .btn-login {
            background-color: #28a745;
            border-color: #28a745;
            padding: 10px 30px;
            font-weight: 500;
            margin-top: 20px;
        }
        .btn-login:hover {
            background-color: #218838;
            border-color: #218838;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-container">
            <div class="success-icon">✓</div>
            <h3>Password Reset Successful</h3>
            <p>Your password has been successfully reset. You can now log in with your new password.</p>
            <a href="login.php" class="btn btn-primary btn-login">Go to Login</a>
        </div>
    </div>
</body>
</html>

