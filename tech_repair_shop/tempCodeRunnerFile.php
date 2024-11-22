<?php include 'db.php'; ?>
<?php include 'nav.php'; ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions</title>
    <link rel="stylesheet" href="ticketStyles.css">

</head>
<body>
    <h1>Create a New Ticket</h1>
    <form method="POST" action="">
        <!-- Transaction Dropdown -->
        <label for="transaction_id">Transaction:</label>
        <select name="transaction_id" id="transaction_id" required>
            <option value="">Select a Transaction</option>
            <?php
            // Fetch Transaction IDs from the database
            $transaction_query = $conn->query("SELECT Transaction_ID FROM `Transaction`");
            while ($row = $transaction_query->fetch_assoc()) {
                echo "<option value='{$row['Transaction_ID']}'>Transaction ID: {$row['Transaction_ID']}</option>";
            }
            ?>
        </select><br><br>

        <!-- Technician Dropdown -->
        <label for="technician_id">Technician:</label>
        <select name="technician_id" id="technician_id" required>
            <option value="">Select a Technician</option>
            <?php
            // Fetch Technician IDs from the database
            $technician_query = $conn->query("SELECT Technician_ID, TFirst_Name, TLast_Name FROM Technician");
            while ($row = $technician_query->fetch_assoc()) {
                echo "<option value='{$row['Technician_ID']}'>Technician: {$row['TFirst_Name']} {$row['TLast_Name']}</option>";
            }
            ?>
        </select><br><br>

        <!-- Device Serial Number -->
        <label for="device_serial_number">Device Serial Number:</label>
        <input type="text" id="device_serial_number" name="device_serial_number" maxlength="50" required><br><br>

        <!-- Description of Issue -->
        <label for="description_of_issue">Description of Issue:</label><br>
        <textarea id="description_of_issue" name="description_of_issue" rows="4" cols="50" required></textarea><br><br>

        <!-- Date of Assignment -->
        <label for="date_of_assignment">Date of Assignment:</label>
        <input type="date" id="date_of_assignment" name="date_of_assignment" value="<?php echo date('Y-m-d'); ?>" required><br><br>

        <!-- Date of Completion -->
        <label for="date_of_completion">Date of Completion:</label>
        <input type="date" id="date_of_completion" name="date_of_completion"><br><br>

        <!-- Submit Button -->
        <button type="submit" name="create_ticket">Create Ticket</button>
    </form>


    <!-- PHP Logic for Adding a Ticket -->
    <?php
    if (isset($_POST['create_ticket'])) {
        // Collect form data
        $transaction_id = $_POST['transaction_id'];
        $technician_id = $_POST['technician_id'];
        $device_serial_number = $_POST['device_serial_number'];
        $description_of_issue = $_POST['description_of_issue'];
        $date_of_assignment = $_POST['date_of_assignment'];
        $date_of_completion = $_POST['date_of_completion'];

        // Prepare and execute the INSERT query
        $stmt = $conn->prepare("INSERT INTO Ticket (Transaction_ID, Technician_ID, Device_Serial_Number, Description_Of_Issue, Date_Of_Assignment, Date_Of_Completion) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param(
            "iissss", // Data types: i = integer, s = string
            $transaction_id,
            $technician_id,
            $device_serial_number,
            $description_of_issue,
            $date_of_assignment,
            $date_of_completion
        );

        if ($stmt->execute()) {
            echo "<p style='color: green;'>Ticket created successfully!</p>";
        } else {
            echo "<p style='color: red;'>Error creating ticket: " . $stmt->error . "</p>";
        }
    }
    ?>

    <?php


    // Query to fetch all tickets from the Ticket table
    $query = "
        SELECT 
            Ticket.Ticket_ID, 
            Ticket.Transaction_ID, 
            Ticket.Customer_ID, 
            Ticket.Technician_ID, 
            Ticket.Device_Serial_Number, 
            Ticket.Description_Of_Issue, 
            Ticket.Date_Of_Assignment, 
            Ticket.Date_Of_Completion, 
            Customer.CFirst_Name AS Customer_First_Name, 
            Customer.CLast_Name AS Customer_Last_Name,
            Technician.TFirst_Name AS Technician_First_Name,
            Technician.TLast_Name AS Technician_Last_Name
        FROM Ticket
        LEFT JOIN Customer ON Ticket.Customer_ID = Customer.Customer_ID
        LEFT JOIN Technician ON Ticket.Technician_ID = Technician.Technician_ID
        ORDER BY Ticket.Ticket_ID ASC
    ";

    $result = $conn->query($query);

    // Check if there are records in the table
    if ($result->num_rows > 0) {
        echo "<h1>Tickets Table</h1>";
        echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
        echo "
            <tr>
                <th>Ticket ID</th>
                <th>Transaction ID</th>
                <th>Customer Name</th>
                <th>Technician Name</th>
                <th>Device Serial Number</th>
                <th>Description of Issue</th>
                <th>Date of Assignment</th>
                <th>Date of Completion</th>
            </tr>";
        
        // Loop through the results and display them in a table
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Ticket_ID']}</td>
                    <td>{$row['Transaction_ID']}</td>
                    <td>{$row['Customer_First_Name']} {$row['Customer_Last_Name']}</td>
                    <td>{$row['Technician_First_Name']} {$row['Technician_Last_Name']}</td>
                    <td>{$row['Device_Serial_Number']}</td>
                    <td>{$row['Description_Of_Issue']}</td>
                    <td>{$row['Date_Of_Assignment']}</td>
                    <td>{$row['Date_Of_Completion']}</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No tickets found.</p>";
    }
    ?>




</body>
</html>