<?php
session_start();
require_once 'admin_auth.php';

// Check if admin is logged in
if (!isAdminLoggedIn()) {
    header("Location: admin_login.php");
    exit;
}

// Get data for admin
$users = getAllUsers();
$health_data = getAllHealthData();
$sleep_data = getAllSleepData();
$workout_data = getAllWorkoutData();
$nutrition_data = getAllNutritionData();
$bmi_data = getAllBmiData();
$reminders = getAllReminders();
$feedback = getAllFeedback();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/bg.jpg') no-repeat center center/cover;
            background-attachment: fixed;
        }
        .navbar { 
            background: linear-gradient(135deg, #555555, #999999);
            padding: 15px; 
        }
        .navbar a { 
            color: white; 
            text-decoration: none; 
            font-size: 18px; 
            margin-right: 20px;
        }
        .admin-container { 
            max-width: 1200px; 
            margin: 30px auto; 
        }
        .welcome-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .welcome-section h2 {
            color: #343a40;
            margin-bottom: 20px;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            background-color: rgba(255, 255, 255, 0.95);
        }
        .card-header {
            background-color: #343a40;
            color: white;
            font-weight: 500;
        }
        .table {
            margin-bottom: 0;
        }
        .normal {
            color: #28a745;
        }
        .warning {
            color: #ffc107;
        }
        .danger {
            color: #dc3545;
        }
        .nav-tabs .nav-link {
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            color: #343a40;
            font-weight: bold;
        }
        .chart-container {
            height: 300px;
            margin-bottom: 30px;
        }
        .stats-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
            text-align: center;
        }
        .stats-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #343a40;
            margin: 10px 0;
        }
        .stats-label {
            font-size: 1.1em;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="admin_dashboard.php" class="navbar-brand"> ADMIN DASHBOARD</a>
            <div class="d-flex">
                <a href="admin_logout.php" class="logout">LOGOUT</a>
            </div>
        </div>
    </nav>
    
    <div class="container admin-container">
        <div class="welcome-section">
            <h2>Welcome to Admin Dashboard</h2>
            <p>Manage users, view health data, and read feedback from users.</p>
        </div>
        
        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-users fa-2x text-primary"></i>
                    <div class="stats-number"><?php echo count($users); ?></div>
                    <div class="stats-label">Registered Users</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-heartbeat fa-2x text-danger"></i>
                    <div class="stats-number"><?php echo count($health_data); ?></div>
                    <div class="stats-label">Health Entries</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-bed fa-2x text-info"></i>
                    <div class="stats-number"><?php echo count($sleep_data); ?></div>
                    <div class="stats-label">Sleep Entries</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <i class="fas fa-comment fa-2x text-success"></i>
                    <div class="stats-number"><?php echo count($feedback); ?></div>
                    <div class="stats-label">User Feedback</div>
                </div>
            </div>
        </div>
        
        <!-- Charts -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        User Registration Trend
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="registrationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Health Data Distribution
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="healthDistributionChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabs for different data -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="true">Users</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="health-tab" data-bs-toggle="tab" data-bs-target="#health" type="button" role="tab" aria-controls="health" aria-selected="false">BP & Sugar</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sleep-tab" data-bs-toggle="tab" data-bs-target="#sleep" type="button" role="tab" aria-controls="sleep" aria-selected="false">Sleep</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="workout-tab" data-bs-toggle="tab" data-bs-target="#workout" type="button" role="tab" aria-controls="workout" aria-selected="false">Workout</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="nutrition-tab" data-bs-toggle="tab" data-bs-target="#nutrition" type="button" role="tab" aria-controls="nutrition" aria-selected="false">Nutrition</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="bmi-tab" data-bs-toggle="tab" data-bs-target="#bmi" type="button" role="tab" aria-controls="bmi" aria-selected="false">BMI</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="reminders-tab" data-bs-toggle="tab" data-bs-target="#reminders" type="button" role="tab" aria-controls="reminders" aria-selected="false">Reminders</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback" type="button" role="tab" aria-controls="feedback" aria-selected="false">Feedback</button>
            </li>
        </ul>
        
        <div class="tab-content" id="adminTabsContent">
            <!-- Users Tab -->
            <div class="tab-pane fade show active" id="users" role="tabpanel" aria-labelledby="users-tab">
                <div class="card">
                    <div class="card-header">
                        Registered Users
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Gender</th>
                                        <th>Age</th>
                                        <th>Height</th>
                                        <th>Weight</th>
                                        <th>Country</th>
                                        <th>State</th>
                                        <th>Registered On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['id']); ?></td>
                                            <td><?php echo htmlspecialchars($user['name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                            <td><?php echo htmlspecialchars($user['gender']); ?></td>
                                            <td><?php echo htmlspecialchars($user['age']); ?></td>
                                            <td><?php echo htmlspecialchars($user['height'] ?? 'N/A'); ?> <?php echo $user['height'] ? 'cm' : ''; ?></td>
                                            
                                            <td><?php echo htmlspecialchars($user['weight'] ?? 'N/A'); ?> <?php echo $user['weight'] ? 'kg' : ''; ?></td>
                                            <td><?php echo htmlspecialchars($user['country']); ?></td>
                                            <td><?php echo htmlspecialchars($user['state']); ?></td>
                                            <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Health Tab -->
            <div class="tab-pane fade" id="health" role="tabpanel" aria-labelledby="health-tab">
                <div class="card">
                    <div class="card-header">
                        Blood Pressure & Sugar Data
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Phone</th>
                                        <th>Date</th>
                                        <th>BP (Systolic/Diastolic)</th>
                                        <th>Sugar (mg/dL)</th>
                                        <th>Status</th>
                                        <th>Recorded On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($health_data as $data): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['id']); ?></td>
                                            <td><?php echo htmlspecialchars($data['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($data['user_phone']); ?></td>
                                            <td><?php echo htmlspecialchars($data['date']); ?></td>
                                            <td><?php echo htmlspecialchars($data['bp_systolic'] . '/' . $data['bp_diastolic']); ?></td>
                                            <td><?php echo htmlspecialchars($data['sugar']); ?></td>
                                            <td>
                                                <?php
                                                $status = [];
                                                $systolic = $data['bp_systolic'];
                                                $diastolic = $data['bp_diastolic'];
                                                $sugar = $data['sugar'];
                                                
                                                if ($systolic < 90 || $diastolic < 60) {
                                                    $status[] = '<span class="warning">Low BP</span>';
                                                } elseif ($systolic > 130 || $diastolic > 80) {
                                                    $status[] = '<span class="danger">High BP</span>';
                                                } else {
                                                    $status[] = '<span class="normal">Normal BP</span>';
                                                }
                                                
                                                if ($sugar < 70) {
                                                    $status[] = '<span class="warning">Low Sugar</span>';
                                                } elseif ($sugar > 140) {
                                                    $status[] = '<span class="danger">High Sugar</span>';
                                                } else {
                                                    $status[] = '<span class="normal">Normal Sugar</span>';
                                                }
                                                
                                                echo implode('<br>', $status);
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Sleep Tab -->
            <div class="tab-pane fade" id="sleep" role="tabpanel" aria-labelledby="sleep-tab">
                <div class="card">
                    <div class="card-header">
                        Sleep Data
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Sleep Start</th>
                                        <th>Wake-up Time</th>
                                        <th>Duration</th>
                                        <th>Quality</th>
                                        <th>Recorded On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($sleep_data as $data): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['id']); ?></td>
                                            <td><?php echo htmlspecialchars($data['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($data['date']); ?></td>
                                            <td><?php echo htmlspecialchars($data['sleep_start']); ?></td>
                                            <td><?php echo htmlspecialchars($data['sleep_end']); ?></td>
                                            <td><?php echo number_format($data['duration'], 2); ?> hrs</td>
                                            <td>
                                                <?php
                                                $duration = $data['duration'];
                                                if ($duration >= 7 && $duration <= 9) {
                                                    echo '<span class="normal">Good</span>';
                                                } elseif ($duration >= 6 && $duration < 7) {
                                                    echo '<span class="warning">Average</span>';
                                                } else {
                                                    echo '<span class="danger">Poor</span>';
                                                }
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Workout Tab -->
            <div class="tab-pane fade" id="workout" role="tabpanel" aria-labelledby="workout-tab">
                <div class="card">
                    <div class="card-header">
                        Workout Data
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Gender</th>
                                        <th>Age Group</th>
                                        <th>Goal</th>
                                        <th>Exercises</th>
                                        <th>Recorded On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($workout_data as $data): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['id']); ?></td>
                                            <td><?php echo htmlspecialchars($data['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($data['date']); ?></td>
                                            <td><?php echo htmlspecialchars($data['gender']); ?></td>
                                            <td><?php echo htmlspecialchars($data['age_group']); ?></td>
                                            <td><?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $data['goal']))); ?></td>
                                            <td>
                                                <ul class="list-unstyled">
                                                    <?php foreach($data['exercises'] as $exercise): ?>
                                                        <li><?php echo htmlspecialchars($exercise); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </td>
                                            <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Nutrition Tab -->
            <div class="tab-pane fade" id="nutrition" role="tabpanel" aria-labelledby="nutrition-tab">
                <div class="card">
                    <div class="card-header">
                        Nutrition Data
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Meal Type</th>
                                        <th>Meals</th>
                                        <th>Total Calories</th>
                                        <th>Recorded On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($nutrition_data as $data): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['id']); ?></td>
                                            <td><?php echo htmlspecialchars($data['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($data['date']); ?></td>
                                            <td><?php echo htmlspecialchars(ucfirst($data['meal_type'])); ?></td>
                                            <td>
                                                <ul class="list-unstyled">
                                                    <?php foreach($data['meals'] as $meal): ?>
                                                        <li><?php echo htmlspecialchars($meal['name']); ?> (<?php echo htmlspecialchars($meal['calories']); ?> cal)</li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </td>
                                            <td><?php echo htmlspecialchars($data['total_calories']); ?> cal</td>
                                            <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- BMI Tab -->
            <div class="tab-pane fade" id="bmi" role="tabpanel" aria-labelledby="bmi-tab">
                <div class="card">
                    <div class="card-header">
                        BMI Data
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Date</th>
                                        <th>Height (cm)</th>
                                        <th>Weight (kg)</th>
                                        <th>BMI</th>
                                        <th>Category</th>
                                        <th>Recorded On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($bmi_data as $data): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['id']); ?></td>
                                            <td><?php echo htmlspecialchars($data['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($data['date']); ?></td>
                                            <td><?php echo htmlspecialchars($data['height']); ?></td>
                                            <td><?php echo htmlspecialchars($data['weight']); ?></td>
                                            <td><?php echo htmlspecialchars($data['bmi']); ?></td>
                                            <td>
                                                <?php
                                                $category = $data['category'];
                                                $class = '';
                                                if ($category == 'Underweight') {
                                                    $class = 'text-primary';
                                                } elseif ($category == 'Normal weight') {
                                                    $class = 'text-success';
                                                } elseif ($category == 'Overweight') {
                                                    $class = 'text-warning';
                                                } else {
                                                    $class = 'text-danger';
                                                }
                                                echo "<span class='$class'>$category</span>";
                                                ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Reminders Tab -->
            <div class="tab-pane fade" id="reminders" role="tabpanel" aria-labelledby="reminders-tab">
                <div class="card">
                    <div class="card-header">
                        Reminders
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Status</th>
                                        <th>Created On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($reminders as $data): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['id']); ?></td>
                                            <td><?php echo htmlspecialchars($data['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($data['title']); ?></td>
                                            <td><?php echo htmlspecialchars($data['description']); ?></td>
                                            <td><?php echo htmlspecialchars($data['reminder_date']); ?></td>
                                            <td><?php echo htmlspecialchars($data['reminder_time']); ?></td>
                                            <td>
                                                <?php if ($data['is_completed']): ?>
                                                    <span class="badge bg-success">Completed</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Pending</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Feedback Tab -->
            <div class="tab-pane fade" id="feedback" role="tabpanel" aria-labelledby="feedback-tab">
                <div class="card">
                    <div class="card-header">
                        User Feedback
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Feedback</th>
                                        <th>Submitted On</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($feedback as $data): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($data['id']); ?></td>
                                            <td><?php echo htmlspecialchars($data['user_name']); ?></td>
                                            <td><?php echo htmlspecialchars($data['feedback_text']); ?></td>
                                            <td><?php echo htmlspecialchars($data['created_at']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // User Registration Trend Chart
            const registrationCtx = document.getElementById('registrationChart').getContext('2d');
            
            // Process user data to get registration counts by month
            const users = <?php echo json_encode($users); ?>;
            const registrationData = processRegistrationData(users);
            
            const registrationChart = new Chart(registrationCtx, {
                type: 'line',
                data: {
                    labels: registrationData.labels,
                    datasets: [{
                        label: 'New Users',
                        data: registrationData.data,
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
            
            // Health Data Distribution Chart
            const healthDistributionCtx = document.getElementById('healthDistributionChart').getContext('2d');
            
            const healthDistributionChart = new Chart(healthDistributionCtx, {
                type: 'pie',
                data: {
                    labels: ['BP & Sugar', 'Sleep', 'Workout', 'Nutrition', 'BMI'],
                    datasets: [{
                        data: [
                            <?php echo count($health_data); ?>,
                            <?php echo count($sleep_data); ?>,
                            <?php echo count($workout_data); ?>,
                            <?php echo count($nutrition_data); ?>,
                            <?php echo count($bmi_data); ?>
                        ],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right'
                        }
                    }
                }
            });
            
            // Function to process registration data
            function processRegistrationData(users) {
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const counts = Array(12).fill(0);
                
                // Get current year
                const currentYear = new Date().getFullYear();
                
                // Count registrations by month for the current year
                users.forEach(user => {
                    const date = new Date(user.created_at);
                    if (date.getFullYear() === currentYear) {
                        counts[date.getMonth()]++;
                    }
                });
                
                return {
                    labels: months,
                    data: counts
                };
            }
        });
    </script>
</body>
</html>


