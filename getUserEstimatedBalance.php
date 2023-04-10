<?php

require_once('db_transfer.php');

if(isset($_POST["UID"]))
{
    $UID = (int)$_POST["UID"];
}else return;


$sql = "SELECT balance FROM wallets WHERE user_id='$UID'";
$result = mysqli_query($conn, $sql);

$sum = 0;

if (mysqli_num_rows($result) > 0) {
    // Store the data for each row in the array
    while($row = mysqli_fetch_assoc($result)) {
        $sum += $row["balance"];
    }
    echo json_encode($sum);
} else {
    echo json_encode('Null');
}

?>