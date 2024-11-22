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
    <title>Technician Assignments</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

    <h1 class="text-3xl font-bold text-center my-6">Technician Assignment Management</h1>

    <div class="flex flex-col md:flex-row gap-5 justify-center">
        <!-- Technician Assignment Form -->
        <div class="flex-1 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Assign Technician to a Ticket</h2>
            <form method="POST" action="" class="space-y-4">
                <!-- Select Technician -->
                <div>
                    <label for="technician_id" class="block text-sm font-medium">Select Technician:</label>
                    <select id="technician_id" name="technician_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a Technician</option>
                        <?php
                        $technician_query = $conn->query("SELECT Technician_ID, TFirst_Name, TLast_Name FROM Technician");
                        while ($row = $technician_query->fetch_assoc()) {
                            echo "<option value='{$row['Technician_ID']}'>
                                    {$row['TFirst_Name']} {$row['TLast_Name']}
                                  </option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Select Ticket -->
                <div>
                    <label for="ticket_id" class="block text-sm font-medium">Select Ticket:</label>
                    <select id="ticket_id" name="ticket_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a Ticket</option>
                        <?php
                        $ticket_query = $conn->query("SELECT Ticket_ID, Description_Of_Issue FROM Ticket WHERE Technician_ID IS NULL");
                        while ($row = $ticket_query->fetch_assoc()) {
                            echo "<option value='{$row['Ticket_ID']}'>
                                    Ticket ID: {$row['Ticket_ID']} - {$row['Description_Of_Issue']}
                                  </option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="assign_technician" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700">
                    Assign Technician
                </button>
            </form>
        </div>

        <!-- Technician Disassignment Form -->
        <div class="flex-1 bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Disassign Technician from a Ticket</h2>
            <form method="POST" action="" class="space-y-4">
                <!-- Select Technician -->
                <div>
                    <label for="technician_id" class="block text-sm font-medium">Select Technician:</label>
                    <select id="technician_id" name="technician_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a Technician</option>
                        <?php
                        $technician_query = $conn->query("SELECT Technician_ID, TFirst_Name, TLast_Name FROM Technician");
                        while ($row = $technician_query->fetch_assoc()) {
                            echo "<option value='{$row['Technician_ID']}'>
                                    {$row['TFirst_Name']} {$row['TLast_Name']}
                                  </option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Select Ticket -->
                <div>
                    <label for="ticket_id" class="block text-sm font-medium">Select Ticket:</label>
                    <select id="ticket_id" name="ticket_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Select a Ticket</option>
                        <?php
                        $ticket_query = $conn->query("
                            SELECT Ticket_ID, Description_Of_Issue 
                            FROM Ticket 
                            WHERE Technician_ID IS NOT NULL
                        ");
                        while ($row = $ticket_query->fetch_assoc()) {
                            echo "<option value='{$row['Ticket_ID']}'>
                                    Ticket ID: {$row['Ticket_ID']} - {$row['Description_Of_Issue']}
                                  </option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" name="disassign_technician" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700">
                    Disassign Technician
                </button>
            </form>
        </div>
    </div>

    <!-- Ticket Table -->
    <div class="mt-6 mx-4 bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-semibold mb-4">Tickets Table</h2>
        <?php
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

        if ($result->num_rows > 0) {
            echo "<table class='w-full table-auto border-collapse border border-gray-300 text-left'>";
            echo "
                <thead>
                    <tr class='bg-gray-200'>
                        <th class='border border-gray-300 px-4 py-2'>Ticket ID</th>
                        <th class='border border-gray-300 px-4 py-2'>Customer Name</th>
                        <th class='border border-gray-300 px-4 py-2'>Technician Name</th>
                        <th class='border border-gray-300 px-4 py-2'>Device Serial Number</th>
                        <th class='border border-gray-300 px-4 py-2'>Description of Issue</th>
                        <th class='border border-gray-300 px-4 py-2'>Date of Assignment</th>
                        <th class='border border-gray-300 px-4 py-2'>Date of Completion</th>
                    </tr>
                </thead>
                <tbody>";
            while ($row = $result->fetch_assoc()) {
                $technician_name = $row['Technician_First_Name'] ? "{$row['Technician_First_Name']} {$row['Technician_Last_Name']}" : "Unassigned";
                echo "<tr class='hover:bg-gray-100'>
                        <td class='border border-gray-300 px-4 py-2'>{$row['Ticket_ID']}</td>
                        <td class='border border-gray-300 px-4 py-2'>{$row['Customer_First_Name']} {$row['Customer_Last_Name']}</td>
                        <td class='border border-gray-300 px-4 py-2'>{$technician_name}</td>
                        <td class='border border-gray-300 px-4 py-2'>{$row['Device_Serial_Number']}</td>
                        <td class='border border-gray-300 px-4 py-2'>{$row['Description_Of_Issue']}</td>
                        <td class='border border-gray-300 px-4 py-2'>{$row['Date_Of_Assignment']}</td>
                        <td class='border border-gray-300 px-4 py-2'>{$row['Date_Of_Completion']}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='text-gray-600'>No tickets available.</p>";
        }
        ?>
    </div>

</body>
</html>


<?php
// Logic for Assigning a Technician to a Ticket
if (isset($_POST['assign_technician'])) {
    $technician_id = $_POST['technician_id'];
    $ticket_id = $_POST['ticket_id'];

    // Update the ticket to assign the technician
    $stmt = $conn->prepare("UPDATE Ticket SET Technician_ID = ?, Date_Of_Assignment = ? WHERE Ticket_ID = ?");
    $current_date = date('Y-m-d');
    $stmt->bind_param("isi", $technician_id, $current_date, $ticket_id);

    if ($stmt->execute()) {
        // Increment the ticket count in the Technician table
        $update_ticket_count = $conn->prepare("UPDATE Technician SET TTicket_Count = TTicket_Count + 1 WHERE Technician_ID = ?");
        $update_ticket_count->bind_param("i", $technician_id);

        if ($update_ticket_count->execute()) {
            echo "<p class='text-green-600 text-center'>Technician assigned successfully, and ticket count updated!</p>";
        } else {
            echo "<p class='text-red-600 text-center'>Error updating ticket count: " . $update_ticket_count->error . "</p>";
        }
    } else {
        echo "<p class='text-red-600 text-center'>Error assigning technician: " . $stmt->error . "</p>";
    }
}

// Logic for Disassigning a Technician from a Ticket
if (isset($_POST['disassign_technician'])) {
    $technician_id = $_POST['technician_id'];
    $ticket_id = $_POST['ticket_id'];

    // Update the ticket to remove the technician assignment and set the completion date
    $stmt = $conn->prepare("UPDATE Ticket SET Technician_ID = NULL, Date_Of_Completion = ? WHERE Ticket_ID = ?");
    $current_date = date('Y-m-d');
    $stmt->bind_param("si", $current_date, $ticket_id);

    if ($stmt->execute()) {
        // Decrement the ticket count in the Technician table
        $update_technician_count = $conn->prepare("UPDATE Technician SET TTicket_Count = TTicket_Count - 1 WHERE Technician_ID = ?");
        $update_technician_count->bind_param("i", $technician_id);

        if ($update_technician_count->execute()) {
            echo "<p class='text-green-600 text-center'>Technician disassigned successfully, and ticket marked as completed!</p>";
        } else {
            echo "<p class='text-red-600 text-center'>Error updating technician ticket count: " . $update_technician_count->error . "</p>";
        }
    } else {
        echo "<p class='text-red-600 text-center'>Error disassigning technician: " . $stmt->error . "</p>";
    }
}
?>

