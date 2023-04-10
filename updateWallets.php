<?php

require_once('db_transfer.php');

if(isset($_POST["SUID"]))
{
    $SUID = (int)$_POST["SUID"];
}else return;

if(isset($_POST["RUID"]))
{
    $RUID = (int)$_POST["RUID"];
}else return;

if(isset($_POST["CID"]))
{
    $CID = (int)$_POST["CID"];
}
else return;

if(isset($_POST["amount"]))
{
    $amount = (double)$_POST["amount"];
}
else return;


$sql = "SELECT balance FROM wallets WHERE user_id='$SUID' and crypto_currency_id='$CID'";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    $SBalance = $row["balance"] - $amount;
}

$sql = "SELECT balance FROM wallets WHERE user_id='$RUID' and crypto_currency_id='$CID'";
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    $RBalance = $row["balance"] + $amount;
}


$sql = "UPDATE wallets SET balance='$SBalance' WHERE user_id='$SUID' and crypto_currency_id='$CID'";
$result = mysqli_query($conn, $sql);

$sql = "UPDATE wallets SET balance='$RBalance' WHERE user_id='$RUID' and crypto_currency_id='$CID'";
$result = mysqli_query($conn, $sql);

// Sender Transaction table
$sql="INSERT INTO transactions (`crypto_currency_id`, `user_id`, `amount`, `charge`, `post_balance`, `trx_type`, `details`, `remark`) VALUES ($CID,$SUID,$amount,'0.00000000',$SBalance,'-','successfully transferred','p2p Transfer')";
$result = mysqli_query($conn, $sql);

// Receiver Transaction table
$sql="INSERT INTO transactions (`crypto_currency_id`, `user_id`, `amount`, `charge`, `post_balance`, `trx_type`, `details`, `remark`) VALUES ($CID,$RUID,$amount,'0.00000000',$RBalance,'+','successfully received','p2p Transfer')";
$result = mysqli_query($conn, $sql);

echo json_encode("success");

?>