<?php
$conn = mysqli_connect("localhost", "root", "", "uni_tuit_db");
if (!$conn) { die("Connection failed: " . mysqli_connect_error()); }
session_start();
?>