<?php
session_start();
require_once 'auth.php';
require_once 'db_connect.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Get user data
$user = getUserData();
$message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $country = $_POST['country'];
    $state = $_POST['state'];
    
    $user_id = $_SESSION['user_id'];
    
    // Update user data in database
    $query = "UPDATE users SET name = ?, email = ?, phone = ?, gender = ?, age = ?, country = ?, state = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssissi", $name, $email, $phone, $gender, $age, $country, $state, $user_id);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Profile updated successfully!</div>";
        // Update session variables
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        
        // Refresh user data
        $user = getUserData();
    } else {
        $message = "<div class='alert alert-danger'>Failed to update profile: " . $conn->error . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; }
        .profile-container { max-width: 600px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1); }
        .profile-header { text-align: center; margin-bottom: 20px; color: #28a745; }
        .navbar { background: #28a745; padding: 15px; }
        .navbar a { color: white; text-decoration: none; font-size: 18px; margin-right: 20px; }
        .btn-update { background: #28a745; color: white; width: 100%; }
        .btn-update:hover { background: #218838; color: white; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="dashboard.php" class="dashboard-link">🏠 Dashboard</a>
            <div class="d-flex">
                <a href="profile.php" class="profile-icon">👤 Profile</a>
                <a href="logout.php" class="logout">🚪 Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container profile-container">
        <div class="profile-header">
            <h3>Edit Profile</h3>
        </div>
        
        <?php echo $message; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" name="gender" class="form-control" required>
                    <option value="male" <?php echo ($user['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                    <option value="female" <?php echo ($user['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                    <option value="other" <?php echo ($user['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="number" id="age" name="age" class="form-control" value="<?php echo htmlspecialchars($user['age']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="country" class="form-label">Country</label>
                <input type="text" id="country" name="country" class="form-control" value="<?php echo htmlspecialchars($user['country']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="state" class="form-label">State</label>
                <input type="text" id="state" name="state" class="form-control" value="<?php echo htmlspecialchars($user['state']); ?>" required>
            </div>
            <button type="submit" class="btn btn-update">Update Profile</button>
        </form>
        
        <div class="text-center mt-3">
            <a href="profile.php" class="btn btn-secondary">Cancel</a>
        </div>
    </div>
</body>
</html>

