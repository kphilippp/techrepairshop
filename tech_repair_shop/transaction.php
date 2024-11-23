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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f9fff9] text-[#2d3e2d] font-sans">
    <h1 class="text-4xl font-bold text-center my-8 text-[#267326]">Manage Transactions</h1>

    <!-- Form to Add a Transaction -->
    <form method="POST" action="" class="bg-white mx-auto p-6 rounded-lg shadow-lg border border-[#b2d8b2] w-[50%]">
        <h2 class="text-2xl font-semibold mb-4 text-[#267326]">Transaction Details</h2>
        <div class="space-y-4">
            <div>
                <label for="customer_id" class="block font-semibold mb-2">Select Customer:</label>
                <select id="customer_id" name="customer_id" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    <option value="">Select a Customer</option>
                    <?php
                    $customer_query = $conn->query("SELECT Customer_ID, CFirst_Name, CLast_Name, Email FROM Customer");
                    while ($row = $customer_query->fetch_assoc()) {
                        echo "<option value='{$row['Customer_ID']}'>
                                {$row['CFirst_Name']} {$row['CLast_Name']} ({$row['Email']})
                              </option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="transaction_amount" class="block font-semibold mb-2">Amount of Transaction:</label>
                <input type="number" id="transaction_amount" name="transaction_amount" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
            <div>
                <label for="payment_status" class="block font-semibold mb-2">Payment Status:</label>
                <select id="payment_status" name="payment_status" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    <option value="1">Paid</option>
                    <option value="0">Pending</option>
                </select>
            </div>
            <div>
                <label for="transaction_date" class="block font-semibold mb-2">Date of Transaction:</label>
                <input type="date" id="transaction_date" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
        </div>

        <h2 class="text-2xl font-semibold mb-4 mt-8 text-[#267326]">Ticket Details</h2>
        <div class="space-y-4">
            <div>
                <label for="device_serial_number" class="block font-semibold mb-2">Device Serial Number:</label>
                <input type="text" id="device_serial_number" name="device_serial_number" maxlength="50" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
            <div>
                <label for="description_of_issue" class="block font-semibold mb-2">Description of Issue:</label>
                <textarea id="description_of_issue" name="description_of_issue" rows="4" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required></textarea>
            </div>
        </div>
        <button type="submit" name="add_transaction" class="bg-[#267326] text-white py-2 px-4 rounded shadow hover:bg-[#1d5a1d] w-full mt-6">
            Add Transaction and Ticket
        </button>
    </form>

    <!-- Display Transactions -->
    <div class="mt-8 mx-auto w-[90%] bg-white p-6 rounded-lg shadow-lg border border-[#b2d8b2] mb-60">
        <h2 class="text-2xl font-semibold mb-4 text-[#267326]">Transactions Table</h2>
        <?php
        $result = $conn->query("SELECT Transaction.Transaction_ID, Transaction.Amount_Charged, Transaction.Date_Of_Transaction, Transaction.Payment_Status, Customer.CFirst_Name, Customer.CLast_Name 
                                FROM `Transaction`
                                LEFT JOIN Customer ON Transaction.Customer_ID = Customer.Customer_ID");

        if ($result->num_rows > 0) {
            echo "<table class='w-full border-collapse border border-[#b2d8b2] text-left'>";
            echo "
                <thead>
                    <tr class='bg-[#267326] text-white'>
                        <th class='border border-[#b2d8b2] px-4 py-2'>Transaction ID</th>
                        <th class='border border-[#b2d8b2] px-4 py-2'>Customer Name</th>
                        <th class='border border-[#b2d8b2] px-4 py-2'>Amount Charged</th>
                        <th class='border border-[#b2d8b2] px-4 py-2'>Date of Transaction</th>
                        <th class='border border-[#b2d8b2] px-4 py-2'>Payment Status</th>
                    </tr>
                </thead>
                <tbody>";
            while ($row = $result->fetch_assoc()) {
                $payment_status = $row['Payment_Status'] ? 'Paid' : 'Pending';
                echo "<tr class='hover:bg-[#f3fff3]'>
                        <td class='border border-[#b2d8b2] px-4 py-2'>{$row['Transaction_ID']}</td>
                        <td class='border border-[#b2d8b2] px-4 py-2'>{$row['CFirst_Name']} {$row['CLast_Name']}</td>
                        <td class='border border-[#b2d8b2] px-4 py-2'>\${$row['Amount_Charged']}</td>
                        <td class='border border-[#b2d8b2] px-4 py-2'>{$row['Date_Of_Transaction']}</td>
                        <td class='border border-[#b2d8b2] px-4 py-2'>{$payment_status}</td>
                      </tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "<p class='text-[#3a603a]'>No transactions available.</p>";
        }
        ?>
    </div>

    <?php
if (isset($_POST['add_transaction'])) {
    // Get form input data
    $customer_id = $_POST['customer_id'];
    $transaction_amount = $_POST['transaction_amount'];
    $payment_status = $_POST['payment_status'];
    $transaction_date = $_POST['transaction_date'];
    $device_serial_number = $_POST['device_serial_number'];
    $description_of_issue = $_POST['description_of_issue'];

    // Validate that a customer was selected
    if (!empty($customer_id)) {
        // Insert the transaction into the Transaction table
        $transaction_stmt = $conn->prepare(
            "INSERT INTO `Transaction` (Customer_ID, Amount_Charged, Payment_Status, Date_Of_Transaction) 
             VALUES (?, ?, ?, ?)"
        );
        $transaction_stmt->bind_param("idss", $customer_id, $transaction_amount, $payment_status, $transaction_date);

        if ($transaction_stmt->execute()) {
            // Get the generated Transaction_ID
            $transaction_id = $conn->insert_id;

            // Insert the ticket into the Ticket table
            $ticket_stmt = $conn->prepare(
                "INSERT INTO `Ticket` (Transaction_ID, Customer_ID, Device_Serial_Number, Description_Of_Issue) 
                 VALUES (?, ?, ?, ?)"
            );
            $ticket_stmt->bind_param("iiss", $transaction_id, $customer_id, $device_serial_number, $description_of_issue);

            if ($ticket_stmt->execute()) {
                echo "<p class='text-green-600 text-center mt-4'>Transaction and Ticket added successfully!</p>";
            } else {
                echo "<p class='text-red-600 text-center mt-4'>Error adding ticket: " . $ticket_stmt->error . "</p>";
            }
        } else {
            echo "<p class='text-red-600 text-center mt-4'>Error adding transaction: " . $transaction_stmt->error . "</p>";
        }
    } else {
        echo "<p class='text-red-600 text-center mt-4'>Please select a customer.</p>";
    }
}
?>


</body>
</html>
