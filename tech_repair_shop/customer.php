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
    <link rel="stylesheet" href="customerStyles.css">

</head>
<body>
    <h1>Manage Customers</h1>

    <!-- Form to Add Customer -->
    <form method="POST" action="">
    <!-- Customer Details Section -->
    <div>
        <h2>Customer Details</h2>
        <label for="first_name">First Name:</label>
        <input type="text" id="first_name" name="first_name" required>

        <label for="middle_initial">Middle Initial:</label>
        <input type="text" id="middle_initial" name="middle_initial">

        <label for="last_name">Last Name:</label>
        <input type="text" id="last_name" name="last_name" required>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address">

        <label for="phone_number">Phone Number:</label>
        <input type="text" id="phone_number" name="phone_number" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
    </div>

    <!-- Transaction and Ticket Details Section -->
    <div>
        <h2>Transaction Details</h2>
        <label for="transaction_amount">Amount of Transaction:</label>
        <input type="number" id="transaction_amount" name="transaction_amount" required>

        <label for="payment_status">Payment Status:</label>
        <select id="payment_status" name="payment_status" required>
            <option value="1">Paid</option>
            <option value="0">Pending</option>
        </select>

        <label for="transaction_date">Date of Transaction:</label>
        <input type="date" id="transaction_date" name="transaction_date" value="<?php echo date('Y-m-d'); ?>" required>

        <h2>Ticket Details</h2>
        <label for="device_serial_number">Device Serial Number:</label>
        <input type="text" id="device_serial_number" name="device_serial_number" required>

        <label for="description_of_issue">Description of Issue:</label>
        <textarea id="description_of_issue" name="description_of_issue" required></textarea>

      
        <button type="submit" name="add_customer">Add Customer</button>
    </div>
</form>


    <!-- PHP Logic for Adding a Customer -->
    <?php
    if (isset($_POST['add_customer'])) {


        // first we need to check if email already exists within the table 
        $email = $_POST['email'];
        $check_email_query = $conn->prepare("SELECT * FROM Customer WHERE Email = ?");
        $check_email_query->bind_param("s", $email);
        $check_email_query->execute();
        $result = $check_email_query->get_result();


        // if there is an exiting email, dont execute insert but if there isnt, proceed
        if ($result->num_rows > 0) {
          echo "<div class=\"resultMessage\">There is already an existing customer with that email\nMaybe you just want to create a new transaction</div>";
        } else {

          // Successfully adding a customer
          $stmt = $conn->prepare("INSERT INTO Customer (CFirst_Name, CMiddle_Initial, CLast_Name, Address, Phone_Number, Email) VALUES (?, ?, ?, ?, ?, ?)");
          $stmt->bind_param("ssssss", $_POST['first_name'], $_POST['middle_initial'], $_POST['last_name'], $_POST['address'], $_POST['phone_number'], $_POST['email']);
          if ($stmt->execute()) {
            echo "<p style='color: green;'>Customer added successfully!</p>";
          } else {
            echo "<p style='color: red;'>Error adding customer: " . $stmt->error . "</p>";
          }


          // Also add a transaction record
          $customer_id = $conn->insert_id;

          $stmt = $conn->prepare("INSERT INTO Transaction (Amount_Charged, Payment_Status, Date_Of_Transaction, Customer_ID) VALUES (?, ?, ?, ?)");
          $stmt->bind_param("iisi", $_POST['transaction_amount'], $_POST['payment_status'], $_POST['transaction_date'], $customer_id);
          if ($stmt->execute()) {
            echo "<p style='color: green;'>Transaction created successfully!</p>";

                    $transaction_id = $conn->insert_id;
                    $date_of_completion = !empty($_POST['date_of_completion']) ? $_POST['date_of_completion'] : null;

                    // Insert new ticket
                    $ticket_stmt = $conn->prepare("INSERT INTO Ticket (Transaction_ID, Customer_ID, Device_Serial_Number, Description_Of_Issue) VALUES (?, ?, ?, ?)");
                    $ticket_stmt->bind_param(
                        "iiss", // Match data types
                        $transaction_id, 
                        $customer_id, 
                        $_POST['device_serial_number'], 
                        $_POST['description_of_issue'], 
                    );
                    if ($ticket_stmt->execute()) {
                        echo "<p style='color: green;'>Ticket created successfully!</p>";
                    } else {
                        echo "<p style='color: red;'>Error adding ticket: " . $ticket_stmt->error . "</p>";
                    }


          } else {
            echo "<p style='color: red;'>Error adding transaction: " . $stmt->error . "</p>";
          }
        }
    }

    // Display Customers
    $result = $conn->query("SELECT * FROM Customer");
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Address</th><th>Phone</th><th>Email</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Customer_ID']}</td>
                    <td>{$row['CFirst_Name']} {$row['CMiddle_Initial']} {$row['CLast_Name']}</td>
                    <td>{$row['Address']}</td>
                    <td>{$row['Phone_Number']}</td>
                    <td>{$row['Email']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No customers found.";
    }
    ?>
</body>
</html>
