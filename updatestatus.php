<?php
$conn = new mysqli("localhost", "root", "", "smartcare");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['approve'])) {

    $id = $_POST['id'];

    $sql = "UPDATE maintenance_request 
            SET status = 'Approved' 
            WHERE id = '$id'";

    if ($conn->query($sql) === TRUE) {
        header("Location:/smartcare/admin_dashboard.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>