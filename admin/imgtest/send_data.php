<?php
require '../../config/db.php';
if (isset($_POST['selected_checkbox']) & is_array($_POST['selected_checkbox'])) {
    $selected_ids = $_POST['selected_checkbox'];

    echo "<h2>Received Selected IDs:</h2>";
    echo "<pre>";
    print_r($selected_ids); // Print the array to see its contents
    echo "</pre>";

    // Example: You can loop through the IDs and process them (e.g., store in DB, fetch details)
    echo "<h3>Processing Each ID:</h3>";
    foreach ($selected_ids as $id) {
        // Sanitize the ID before using it in a query or any other operation
        $sanitized_id = mysqli_real_escape_string($conn, $id); // Important for security!

        echo "<p>Processing ID: " . htmlspecialchars($sanitized_id) . "</p>";
    }

    echo "<h3>Processing Each ID (Simple):</h3>";
    foreach ($selected_ids as $id) {
        echo $id;
    }

}

?>