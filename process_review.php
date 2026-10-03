<?php
include 'db.php';

if(isset($_POST['status'])) {
    $id = $_POST['id'];
    $current_interval = $_POST['current_interval'];
    $status = $_POST['status'];

    if ($status == 'correct') {
        // Double the interval (1 day -> 2 days -> 4 -> 8 -> 16)
        $new_interval = $current_interval * 2;
    } else {
        // If wrong, reset back to 1 day
        $new_interval = 1;
    }

    // Calculate the new date
    $next_review = date('Y-m-d', strtotime("+$new_interval days"));

    // Update the database
    $sql = "UPDATE mistakes SET interval_days = $new_interval, next_review = '$next_review' WHERE id = $id";
    $conn->query($sql);

    // Send back to dashboard
    header("Location: index.php");
    exit();
}
?>