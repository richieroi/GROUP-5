<?php
require 'config.php';
session_start();

$message = '';
$wish_text = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['wish_text'])) {
    $id = $_POST['id'];
    $wish_text = $_POST['wish_text'];

    // Get database connection
    $conn = new mysqli('localhost', 'roi', '1234', 'birthday_bliss');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("UPDATE birthday_wishes SET wish_text=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sii", $wish_text, $id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $message = "Wish updated successfully.";
    } else {
        $message = "Error updating wish: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];

    // Get database connection
    $conn = new mysqli('localhost', 'roi', '1234', 'birthday_bliss');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("DELETE FROM birthday_wishes WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $message = "Wish deleted successfully.";
    } else {
        $message = "Error deleting wish: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Fetch the selected wish
    $conn = new mysqli('localhost', 'roi', '1234', 'birthday_bliss');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT wish_text FROM birthday_wishes WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($wish_text);
    $stmt->fetch();

    $stmt->close();
    $conn->close();
} else {
    // Fetch all wishes for the user
    $conn = new mysqli('localhost', 'roi', '1234', 'birthday_bliss');

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT id, wish_text FROM birthday_wishes WHERE user_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($id, $wish_text);

    $wishes = [];
    while ($stmt->fetch()) {
        $wishes[] = ['id' => $id, 'wish_text' => $wish_text];
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Wish</title>
</head>
<body>
    <div class="container">
        <h2>Edit Wish</h2>
        <?php if ($message): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id']) && !isset($_POST['wish_text'])): ?>
            <form action="edit_wish.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                <div class="form-group">
                    <label for="wish_text">Wish Text:</label>
                    <textarea id="wish_text" name="wish_text" required><?php echo htmlspecialchars($wish_text); ?></textarea>
                </div>
                <button type="submit">Update Wish</button>
            </form>
        <?php else: ?>
            <form action="edit_wish.php" method="post">
                <div class="form-group">
                    <label for="wish_select">Select Wish to Edit:</label>
                    <select id="wish_select" name="id" required>
                        <?php foreach ($wishes as $wish): ?>
                            <option value="<?php echo htmlspecialchars($wish['id']); ?>"><?php echo htmlspecialchars($wish['wish_text']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Edit Wish</button>
            </form>
            <form action="edit_wish.php" method="post">
                <div class="form-group">
                    <label for="delete_select">Select Wish to Delete:</label>
                    <select id="delete_select" name="delete_id" required>
                        <?php foreach ($wishes as $wish): ?>
                            <option value="<?php echo htmlspecialchars($wish['id']); ?>"><?php echo htmlspecialchars($wish['wish_text']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit">Delete Wish</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>