<?php
require_once(__DIR__ . '/vendor/autoload.php');

// Load environment variables from .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'roi');
define('DB_PASS', '1234');
define('DB_NAME', 'birthday_bliss');

// ClickSend API credentials
define('CLICKSEND_USERNAME', $_ENV['CLICKSEND_USERNAME']);
define('CLICKSEND_API_KEY', $_ENV['CLICKSEND_API_KEY']);

// Establish database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . $conn->connect_error);
}
?>