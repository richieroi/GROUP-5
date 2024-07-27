<?php
// dashboard.php
require 'config.php';

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Use the existing connection from config.php
global $conn;

// Fetch user data from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT users.username, user_profiles.full_name, users.email 
        FROM users 
        JOIN user_profiles ON users.id = user_profiles.user_id 
        WHERE users.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $username = $user['username'];
    // $full_name = $user['full_name'];
    $email = $user['email'];
    // Display user data or perform other actions
} else {
    echo "";
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        h1 {
            color: #333;
        }
        ul {
            list-style: none;
            padding: 0;
        }
        ul li {
            display: inline;
            margin-right: 10px;
        }
        ul li a {
            text-decoration: none;
            color: #007BFF;
            padding: 5px 10px;
            border: 1px solid #007BFF;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }
        ul li a:hover {
            background-color: #007BFF;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        <ul>
            <li><a href="profile.php">Profile</a></li>
            <li><a href="add_wish.php">Add Wish</a></li>
            <li><a href="view_wishes.php">Saved Wishes</a></li>
            <li><a href="edit_wish.php">Edit Wish</a></li>
            <li><a href="about_us.php">About Us</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</body>
</html>