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
    <title>Manage Technicians</title>
    <link rel="stylesheet" href="technicianStyles.css">

</head>
<body>
    <h1>Manage Technicians</h1>

    <!-- Form to Add Customer -->
    <form method="POST" action="">
        First Name: <input type="text" name="first_name" required>
        Middle Initial: <input type="text" name="middle_initial">
        Last Name: <input type="text" name="last_name" required>
        <button type="submit" name="add_customer">Add Technician</button>
    </form>

    <!-- PHP Logic for Adding a Customer -->
    <?php
    if (isset($_POST['add_customer'])) {


        // first we need to check if email already exists within the table 
        $email = $_POST['first_name'];
        $check_email_query = $conn->prepare("SELECT * FROM Technician WHERE TFirst_Name = ?");
        $check_email_query->bind_param("s", $email);
        $check_email_query->execute();
        $result = $check_email_query->get_result();


        // if there is an exiting email, dont execute insert but if there isnt, proceed
        if ($result->num_rows > 0) {
          echo "<div class=\"resultMessage\">There is already an existing technician with that name</div>";
        } else {
          $stmt = $conn->prepare("INSERT INTO Technician (TFirst_Name, TMiddle_Initial, TLast_Name) VALUES (?, ?, ?)");
          $stmt->bind_param("sss", $_POST['first_name'], $_POST['middle_initial'], $_POST['last_name']);
          if ($stmt->execute()) {
            echo "Technician added successfully!";
          } else {
            echo "Error: " . $stmt->error;
          }
        }
    }

    // Display Customers
    $result = $conn->query("SELECT * FROM Technician");
    if ($result->num_rows > 0) {
        echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Ticket Count</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['Technician_ID']}</td>
                    <td>{$row['TFirst_Name']} {$row['TMiddle_Initial']} {$row['TLast_Name']}</td>
                    <td>{$row['TTicket_Count']}</td>
                  </tr>";
        }
        echo "</table>";
    } else {
        echo "No customers found.";
    }
    ?>
</body>
</html>
