<?php
session_start();
include "control.class.php";
include 'mydb.class.php';
	
	$login = new control(session_id(),new MyDB());


 
?>