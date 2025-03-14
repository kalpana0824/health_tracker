<?php
// session_start();
require_once 'db_connect.php';

// Admin login credentials
// In a real application, these would be stored in the database
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // This should be hashed in a real application

// Login admin
function loginAdmin($username, $password) {
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

// Check if admin is logged in
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Get all users data for admin
function getAllUsers() {
    global $conn;
    
    if (!isAdminLoggedIn()) {
        return [];
    }
    
    $query = "SELECT id, name, email, phone, gender, age, height, weight, country, state, created_at FROM users";
    $result = $conn->query($query);
    
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    
    return $users;
}

// Get all health data for admin
function getAllHealthData() {
    global $conn;
    
    if (!isAdminLoggedIn()) {
        return [];
    }
    
    $query = "SELECT h.*, u.name AS user_name, u.phone AS user_phone 
              FROM health_data h 
              JOIN users u ON h.user_id = u.id 
              ORDER BY h.date DESC";
    $result = $conn->query($query);
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Get all sleep data for admin
function getAllSleepData() {
    global $conn;
    
    if (!isAdminLoggedIn()) {
        return [];
    }
    
    $query = "SELECT s.*, u.name AS user_name 
              FROM sleep_data s 
              JOIN users u ON s.user_id = u.id 
              ORDER BY s.date DESC";
    $result = $conn->query($query);
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Get all workout data for admin
function getAllWorkoutData() {
    global $conn;
    
    if (!isAdminLoggedIn()) {
        return [];
    }
    
    $query = "SELECT w.*, u.name AS user_name 
              FROM workout_data w 
              JOIN users u ON w.user_id = u.id 
              ORDER BY w.date DESC";
    $result = $conn->query($query);
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['exercises'] = json_decode($row['exercises'], true);
        $data[] = $row;
    }
    
    return $data;
}

// Get all nutrition data for admin
function getAllNutritionData() {
    global $conn;
    
    if (!isAdminLoggedIn()) {
        return [];
    }
    
    $query = "SELECT n.*, u.name AS user_name 
              FROM nutrition_data n 
              JOIN users u ON n.user_id = u.id 
              ORDER BY n.date DESC";
    $result = $conn->query($query);
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['meals'] = json_decode($row['meals'], true);
        $data[] = $row;
    }
    
    return $data;
}

// Get all BMI data for admin
function getAllBmiData() {
    global $conn;
    
    if (!isAdminLoggedIn()) {
        return [];
    }
    
    $query = "SELECT b.*, u.name AS user_name 
              FROM bmi_data b 
              JOIN users u ON b.user_id = u.id 
              ORDER BY b.date DESC";
    $result = $conn->query($query);
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Get all reminders for admin
function getAllReminders() {
    global $conn;
    
    if (!isAdminLoggedIn()) {
        return [];
    }
    
    $query = "SELECT r.*, u.name AS user_name 
              FROM reminders r 
              JOIN users u ON r.user_id = u.id 
              ORDER BY r.reminder_date ASC, r.reminder_time ASC";
    $result = $conn->query($query);
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Get all feedback for admin
function getAllFeedback() {
    global $conn;
    
    if (!isAdminLoggedIn()) {
        return [];
    }
    
    $query = "SELECT f.*, u.name AS user_name 
              FROM feedback f 
              JOIN users u ON f.user_id = u.id 
              ORDER BY f.created_at DESC";
    $result = $conn->query($query);
    
    $feedback = [];
    while ($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
    
    return $feedback;
}

// Logout admin
function logoutAdmin() {
    unset($_SESSION['admin_logged_in']);
}
?>

