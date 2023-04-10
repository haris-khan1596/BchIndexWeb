<?php

require_once('db_transfer.php');

if(isset($_POST["UID"]))
{
    $UID = (int)$_POST["UID"];
}else return;

if(isset($_POST["CID"]))
{
    $CID = (int)$_POST["CID"];
}
else return;


$sql = "SELECT balance FROM wallets WHERE user_id='$UID' and crypto_currency_id='$CID'";
$result = mysqli_query($conn, $sql);


if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    echo json_encode($row);
}
else{
    echo json_encode("Null");
}

?>