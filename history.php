<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Display</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* CSS styles to make the table smaller and center-align text */
        table {
            width: 80%;
            /* Adjust the width as needed */
            border-collapse: collapse;
            margin: 0 auto;
            /* Center the table on the page */
        }

        th,
        td {
            padding: 10px;
            /* Adjust cell padding */
            text-align: left;
            border: 1px solid #ddd;
            /* Adjust border thickness and color */
        }

        th {
            background-color: #f2f2f2;
            /* Header background color */
        }

        .header-container {
            text-align: center;
            /* Center-align the "History" text */
            margin-bottom: 20px;
            /* Add some space between "History" and the table */
        }

        .home-button {
            font-size: 24px;
            /* Increase the font size for the home icon */
        }

        /* Adjust column widths */
        th:nth-child(1),
        td:nth-child(1) {
            width: 2%;
            /* Adjust the width for the ID column */
        }

        th:nth-child(2),
        td:nth-child(2) {
            width: 5%;
            /* Adjust the width for Scan 1 column */
        }

        th:nth-child(3),
        td:nth-child(3) {
            width: 5%;
            /* Adjust the width for Scan 2 column */
        }

        th:nth-child(4),
        td:nth-child(4) {
            width: 20%;
            /* Adjust the width for QR Data column */
        }

        th:nth-child(5),
        td:nth-child(5) {
            width: 30%;
            /* Adjust the width for Text column */
        }
    </style>
</head>

<body>
    <div class="header-container">
        <h1>History</h1>
        <button class="home-button" onclick="window.location.href = 'index.php';"><i class="fas fa-home"></i></button>
    </div>
    <table>
        <tr>
            <th>ID</th>
            <th>Scan 1</th>
            <th>Scan 2</th>
            <th>QR Data</th>
            <th>Text</th>
        </tr>
        <?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$database = "iman";  // Change this to your database name

// Create a connection to the database
$conn = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve data from the database
$sql = "SELECT id, scan1, scan2, qr_scanned, text_extracted FROM data"; // Include the "type" field in the query
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["id"] . "</td>"; // Add the missing '>' here
                
        // Output scan1 and scan2 without making them links
        echo "<td>" . $row["scan1"] . "</td>";
        echo "<td>" . $row["scan2"] . "</td>";
        
 // Make qr_scanned a clickable link
 echo "<td><a href='" . $row["qr_scanned"] . "' target='_blank'>" . $row["qr_scanned"] . "</a></td>";

        // Output Text without making it a link
        echo "<td>" . $row["text_extracted"] . "</td>";
        echo "</tr>";
    }
} else {
    echo "NO Result Found";
}

$conn->close();
?>


    </table>
</body>

</html>