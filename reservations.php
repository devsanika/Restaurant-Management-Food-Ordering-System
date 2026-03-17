<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant";

// Enable error reporting (for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Establish connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Collect form data
    $email = $_POST['email'];
    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $noOfGuests = $_POST['noOfGuests'];
    $reservedTime = $_POST['reservedTime']; // HH:MM
    $reservedDate = $_POST['reservedDate']; // YYYY-MM-DD

    // Convert time to HH:MM:SS
    $reservedTimeWithSeconds = date('H:i:s', strtotime($reservedTime));

    // Get current date & time
    $currentDate = date('Y-m-d');
    $currentTime = date('H:i:s');

    /* --------------------
       DATE VALIDATION
    -------------------- */
    if ($reservedDate < $currentDate) {
        echo "<script>
                alert('You cannot select a past date for reservation.');
                window.history.back();
              </script>";
        exit();
    }

    /* --------------------
       TIME VALIDATION
       (only if date is today)
    -------------------- */
    if ($reservedDate == $currentDate && $reservedTimeWithSeconds < $currentTime) {
        echo "<script>
                alert('You cannot select a past time for today.');
                window.history.back();
              </script>";
        exit();
    }

    // Insert query
    $sql = "INSERT INTO reservations 
            (email, name, contact, noOfGuests, reservedTime, reservedDate)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "sssiis",
        $email,
        $name,
        $contact,
        $noOfGuests,
        $reservedTimeWithSeconds,
        $reservedDate
    );

    if ($stmt->execute()) {
        echo '<script>
                alert("Reservation successful!");
                window.location.href="reservations.php";
              </script>';
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
