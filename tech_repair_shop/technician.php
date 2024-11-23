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
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f9fff9] text-[#2d3e2d] font-sans">
    <h1 class="text-4xl font-bold text-center my-8 text-[#267326]">Manage Technicians</h1>

    <div class="flex-1/4 flex flex-col md:flex-row gap-8 justify-center">
        <!-- Form to Add Technician -->
        <form method="POST" action="" class="bg-white p-6 rounded-lg shadow-lg border border-[#b2d8b2] w-1/2">
            <h2 class="text-2xl font-semibold mb-4 text-[#2d582d]">Add Technician</h2>
            <div class="space-y-4">
                <div>
                    <label for="first_name" class="block text-sm font-bold text-[#3a603a]">First Name:</label>
                    <input type="text" id="first_name" name="first_name" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                </div>
                <div>
                    <label for="middle_initial" class="block text-sm font-bold text-[#3a603a]">Middle Initial:</label>
                    <input type="text" id="middle_initial" name="middle_initial" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]">
                </div>
                <div>
                    <label for="last_name" class="block text-sm font-bold text-[#3a603a]">Last Name:</label>
                    <input type="text" id="last_name" name="last_name" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                </div>
                <button type="submit" name="add_customer" class="w-full bg-[#267326] text-white py-2 rounded shadow hover:bg-[#1d5a1d]">
                    Add Technician
                </button>
            </div>
        </form>

        <!-- Technician Table -->
        <div class="bg-white p-6 rounded-lg shadow-lg border border-[#b2d8b2]">
            <h2 class="text-2xl font-semibold mb-4 text-[#2d582d]">Technician List</h2>
            <?php 
            $result = $conn->query("SELECT * FROM Technician");
            if ($result->num_rows > 0) {
                echo "<table class='w-full border-collapse border border-[#b2d8b2] text-left'>";
                echo "
                    <thead>
                        <tr class='bg-[#267326] text-white'>
                            <th class='border border-[#b2d8b2] px-4 py-2'>ID</th>
                            <th class='border border-[#b2d8b2] px-4 py-2'>Name</th>
                            <th class='border border-[#b2d8b2] px-4 py-2'>Ticket Count</th>
                        </tr>
                    </thead>
                    <tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr class='hover:bg-[#f3fff3]'>
                            <td class='border border-[#b2d8b2] px-4 py-2'>{$row['Technician_ID']}</td>
                            <td class='border border-[#b2d8b2] px-4 py-2'>{$row['TFirst_Name']} {$row['TMiddle_Initial']} {$row['TLast_Name']}</td>
                            <td class='border border-[#b2d8b2] px-4 py-2'>{$row['TTicket_Count']}</td>
                          </tr>";
                }
                echo "</tbody></table>";
            } else {
                echo "<p class='text-[#3a603a]'>No technicians found.</p>";
            }
            ?>
        </div>
    </div>

    <!-- PHP Logic for Adding a Technician -->
    <?php
    if (isset($_POST['add_customer'])) {
        $email = $_POST['first_name'];
        $check_email_query = $conn->prepare("SELECT * FROM Technician WHERE TFirst_Name = ?");
        $check_email_query->bind_param("s", $email);
        $check_email_query->execute();
        $result = $check_email_query->get_result();

        if ($result->num_rows > 0) {
            echo "<div class='text-center text-red-600'>There is already an existing technician with that name.</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO Technician (TFirst_Name, TMiddle_Initial, TLast_Name) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $_POST['first_name'], $_POST['middle_initial'], $_POST['last_name']);
            if ($stmt->execute()) {
                echo "<div class='text-center text-green-600'>Technician added successfully!</div>";
            } else {
                echo "<div class='text-center text-red-600'>Error: " . $stmt->error . "</div>";
            }
        }
    }
    ?>
</body>
</html>
