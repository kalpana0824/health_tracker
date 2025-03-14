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

// Process registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $height = isset($_POST['height']) ? $_POST['height'] : null;
    $weight = isset($_POST['weight']) ? $_POST['weight'] : null;
    $country = $_POST['country'];
    $state = $_POST['state'];
    $password = $_POST['password'];
    
    $result = registerUser($name, $email, $phone, $password, $gender, $age, $height, $weight, $country, $state);
    
    if ($result["success"]) {
        $success = $result["message"] . " You can now <a href='login.php'>login</a>.";
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
    <title>Register - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/bg.jpg') no-repeat center center/cover;
            background-attachment: fixed;
        }
        .register-container { 
            max-width: 500px; 
            margin: 50px auto; 
            padding: 30px; 
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
            color: #28a745;
        }
        .password-wrapper { 
            position: relative; 
        }
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        .btn-register {
            background-color: #28a745;
            border-color: #28a745;
            width: 100%;
            padding: 10px;
            font-weight: 500;
        }
        .btn-register:hover {
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
        <div class="register-container">
            <h3 class="register-header">Create an Account</h3>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <form id="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-control" required>
                        <option value="">Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="age" class="form-label">Age</label>
                    <input type="number" id="age" name="age" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="height" class="form-label">Height (cm) - Optional</label>
                    <input type="number" id="height" name="height" class="form-control" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="weight" class="form-label">Weight (kg) - Optional</label>
                    <input type="number" id="weight" name="weight" class="form-control" step="0.1">
                </div>
                <div class="mb-3">
                    <label for="country" class="form-label">Country</label>
                    <input type="text" id="country" name="country" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="state" class="form-label">State</label>
                    <input type="text" id="state" name="state" class="form-control" required>
                </div>
                <div class="mb-3 password-wrapper">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    <span class="toggle-password" onclick="togglePassword()">👁️</span>
                </div>
                <button type="submit" class="btn btn-primary btn-register">Register</button>
            </form>
            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }
    </script>
</body>
</html>

