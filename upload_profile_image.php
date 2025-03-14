<?php
session_start();
require_once 'auth.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

// Check if file was uploaded without errors
if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] == 0) {
    $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
    $filename = $_FILES["profile_image"]["name"];
    $filetype = $_FILES["profile_image"]["type"];
    $filesize = $_FILES["profile_image"]["size"];

    // Verify file extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if (!array_key_exists($ext, $allowed)) {
        die("Error: Please select a valid file format.");
    }

    // Verify file type
    if (!in_array($filetype, $allowed)) {
        die("Error: Please select a valid file format.");
    }

    // Verify file size - 5MB maximum
    $maxsize = 5 * 1024 * 1024;
    if ($filesize > $maxsize) {
        die("Error: File size is larger than the allowed limit (5MB).");
    }

    // Create upload directory if it doesn't exist
    if (!file_exists("uploads/")) {
        mkdir("uploads/", 0777, true);
    }

    // Create unique filename for the image
    $user_id = $_SESSION['user_id'];
    $new_filename = "user_" . $user_id . "_" . time() . "." . $ext;
    
    // Save the file
    if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], "uploads/" . $new_filename)) {
        // Update user profile in database with image path (not implemented in this example)
        // For this example, we'll just redirect back to profile
        header("Location: profile.php?upload=success");
        exit;
    } else {
        echo "Error: There was a problem uploading your file. Please try again.";
    }
} else {
    echo "Error: " . $_FILES["profile_image"]["error"];
}
?>

