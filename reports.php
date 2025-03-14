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

// Get all health data
$health_data = getHealthData();
$sleep_data = getSleepData();
$workout_data = getWorkoutData();
$nutrition_data = getNutritionData();
$bmi_data = getBmiData();

// Process feedback form
$feedback_message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['feedback'])) {
    $feedback = $_POST['feedback'];
    $result = saveFeedback($feedback);
    
    if ($result["success"]) {
        $feedback_message = "<div class='alert alert-success'>" . $result["message"] . "</div>";
    } else {
        $feedback_message = "<div class='alert alert-danger'>" . $result["message"] . "</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Reports - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4; 
             background: url('images/bg.jpg') no-repeat center center/cover;
            background-size: cover;  /* Ensures the image covers the full page */
          background-position: center; /* Centers the image */
           background-repeat: no-repeat; /* Prevents image repetition */
           background-size: 100% 100%; /* Stretch to fit */
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
        .report-container { 
            max-width: 1000px; 
            margin: 50px auto; 
            background: white; 
            padding: 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        }
        .report-header { 
            text-align: center; 
            margin-bottom: 30px; 
            color: #28a745;
        }
        .report-section {
            margin-bottom: 40px;
        }
        .report-section h4 {
            color: #28a745;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #dee2e6;
        }
        .chart-container {
            height: 300px;
            margin-bottom: 30px;
        }
        .feedback-section { 
            margin-top: 50px; 
            padding-top: 20px;
            border-top: 1px solid #dee2e6;
        }
        .btn-submit {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-submit:hover {
            background-color: #218838;
            border-color: #218838;
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
        .no-data {
            text-align: center;
            padding: 50px;
            color: #6c757d;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
        .nav-tabs .nav-link {
            color: #495057;
        }
        .nav-tabs .nav-link.active {
            color: #28a745;
            font-weight: bold;
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
    
    <div class="container report-container">
        <h2 class="report-header">Your Health Reports</h2>
        <p class="text-center">View comprehensive reports of your health data and track your progress over time.</p>
        
        <ul class="nav nav-tabs mb-4" id="reportTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="bp-sugar-tab" data-bs-toggle="tab" data-bs-target="#bp-sugar" type="button" role="tab" aria-controls="bp-sugar" aria-selected="false">BP & Sugar</button>
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
        </ul>
        
        <div class="tab-content" id="reportTabsContent">
            <!-- Overview Tab -->
            <div class="tab-pane fade show active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
                <div class="report-section">
                    <h4>Health Overview</h4>
                    
                    <?php if (empty($health_data) && empty($sleep_data) && empty($workout_data) && empty($nutrition_data) && empty($bmi_data)): ?>
                        <div class="no-data">
                            <h5>No health data available yet</h5>
                            <p>Start tracking your health metrics to see your reports here.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Latest Health Metrics</h5>
                                        <ul class="list-group list-group-flush">
                                            <?php if (!empty($health_data)): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Blood Pressure
                                                    <span><?php echo $health_data[0]['bp_systolic'] . '/' . $health_data[0]['bp_diastolic']; ?> mmHg</span>
                                                </li>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Blood Sugar
                                                    <span><?php echo $health_data[0]['sugar']; ?> mg/dL</span>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($sleep_data)): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    Last Sleep Duration
                                                    <span><?php echo number_format($sleep_data[0]['duration'], 2); ?> hrs</span>
                                                </li>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($bmi_data)): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    BMI
                                                    <span><?php echo $bmi_data[0]['bmi']; ?> (<?php echo $bmi_data[0]['category']; ?>)</span>
                                                </li>
                                            <?php endif; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title">Activity Summary</h5>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                BP & Sugar Entries
                                                <span class="badge bg-primary rounded-pill"><?php echo count($health_data); ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Sleep Entries
                                                <span class="badge bg-primary rounded-pill"><?php echo count($sleep_data); ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Workout Plans
                                                <span class="badge bg-primary rounded-pill"><?php echo count($workout_data); ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Nutrition Entries
                                                <span class="badge bg-primary rounded-pill"><?php echo count($nutrition_data); ?></span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                BMI Calculations
                                                <span class="badge bg-primary rounded-pill"><?php echo count($bmi_data); ?></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Health Recommendations</h5>
                                        <div class="card-text">
                                            <ul>
                                                <?php if (!empty($health_data)): ?>
                                                    <?php
                                                    $systolic = $health_data[0]['bp_systolic'];
                                                    $diastolic = $health_data[0]['bp_diastolic'];
                                                    $sugar = $health_data[0]['sugar'];
                                                    
                                                    if ($systolic < 90 || $diastolic < 60) {
                                                        echo "<li>Your blood pressure is low. Consider increasing salt intake slightly and staying hydrated.</li>";
                                                    } elseif ($systolic > 130 || $diastolic > 80) {
                                                        echo "<li>Your blood pressure is high. Consider reducing salt intake, exercising regularly, and managing stress.</li>";
                                                    } else {
                                                        echo "<li>Your blood pressure is normal. Maintain a healthy lifestyle to keep it that way.</li>";
                                                    }
                                                    
                                                    if ($sugar < 70) {
                                                        echo "<li>Your blood sugar is low. Consider eating small, frequent meals and including complex carbohydrates.</li>";
                                                    } elseif ($sugar > 140) {
                                                        echo "<li>Your blood sugar is high. Consider reducing sugar and refined carbohydrate intake, and exercising regularly.</li>";
                                                    } else {
                                                        echo "<li>Your blood sugar is normal. Maintain a balanced diet to keep it stable.</li>";
                                                    }
                                                    ?>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($sleep_data)): ?>
                                                    <?php
                                                    $duration = $sleep_data[0]['duration'];
                                                    if ($duration < 6) {
                                                        echo "<li>You're not getting enough sleep. Aim for 7-9 hours of sleep per night for optimal health.</li>";
                                                    } elseif ($duration > 9) {
                                                        echo "<li>You're sleeping more than recommended. Excessive sleep can be linked to health issues.</li>";
                                                    } else {
                                                        echo "<li>Your sleep duration is good. Maintain a consistent sleep schedule for better quality rest.</li>";
                                                    }
                                                    ?>
                                                <?php endif; ?>
                                                
                                                <?php if (!empty($bmi_data)): ?>
                                                    <?php
                                                    $category = $bmi_data[0]['category'];
                                                    if ($category == 'Underweight') {
                                                        echo "<li>You are underweight. Consider increasing your calorie intake with nutrient-rich foods.</li>";
                                                    } elseif ($category == 'Overweight' || $category == 'Obese') {
                                                        echo "<li>Your BMI indicates you could benefit from weight management. Focus on a balanced diet and regular exercise.</li>";
                                                    } else {
                                                        echo "<li>Your BMI is in the healthy range. Maintain your current weight with a balanced diet and regular exercise.</li>";
                                                    }
                                                    ?>
                                                <?php endif; ?>
                                                
                                                <li>Stay hydrated by drinking at least 8 glasses of water daily.</li>
                                                <li>Include at least 30 minutes of physical activity in your daily routine.</li>
                                                <li>Eat a balanced diet rich in fruits, vegetables, lean proteins, and whole grains.</li>
                                                <li>Practice stress management techniques like meditation or deep breathing.</li>
                                                <li>Get regular health check-ups and screenings as recommended by your healthcare provider.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- BP & Sugar Tab -->
            <div class="tab-pane fade" id="bp-sugar" role="tabpanel" aria-labelledby="bp-sugar-tab">
                <div class="report-section">
                    <h4>Blood Pressure & Sugar Report</h4>
                    
                    <?php if (empty($health_data)): ?>
                        <div class="no-data">
                            <h5>No BP or sugar data available yet</h5>
                            <p>Start tracking your BP and sugar levels by visiting the <a href="bp_sugar.php">BP & Sugar Tracker</a>.</p>
                        </div>
                    <?php else: ?>
                        <div class="chart-container">
                            <canvas id="bpChart"></canvas>
                        </div>
                        
                        <div class="chart-container">
                            <canvas id="sugarChart"></canvas>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>BP (Systolic/Diastolic)</th>
                                        <th>Sugar (mg/dL)</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($health_data as $data): ?>
                                        <tr>
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
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Sleep Tab -->
            <div class="tab-pane fade" id="sleep" role="tabpanel" aria-labelledby="sleep-tab">
                <div class="report-section">
                    <h4>Sleep Report</h4>
                    
                    <?php if (empty($sleep_data)): ?>
                        <div class="no-data">
                            <h5>No sleep data available yet</h5>
                            <p>Start tracking your sleep patterns by visiting the <a href="sleep.php">Sleep Tracker</a>.</p>
                        </div>
                    <?php else: ?>
                        <div class="chart-container">
                            <canvas id="sleepChart"></canvas>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
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
                                                    echo '<span class="normal">Good</span>';
                                                } elseif ($duration >= 6 && $duration < 7) {
                                                    echo '<span class="warning">Average</span>';
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
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Workout Tab -->
            <div class="tab-pane fade" id="workout" role="tabpanel" aria-labelledby="workout-tab">
                <div class="report-section">
                    <h4>Workout Report</h4>
                    
                    <?php if (empty($workout_data)): ?>
                        <div class="no-data">
                            <h5>No workout data available yet</h5>
                            <p>Start planning your workouts by visiting the <a href="workout.php">Workout Planner</a>.</p>
                        </div>
                    <?php else: ?>
                        <div class="chart-container">
                            <canvas id="workoutChart"></canvas>
                        </div>
                        
                        <div class="row">
                            <?php foreach($workout_data as $workout): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <?php echo date('F d, Y', strtotime($workout['date'])); ?> - 
                                            <strong><?php echo ucfirst(str_replace('-', ' ', $workout['goal'])); ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">Exercises:</h5>
                                            <ul class="list-group list-group-flush">
                                                <?php foreach($workout['exercises'] as $exercise): ?>
                                                    <li class="list-group-item"><?php echo htmlspecialchars($exercise); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Nutrition Tab -->
            <div class="tab-pane fade" id="nutrition" role="tabpanel" aria-labelledby="nutrition-tab">
                <div class="report-section">
                    <h4>Nutrition Report</h4>
                    
                    <?php if (empty($nutrition_data)): ?>
                        <div class="no-data">
                            <h5>No nutrition data available yet</h5>
                            <p>Start tracking your nutrition by visiting the <a href="nutrition.php">Nutrition Tracker</a>.</p>
                        </div>
                    <?php else: ?>
                        <div class="chart-container">
                            <canvas id="nutritionChart"></canvas>
                        </div>
                        
                        <div class="row">
                            <?php foreach($nutrition_data as $nutrition): ?>
                                <div class="col-md-6 mb-4">
                                    <div class="card">
                                        <div class="card-header">
                                            <?php echo date('F d, Y', strtotime($nutrition['date'])); ?> - 
                                            <strong><?php echo ucfirst($nutrition['meal_type']); ?></strong>
                                        </div>
                                        <div class="card-body">
                                            <h5 class="card-title">Meals:</h5>
                                            <ul class="list-group list-group-flush">
                                                <?php foreach($nutrition['meals'] as $meal): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <?php echo htmlspecialchars($meal['name']); ?>
                                                        <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($meal['calories']); ?> cal</span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                            <div class="mt-3 text-end">
                                                <strong>Total: <?php echo htmlspecialchars($nutrition['total_calories']); ?> cal</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- BMI Tab -->
            <div class="tab-pane fade" id="bmi" role="tabpanel" aria-labelledby="bmi-tab">
                <div class="report-section">
                    <h4>BMI Report</h4>
                    
                    <?php if (empty($bmi_data)): ?>
                        <div class="no-data">
                            <h5>No BMI data available yet</h5>
                            <p>Calculate your BMI by visiting the <a href="bmi_calculator.php">BMI Calculator</a>.</p>
                        </div>
                    <?php else: ?>
                        <div class="chart-container">
                            <canvas id="bmiChart"></canvas>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Height (cm)</th>
                                        <th>Weight (kg)</th>
                                        <th>BMI</th>
                                        <th>Category</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($bmi_data as $data): ?>
                                        <tr>
                                            <td><?php echo date('M d, Y', strtotime($data['date'])); ?></td>
                                            <td><?php echo $data['height']; ?></td>
                                            <td><?php echo $data['weight']; ?></td>
                                            <td><?php echo $data['bmi']; ?></td>
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
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="feedback-section">
            <h4>Submit Your Feedback</h4>
            <?php echo $feedback_message; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <textarea id="feedback" name="feedback" class="form-control" rows="3" placeholder="Enter your feedback..."></textarea>
                <button type="submit" class="btn btn-success mt-2 btn-submit">Submit</button>
            </form>
        </div>
    </div>
    
    <script>
        // Initialize charts if data is available
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!empty($health_data)): ?>
                // BP Chart
                const bpCtx = document.getElementById('bpChart').getContext('2d');
                const bpChart = new Chart(bpCtx, {
                    type: 'line',
                    data: {
                        labels: [<?php echo implode(', ', array_map(function($data) { return "'" . date('M d', strtotime($data['date'])) . "'"; }, array_reverse($health_data))); ?>],
                        datasets: [
                            {
                                label: 'Systolic',
                                data: [<?php echo implode(', ', array_map(function($data) { return $data['bp_systolic']; }, array_reverse($health_data))); ?>],
                                borderColor: 'rgba(255, 99, 132, 1)',
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                tension: 0.1
                            },
                            {
                                label: 'Diastolic',
                                data: [<?php echo implode(', ', array_map(function($data) { return $data['bp_diastolic']; }, array_reverse($health_data))); ?>],
                                borderColor: 'rgba(54, 162, 235, 1)',
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Blood Pressure Trends'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'mmHg'
                                }
                            }
                        }
                    }
                });
                
                // Sugar Chart
                const sugarCtx = document.getElementById('sugarChart').getContext('2d');
                const sugarChart = new Chart(sugarCtx, {
                    type: 'line',
                    data: {
                        labels: [<?php echo implode(', ', array_map(function($data) { return "'" . date('M d', strtotime($data['date'])) . "'"; }, array_reverse($health_data))); ?>],
                        datasets: [
                            {
                                label: 'Blood Sugar',
                                data: [<?php echo implode(', ', array_map(function($data) { return $data['sugar']; }, array_reverse($health_data))); ?>],
                                borderColor: 'rgba(75, 192, 192, 1)',
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Blood Sugar Trends'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'mg/dL'
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>
            
            <?php if (!empty($sleep_data)): ?>
                // Sleep Chart
                const sleepCtx = document.getElementById('sleepChart').getContext('2d');
                const sleepChart = new Chart(sleepCtx, {
                    type: 'line',
                    data: {
                        labels: [<?php echo implode(', ', array_map(function($data) { return "'" . date('M d', strtotime($data['date'])) . "'"; }, array_reverse($sleep_data))); ?>],
                        datasets: [
                            {
                                label: 'Sleep Duration',
                                data: [<?php echo implode(', ', array_map(function($data) { return $data['duration']; }, array_reverse($sleep_data))); ?>],
                                borderColor: 'rgba(153, 102, 255, 1)',
                                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Sleep Duration Trends'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Hours'
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>
            
            <?php if (!empty($workout_data)): ?>
                // Workout Chart
                const workoutCtx = document.getElementById('workoutChart').getContext('2d');
                const workoutData = {
                    labels: ['Weight Loss', 'Muscle Gain', 'Maintain Fitness', 'Regular Exercise'],
                    datasets: [{
                        label: 'Workout Goals',
                        data: [
                            <?php echo count(array_filter($workout_data, function($w) { return $w['goal'] == 'weight-loss'; })); ?>,
                            <?php echo count(array_filter($workout_data, function($w) { return $w['goal'] == 'muscle-gain'; })); ?>,
                            <?php echo count(array_filter($workout_data, function($w) { return $w['goal'] == 'maintain'; })); ?>,
                            <?php echo count(array_filter($workout_data, function($w) { return $w['goal'] == 'regular'; })); ?>
                        ],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)'
                        ],
                        borderWidth: 1
                    }]
                };
                const workoutChart = new Chart(workoutCtx, {
                    type: 'pie',
                    data: workoutData,
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Workout Goals Distribution'
                            }
                        }
                    }
                });
            <?php endif; ?>
            
            <?php if (!empty($nutrition_data)): ?>
                // Nutrition Chart
                const nutritionCtx = document.getElementById('nutritionChart').getContext('2d');
                const nutritionChart = new Chart(nutritionCtx, {
                    type: 'line',
                    data: {
                        labels: [<?php echo implode(', ', array_map(function($data) { return "'" . date('M d', strtotime($data['date'])) . "'"; }, array_reverse($nutrition_data))); ?>],
                        datasets: [
                            {
                                label: 'Calorie Intake',
                                data: [<?php echo implode(', ', array_map(function($data) { return $data['total_calories']; }, array_reverse($nutrition_data))); ?>],
                                borderColor: 'rgba(255, 159, 64, 1)',
                                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'Calorie Intake Trends'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'Calories'
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>
            
            <?php if (!empty($bmi_data)): ?>
                // BMI Chart
                const bmiCtx = document.getElementById('bmiChart').getContext('2d');
                const bmiChart = new Chart(bmiCtx, {
                    type: 'line',
                    data: {
                        labels: [<?php echo implode(', ', array_map(function($data) { return "'" . date('M d', strtotime($data['date'])) . "'"; }, array_reverse($bmi_data))); ?>],
                        datasets: [
                            {
                                label: 'BMI',
                                data: [<?php echo implode(', ', array_map(function($data) { return $data['bmi']; }, array_reverse($bmi_data))); ?>],
                                borderColor: 'rgba(201, 203, 207, 1)',
                                backgroundColor: 'rgba(201, 203, 207, 0.2)',
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            title: {
                                display: true,
                                text: 'BMI Trends'
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: false,
                                title: {
                                    display: true,
                                    text: 'BMI'
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>

