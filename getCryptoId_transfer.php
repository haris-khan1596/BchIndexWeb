<?php 

require_once('db_transfer.php');

if(isset($_POST["name"]))
{
    $name = $_POST["name"];
}
else return;

$sql = "SELECT id FROM crypto_currencies WHERE name='$name'";
$result = mysqli_query($conn, $sql);


if(mysqli_num_rows($result) > 0){
    $row = mysqli_fetch_assoc($result);
    echo json_encode($row);
}
else{
    echo json_encode("Null");
}



?>