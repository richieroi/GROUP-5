<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM birthday_wishes WHERE user_id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "Wish: " . $row['wish_text'] . " - Created at: " . $row['created_at'] . "<br>";
    }
} else {
    echo "No wishes found!";
}

$conn->close();
?>
