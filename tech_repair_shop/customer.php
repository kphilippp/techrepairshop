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
    <title>Manage Customers</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f9fff9] text-[#2d3e2d] font-sans">
    <h1 class="text-4xl font-bold text-center my-8 text-[#267326]">Manage Customers</h1>

    <!-- Form to Add Customer -->
    <form method="POST" action="" class="flex flex-wrap justify-center gap-10 bg-white p-6 mx-auto rounded-lg shadow-lg border border-[#b2d8b2] w-[80%]">
        <!-- Customer Details Section -->
        <div class="flex-1 flex flex-col gap-4">
            <h2 class="text-2xl font-semibold mb-4 text-[#2d582d]">Customer Details</h2>
            <div>
                <label for="first_name" class="block font-semibold mb-2 text-[#3a603a]">First Name:</label>
                <input type="text" id="first_name" name="first_name" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
            <div>
                <label for="middle_initial" class="block font-semibold mb-2 text-[#3a603a]">Middle Initial:</label>
                <input type="text" id="middle_initial" name="middle_initial" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]">
            </div>
            <div>
                <label for="last_name" class="block font-semibold mb-2 text-[#3a603a]">Last Name:</label>
                <input type="text" id="last_name" name="last_name" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
            <div>
                <label for="address" class="block font-semibold mb-2 text-[#3a603a]">Address:</label>
                <input type="text" id="address" name="address" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]">
            </div>
            <div>
                <label for="phone_number" class="block font-semibold mb-2 text-[#3a603a]">Phone Number:</label>
                <input type="text" id="phone_number" name="phone_number" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
            <div>
                <label for="email" class="block font-semibold mb-2 text-[#3a603a]">Email:</label>
                <input type="email" id="email" name="email" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
        </div>

        <!-- Transaction and Ticket Details Section -->
        <div class="flex-1 flex flex-col gap-4">
            <h2 class="text-2xl font-semibold mb-4 text-[#2d582d]">Transaction Details</h2>
            <div>
                <label for="transaction_amount" class="block font-semibold mb-2 text-[#3a603a]">Amount of Transaction:</label>
                <input type="number" id="transaction_amount" name="transaction_amount" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
            <div>
                <label for="payment_status" class="block font-semibold mb-2 text-[#3a603a]">Payment Status:</label>
                <select id="payment_status" name="payment_status" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    <option value="1">Paid</option>
                    <option value="0">Pending</option>
                </select>
            </div>
            <div>
                <label for="transaction_date" class="block font-semibold mb-2 text-[#3a603a]">Date of Transaction:</label>
                <input type="date" id="transaction_date" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
            <h2 class="text-2xl font-semibold mb-4 mt-8 text-[#2d582d]">Ticket Details</h2>
            <div>
                <label for="device_serial_number" class="block font-semibold mb-2 text-[#3a603a]">Device Serial Number:</label>
                <input type="text" id="device_serial_number" name="device_serial_number" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
            </div>
            <div>
                <label for="description_of_issue" class="block font-semibold mb-2 text-[#3a603a]">Description of Issue:</label>
                <textarea id="description_of_issue" name="description_of_issue" rows="4" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required></textarea>
            </div>
            <button type="submit" name="add_customer" class="bg-[#267326] text-white py-2 px-4 rounded shadow hover:bg-[#154215] w-full mt-6">
                Add Customer
            </button>
        </div>
    </form>

    <!-- PHP Logic for Adding a Customer -->
    <?php
    if (isset($_POST['add_customer'])) {
        $email = $_POST['email'];
        $check_email_query = $conn->prepare("SELECT * FROM Customer WHERE Email = ?");
        $check_email_query->bind_param("s", $email);
        $check_email_query->execute();
        $result = $check_email_query->get_result();

        if ($result->num_rows > 0) {
            echo "<div class='text-center text-red-600'>There is already an existing customer with that email.<br>Maybe you just want to create a new transaction.</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO Customer (CFirst_Name, CMiddle_Initial, CLast_Name, Address, Phone_Number, Email) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $_POST['first_name'], $_POST['middle_initial'], $_POST['last_name'], $_POST['address'], $_POST['phone_number'], $_POST['email']);
            if ($stmt->execute()) {
                echo "<p class='text-green-600 text-center'>Customer added successfully!</p>";
                $customer_id = $conn->insert_id;

                $stmt = $conn->prepare("INSERT INTO Transaction (Amount_Charged, Payment_Status, Date_Of_Transaction, Customer_ID) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("iisi", $_POST['transaction_amount'], $_POST['payment_status'], $_POST['transaction_date'], $customer_id);
                if ($stmt->execute()) {
                    echo "<p class='text-green-600 text-center'>Transaction created successfully!</p>";

                    $transaction_id = $conn->insert_id;
                    $ticket_stmt = $conn->prepare("INSERT INTO Ticket (Transaction_ID, Customer_ID, Device_Serial_Number, Description_Of_Issue) VALUES (?, ?, ?, ?)");
                    $ticket_stmt->bind_param("iiss", $transaction_id, $customer_id, $_POST['device_serial_number'], $_POST['description_of_issue']);
                    if ($ticket_stmt->execute()) {
                        echo "<p class='text-green-600 text-center'>Ticket created successfully!</p>";
                    } else {
                        echo "<p class='text-red-600 text-center'>Error adding ticket: " . $ticket_stmt->error . "</p>";
                    }
                } else {
                    echo "<p class='text-red-600 text-center'>Error adding transaction: " . $stmt->error . "</p>";
                }
            } else {
                echo "<p class='text-red-600 text-center'>Error adding customer: " . $stmt->error . "</p>";
            }
        }
    }
    ?>

<div class="mt-10 bg-white p-6 rounded-lg shadow-lg border border-[#b2d8b2] mx-48 mb-60">
    <h2 class="text-2xl font-semibold mb-4 text-[#2d582d]">Customers Table</h2>
    <?php
    $query = "
        SELECT 
            Customer.Customer_ID, 
            Customer.CFirst_Name, 
            Customer.CMiddle_Initial, 
            Customer.CLast_Name, 
            Customer.Address, 
            Customer.Phone_Number, 
            Customer.Email 
        FROM Customer
        ORDER BY Customer.Customer_ID ASC
    ";

    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        echo "<table class='w-full table-auto border-collapse border border-[#b2d8b2] text-left'>";
        echo "
            <thead>
                <tr class='bg-[#267326] text-white'>
                    <th class='border border-[#b2d8b2] px-4 py-2'>Customer ID</th>
                    <th class='border border-[#b2d8b2] px-4 py-2'>Name</th>
                    <th class='border border-[#b2d8b2] px-4 py-2'>Address</th>
                    <th class='border border-[#b2d8b2] px-4 py-2'>Phone</th>
                    <th class='border border-[#b2d8b2] px-4 py-2'>Email</th>
                </tr>
            </thead>
            <tbody>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr class='hover:bg-[#f3fff3]'>
                    <td class='border border-[#b2d8b2] px-4 py-2'>{$row['Customer_ID']}</td>
                    <td class='border border-[#b2d8b2] px-4 py-2'>
                        {$row['CFirst_Name']} {$row['CMiddle_Initial']} {$row['CLast_Name']}
                    </td>
                    <td class='border border-[#b2d8b2] px-4 py-2'>{$row['Address']}</td>
                    <td class='border border-[#b2d8b2] px-4 py-2'>{$row['Phone_Number']}</td>
                    <td class='border border-[#b2d8b2] px-4 py-2'>{$row['Email']}</td>
                  </tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='text-[#3a603a]'>No customers available.</p>";
    }
    ?>
</div>


</body>
</html>
