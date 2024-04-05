<?php
include('dbcon.php');

$id = $_POST['selector'];
$member_id = $_POST['member_id'];
$due_date = $_POST['due_date'];

if ($id == '') {
    header("location: borrow.php");
} else {
    // Insert into the borrow table with the current date for date_borrow
    mysqli_query($conn, "INSERT INTO borrow (member_id, date_borrow, due_date) VALUES ('$member_id', NOW(), '$due_date')") or die(mysqli_error());

    // Fetch the last inserted borrow_id
    $query = mysqli_query($conn, "SELECT * FROM borrow ORDER BY borrow_id DESC LIMIT 1") or die(mysqli_error());
    $row = mysqli_fetch_array($query);
    $borrow_id = $row['borrow_id'];

    // Insert into borrowdetails for each selected book
    $N = count($id);
    for ($i = 0; $i < $N; $i++) {
        mysqli_query($conn, "INSERT INTO borrowdetails (book_id, borrow_id, borrow_status, date_borrow) VALUES ('$id[$i]', '$borrow_id', 'pending', NOW())") or die(mysqli_error());
    }

    header("location: borrow.php");
}
?>
