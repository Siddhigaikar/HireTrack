<?php
session_start();

$conn=new mysqli("localhost","root","","hirehub");

if($conn->connect_error){
    die("Database not connected");
}
?>
