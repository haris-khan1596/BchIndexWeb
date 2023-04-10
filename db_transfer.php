<?php
// $dns = 'mysql:host=localhost;dbname=mygzkdqb_laravel';
// $user = 'mygzkdqb';
// $pass = 'Junaid@101325!';

// try{
//     $db = new PDO($dns, $user, $pass);
//     // echo 'Connected';
// }
// catch(PDOException $e){
//     echo $e->getMessage();
// }

$host = "localhost";
$username = "mygzkdqb_laravel";
$password = "Laravel123@";
$dbname = "mygzkdqb_laravel";

$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    // echo 'connected';
}

?>