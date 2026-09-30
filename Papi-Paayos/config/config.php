<?php

function connection(){
    $host = "localhost";
    $user = "root";
    $pass = "";
    $dbname = "papipaayos";

    $con = new mysqli($host,$user,$pass,$dbname);
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }else{
        $con->set_charset("utf8mb4");
        return $con;
    }
}

?>

