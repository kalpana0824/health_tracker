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
$bmi_result = null;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $date = $_POST['date'];
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);
    
    // Calculate BMI
    $bmi = calculateBMI($weight, $height);
    $category = getBmiCategory($bmi);
    
    $result = saveBmiData($date, $height, $weight, $bmi, $category);
    
    if ($result["success"]) {
        $message = "<div class='alert alert-success'>" . $result["message"] . "</div>";
        $bmi_result = [
            'bmi' => $bmi,
            'category' => $category
        ];
    } else {
        $message = "<div class='alert alert-danger'>" . $result["message"] . "</div>";
    }
}

// Get user data
$user = getUserData();

// Get user's BMI data
$bmi_data = getBmiData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMI Calculator - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
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
        .bmi-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .bmi-header {
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
        .bmi-result {
            margin-top: 20px;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }
        .bmi-value {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .bmi-category {
            font-size: 1.2em;
            margin-bottom: 15px;
        }
        .bmi-underweight {
            background-color: #cce5ff;
            color: #004085;
        }
        .bmi-normal {
            background-color: #d4edda;
            color: #155724;
        }
        .bmi-overweight {
            background-color: #fff3cd;
            color: #856404;
        }
        .bmi-obese {
            background-color: #f8d7da;
            color: #721c24;
        }
        .bmi-history {
            margin-top: 30px;
        }
        .bmi-chart {
            height: 300px;
            margin-top: 30px;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
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
    
    <div class="container bmi-container">
        <h2 class="bmi-header">BMI Calculator</h2>
        <p class="text-center">Calculate your Body Mass Index (BMI) to assess your weight relative to your height.</p>
        
        <?php echo $message; ?>
        
        <div class="row">
            <div class="col-md-6">
                <form id="bmiForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <input type="hidden" id="date" name="date" value="<?php echo date('Y-m-d'); ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Height (cm):</label>
                        <input type="number" id="height" name="height" class="form-control" step="0.1" min="50" max="250" required value="<?php echo $user['height'] ?? ''; ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Weight (kg):</label>
                        <input type="number" id="weight" name="weight" class="form-control" step="0.1" min="20" max="300" required value="<?php echo $user['weight'] ?? ''; ?>">
                    </div>
                    
                    <button type="submit" class="btn btn-success w-100 btn-submit">Calculate BMI</button>
                </form>
                
                <?php if ($bmi_result): ?>
                    <?php
                    $bmi_class = '';
                    if ($bmi_result['category'] == 'Underweight') {
                        $bmi_class = 'bmi-underweight';
                    } elseif ($bmi_result['category'] == 'Normal weight') {
                        $bmi_class = 'bmi-normal';
                    } elseif ($bmi_result['category'] == 'Overweight') {
                        $bmi_class = 'bmi-overweight';
                    } else {
                        $bmi_class = 'bmi-obese';
                    }
                    ?>
                    <div class="bmi-result <?php echo $bmi_class; ?>">
                        <div class="bmi-value"><?php echo $bmi_result['bmi']; ?></div>
                        <div class="bmi-category"><?php echo $bmi_result['category']; ?></div>
                        <p>
                            <?php
                            if ($bmi_result['category'] == 'Underweight') {
                                echo 'You are underweight. Consider consulting with a healthcare professional about a healthy weight gain plan.';
                            } elseif ($bmi_result['category'] == 'Normal weight') {
                                echo 'You have a healthy weight. Maintain your current lifestyle with a balanced diet and regular exercise.';
                            } elseif ($bmi_result['category'] == 'Overweight') {
                                echo 'You are overweight. Consider adopting a healthier diet and increasing physical activity.';
                            } else {
                                echo 'You are in the obese category. It is recommended to consult with a healthcare professional for a weight management plan.';
                            }
                            ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="col-md-6">
                <h4>BMI Categories</h4>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>BMI Range</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-primary">
                            <td>Below 18.5</td>
                            <td>Underweight</td>
                        </tr>
                        <tr class="table-success">
                            <td>18.5 - 24.9</td>
                            <td>Normal weight</td>
                        </tr>
                        <tr class="table-warning">
                            <td>25.0 - 29.9</td>
                            <td>Overweight</td>
                        </tr>
                        <tr class="table-danger">
                            <td>30.0 and above</td>
                            <td>Obese</td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="mt-4">
                    <h4>BMI Formula</h4>
                    <p>BMI = weight(kg) / (height(m))²</p>
                    <p>Example: A person weighing 70kg with a height of 175cm (1.75m) has a BMI of 70 / (1.75)² = 22.9</p>
                </div>
            </div>
        </div>
        
        <div class="bmi-history">
            <h4>Your BMI History</h4>
            
            <?php if (empty($bmi_data)): ?>
                <p class="text-center">No BMI data available yet.</p>
            <?php else: ?>
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
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

