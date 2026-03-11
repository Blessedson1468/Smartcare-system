<?php
include 'database.php'; // connects to your database

if (isset($_POST['submit'])) {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    // Check if username already exists
    $checkSql = "SELECT * FROM users WHERE username = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $message = "❌ Username already exists!";
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $sql = "INSERT INTO users (username, password, role, status) VALUES (?, ?, ?, 'pending')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $hashedPassword, $role);
        if ($stmt->execute()) {
            echo '<script>alert("✅ Account created successfully! Awaiting admin approval.");</script>';
        } else {
            echo '<script>alert("❌ Error creating user: ' . $conn->error . '");</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account- SmartCare</title>
<style>
body { font-family: Arial, sans-serif; background: #ebeee6; margin: 0; padding: 0; }
.container { width: 400px; margin: 80px auto; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
h2 { text-align: center; }
label { font-weight: bold; }
input, select { width: 100%; padding: 8px; margin-top: 5px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #ec880d; }
button { width: 100%; padding: 10px; background-color: #1051a7; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; }
button:hover { background-color: #083ba9; }
.message { text-align: center; margin-bottom: 15px; font-weight: bold; }
</style>
</head>
<body>

<div class="container">
    <h2>Create Account</h2>

    <?php if (!empty($message)) echo "<div class='message'>$message</div>"; ?>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Role</label>
        <select name="role" required>
            <option value="">--Select Role--</option>
            <option value="admin">Admin</option>
            <option value="technician">Technician</option>
        </select>

        <button type="submit" name="submit">Create User</button>
        <a href="login.php" style="display:block; text-align:center; margin-top:10px; color:#126de4;"> Login</a>
    </form>
</div>

</body>
</html>