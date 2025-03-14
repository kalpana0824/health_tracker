<?php
session_start();
require_once 'auth.php';

// Check if user is already logged in
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.php");
    exit;
}

$error = "";
$success = "";

// Check for password reset success
if (isset($_GET['reset']) && $_GET['reset'] === 'success') {
    $success = "Your password has been reset successfully. You can now login with your new password.";
}

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    
    $result = loginUser($phone, $password);
    
    if ($result["success"]) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = $result["message"];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/bg.jpg') no-repeat center center/cover;
            background-attachment: fixed;
        }
        .login-container { 
            max-width: 400px; 
            margin: 100px auto; 
            padding: 30px; 
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
            color: #28a745;
        }
        .forgot-password { 
            font-size: 0.9em; 
            color: #6c757d;
            transition: color 0.3s;
        }
        .forgot-password:hover {
            color: #28a745;
        }
        .btn-login {
            background-color: #28a745;
            border-color: #28a745;
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }
        .btn-login:hover {
            background-color: #218838;
            border-color: #218838;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h3 class="login-header">Login to Health Tracker</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <div class="mb-3 text-end">
                    <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
                </div>
                <button type="submit" class="btn btn-primary btn-login">Login</button>
            </form>
            <div class="register-link">
                Don't have an account? <a href="register.php">Register here</a>
            </div>
        </div>
    </div>
</body>
</html>

