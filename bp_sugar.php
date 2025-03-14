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
    $bp = $_POST['bp'];
    list($systolic, $diastolic) = explode('/', $bp);
    $sugar = $_POST['sugar'];
    
    $result = saveHealthData($date, $systolic, $diastolic, $sugar);
    
    if ($result["success"]) {
        $message = "<div class='alert alert-success'>" . $result["message"] . "</div>";
    } else {
        $message = "<div class='alert alert-danger'>" . $result["message"] . "</div>";
    }
}

// Get user's health data
$health_data = getHealthData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BP & Sugar Tracker - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f4;
            background-image: url('images/bg.jpg'); //no-repeat center center/cover;
            background-attachment: fixed;
            background-size: cover;  /* Ensures the image covers the full page */
             background-position: center; /* Centers the image */
           background-repeat: no-repeat; /* Prevents image repetition */
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
        .tracker-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .tracker-header {
            text-align: center;
            margin-bottom: 30px;
            color: #28a745;
        }
        .result { 
            font-weight: bold; 
            color: #28a745;
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
        .normal {
            color: #28a745;
        }
        .warning {
            color: #ffc107;
        }
        .danger {
            color: #dc3545;
        }
        .btn-submit {
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-submit:hover {
            background-color: #218838;
            border-color: #218838;
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
    
    <div class="container tracker-container">
        <h2 class="tracker-header">BP & Sugar Tracker</h2>
        <?php echo $message; ?>
        <div class="row">
            <div class="col-md-5">
                <form id="bpSugarForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="mb-3">
                        <label class="form-label">Date</label>
                        <input type="date" class="form-control" id="date" name="date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Blood Pressure (Systolic/Diastolic)</label>
                        <input type="text" class="form-control" id="bp" name="bp" placeholder="e.g. 120/80" required pattern="\d+/\d+">
                        <div class="form-text">Format: Systolic/Diastolic (e.g., 120/80)</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Blood Sugar Level (mg/dL)</label>
                        <input type="number" class="form-control" id="sugar" name="sugar" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100 btn-submit">Save & Analyze</button>
                </form>
                <p class="text-center mt-3 result" id="analysisResult"></p>
            </div>
            
            <div class="col-md-7 table-container">
                <h4>Your Recent Records</h4>
                <table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>BP (Systolic/Diastolic)</th>
                            <th>Sugar (mg/dL)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="dataTable">
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
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('bpSugarForm').addEventListener('submit', function(e) {
                // We'll still submit the form to server, but also show immediate analysis
                analyzeData();
            });
        });
        
        function analyzeData() {
            let bp = document.getElementById("bp").value;
            let sugar = parseInt(document.getElementById("sugar").value);
            let resultText = "";
            let [systolic, diastolic] = bp.split("/").map(Number);

            if (systolic < 90 || diastolic < 60) {
                resultText += "Low Blood Pressure. ";
            } else if (systolic > 130 || diastolic > 80) {
                resultText += "High Blood Pressure. ";
            } else {
                resultText += "Blood Pressure is Normal. ";
            }

            if (sugar < 70) {
                resultText += "Low Blood Sugar.";
            } else if (sugar > 140) {
                resultText += "High Blood Sugar.";
            } else {
                resultText += "Blood Sugar is Normal.";
            }

            document.getElementById("analysisResult").innerText = resultText;
        }
    </script>
</body>
</html>

