<?php
session_start();
require_once 'auth.php';
require_once 'user_data.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$message = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $sleep_start = $_POST['sleep_start'];
    $sleep_end = $_POST['sleep_end'];
    
    // Calculate sleep duration
    $start_time = new DateTime($sleep_start);
    $end_time = new DateTime($sleep_end);
    
    // If end time is earlier than start time, add a day to end time
    if ($end_time < $start_time) {
        $end_time->modify('+1 day');
    }
    
    $interval = $start_time->diff($end_time);
    $hours = $interval->h + ($interval->days * 24);
    $minutes = $interval->i;
    $duration = $hours + ($minutes / 60);
    
    $result = saveSleepData($date, $sleep_start, $sleep_end, $duration);
    
    if ($result["success"]) {
        $message = "<div class='alert alert-success'>" . $result["message"] . "</div>";
    } else {
        $message = "<div class='alert alert-danger'>" . $result["message"] . "</div>";
    }
}

// Get user's sleep data
$sleep_data = getSleepData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sleep Tracker - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/bg.jpg') no-repeat center center/cover;
        }
        .navbar { 
            background: #28a745; 
            padding: 15px; 
        }
        .navbar a { 
            color: white; 
            text-decoration: none; 
            font-size: 18px; 
            margin-right: 20px;
        }
        .sleep-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .sleep-header {
            text-align: center;
            margin-bottom: 30px;
            color: #28a745;
        }
        .btn-submit {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-submit:hover {
            background-color: #218838;
            border-color: #218838;
        }
        .table-container { 
            margin-top: 30px; 
        }
        .table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .table th, .table td {
            text-align: center;
            padding: 12px;
            vertical-align: middle;
        }
        .table th {
            background-color: #f8f9fa;
            color: #495057;
        }
        .good-sleep {
            color: #28a745;
        }
        .average-sleep {
            color: #ffc107;
        }
        .poor-sleep {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="dashboard.php" class="dashboard-link">DASHBOARD</a>
            <div class="d-flex">
                <a href="profile.php" class="profile-icon">PROFILE</a>
                <a href="logout.php" class="logout">LOGOUT</a>
            </div>
        </div>
    </nav>
    
    <div class="container sleep-container">
        <h2 class="sleep-header">Sleep Tracker</h2>
        <p class="text-center">Log your daily sleep schedule and track your sleep patterns.</p>
        
        <?php echo $message; ?>
        
        <div class="row">
            <div class="col-md-5">
                <form id="sleepForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label class="form-label">Date:</label>
                        <input type="date" id="date" name="date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Sleep Start Time:</label>
                        <input type="time" id="sleep_start" name="sleep_start" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Wake-up Time:</label>
                        <input type="time" id="sleep_end" name="sleep_end" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 btn-submit">Save Sleep Data</button>
                </form>
            </div>
            
            <div class="col-md-7 table-container">
                <h4>Your Recent Sleep Records</h4>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Sleep Start</th>
                            <th>Wake-up Time</th>
                            <th>Duration</th>
                            <th>Quality</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($sleep_data as $data): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($data['date']); ?></td>
                                <td><?php echo htmlspecialchars($data['sleep_start']); ?></td>
                                <td><?php echo htmlspecialchars($data['sleep_end']); ?></td>
                                <td><?php echo number_format($data['duration'], 2); ?> hrs</td>
                                <td>
                                    <?php
                                    $duration = $data['duration'];
                                    if ($duration >= 7 && $duration <= 9) {
                                        echo '<span class="good-sleep">Good</span>';
                                    } elseif ($duration >= 6 && $duration < 7) {
                                        echo '<span class="average-sleep">Average</span>';
                                    } else {
                                        echo '<span class="poor-sleep">Poor</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-4">
            <h4>Sleep Quality Guidelines</h4>
            <ul>
                <li><span class="good-sleep">Good Sleep:</span> 7-9 hours per night</li>
                <li><span class="average-sleep">Average Sleep:</span> 6-7 hours per night</li>
                <li><span class="poor-sleep">Poor Sleep:</span> Less than 6 hours or more than 9 hours per night</li>
            </ul>
            <p>Consistent sleep patterns are important for overall health. Try to maintain a regular sleep schedule.</p>
        </div>
    </div>
</body>
</html>

