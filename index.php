<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: Login.php");
    exit();
}
$username=$_SESSION['username'];

// ONLY technicians can see this specific form
if ($_SESSION['role'] !== 'technician') {
    header("Location: admindashboard.php");
    exit();
}

include 'database.php'; // include your database connection


$success = "";
$error = "";
$searchResult = null;
$searchMessage = "";

if (isset($_GET['search'])) {
    $serial = $_GET['serial_number'];

    $query = "SELECT * FROM maintenance_request WHERE serial_number = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $serial);
    $stmt->execute();
    $searchResult = $stmt->get_result()->fetch_assoc();

    if (!$searchResult) {
        $searchMessage = "Device not found.Add it";
    }
}

if (isset($_POST['submit'])) {
    $serial_number = $_POST['serial_number'];
    $device_name = $_POST['device_name'];
    $department = $_POST['department'];
    $condition = $_POST['condition'];
    $person = $_POST['person'];
    $remarks = $_POST['remarks'];

    $sql = "INSERT INTO maintenance_request
    (serial_number, device_name, department, `condition`, person, remarks, status)
    VALUES (?, ?, ?, ?, ?, ?, 'pending')";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssss",
        $serial_number,
        $device_name,
        $department,
        $condition,
        $person,
        $remarks
    );

    if ($stmt->execute()) {
        echo "<script>alert('✅ Recorded successfully');</script>";
    } else {
        echo "<script>alert('❌ failed, please try again');</script>";
    }
}

if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $condition = $_POST['condition'];
    $remarks = $_POST['remarks'];

    $update = "UPDATE maintenance_request
              SET `condition` = ?, remarks = ?
              WHERE id = ?";

    $stmt = $conn->prepare($update);
    $stmt->bind_param("ssi", $condition, $remarks, $id);

    if ($stmt->execute()) {
        echo "<script>alert('✅ updated successfully');</script>";
    } else {
        echo "<script>alert('❌ Update failed');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartCare</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ebeee6;
            margin: 0;
            padding: 0;
        }
        
        .search-container {
            width: 400px;
            margin: 20px auto;
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .search-container input {
            width: 90%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #f2880f;
            border-radius: 4px;
        }

        .search-container button {
            width: 90%;
            padding: 10px;
            background-color: #0b0ff7;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-container button:hover {
            background-color: #1c10f9;
        }

        .container {
            width: 400px;
            margin: 20px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0px 0px 10px rgba(37, 224, 27, 0.1);
        }

        h2 {
            text-align: center;
        }

        label {
            font-weight: bold;
        }

        input, select, textarea {
            width: 90%;
            padding: 8px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 4px;
            border: 1px solid #f2880f;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #0b0ff7;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #1c10f9;
        }
    </style>
</head>

<body>
    
<div style="max-width: 400px; margin: 15px auto; display: flex; justify-content: space-between;
 align-items: center; background: white; padding: 10px 20px;
  border-radius: 8px; box-shadow: 0px 4px 10px rgba(0,0,0,0.05); border-left: 5px solid #f2880f;">
    <div style="font-size: 14px; color: #333;">
        <span style="color: #f2880f; font-weight: bold;">User:</span> 
        <?php echo htmlspecialchars($username); ?>
    </div>
    <a href="logout.php" style="text-decoration: none; background-color: #d9534f; color: white; padding: 6px 12px; border-radius: 4px; font-size: 13px; font-weight: bold; transition: 0.3s;" onmouseover="this.style.backgroundColor='#c9302c'" onmouseout="this.style.backgroundColor='#d9534f'">
        Logout
    </a>
</div>

<div class="search-container">
    <form method="GET" action="">
        <input type="text" name="serial_number" placeholder="Search Serial Number" required>
        <button type="submit" name="search">Search</button>
    </form>
</div>

<div class="container">
    <h2>SmartCare</h2>

    <?php if ($searchMessage): ?>
        <div style="color: red; text-align: center; font-weight: bold; margin-bottom: 10px;">
            <?php echo $searchMessage; ?>
        </div>
    <?php endif; ?>

    <?php if ($searchResult): ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo $searchResult['id']; ?>">

        <label>Serial Number</label>
        <input type="text" value="<?php echo htmlspecialchars($searchResult['serial_number']);
         ?>" readonly style="background-color: #f4f4f4;">

        <label>Device Name</label>
        <input type="text" value="<?php echo htmlspecialchars($searchResult['device_name']);
         ?>" readonly style="background-color: #f4f4f4;">

        <label>Department</label>
        <input type="text" value="<?php echo htmlspecialchars($searchResult['department']);
         ?>" readonly style="background-color: #f4f4f4;">

        <label>Current Condition</label>
        <input type="text" value="<?php echo htmlspecialchars($searchResult['condition']);
         ?>" readonly style="background-color: #f4f4f4;">

        <hr style="border: 0.5px solid #f2880f; margin: 20px 0;">

        <label>Update Working Condition</label>
        <select name="condition" required>
            <option value="Good" <?php if($searchResult['condition'] == 'Good') echo 'selected'; ?>>Good</option>
            <option value="Slow" <?php if($searchResult['condition'] == 'Slow') echo 'selected'; ?>>Slow</option>
            <option value="Needs Repair" <?php if($searchResult['condition'] == 'Needs Repair') echo 'selected'; ?>>Needs Repair</option>
            <option value="Faulty" <?php if($searchResult['condition'] == 'Faulty') echo 'selected'; ?>>Faulty</option>
        </select>

        <label>Remarks</label>
        <textarea name="remarks" rows="3"><?php echo htmlspecialchars($searchResult['remarks']); ?></textarea>

        <button type="submit" name="update">Update Record</button>
    </form>

    <?php else: ?>
    <form action="" method="POST">
        <label>Serial Number</label>
        <input type="text" name="serial_number" 
               value="<?php echo isset($_GET['serial_number']) ? htmlspecialchars($_GET['serial_number']) : ''; ?>" 
               required>

        <label>Device Name</label>
        <input type="text" name="device_name" placeholder="e.g. HP Laptop" required>

        <label>Department</label>
        <select name="department" required>
            <option value="">-- Select Department --</option>
            <option value="FINANCE">FINANCE</option>
            <option value="HR">HR</option>
            <option value="ICT">ICT</option>
            <option value="SANAA">SANAA</option>
            <option value="SUPPLY CHAIN">SUPPLY CHAIN</option>
            <option value="KNLS">KNLS</option>
        </select>

        <label>Condition</label>
        <select name="condition" required>
            <option value="">-- Select Condition --</option>
            <option value="Good">Good</option>
            <option value="Slow">Slow</option>
            <option value="Needs Repair">Needs Repair</option>
            <option value="Faulty">Faulty</option>
        </select>

        <label>Person In Charge</label>
        <input type="text" name="person" placeholder="Staff Name" required>

        <label>Remarks</label>
        <textarea name="remarks" rows="3" placeholder="Additional notes..."></textarea>

        <button type="submit" name="submit">Register New Device</button>
    </form>
    <?php endif; ?>

</div>
</body>

</html>