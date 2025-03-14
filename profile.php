<?php
session_start();
require_once 'auth.php';
require_once 'user_data.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Get user data
$user = getUserData();

// Calculate BMI if height and weight are provided
$bmi = null;
if (isset($_POST['height']) && isset($_POST['weight'])) {
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $bmi = calculateBMI($weight, $height);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .profile-container { max-width: 600px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); }
        .profile-header { text-align: center; margin-bottom: 20px; color: #28a745; }
        .profile-img { width: 100px; height: 100px; border-radius: 50%; margin: 0 auto 20px; display: block; object-fit: cover; }
        .upload-btn { margin-top: 10px; }
        .btn-edit { background: #28a745; color: white; }
        .navbar { background: #28a745; padding: 15px; }
        .navbar a { color: white; text-decoration: none; font-size: 18px; margin-right: 20px; }
        .bmi-calculator { margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="dashboard.php" class="dashboard-link"> DASHBOARD</a>
            <div class="d-flex">
                <a href="reports.php" class="profile-icon">REPORTS</a>
                <a href="logout.php" class="logout">LOGOUT</a>
            </div>
        </div>
    </nav>
    
    <div class="container profile-container">
        <div class="profile-header">
            <h3>User Profile</h3>
            <img id="profileImage" src="uploads/default-profile.png" alt="Profile Image" class="profile-img">
            <form action="upload_profile_image.php" method="POST" enctype="multipart/form-data">
                <input type="file" id="uploadImage" name="profile_image" accept="image/*" class="form-control upload-btn">
                <button type="submit" class="btn btn-sm btn-success mt-2">Upload Image</button>
            </form>
        </div>
        
        <table class="table table-bordered">
            <tr>
                <th>Name</th>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
            </tr>
            <tr>
                <th>Email</th>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
            </tr>
            <tr>
                <th>Phone</th>
                <td><?php echo htmlspecialchars($user['phone']); ?></td>
            </tr>
            <tr>
                <th>Gender</th>
                <td><?php echo htmlspecialchars($user['gender']); ?></td>
            </tr>
            <tr>
                <th>Age</th>
                <td><?php echo htmlspecialchars($user['age']); ?></td>
            </tr>
            <tr>
                <th>Country</th>
                <td><?php echo htmlspecialchars($user['country']); ?></td>
            </tr>
            <tr>
                <th>State</th>
                <td><?php echo htmlspecialchars($user['state']); ?></td>
            </tr>
            <?php if ($bmi): ?>
            <tr>
                <th>BMI</th>
                <td><?php echo $bmi; ?> (<?php echo getBmiCategory($bmi); ?>)</td>
            </tr>
            <?php endif; ?>
        </table>
        
        <div class="text-center">
            <a href="edit_profile.php" class="btn btn-edit">Edit Profile</a>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        
        <div class="bmi-calculator">
            <h4 class="text-center">Calculate Your BMI</h4>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="height" class="form-label">Height (cm)</label>
                        <input type="number" id="height" name="height" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="weight" class="form-label">Weight (kg)</label>
                        <input type="number" id="weight" name="weight" class="form-control" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-success w-100">Calculate BMI</button>
            </form>
            <?php if ($bmi): ?>
                <div class="alert alert-info mt-3">
                    Your BMI: <?php echo $bmi; ?> - <?php echo getBmiCategory($bmi); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        document.getElementById("uploadImage").addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById("profileImage").src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>

