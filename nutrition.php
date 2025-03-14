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
    $meal_type = $_POST['meal_type'];
    $selected_meals = isset($_POST['selected_meals']) ? json_decode($_POST['selected_meals'], true) : [];
    $total_calories = isset($_POST['total_calories']) ? intval($_POST['total_calories']) : 0;
    
    $result = saveNutritionData($date, $meal_type, $selected_meals, $total_calories);
    
    if ($result["success"]) {
        $message = "<div class='alert alert-success'>" . $result["message"] . "</div>";
    } else {
        $message = "<div class='alert alert-danger'>" . $result["message"] . "</div>";
    }
}

// Get user's nutrition data
$nutrition_data = getNutritionData();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nutrition Plan - Health Tracker</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
        .nutrition-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .nutrition-header {
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
        .nutrition-history {
            margin-top: 30px;
        }
        .nutrition-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            padding: 15px;
        }
        .nutrition-date {
            font-size: 0.9em;
            color: #6c757d;
        }
        .nutrition-type {
            font-weight: bold;
            color: #28a745;
        }
        .meal-list {
            margin-top: 10px;
        }
        .meal-item {
            background: #f8f9fa;
            padding: 8px 12px;
            margin-bottom: 5px;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
        }
        .meal-calories {
            font-weight: bold;
        }
        .total-calories {
            font-weight: bold;
            color: #dc3545;
            margin-top: 10px;
            text-align: right;
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
    
    <div class="container nutrition-container">
        <h2 class="nutrition-header">Nutrition Plan</h2>
        <p class="text-center">Select your meal preferences and calculate total calorie intake.</p>
        
        <?php echo $message; ?>
        
        <form id="nutritionForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" id="selected_meals" name="selected_meals" value="">
            <input type="hidden" id="total_calories" name="total_calories" value="0">
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date:</label>
                    <input type="date" id="date" name="date" class="form-control" required value="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Select Meal Type:</label>
                    <select id="meal_type" name="meal_type" class="form-select" onchange="loadMeals()">
                        <option value="vegan">Vegan</option>
                        <option value="vegetarian">Vegetarian</option>
                        <option value="non-vegetarian">Non-Vegetarian</option>
                    </select>
                </div>
            </div>
            
            <div class="mt-3" id="mealOptions"></div>
            
            <div class="text-center mt-3">
                <p class="fw-bold" id="caloriesResult">Total Calories: 0 cal</p>
            </div>
            
            <button type="submit" class="btn btn-success w-100 mt-3 btn-submit">Save Nutrition Data</button>
        </form>
        
        <div class="nutrition-history">
            <h4>Your Nutrition History</h4>
            
            <?php if (empty($nutrition_data)): ?>
                <p class="text-center">No nutrition data available yet.</p>
            <?php else: ?>
                <?php foreach($nutrition_data as $nutrition): ?>
                    <div class="nutrition-card">
                        <div class="nutrition-date"><?php echo date('F d, Y', strtotime($nutrition['date'])); ?></div>
                        <div class="nutrition-type">Meal Type: <?php echo ucfirst($nutrition['meal_type']); ?></div>
                        <div class="meal-list">
                            <?php foreach($nutrition['meals'] as $meal): ?>
                                <div class="meal-item">
                                    <span><?php echo htmlspecialchars($meal['name']); ?></span>
                                    <span class="meal-calories"><?php echo htmlspecialchars($meal['calories']); ?> cal</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="total-calories">Total: <?php echo htmlspecialchars($nutrition['total_calories']); ?> cal</div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        const meals = {
            vegan: [
                { name: "Tofu Salad", calories: 150 },
                { name: "Vegan Burger", calories: 300 },
                { name: "Quinoa Bowl", calories: 250 },
                { name: "Chickpea Curry", calories: 200 },
                { name: "Lentil Soup", calories: 180 },
                { name: "Smoothie Bowl", calories: 220 },
                { name: "Vegan Pancakes", calories: 270 },
                { name: "Grilled Vegetables", calories: 160 },
                { name: "Vegan Sushi", calories: 210 },
                { name: "Nut & Fruit Mix", calories: 190 }
            ],
            vegetarian: [
                { name: "Paneer Tikka", calories: 250 },
                { name: "Dal Tadka", calories: 220 },
                { name: "Vegetable Biryani", calories: 320 },
                { name: "Rajma Chawal", calories: 290 },
                { name: "Palak Paneer", calories: 260 },
                { name: "Aloo Paratha", calories: 330 },
                { name: "Masala Dosa", calories: 300 },
                { name: "Chole Bhature", calories: 450 },
                { name: "Matar Paneer", calories: 270 },
                { name: "Stuffed Capsicum", calories: 200 }
            ],
            "non-vegetarian": [
                { name: "Grilled Chicken", calories: 350 },
                { name: "Fish Curry", calories: 280 },
                { name: "Egg Bhurji", calories: 200 },
                { name: "Chicken Biryani", calories: 450 },
                { name: "Prawn Masala", calories: 320 },
                { name: "Mutton Curry", calories: 500 },
                { name: "Omelette", calories: 180 },
                { name: "Tandoori Chicken", calories: 400 },
                { name: "Beef Stir Fry", calories: 370 },
                { name: "Fish Tikka", calories: 290 }
            ]
        };

        function loadMeals() {
            let mealType = document.getElementById("meal_type").value;
            let mealOptionsDiv = document.getElementById("mealOptions");
            mealOptionsDiv.innerHTML = "";
            
            meals[mealType].forEach((meal, index) => {
                mealOptionsDiv.innerHTML += `<div class='form-check'>
                    <input class='form-check-input meal-checkbox' type='checkbox' value='${meal.calories}' id='meal${index}' data-name="${meal.name}" data-calories="${meal.calories}">
                    <label class='form-check-label' for='meal${index}'> ${meal.name} (${meal.calories} cal) </label>
                </div>`;
            });
            
            // Add event listeners to checkboxes
            document.querySelectorAll('.meal-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', calculateCalories);
            });
        }
        
        function calculateCalories() {
            let checkboxes = document.querySelectorAll(".meal-checkbox:checked");
            let totalCalories = 0;
            let selectedMeals = [];
            
            checkboxes.forEach(checkbox => {
                totalCalories += parseInt(checkbox.value);
                selectedMeals.push({
                    name: checkbox.dataset.name,
                    calories: parseInt(checkbox.dataset.calories)
                });
            });
            
            document.getElementById("caloriesResult").innerText = `Total Calories: ${totalCalories} cal`;
            document.getElementById("total_calories").value = totalCalories;
            document.getElementById("selected_meals").value = JSON.stringify(selectedMeals);
        }
        
        // Load meals on page load
        window.onload = loadMeals;
    </script>
</body>
</html>

