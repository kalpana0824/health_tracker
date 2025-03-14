<?php

require_once 'db_connect.php';

// Save health data (BP and Sugar)
function saveHealthData($date, $bp_systolic, $bp_diastolic, $sugar) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return ["success" => false, "message" => "User not logged in"];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "INSERT INTO health_data (user_id, date, bp_systolic, bp_diastolic, sugar) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isiii", $user_id, $date, $bp_systolic, $bp_diastolic, $sugar);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Health data saved successfully"];
    } else {
        return ["success" => false, "message" => "Failed to save health data: " . $conn->error];
    }
}

// Get user's health data
function getHealthData() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM health_data WHERE user_id = ? ORDER BY date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Save sleep data
function saveSleepData($date, $sleep_start, $sleep_end, $duration) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return ["success" => false, "message" => "User not logged in"];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "INSERT INTO sleep_data (user_id, date, sleep_start, sleep_end, duration) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssd", $user_id, $date, $sleep_start, $sleep_end, $duration);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Sleep data saved successfully"];
    } else {
        return ["success" => false, "message" => "Failed to save sleep data: " . $conn->error];
    }
}

// Get user's sleep data
function getSleepData() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM sleep_data WHERE user_id = ? ORDER BY date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Save workout data
function saveWorkoutData($date, $gender, $age_group, $goal, $exercises) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return ["success" => false, "message" => "User not logged in"];
    }
    
    $user_id = $_SESSION['user_id'];
    $exercises_json = json_encode($exercises);
    
    $query = "INSERT INTO workout_data (user_id, date, gender, age_group, goal, exercises) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssss", $user_id, $date, $gender, $age_group, $goal, $exercises_json);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Workout data saved successfully"];
    } else {
        return ["success" => false, "message" => "Failed to save workout data: " . $conn->error];
    }
}

// Get user's workout data
function getWorkoutData() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM workout_data WHERE user_id = ? ORDER BY date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['exercises'] = json_decode($row['exercises'], true);
        $data[] = $row;
    }
    
    return $data;
}

// Save nutrition data
function saveNutritionData($date, $meal_type, $meals, $total_calories) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return ["success" => false, "message" => "User not logged in"];
    }
    
    $user_id = $_SESSION['user_id'];
    $meals_json = json_encode($meals);
    
    $query = "INSERT INTO nutrition_data (user_id, date, meal_type, meals, total_calories) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isssi", $user_id, $date, $meal_type, $meals_json, $total_calories);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Nutrition data saved successfully"];
    } else {
        return ["success" => false, "message" => "Failed to save nutrition data: " . $conn->error];
    }
}

// Get user's nutrition data
function getNutritionData() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM nutrition_data WHERE user_id = ? ORDER BY date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $row['meals'] = json_decode($row['meals'], true);
        $data[] = $row;
    }
    
    return $data;
}

// Save BMI data
function saveBmiData($date, $height, $weight, $bmi, $category) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return ["success" => false, "message" => "User not logged in"];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "INSERT INTO bmi_data (user_id, date, height, weight, bmi, category) 
              VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("isddds", $user_id, $date, $height, $weight, $bmi, $category);
    
    if ($stmt->execute()) {
        // Update user's height and weight
        $update_query = "UPDATE users SET height = ?, weight = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ddi", $height, $weight, $user_id);
        $update_stmt->execute();
        
        return ["success" => true, "message" => "BMI data saved successfully"];
    } else {
        return ["success" => false, "message" => "Failed to save BMI data: " . $conn->error];
    }
}

// Get user's BMI data
function getBmiData() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM bmi_data WHERE user_id = ? ORDER BY date DESC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Calculate BMI
function calculateBMI($weight, $height) {
    // Height in meters
    $height_m = $height / 100;
    // BMI formula: weight (kg) / (height (m))²
    $bmi = $weight / ($height_m * $height_m);
    return round($bmi, 1);
}

// Get BMI category
function getBmiCategory($bmi) {
    if ($bmi < 18.5) {
        return "Underweight";
    } elseif ($bmi >= 18.5 && $bmi < 25) {
        return "Normal weight";
    } elseif ($bmi >= 25 && $bmi < 30) {
        return "Overweight";
    } else {
        return "Obese";
    }
}

// Save reminder
function saveReminder($title, $description, $reminder_date, $reminder_time) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return ["success" => false, "message" => "User not logged in"];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "INSERT INTO reminders (user_id, title, description, reminder_date, reminder_time) 
              VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("issss", $user_id, $title, $description, $reminder_date, $reminder_time);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Reminder saved successfully"];
    } else {
        return ["success" => false, "message" => "Failed to save reminder: " . $conn->error];
    }
}

// Get user's reminders
function getReminders() {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return [];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM reminders WHERE user_id = ? ORDER BY reminder_date ASC, reminder_time ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    
    return $data;
}

// Mark reminder as completed
function completeReminder($reminder_id) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return ["success" => false, "message" => "User not logged in"];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "UPDATE reminders SET is_completed = TRUE WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $reminder_id, $user_id);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Reminder marked as completed"];
    } else {
        return ["success" => false, "message" => "Failed to update reminder: " . $conn->error];
    }
}

// Delete reminder
function deleteReminder($reminder_id) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return ["success" => false, "message" => "User not logged in"];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "DELETE FROM reminders WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $reminder_id, $user_id);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Reminder deleted successfully"];
    } else {
        return ["success" => false, "message" => "Failed to delete reminder: " . $conn->error];
    }
}

// Save user feedback
function saveFeedback($feedback_text) {
    global $conn;
    
    if (!isset($_SESSION['user_id'])) {
        return ["success" => false, "message" => "User not logged in"];
    }
    
    $user_id = $_SESSION['user_id'];
    $query = "INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $user_id, $feedback_text);
    
    if ($stmt->execute()) {
        return ["success" => true, "message" => "Feedback submitted successfully"];
    } else {
        return ["success" => false, "message" => "Failed to submit feedback: " . $conn->error];
    }
}
?>

