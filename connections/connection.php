<?php

function connection(){
    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "BARANGAY_SYSTEM";

    $con = new mysqli($hostname,$username,$password,$database);

        if($con->connect_error){
            echo $con->connect_error;
        }
        else{
            return $con; 
        }
}

?>