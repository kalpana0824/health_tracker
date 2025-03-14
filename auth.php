<?php
//session_start();
require_once 'db_connect.php';

// Register user
function registerUser($name, $email, $phone, $password, $gender, $age, $height, $weight, $country, $state) {
    global $conn;
    
    // Check if email or phone already exists
    $check_query = "SELECT * FROM users WHERE email = ? OR phone = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("ss", $email, $phone);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        return ["success" => false, "message" => "Email or phone number already exists"];
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user into database
    $insert_query = "INSERT INTO users (name, email, phone, password, gender, age, height, weight, country, state) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $insert_stmt->bind_param("sssssiddss", $name, $email, $phone, $hashed_password, $gender, $age, $height, $weight, $country, $state);
    
    if ($insert_stmt->execute()) {
        return ["success" => true, "message" => "Registration successful"];
    } else {
        return ["success" => false, "message" => "Registration failed: " . $conn->error];
    }
}

// Login user
function loginUser($phone, $password) {
    global $conn;
    
    $query = "SELECT * FROM users WHERE phone = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['logged_in'] = true;
            
            return ["success" => true, "message" => "Login successful"];
        }
    }
    
    return ["success" => false, "message" => "Invalid phone number or password"];
}

// Reset password
function resetPassword($phone, $new_password) {
    global $conn;
    
    $query = "SELECT * FROM users WHERE phone = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_query = "UPDATE users SET password = ? WHERE phone = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ss", $hashed_password, $phone);
        
        if ($update_stmt->execute()) {
            return ["success" => true, "message" => "Password reset successful"];
        }
    }
    
    return ["success" => false, "message" => "Password reset failed"];
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Get user data
function getUserData() {
    global $conn;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT id, name, email, phone, gender, age, height, weight, country, state FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        return $result->fetch_assoc();
    }
    
    return null;
}

// Logout user
function logoutUser() {
    session_unset();
    session_destroy();
}
?>

