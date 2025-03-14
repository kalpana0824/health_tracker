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

// Get health data
$health_data = getHealthData();
$sleep_data = getSleepData();
$workout_data = getWorkoutData();
$nutrition_data = getNutritionData();
$bmi_data = getBmiData();
$reminders = getReminders();

// Get latest BMI if available
$latest_bmi = null;
if (!empty($bmi_data)) {
    $latest_bmi = $bmi_data[0];
}

// Get upcoming reminders
$upcoming_reminders = array_filter($reminders, function($reminder) {
    return !$reminder['is_completed'] && 
           (strtotime($reminder['reminder_date']) >= strtotime(date('Y-m-d')) || 
            (strtotime($reminder['reminder_date']) == strtotime(date('Y-m-d')) && 
             strtotime($reminder['reminder_time']) > strtotime(date('H:i:s'))));
});
$upcoming_reminders = array_slice($upcoming_reminders, 0, 3);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: url('images/bg.jpg') no-repeat center center/cover;
            background-attachment: fixed;
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
        .navbar a:hover {
            text-decoration: underline;
        }
        .dashboard-container { 
            max-width: 1200px; 
            margin: 30px auto; 
        }
        .welcome-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            text-align: center;
        }
        .welcome-section h2 {
            color: #28a745;
            margin-bottom: 20px;
        }
        .feature-card { 
            transition: transform 0.3s ease-in-out;
            height: 100%;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.95);
        }
        .feature-card:hover { 
            transform: translateY(-10px); 
        }
        .feature-card .card-body {
            display: flex;
            flex-direction: column;
        }
        .feature-card .card-title {
            color: #28a745;
            font-weight: 600;
        }
        .feature-card .btn {
            margin-top: auto;
            background-color: #28a745;
            border-color: #28a745;
        }
        .feature-card .btn:hover {
            background-color: #218838;
            border-color: #218838;
        }
        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        .stats-card h4 {
            color: #28a745;
            margin-bottom: 15px;
        }
        .reminder-item {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 10px 15px;
            margin-bottom: 10px;
            border-radius: 0 5px 5px 0;
        }
        .reminder-date {
            font-size: 0.8em;
            color: #6c757d;
        }
        .footer { 
            background: #28a745; 
            color: white; 
            padding: 20px 0; 
            text-align: center; 
            margin-top: 50px;
        }
        .footer a {
            color: white;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .health-icon {
            font-size: 2.5rem;
            color: #28a745;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="index.html" class="navbar-brand">HEALTH TRACKER</a>
            <div class="d-flex">
                <a href="profile.php" class="profile-icon">PROFILE</a>
                <a href="logout.php" class="logout">LOGOUT</a>
            </div>
        </div>
    </nav>
    
    <div class="container dashboard-container">
        <div class="welcome-section">
            <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
            <p>Track your health metrics and improve your lifestyle with our comprehensive tools.</p>
        </div>
        
        <!-- Quick Stats Section -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="stats-card">
                    <h4>Health Overview</h4>
                    <?php if (!empty($health_data)): ?>
                        <p><strong>Latest BP:</strong> <?php echo $health_data[0]['bp_systolic'] . '/' . $health_data[0]['bp_diastolic']; ?> mmHg</p>
                        <p><strong>Latest Sugar:</strong> <?php echo $health_data[0]['sugar']; ?> mg/dL</p>
                    <?php else: ?>
                        <p>No BP or sugar data recorded yet.</p>
                    <?php endif; ?>
                    
                    <?php if ($latest_bmi): ?>
                        <p><strong>BMI:</strong> <?php echo $latest_bmi['bmi']; ?> (<?php echo $latest_bmi['category']; ?>)</p>
                    <?php else: ?>
                        <p>No BMI data recorded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="stats-card">
                    <h4>Sleep & Activity</h4>
                    <?php if (!empty($sleep_data)): ?>
                        <p><strong>Last Sleep:</strong> <?php echo $sleep_data[0]['duration']; ?> hours</p>
                    <?php else: ?>
                        <p>No sleep data recorded yet.</p>
                    <?php endif; ?>
                    
                    <?php if (!empty($workout_data)): ?>
                        <p><strong>Latest Workout:</strong> <?php echo ucfirst($workout_data[0]['goal']); ?> focused</p>
                    <?php else: ?>
                        <p>No workout data recorded yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-md-4 mb-4">
                <div class="stats-card">
                    <h4>Upcoming Reminders</h4>
                    <?php if (!empty($upcoming_reminders)): ?>
                        <?php foreach($upcoming_reminders as $reminder): ?>
                            <div class="reminder-item">
                                <div><?php echo htmlspecialchars($reminder['title']); ?></div>
                                <div class="reminder-date"><?php echo date('M d, Y', strtotime($reminder['reminder_date'])); ?> at <?php echo date('h:i A', strtotime($reminder['reminder_time'])); ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No upcoming reminders.</p>
                    <?php endif; ?>
                    <a href="reminders.php" class="btn btn-sm btn-outline-success mt-2">Manage Reminders</a>
                </div>
            </div>
        </div>
        
        <!-- Features Section -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card feature-card p-3">
                    <div class="card-body text-center">
                        <i class="fas fa-heartbeat health-icon"></i>
                        <h4 class="card-title">BP & Sugar Tracking</h4>
                        <p class="card-text">Log your daily blood pressure and sugar levels to monitor your health.</p>
                        <a href="bp_sugar.php" class="btn btn-primary mt-3">Go to Tracker</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card p-3">
                    <div class="card-body text-center">
                        <i class="fas fa-bed health-icon"></i>
                        <h4 class="card-title">Sleep Tracking</h4>
                        <p class="card-text">Record your sleep patterns and get insights for better rest.</p>
                        <a href="sleep.php" class="btn btn-primary mt-3">Track Sleep</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card p-3">
                    <div class="card-body text-center">
                        <i class="fas fa-dumbbell health-icon"></i>
                        <h4 class="card-title">Workout Planner</h4>
                        <p class="card-text">Get personalized workout plans based on your goals and fitness level.</p>
                        <a href="workout.php" class="btn btn-primary mt-3">Plan Workout</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card p-3">
                    <div class="card-body text-center">
                        <i class="fas fa-apple-alt health-icon"></i>
                        <h4 class="card-title">Nutrition Tracker</h4>
                        <p class="card-text">Track your meals and calculate your daily calorie intake.</p>
                        <a href="nutrition.php" class="btn btn-primary mt-3">Track Nutrition</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card p-3">
                    <div class="card-body text-center">
                        <i class="fas fa-weight health-icon"></i>
                        <h4 class="card-title">BMI Calculator</h4>
                        <p class="card-text">Calculate your Body Mass Index and get health recommendations.</p>
                        <a href="bmi_calculator.php" class="btn btn-primary mt-3">Calculate BMI</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card feature-card p-3">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-line health-icon"></i>
                        <h4 class="card-title">Health Reports</h4>
                        <p class="card-text">View your health data reports and track progress over time.</p>
                        <a href="reports.php" class="btn btn-primary mt-3">View Reports</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <section class="footer">
        <div class="container">
            <h3>Contact Us</h3>
            <p>Email: support@healthtracker.com</p>
            <p>Phone: +123 456 7890</p>
            <p>
                <a href="#">Instagram</a> | 
                <a href="#">Twitter</a> | 
                <a href="#">Facebook</a>
            </p>
            <p>&copy; 2024 Health Tracker. All rights reserved.</p>
        </div>
    </section>
</body>
</html>

