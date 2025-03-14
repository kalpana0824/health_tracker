<?php
session_start();
require_once 'admin_auth.php';

// Check if admin is already logged in
if (isAdminLoggedIn()) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = "";

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    if (loginAdmin($username, $password)) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            background-image: url('images/bg.jpg'); //no-repeat center center/cover;
            background-repeat: no-repeat; /* Prevents image repetition */
           background-size: 100% 116%; /* Stretch to fit */
        }
        .login-container { 
            max-width: 400px; 
            margin: 100px auto; 
            padding: 30px; 
            background-color: white;
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
            color: #343a40;
        }
        .btn-login {
            background-color: #343a40;
            border-color: #343a40;
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }
        .btn-login:hover {
            background-color: #23272b;
            border-color: #23272b;
        }
        .home-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h3 class="login-header">Admin Login</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" id="username" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-login">Login</button>
            </form>
            <div class="home-link">
                <a href="index.html">Back to Home</a>
            </div>
        </div>
    </div>
</body>
</html>

