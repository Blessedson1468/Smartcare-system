<?php
session_start();
include 'database.php';

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// 2. Handle Maintenance Approval
if (isset($_POST['approve'])) {
    $id = $_POST['id'];
    $update = "UPDATE maintenance_request SET status = 'Approved' WHERE id = ?";
    $stmt = $conn->prepare($update);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: admindashboard.php");
        exit();
    }
}

// 3. NEW: Handle User/Staff Approval
if (isset($_POST['approve_user'])) {
    $user_id = $_POST['user_id'];
    $update_user = "UPDATE users SET status = 'Approved' WHERE id = ?";
    $stmt_user = $conn->prepare($update_user);
    $stmt_user->bind_param("i", $user_id);
    if ($stmt_user->execute()) {
        header("Location: admindashboard.php");
        exit();
    }
}

// 4. Fetch Data
$maintenance_sql = "SELECT * FROM maintenance_request ORDER BY status DESC, id DESC";
$maintenance_result = $conn->query($maintenance_sql);

$users_sql = "SELECT id, username, role, status FROM users WHERE status = 'Pending'";
$users_result = $conn->query($users_sql);
?>

<div style="max-width: 1000px; margin: 20px auto; font-family: Arial; padding: 20px; background: #fff; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
    
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h2>SmartCare Admin Dashboard</h2>
        <a href="logout.php" style="color: red; text-decoration: none; font-weight: bold; padding: 10px; border: 1px solid red; border-radius: 5px;">Logout</a>
    </div>

    <hr>

    <section>
        <h3>Pending Staff Access Requests</h3>
        <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse; margin-bottom: 30px;">
            <tr style="background-color: #f2880f; color: white;">
                <th>Username</th>
                <th>Requested Role</th>
                <th>Action</th>
            </tr>
            <?php if ($users_result->num_rows > 0) : ?>
                <?php while($user = $users_result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($user['username']); ?></td>
                    <td><?= htmlspecialchars($user['role']); ?></td>
                    <td>
                        <form method="POST" style="margin:0;">
                            <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                            <button type="submit" name="approve_user" style="background-color:blue; color:white; border:none; padding: 8px; border-radius: 4px; cursor:pointer;">
                                Approve User
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr><td colspan="3" style="text-align:center; color: #777;">No pending staff requests.</td></tr>
            <?php endif; ?>
        </table>
    </section>

    <section>
        <h3>Device Maintenance Requests</h3>
        <table border="1" cellpadding="10" style="width:100%; border-collapse: collapse; text-align: left;">
            <tr style="background-color: #2d0bc3; color: white;">
                <th>Serial Number</th>
                <th>Device Name</th>
                <th>Dept</th>
                <th>Condition</th>
                <th>Person</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php if ($maintenance_result->num_rows > 0) : ?>
                <?php while($row = $maintenance_result->fetch_assoc()) : ?>
                <tr>
                    <td><?= htmlspecialchars($row['serial_number']); ?></td>
                    <td><?= htmlspecialchars($row['device_name']); ?></td>
                    <td><?= htmlspecialchars($row['department']); ?></td>
                    <td><?= htmlspecialchars($row['condition']); ?></td>
                    <td><?= htmlspecialchars($row['person']); ?></td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 12px; font-size: 12px; background: <?= ($row['status'] == 'Approved') ? '#d4edda' : '#fff3cd' ?>;">
                            <?= htmlspecialchars($row['status']); ?>
                        </span>
                    </td>
                    <td>
                        <?php if (strtolower($row['status']) == 'pending') : ?>
                            <form method="POST" style="margin:0;">
                                <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                <button type="submit" name="approve" style="background-color:orange; border:none; padding: 5px 10px; border-radius: 4px; cursor:pointer;">Approve</button>
                            </form>
                        <?php else : ?>
                            <button disabled style="background-color:#28a745; color:white; border:none; padding: 5px 10px; border-radius: 4px;">Done</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else : ?>
                <tr><td colspan="7" style="text-align:center;">No maintenance records found.</td></tr>
            <?php endif; ?>
        </table>
    </section>
</div>