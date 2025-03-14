<?php
session_start();
require_once 'auth.php';

$error = "";
$success = "";

// Process password reset form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_POST['phone'];
    $new_password = $_POST['new-password'];
    $confirm_password = $_POST['confirm-password'];
    
    if ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        $result = resetPassword($phone, $new_password);
        
        if ($result["success"]) {
            // Redirect directly to login page with success message
            $_SESSION['reset_success'] = true;
            header("Location: login.php?reset=success");
            exit;
        } else {
            $error = $result["message"];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/bg.jpg') no-repeat center center/cover;
            background-attachment: fixed;
        }
        .reset-container { 
            max-width: 400px; 
            margin: 100px auto; 
            padding: 30px; 
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .reset-header {
            text-align: center;
            margin-bottom: 30px;
            color: #28a745;
        }
        .btn-reset {
            background-color: #28a745;
            border-color: #28a745;
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }
        .btn-reset:hover {
            background-color: #218838;
            border-color: #218838;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="reset-container">
            <h3 class="reset-header">Reset Password</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="new-password" class="form-label">New Password</label>
                    <input type="password" id="new-password" name="new-password" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="confirm-password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary btn-reset">Reset Password</button>
            </form>
            <div class="login-link">
                Remember your password? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>
</body>
</html>

