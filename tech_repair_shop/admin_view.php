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
    <title>Login</title>
    <link rel="stylesheet" href="customerStyles.css">

</head>
<body>
    <h1>Login</h1>

    <!-- Form to Add Customer -->
    <form method="POST" action="">
    <!-- Customer Details Section -->
    <div>

        <label for="Username">First Name:</label>
        <input type="text" id="username" name="username" required>
        <label for="Password">First Name:</label>
        <input type="text" id="password" name="password" required>

        
    </div>
    <button type="submit" name="login">Login</button>

</form>




</body> 
</html>
