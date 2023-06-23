<?php
// Include the database connection file
include 'connection.php';

// SQL query
$sql = "SELECT id, time, note FROM your_table";

// Execute the query
$result = $conn->query($sql);

// Check if any rows are returned
if ($result->num_rows > 0) {
    // Loop through the rows and output data
    while ($row = $result->fetch_assoc()) {
        // Access data using column names
        $id = $row["id"];
        $time = $row["time"];
        $note = $row["note"];

        // Display the data
        echo "ID: " . $id . "<br>";
        echo "Time: " . $time . "<br>";
        echo "Note: " . $note . "<br><br>";
    }
} else {
    echo "No results found.";
}

// Close the connection
$conn->close();
?>
