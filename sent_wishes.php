<?php
require 'config.php';

// Get database connection
$conn = new mysqli('localhost', 'roi', '1234', 'birthday_bliss');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch sent wishes
$sql_sent_wishes = "
    SELECT up.full_name, up.phone, bw.wish_text AS custom_wish, bw.sent_at
    FROM user_profiles up
    JOIN birthday_wishes bw ON up.user_id = bw.user_id
    WHERE bw.sent_at IS NOT NULL
    ORDER BY bw.sent_at DESC
";
$result = $conn->query($sql_sent_wishes);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Sent Birthday Wishes</title>
</head>
<body>
    <h1>Sent Birthday Wishes</h1>
    <table border="1">
        <tr>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Wish</th>
            <th>Sent At</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['full_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                echo "<td>" . htmlspecialchars($row['custom_wish']) . "</td>";
                echo "<td>" . htmlspecialchars($row['sent_at']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No sent wishes found.</td></tr>";
        }
        ?>
    </table>
</body>
</html>
<?php
$conn->close();
?>