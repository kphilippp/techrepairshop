<?php include 'db.php'; ?>
<?php include 'nav.php'; ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Determine the current form to display (login or change password)
$currentForm = 'login';
if (isset($_GET['changepass']) && $_GET['changepass'] === 'true') {
  $currentForm = 'changepass';
} else {
  $currentForm = 'login';
}

// Logic for Login
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Vulnerable query (DO NOT USE IN PRODUCTION)
    $query = "SELECT * FROM Users WHERE Username = '$username' AND Password = '$password'";
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        $_SESSION['logged_in'] = true; // Set session variable for login state
        $_SESSION['username'] = $username;
        echo "<p class='text-green-600 text-center mt-4'>Login successful! Showing database tables...</p>";
    } else {
        echo "<p class='text-red-600 text-center mt-4'>Invalid username or password. Please try again.</p>";
    }
}

// Logic for Login
if (isset($_POST['loginSP'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM Users WHERE Username = ? AND Password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $_SESSION['logged_in'] = true; // Set session variable for login state
      $_SESSION['username'] = $username;
      echo "<p class='text-green-600 text-center mt-4'>Login successful! Showing database tables...</p>";

    } else {
      echo "<p class='text-red-600 text-center mt-4'>Invalid username or password. Please try again.</p>";
    }
}

// Logic for Changing Password
if (isset($_POST['changepass'])) {
    $username = $_POST['username'];
    $oldPassword = $_POST['password'];
    $newPassword = $_POST['newpassword'];

    // Vulnerable UPDATE query
    $query = "UPDATE Users SET Password = '$newPassword' WHERE Username = '$username' AND (Password = '$oldPassword')";
    if ($conn->query($query)) {
        echo "<p class='text-green-600 text-center mt-4'>Password updated successfully!</p>";
    } else {
        echo "<p class='text-red-600 text-center mt-4'>Error updating password: " . $conn->error . "</p>";
    }
}

// Logout logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Logic for Deleting a Row
if (isset($_POST['delete_row'])) {
    $tableName = $_POST['table'];
    $primaryKeyData = json_decode($_POST['primary_key'], true);

    // Construct the DELETE query based on the row's primary key(s)
    $conditions = [];
    foreach ($primaryKeyData as $column => $value) {
        $escapedValue = $conn->real_escape_string($value);
        $conditions[] = "`$column` = '$escapedValue'";
    }
    $whereClause = implode(" AND ", $conditions);

    $deleteQuery = "DELETE FROM `$tableName` WHERE $whereClause";
    if ($conn->query($deleteQuery)) {
        echo "<p class='text-green-600 text-center mt-4'>Row deleted successfully from $tableName.</p>";
    } else {
        echo "<p class='text-red-600 text-center mt-4'>Error deleting row: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f9fff9] text-[#2d3e2d] font-sans">
    <h1 class="text-4xl font-bold text-center my-8 text-[#267326]">Admin Manager</h1>

    <?php if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']): ?>
        <!-- Login Form -->
        <?php if ($currentForm === 'login'): ?>
            <form method="POST" action="" class="bg-white mx-auto p-6 rounded-lg shadow-lg border border-[#b2d8b2] w-[50%]">
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block font-semibold mb-2">Username:</label>
                        <input type="text" id="username" name="username" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    </div>
                    <div>
                        <label for="password" class="block font-semibold mb-2">Password:</label>
                        <input type="password" id="password" name="password" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    </div>
                </div>
                <button type="submit" name="login" class="bg-[#267326] text-white py-2 px-4 rounded shadow hover:bg-[#1d5a1d] w-full mt-6">
                    Login
                </button>

                <a href="?changepass=true" class="bg-[#267326] text-white py-2 px-4 text-center rounded shadow hover:bg-[#1d5a1d] w-full mt-6 block">
                Change Password
                </a>
            </form>
            <h1 class="w-full text-center text-lg mt-20 mb-4 font-bold">Stored Procedure Login</h1>
            <form method="POST" action="" class="bg-white mx-auto p-6 rounded-lg shadow-lg border border-[#b2d8b2] w-[50%]">
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block font-semibold mb-2">Username:</label>
                        <input type="text" id="username" name="username" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    </div>
                    <div>
                        <label for="password" class="block font-semibold mb-2">Password:</label>
                        <input type="password" id="password" name="password" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    </div>
                </div>
                <button type="submit" name="loginSP" class="bg-[#267326] text-white py-2 px-4 rounded shadow hover:bg-[#1d5a1d] w-full mt-6">
                    Login
                </button>
            </form>
        <?php elseif ($currentForm === 'changepass'): ?>
            <!-- Change Password Form --> 
            <form method="POST" action="" class="bg-white mx-auto p-6 rounded-lg shadow-lg border border-[#b2d8b2] w-[50%]">
                <div class="space-y-4">
                    <div>
                        <label for="username" class="block font-semibold mb-2">Username:</label>
                        <input type="text" id="username" name="username" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    </div>
                    <div>
                        <label for="password" class="block font-semibold mb-2">Old Password:</label>
                        <input type="password" id="password" name="password" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    </div>
                    <div>
                        <label for="newpassword" class="block font-semibold mb-2">New Password:</label>
                        <input type="password" id="newpassword" name="newpassword" class="w-full border border-[#d9ecd9] rounded-md p-2 bg-[#f3fff3]" required>
                    </div>
                </div>
                <button type="submit" name="changepass" class="bg-[#267326] text-white py-2 px-4 rounded shadow hover:bg-[#1d5a1d] w-full mt-6">
                    Change Password
                </button>
                <a href="?changepass=false" class="bg-[#267326] text-white py-2 px-4 text-center rounded shadow hover:bg-[#1d5a1d] w-full mt-6 block">
                    Return to Login
                </a>

            </form>
        <?php endif; ?>
    <?php else: ?>
        <!-- Logged In - Display Tables -->
        <a href="?logout=true" class="text-red-600 hover:underline text-center block my-4">Logout</a>
        <div class="mt-6 px-44">
            <?php
            // Fetch all tables in the database
            $tablesResult = $conn->query("SHOW TABLES");

            while ($tableRow = $tablesResult->fetch_row()) {
                $tableName = $tableRow[0];
                echo "<h2 class='text-xl font-semibold mt-4'>$tableName</h2>";

                // Fetch all rows from the current table
                $tableData = $conn->query("SELECT * FROM `$tableName`");
                if ($tableData->num_rows > 0) {
                    echo "<table class='w-full table-auto border-collapse border border-gray-300 text-left mt-4'>";
                    echo "<thead>
                            <tr class='bg-gray-200'>";
                    
                    // Display table column names
                    $columns = $tableData->fetch_fields();
                    foreach ($columns as $column) {
                        echo "<th class='border border-gray-300 px-4 py-2'>{$column->name}</th>";
                    }
                    echo "<th class='border border-gray-300 px-4 py-2'>Action</th>";
                    echo "</tr>
                          </thead>
                          <tbody>";

                    // Display table rows
                    while ($row = $tableData->fetch_assoc()) {
                        echo "<tr class='hover:bg-gray-100'>";
                        foreach ($row as $value) {
                            echo "<td class='border border-gray-300 px-4 py-2 break-words'>$value</td>";
                        }
                        // Add a delete button
                        echo "<td class='border border-gray-300 px-4 flex justify-center'>
                                <form method='POST' action=''>
                                    <input type='hidden' name='table' value='$tableName'>
                                    <input type='hidden' name='primary_key' value='".json_encode($row)."'>
                                    <button type='submit' name='delete_row' class='bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700 mt-4'>Delete</button>
                                </form>
                              </td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table>";
                } else {
                    echo "<p class='text-gray-600'>No rows found in $tableName.</p>";
                }
            }
            ?>
        </div>
    <?php endif; ?>
</body>
</html>
