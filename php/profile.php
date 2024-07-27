<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Database connection
$conn = new mysqli("localhost", "roi", "1234", "birthday_bliss");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch user information
$stmt = $conn->prepare("SELECT username, email, dob FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();
$stmt->close();

// Fetch user profile information
$stmt = $conn->prepare("SELECT birthdate, phone FROM user_profiles WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_profile = $stmt->get_result();
$user_profile = $result_profile->fetch_assoc();
$stmt->close();

// Fetch user's wishes
$stmt = $conn->prepare("SELECT wish_text, created_at FROM birthday_wishes WHERE user_id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_wishes = $stmt->get_result();
$stmt->close();

// Calculate if the birthday is upcoming
$today = new DateTime();
$birthdate = new DateTime($user_profile['birthdate']);

// Extract month and day
$today_month_day = $today->format('m-d');
$birthdate_month_day = $birthdate->format('m-d');

// Check if the birthday is today or within the next 30 days
$is_upcoming = false;
for ($i = 0; $i <= 30; $i++) {
    $check_date = clone $today;
    $check_date->modify("+$i day");
    if ($check_date->format('m-d') == $birthdate_month_day) {
        $is_upcoming = true;
        break;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        nav {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }
        nav a {
            color: #fff;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            text-decoration: underline;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h1 {
            color: #333;
        }
        p {
            color: #555;
        }
        .wishes {
            margin-top: 20px;
        }
        .wish {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .wish p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <nav>
        <a href="index.php">Home</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="login.php">Logout</a>
    </nav>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($user['username'] ?? ''); ?>!</h1>
        <p>Email: <?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
        <p>Phone Number: <?php echo htmlspecialchars($user_profile['phone'] ?? ''); ?></p>
        <!-- <p>Date of Birth: <?php echo htmlspecialchars($user['dob'] ?? ''); ?></p> -->
        <p>Birthdate: <?php echo htmlspecialchars($user_profile['birthdate'] ?? ''); ?></p>
        <p>Birthday Status: <?php echo $is_upcoming ? 'Upcoming' : 'Not Upcoming'; ?></p>
        <div class="wishes">
            <?php
            if ($result_wishes->num_rows > 0) {
                echo "<h2>Your Birthday Wishes:</h2>";
                while ($row = $result_wishes->fetch_assoc()) {
                    echo "<div class='wish'>";
                    echo "<p>Wish: " . htmlspecialchars($row['wish_text']) . "</p>";
                    echo "<p>Created at: " . htmlspecialchars($row['created_at']) . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p>No wishes found!</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>