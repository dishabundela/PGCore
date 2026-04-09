<?php
require_once 'db.php';

echo "Testing database connection...<br>";

$sql = "SELECT COUNT(*) as total FROM complaints";
$result = mysqli_query($conn, $sql);

if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "✅ Connected successfully! Total complaints: " . $row['total'];
} else {
    echo "❌ Query failed: " . mysqli_error($conn);
}

mysqli_close($conn);
?>