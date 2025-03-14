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
    $gender = $_POST['gender'];
    $age_group = $_POST['age_group'];
    $goal = $_POST['goal'];
    $exercises = json_decode($_POST['exercises'], true);
    
    $result = saveWorkoutData($date, $gender, $age_group, $goal, $exercises);
    
    if ($result["success"]) {
        $message = "<div class='alert alert-success'>" . $result["message"] . "</div>";
    } else {
        $message = "<div class='alert alert-danger'>" . $result["message"] . "</div>";
    }
}

// Get user data
$user = getUserData();

// Get user's workout data
$workout_data = getWorkoutData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workout Planner - Health Tracker</title>
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
        .workout-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .workout-header {
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
        .workout-history {
            margin-top: 30px;
        }
        .workout-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            padding: 15px;
        }
        .workout-date {
            font-size: 0.9em;
            color: #6c757d;
        }
        .workout-goal {
            font-weight: bold;
            color: #28a745;
        }
        .exercise-list {
            margin-top: 10px;
        }
        .exercise-item {
            background: #f8f9fa;
            padding: 8px 12px;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        #workoutPlan {
            margin-top: 20px;
            padding: 15px;
            border-radius: 10px;
            background-color: #f8f9fa;
            display: none;
        }
        #workoutPlan.show {
            display: block;
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
    
    <div class="container workout-container">
        <h2 class="workout-header">Workout Planner</h2>
        <p class="text-center">Select your gender, age, and fitness goal to get a personalized workout plan.</p>
        
        <?php echo $message; ?>
        
        <form id="workoutForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" id="exercises" name="exercises" value="">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date:</label>
                    <input type="date" id="date" name="date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Select Gender:</label>
                    <select id="gender" name="gender" class="form-select" required>
                        <option value="male" <?php echo ($user['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                        <option value="female" <?php echo ($user['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Select Age Group:</label>
                    <select id="age_group" name="age_group" class="form-select" required>
                        <option value="18-25" <?php echo ($user['age'] >= 18 && $user['age'] <= 25) ? 'selected' : ''; ?>>18-25</option>
                        <option value="26-40" <?php echo ($user['age'] >= 26 && $user['age'] <= 40) ? 'selected' : ''; ?>>26-40</option>
                        <option value="41-60" <?php echo ($user['age'] >= 41 && $user['age'] <= 60) ? 'selected' : ''; ?>>41-60</option>
                        <option value="60+" <?php echo ($user['age'] > 60) ? 'selected' : ''; ?>>60+</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Select Fitness Goal:</label>
                    <select id="goal" name="goal" class="form-select" required>
                        <option value="weight-loss">Weight Loss</option>
                        <option value="muscle-gain">Muscle Gain</option>
                        <option value="maintain">Maintain Fitness</option>
                        <option value="regular">Regular Exercise</option>
                    </select>
                </div>
            </div>
            
            <button type="button" class="btn btn-success w-100 mb-4" onclick="generateWorkout()">Generate Workout Plan</button>
            
            <div id="workoutPlan" class="mb-4"></div>
            
            <button type="submit" id="saveButton" class="btn btn-primary w-100 btn-submit" style="display: none;">Save Workout Plan</button>
        </form>
        
        <div class="workout-history">
            <h4>Your Workout History</h4>
            
            <?php if (empty($workout_data)): ?>
                <p class="text-center">No workout data available yet.</p>
            <?php else: ?>
                <?php foreach($workout_data as $workout): ?>
                    <div class="workout-card">
                        <div class="workout-date"><?php echo date('F d, Y', strtotime($workout['date'])); ?></div>
                        <div class="workout-goal">Goal: <?php echo ucfirst(str_replace('-', ' ', $workout['goal'])); ?></div>
                        <div class="exercise-list">
                            <?php foreach($workout['exercises'] as $exercise): ?>
                                <div class="exercise-item">
                                    <?php echo htmlspecialchars($exercise); ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        // Define workout plans for different genders, age groups, and goals
        const workouts = {
            male: {
                "18-25": { 
                    "weight-loss": ["Jump Rope (20 min)", "Running (30 min)", "HIIT (15 min)", "Burpees (3 sets of 10)", "Mountain Climbers (3 sets of 20)"], 
                    "muscle-gain": ["Push-ups (4 sets of 15)", "Deadlifts (4 sets of 8)", "Bench Press (4 sets of 10)", "Pull-ups (3 sets of 8)", "Squats (4 sets of 12)"], 
                    "maintain": ["Cycling (30 min)", "Planks (3 sets of 45 sec)", "Squats (3 sets of 15)", "Lunges (3 sets of 10 each leg)", "Push-ups (3 sets of 10)"], 
                    "regular": ["Jogging (20 min)", "Yoga (15 min)", "Stretching (10 min)", "Light Dumbbell Exercises (15 min)", "Bodyweight Squats (3 sets of 15)"] 
                },
                "26-40": { 
                    "weight-loss": ["Zumba (30 min)", "Treadmill (25 min)", "Pilates (20 min)", "Jumping Jacks (3 sets of 20)", "High Knees (3 sets of 30 sec)"], 
                    "muscle-gain": ["Weight Training (40 min)", "Pull-ups (3 sets of 8)", "Dumbbells (4 sets of 12)", "Bench Press (4 sets of 8)", "Deadlifts (3 sets of 10)"], 
                    "maintain": ["Walking (30 min)", "Planks (3 sets of 30 sec)", "Yoga (20 min)", "Light Jogging (15 min)", "Bodyweight Exercises (15 min)"], 
                    "regular": ["Meditation (10 min)", "Stretching (15 min)", "Swimming (20 min)", "Light Cardio (15 min)", "Core Exercises (10 min)"] 
                },
                "41-60": { 
                    "weight-loss": ["Walking (40 min)", "Cycling (30 min)", "Swimming (25 min)", "Light Aerobics (20 min)", "Stretching (15 min)"], 
                    "muscle-gain": ["Light Weights (30 min)", "Stretching (15 min)", "Yoga (20 min)", "Resistance Band Exercises (15 min)", "Bodyweight Squats (3 sets of 12)"], 
                    "maintain": ["Yoga (30 min)", "Dancing (20 min)", "Pilates (20 min)", "Light Walking (30 min)", "Stretching (15 min)"], 
                    "regular": ["Meditation (15 min)", "Light Jogging (20 min)", "Tai Chi (20 min)", "Walking (30 min)", "Gentle Stretching (15 min)"] 
                },
                "60+": { 
                    "weight-loss": ["Walking (30 min)", "Water Aerobics (25 min)", "Chair Exercises (15 min)", "Light Dancing (15 min)", "Stretching (15 min)"], 
                    "muscle-gain": ["Light Resistance Bands (20 min)", "Chair Squats (3 sets of 10)", "Wall Push-ups (3 sets of 8)", "Seated Arm Exercises (15 min)", "Ankle Weights (10 min)"], 
                    "maintain": ["Gentle Yoga (25 min)", "Walking (20 min)", "Balance Exercises (15 min)", "Light Stretching (15 min)", "Seated Exercises (15 min)"], 
                    "regular": ["Tai Chi (20 min)", "Gentle Stretching (15 min)", "Walking (20 min)", "Chair Exercises (15 min)", "Breathing Exercises (10 min)"] 
                }
            },
            female: {
                "18-25": { 
                    "weight-loss": ["Jump Rope (20 min)", "HIIT (15 min)", "Running (25 min)", "Dancing (30 min)", "Circuit Training (20 min)"], 
                    "muscle-gain": ["Lunges (4 sets of 12)", "Pilates (25 min)", "Squats (4 sets of 15)", "Resistance Band Exercises (20 min)", "Push-ups (3 sets of 10)"], 
                    "maintain": ["Cycling (25 min)", "Yoga (30 min)", "Stretching (15 min)", "Light Weights (20 min)", "Bodyweight Exercises (15 min)"], 
                    "regular": ["Jogging (20 min)", "Zumba (30 min)", "Planks (3 sets of 30 sec)", "Bodyweight Squats (3 sets of 12)", "Stretching (15 min)"] 
                },
                "26-40": { 
                    "weight-loss": ["Zumba (30 min)", "Cardio (25 min)", "Jump Rope (15 min)", "HIIT (20 min)", "Dancing (30 min)"], 
                    "muscle-gain": ["Weight Training (30 min)", "Dumbbells (4 sets of 12)", "Resistance Bands (20 min)", "Squats (4 sets of 15)", "Lunges (3 sets of 12 each leg)"], 
                    "maintain": ["Planks (3 sets of 30 sec)", "Walking (30 min)", "Yoga (25 min)", "Light Cardio (20 min)", "Stretching (15 min)"], 
                    "regular": ["Stretching (20 min)", "Dancing (25 min)", "Swimming (30 min)", "Light Jogging (20 min)", "Pilates (20 min)"] 
                },
                "41-60": { 
                    "weight-loss": ["Walking (35 min)", "Yoga (25 min)", "Tai Chi (20 min)", "Light Dancing (25 min)", "Swimming (30 min)"], 
                    "muscle-gain": ["Light Resistance (25 min)", "Stretching (20 min)", "Pilates (25 min)", "Bodyweight Exercises (15 min)", "Resistance Bands (20 min)"], 
                    "maintain": ["Swimming (25 min)", "Stretching (20 min)", "Meditation (15 min)", "Light Yoga (25 min)", "Walking (30 min)"], 
                    "regular": ["Light Yoga (30 min)", "Tai Chi (25 min)", "Dancing (20 min)", "Walking (30 min)", "Gentle Stretching (15 min)"] 
                },
                "60+": { 
                    "weight-loss": ["Walking (25 min)", "Chair Yoga (20 min)", "Water Aerobics (25 min)", "Light Dancing (15 min)", "Stretching (15 min)"], 
                    "muscle-gain": ["Seated Exercises (20 min)", "Light Resistance Bands (15 min)", "Wall Push-ups (3 sets of 8)", "Chair Squats (3 sets of 10)", "Ankle Weights (10 min)"], 
                    "maintain": ["Gentle Yoga (25 min)", "Walking (20 min)", "Balance Exercises (15 min)", "Chair Exercises (15 min)", "Stretching (15 min)"], 
                    "regular": ["Tai Chi (20 min)", "Gentle Stretching (20 min)", "Seated Exercises (15 min)", "Walking (20 min)", "Breathing Exercises (10 min)"] 
                }
            }
        };
        
        function generateWorkout() {
            let gender = document.getElementById("gender").value;
            let ageGroup = document.getElementById("age_group").value;
            let goal = document.getElementById("goal").value;
            
            // Check if the workout plan exists for the selected combination
            if (workouts[gender] && 
                workouts[gender][ageGroup] && 
                workouts[gender][ageGroup][goal]) {
                
                let plan = workouts[gender][ageGroup][goal];
                
                let workoutPlanDiv = document.getElementById("workoutPlan");
                workoutPlanDiv.innerHTML = "<h4 class='text-center mb-3'>Your Personalized Workout Plan</h4><ul class='list-group'>";
                
                plan.forEach((exercise) => {
                    let youtubeLink = `https://www.youtube.com/results?search_query=${encodeURIComponent(exercise)}+exercise+tutorial`;
                    workoutPlanDiv.innerHTML += `<li class='list-group-item d-flex justify-content-between align-items-center'>
                        <div>
                            ${exercise} <br>
                            <a href='${youtubeLink}' target='_blank' class="text-decoration-none"><i class="fab fa-youtube text-danger"></i> Watch Tutorial</a>
                        </div>
                    </li>`;
                });
                
                workoutPlanDiv.innerHTML += "</ul>";
                
                // Show workout plan and save button
                workoutPlanDiv.classList.add("show");
                document.getElementById("saveButton").style.display = "block";
                
                // Store exercises in hidden input
                document.getElementById("exercises").value = JSON.stringify(plan);
            } else {
                // Handle case where workout plan doesn't exist
                let workoutPlanDiv = document.getElementById("workoutPlan");
                workoutPlanDiv.innerHTML = "<div class='alert alert-warning'>No workout plan available for the selected combination. Please try different options.</div>";
                workoutPlanDiv.classList.add("show");
                document.getElementById("saveButton").style.display = "none";
            }
        }
    </script>
</body>
</html>
