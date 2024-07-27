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

// Get today's date
$today = date('m-d');

// Fetch users with today's birthdate
$sql = "SELECT u.phone, u.full_name, w.wish_text 
        FROM user_profiles u 
        JOIN birthday_wishes w ON u.user_id = w.user_id 
        WHERE DATE_FORMAT(u.birthdate, '%m-%d') = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Configure ClickSend API
    $config = \ClickSend\Configuration::getDefaultConfiguration()
        ->setUsername(CLICKSEND_USERNAME)
        ->setPassword(CLICKSEND_API_KEY);

    $apiInstance = new \ClickSend\Api\SMSApi(new \GuzzleHttp\Client(), $config);

    $sms_messages = new \ClickSend\Model\SmsMessageCollection();
    $messages = [];

    while ($row = $result->fetch_assoc()) {
        $phone = $row['phone'];
        // Validate phone number format
        if (preg_match('/^\+\d{10,15}$/', $phone)) {
            $msg = new \ClickSend\Model\SmsMessage();
            $msg->setSource("Birthday Bliss");
            $msg->setBody($row['wish_text'] ?: "Happy Birthday, " . $row['full_name'] . "!");
            $msg->setTo($phone);
            $msg->setFrom('BdayBliss'); // Specify the "from" number or name here
            $messages[] = $msg;
        }
    }

    $sms_messages->setMessages($messages);

    try {
        $result = $apiInstance->smsSendPost($sms_messages);
        print_r($result);
    } catch (Exception $e) {
        echo 'Exception when calling SMSApi->smsSendPost: ', $e->getMessage(), PHP_EOL;
    }
} else {
    echo "No users with today's birthdate found.";
}

$stmt->close();
$conn->close();
?>