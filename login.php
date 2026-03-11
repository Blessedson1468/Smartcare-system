<?php
include 'database.php';
session_start();

if (isset($_POST['submit'])) {

    $username = trim($_POST['username']); 
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // 3. Verify Password
        if (password_verify($password, $row['password'])) {
            
            // 4. Check Approval Status
            if ($row['status'] !== 'Approved') {
                echo "<script>alert('❌ Waiting for admin approval.'); window.location='login.php';</script>";
                exit();
            }

            // 5. Success! Set sessions
            $_SESSION['username'] = $row['username'];
            $_SESSION['role'] = $row['role'];

            // 6. Redirect based on role
            if ($row['role'] === 'admin') {
                header("Location: admindashboard.php");
            } else {
                header("Location: index.php");
            }
            exit();

        } else {
            echo "<script>alert('❌ Invalid password'); window.location='login.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('❌ User not found'); window.location='login.php';</script>";
        exit();
    }
}
?>

      

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - SmartCare</title>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #ebeee6;
    margin: 0;
    padding: 0;
}

.container {
    width: 400px;
    margin: 80px auto;
    background: white;
    padding: 25px;
    border-radius: 8px;
    box-shadow: 0px 0px 10px rgba(0,0,0,0.1);
}

h2 {
    text-align: center;
}

label {
    font-weight: bold;
}

input {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    margin-bottom: 15px;
    border-radius: 4px;
    border: 1px solid #f79e0d;
}

button {
    width: 100%;
    padding: 10px;
    background-color: #2d0bc3;
    color: white;
    border: none;
    border-radius: 5px;
    font-size: 16px;
    cursor: pointer;
}

button:hover {
    background-color: #19076a;
}
</style>
</head>

<body>

<div class="container">
    <h2>SmartCare Login</h2>

    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button type="submit" name="submit">Login</button>
    </form>
</div>

</body>
</html>