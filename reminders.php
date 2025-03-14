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
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'add') {
            $title = $_POST['title'];
            $description = $_POST['description'];
            $reminder_date = $_POST['reminder_date'];
            $reminder_time = $_POST['reminder_time'];
            
            $result = saveReminder($title, $description, $reminder_date, $reminder_time);
            
            if ($result["success"]) {
                $message = "<div class='alert alert-success'>" . $result["message"] . "</div>";
            } else {
                $message = "<div class='alert alert-danger'>" . $result["message"] . "</div>";
            }
        } elseif ($_POST['action'] == 'complete') {
            $reminder_id = $_POST['reminder_id'];
            
            $result = completeReminder($reminder_id);
            
            if ($result["success"]) {
                $message = "<div class='alert alert-success'>" . $result["message"] . "</div>";
            } else {
                $message = "<div class='alert alert-danger'>" . $result["message"] . "</div>";
            }
        } elseif ($_POST['action'] == 'delete') {
            $reminder_id = $_POST['reminder_id'];
            
            $result = deleteReminder($reminder_id);
            
            if ($result["success"]) {
                $message = "<div class='alert alert-success'>" . $result["message"] . "</div>";
            } else {
                $message = "<div class='alert alert-danger'>" . $result["message"] . "</div>";
            }
        }
    }
}

// Get user's reminders
$reminders = getReminders();

// Separate reminders into upcoming and completed
$upcoming_reminders = array_filter($reminders, function($reminder) {
    return !$reminder['is_completed'];
});

$completed_reminders = array_filter($reminders, function($reminder) {
    return $reminder['is_completed'];
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminders - Health Tracker</title>
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
        .reminders-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .reminders-header {
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
        .reminder-card {
            background: #f8f9fa;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 0 5px 5px 0;
            position: relative;
        }
        .reminder-card.completed {
            border-left-color: #6c757d;
            opacity: 0.7;
        }
        .reminder-title {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 5px;
        }
        .reminder-description {
            margin-bottom: 10px;
        }
        .reminder-datetime {
            font-size: 0.9em;
            color: #6c757d;
        }
        .reminder-actions {
            position: absolute;
            top: 15px;
            right: 15px;
        }
        .reminder-actions button {
            background: none;
            border: none;
            font-size: 1.1em;
            margin-left: 10px;
            cursor: pointer;
        }
        .complete-btn {
            color: #28a745;
        }
        .delete-btn {
            color: #dc3545;
        }
        .no-reminders {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a href="dashboard.php" class="dashboard-link">🏠 Dashboard</a>
            <div class="d-flex">
                <a href="profile.php" class="profile-icon">👤 Profile</a>
                <a href="logout.php" class="logout">🚪 Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container reminders-container">
        <h2 class="reminders-header">Health Reminders</h2>
        <p class="text-center">Set reminders for medications, check-ups, and other health-related tasks.</p>
        
        <?php echo $message; ?>
        
        <div class="row mb-4">
            <div class="col-md-12">
                <button class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addReminderModal">
                    <i class="fas fa-plus-circle me-2"></i> Add New Reminder
                </button>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <h4>Upcoming Reminders</h4>
                
                <?php if (empty($upcoming_reminders)): ?>
                    <div class="no-reminders">
                        <p>No upcoming reminders. Add a new reminder to get started.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($upcoming_reminders as $reminder): ?>
                        <div class="reminder-card">
                            <div class="reminder-title"><?php echo htmlspecialchars($reminder['title']); ?></div>
                            <div class="reminder-description"><?php echo htmlspecialchars($reminder['description']); ?></div>
                            <div class="reminder-datetime">
                                <i class="far fa-calendar-alt me-1"></i> <?php echo date('M d, Y', strtotime($reminder['reminder_date'])); ?> at 
                                <i class="far fa-clock ms-1 me-1"></i> <?php echo date('h:i A', strtotime($reminder['reminder_time'])); ?>
                            </div>
                            <div class="reminder-actions">
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="complete">
                                    <input type="hidden" name="reminder_id" value="<?php echo $reminder['id']; ?>">
                                    <button type="submit" class="complete-btn" title="Mark as Completed">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                </form>
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="reminder_id" value="<?php echo $reminder['id']; ?>">
                                    <button type="submit" class="delete-btn" title="Delete Reminder" onclick="return confirm('Are you sure you want to delete this reminder?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <h4>Completed Reminders</h4>
                
                <?php if (empty($completed_reminders)): ?>
                    <div class="no-reminders">
                        <p>No completed reminders yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($completed_reminders as $reminder): ?>
                        <div class="reminder-card completed">
                            <div class="reminder-title"><?php echo htmlspecialchars($reminder['title']); ?></div>
                            <div class="reminder-description"><?php echo htmlspecialchars($reminder['description']); ?></div>
                            <div class="reminder-datetime">
                                <i class="far fa-calendar-alt me-1"></i> <?php echo date('M d, Y', strtotime($reminder['reminder_date'])); ?> at 
                                <i class="far fa-clock ms-1 me-1"></i> <?php echo date('h:i A', strtotime($reminder['reminder_time'])); ?>
                            </div>
                            <div class="reminder-actions">
                                <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" style="display: inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="reminder_id" value="<?php echo $reminder['id']; ?>">
                                    <button type="submit" class="delete-btn" title="Delete Reminder" onclick="return confirm('Are you sure you want to delete this reminder?');">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Add Reminder Modal -->
    <div class="modal fade" id="addReminderModal" tabindex="-1" aria-labelledby="addReminderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addReminderModalLabel">Add New Reminder</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addReminderForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label class="form-label">Title:</label>
                            <input type="text" id="title" name="title" class="form-control" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Description:</label>
                            <textarea id="description" name="description" class="form-control" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Date:</label>
                            <input type="date" id="reminder_date" name="reminder_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Time:</label>
                            <input type="time" id="reminder_time" name="reminder_time" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-success w-100 btn-submit">Save Reminder</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

