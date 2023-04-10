<?php
require_once('db_transfer.php');

$sql = "SELECT name FROM crypto_currencies";
$result = mysqli_query($conn, $sql);

$list = array();

// If there are results, store them in the array
if (mysqli_num_rows($result) > 0) {
    // Store the data for each row in the array
    while($row = mysqli_fetch_assoc($result)) {
        $list[] = $row["name"];
    }
    echo json_encode($list);
} else {
    echo json_encode('Null');
}

?>