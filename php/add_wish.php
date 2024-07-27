<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $wish_text = $_POST['wish_text'];

    // Use prepared statements to prevent SQL injection
    $sql = "INSERT INTO birthday_wishes (user_id, wish_text) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE wish_text = VALUES(wish_text)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $wish_text);

    if ($stmt->execute()) {
        echo "Wish added successfully!";
        header("Location: dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Custom Birthday Wish</title>
</head>
<body>
    <h1>Add Your Custom Birthday Wish</h1>
    <form action="add_wish.php" method="post">
        <label for="wish_text">Custom Wish:</label>
        <textarea id="wish_text" name="wish_text" required></textarea><br><br>
        <input type="submit" value="Save Wish">
    </form>
</body>
</html>