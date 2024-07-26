<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page - Send Birthday Wishes</title>
</head>
<body>
    <h1>Admin Page - Send Birthday Wishes</h1>
    <form action="send_wish.php" method="post">
        <button type="submit">Send Birthday Wishes</button>
    </form>

    <h2>Sent Birthday Wishes</h2>
    <table border="1">
        <tr>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Wish Text</th>
            <th>Sent At</th>
        </tr>
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

        $conn->close();
        ?>
    </table>
</body>
</html>