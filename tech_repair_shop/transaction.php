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
    <title>Manage Transactions</title>
    <link rel="stylesheet" href="transactionStyles.css">
</head>
<body>
    <h1>Manage Transactions</h1>

    <!-- Form to Add a Transaction -->
    <form method="POST" action="">
        <h2>Transaction Details</h2>
        <label for="customer_id">Select Customer:</label>
        <select id="customer_id" name="customer_id" required>
            <option value="">Select a Customer</option>
            <?php
            // Fetch all customers from the database
            $customer_query = $conn->query("SELECT Customer_ID, CFirst_Name, CLast_Name, Email FROM Customer");
            while ($row = $customer_query->fetch_assoc()) {
                echo "<option value='{$row['Customer_ID']}'>
                        {$row['CFirst_Name']} {$row['CLast_Name']} ({$row['Email']})
                      </option>";
            }
            ?>
        </select><br><br>

        <label for="transaction_amount">Amount of Transaction:</label>
        <input type="number" id="transaction_amount" name="transaction_amount" required><br><br>

        <label for="payment_status">Payment Status:</label>
        <select id="payment_status" name="payment_status" required>
            <option value="1">Paid</option>
            <option value="0">Pending</option>
        </select><br><br>

        <label for="transaction_date">Date of Transaction:</label>
        <input type="date" id="transaction_date" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" required><br><br>

        <!-- Ticket Details -->
        <h2>Ticket Details</h2>
        <label for="device_serial_number">Device Serial Number:</label>
        <input type="text" id="device_serial_number" name="device_serial_number" maxlength="50" required><br><br>

        <label for="description_of_issue">Description of Issue:</label><br>
        <textarea id="description_of_issue" name="description_of_issue" rows="4" cols="50" required></textarea><br><br>

        <button type="submit" name="add_transaction">Add Transaction and Ticket</button>
    </form>

    <!-- PHP Logic for Adding a Transaction and Ticket -->
    <?php
    if (isset($_POST['add_transaction'])) {
        // Transaction Details
        $customer_id = $_POST['customer_id'];
        $amount = $_POST['transaction_amount'];
        $payment_status = $_POST['payment_status'];
        $transaction_date = $_POST['transaction_date'];

        // Ticket Details
        $device_serial_number = $_POST['device_serial_number'];
        $description_of_issue = $_POST['description_of_issue'];
        $date_of_assignment = $_POST['date_of_assignment'];
        $date_of_completion = !empty($_POST['date_of_completion']) ? $_POST['date_of_completion'] : null;

        // Validate that a customer was selected
        if (!empty($customer_id)) {
            // Insert the transaction into the database
            $transaction_stmt = $conn->prepare("INSERT INTO `Transaction` (Amount_Charged, Payment_Status, Date_Of_Transaction, Customer_ID) VALUES (?, ?, ?, ?)");
            $transaction_stmt->bind_param("iisi", $amount, $payment_status, $transaction_date, $customer_id);

            if ($transaction_stmt->execute()) {
                $transaction_id = $conn->insert_id; // Get the generated Transaction_ID
                echo "<div class='resultMessage' style='color: green;'>Transaction added successfully!</div>";

                // Insert the ticket into the database
                $ticket_stmt = $conn->prepare("INSERT INTO Ticket (Transaction_ID, Customer_ID, Device_Serial_Number, Description_Of_Issue) VALUES (?, ?, ?, ?)");
                $ticket_stmt->bind_param(
                    "iiss", // Match data types
                    $transaction_id,
                    $customer_id,
                    $device_serial_number,
                    $description_of_issue
                );

                if ($ticket_stmt->execute()) {
                    echo "<div class='resultMessage' style='color: green;'>Ticket added successfully!</div>";
                } else {
                    echo "<div class='resultMessage' style='color: red;'>Error adding ticket: " . $ticket_stmt->error . "</div>";
                }
            } else {
                echo "<div class='resultMessage' style='color: red;'>Error adding transaction: " . $transaction_stmt->error . "</div>";
            }
        } else {
            echo "<div class='resultMessage' style='color: red;'>Please select a customer.</div>";
        }
    }
    ?>

    <!-- Display Transactions -->
    <?php
    $result = $conn->query("SELECT Transaction.Transaction_ID, Transaction.Amount_Charged, Transaction.Date_Of_Transaction, Transaction.Payment_Status, Customer.CFirst_Name, Customer.CLast_Name 
                            FROM `Transaction`
                            LEFT JOIN Customer ON Transaction.Customer_ID = Customer.Customer_ID");

    if ($result->num_rows > 0) {
        echo "<h2>Transactions Table</h2>";
        echo "<table border='1'>";
        echo "
            <tr>
                <th>Transaction ID</th>
                <th>Customer Name</th>
                <th>Amount Charged</th>
                <th>Date of Transaction</th>
                <th>Payment Status</th>
            </tr>";
        while ($row = $result->fetch_assoc()) {
            $payment_status = $row['Payment_Status'] ? 'Paid' : 'Pending';
            echo "<tr>
                    <td>{$row['Transaction_ID']}</td>
                    <td>{$row['CFirst_Name']} {$row['CLast_Name']}</td>
                    <td>\${$row['Amount_Charged']}</td>
                    <td>{$row['Date_Of_Transaction']}</td>
                    <td>$payment_status</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No transactions available.</p>";
    }
    ?>
</body>
</html>
